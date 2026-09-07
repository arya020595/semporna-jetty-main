<?php

namespace App\Http\Controllers\CompanyManifest;

use App\Actions\CompanyManifest\CreatePayment;
use App\Actions\CompanyManifest\GeneratePaymentReceipt;
use App\Actions\CompanyManifest\GetManifestPayment;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyManifest\FormPaymentRequest;
use App\Http\Requests\CompanyManifest\PaymentSearchRequest;
use App\Http\Resources\CompanyManifest\ManifestPaymentConfirmationResource;
use App\Http\Resources\CompanyManifest\ManifestPaymentResource;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\Payment;
use App\Models\RefDestination;
use App\Models\User;
use App\Services\Payment\SenangPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PaymentFormController extends Controller
{
    protected SenangPayService $senangPayService;

    public function __construct(SenangPayService $senangPayService)
    {
        $this->senangPayService = $senangPayService;
    }

    public function index(PaymentSearchRequest $request, GetManifestPayment $action)
    {
        /**
         * @var \App\Models\User
         */
        $user = Auth::user();

        $filters = $request->validated();
        $data = $action->setUser($user)->execute($filters);
        $request->session()->put('filters', $filters);

        return Inertia::render('Payment/Index', [
            "title" => "Payment",
            "additional" => [
                "data" => ManifestPaymentResource::collection($data),
                "columns" => $action->getColumns(),
                "filters" => $filters,
                "urlConfirmation" => route("panel.payment.confirmation"),
                "urlIndex" => route("panel.payment.index"),
                "urlReceipts" => route("panel.payment.receipts"),
            ]
        ]);
    }

    public function confirmation(Request $request)
    {
        $arrManifestId = $request->manifest_id ?? [];

        $arrManifest = Manifest::query()
            ->with(["manifestDestination", "manifestFee" => function ($query) {
                return $query->where("status", ManifestFee::STATUS_PENDING);
            }])
            ->whereIn("id", $arrManifestId)
            ->where("payment_status", Manifest::PAYMENT_STATUS_PENDING)
            ->get();

        if (!$arrManifest->count()) {
            throw ValidationException::withMessages([
                "manifest" => 'No Manifest Found!'
            ]);
        }

        $arrDepartureId = $arrManifest->pluck("departure_id")->toArray();
        if (count(array_unique($arrDepartureId)) > 1) {
            throw ValidationException::withMessages([
                "manifest" => 'Selected manifests must be from a single departure.'
            ]);
        }


        return Inertia::render('Payment/Confirmation', [
            'title' => "Payment Confirmation",
            "additional" => [
                "data" => ManifestPaymentConfirmationResource::collection($arrManifest),
                "urlIndex" => route("panel.payment.index"),
                "urlPayment" => route("panel.payment.store"),
                "arrManifestId" => $arrManifest->pluck("id")
            ]
        ]);
    }


    public function store(FormPaymentRequest $request, CreatePayment $action)
    {
        $arrData = $request->validated();
        $arrManifestId = $arrData["manifest_id"];
        // check if manifest fee is pending, if not return back to confirmation

        $arrManifest = Manifest::query()
            ->with(["manifestDestination", "manifestFee" => function ($query) {
                return $query->where("status", ManifestFee::STATUS_PENDING);
            }])
            ->whereIn("id", $arrManifestId)
            ->where("payment_status", Manifest::PAYMENT_STATUS_PENDING)
            ->get();

        if ($arrManifest->count() != count($arrManifestId)) {
            throw ValidationException::withMessages([
                "message" => "Some manifest alrady paid, check again your manifest data"
            ]);
        }

        $arrDepartureId = $arrManifest->pluck("departure_id")->toArray();
        if (count(array_unique($arrDepartureId)) > 1) {
            throw ValidationException::withMessages([
                "manifest" => 'Selected manifests must be from a single departure.'
            ]);
        }

        if (!$departure = RefDestination::find($arrDepartureId[0])) {
            throw ValidationException::withMessages([
                "manifest" => 'Departure not found.'
            ]);
        }

        $senangPayAccount = $departure->code == RefDestination::CODE_SEAFEST
            ? CreatePayment::MERCHANT_SEAFEST
            : CreatePayment::MERCHANT_HARTAWAN_STABIL;

        $payment = DB::transaction(function () use ($action, $arrData, $arrManifest, $senangPayAccount) {
            /**
             * @var User
             */

            $user = Auth::user();
            $arrData = [
                "name" => $user->name,
                "email" => $user->email,
                "tel_no" => $user->tel_no,
                "manifest_id" => $arrManifest->pluck("id")->toArray(),
                "merchant_id" => $senangPayAccount
            ];

            $payment = $action->execute($arrData, $user);

            return $payment;
        });

        $arrManifestNumber = $arrManifest
            ->pluck("form_number")
            ->toArray();

        $merchantId = config("senangpay." . $senangPayAccount . ".merchant_id");
        $secretKey = config("senangpay." . $senangPayAccount . ".secret_key");

        try {
            $urlPayment = $this->senangPayService
                ->setMerchantId($merchantId)
                ->setSecretKey($secretKey)
                ->generatePaymentUrl(
                    $amount = $payment->amount,
                    $orderId = SenangPayService::PREFIX_CODE . $payment->code,
                    $detail = 'Manifest: ' . implode(", ", $arrManifestNumber),
                    $name = trim($payment->first_name . " " . $payment->last_name),
                    $email = $payment->email,
                    $phone = $payment->tel_no ?? "",
                    route('senangpay.return'),
                    route('senangpay.callback')
                );
        } catch (\Exception $e) {
            Log::error('SenangPay payment initiation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with("message", [
                    "status" => "error",
                    "message" => "Could not initiate payment. Please try again."
                ]);
        }

        return Inertia::render('Payment/Confirmation', [
            "urlPayment" => $urlPayment
        ]);
    }

    public function downloadReceipt(Request $request, GeneratePaymentReceipt $action)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        return $action->handle(Auth::user(), $request->start_date, $request->end_date);
    }
}
