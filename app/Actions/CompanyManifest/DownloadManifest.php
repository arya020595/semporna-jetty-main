<?php

namespace App\Actions\CompanyManifest;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\RefNationality;
use App\Helpers\DatatablesHelper;
use App\Models\Boatman;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\RefDestination;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\Facades\DNS2DFacade;
use Spatie\Permission\Models\Role;

class DownloadManifest
{
    protected $columns;

    /**
     * @var User
     */
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    /**
     * Execute the action
     *
     * @param  Manifest  $manifest
     * @return \Barryvdh\DomPDF\PDF
     */
    public function execute(Manifest $manifest)
    {
        $manifest->load(
            'departure',
            'approvement.role',
            'approvement.user',
            'manifestDestination'
        );

        $createdByUser = User::find($manifest->created_by);

        $bypassApproval = config('features.bypass_approval_flow');

        if (config('features.bypass_seafest_payment') && $manifest->departure && $manifest->departure->code == RefDestination::CODE_SEAFEST) {
            $bypassApproval = true;
        }

        $isSabahParks = $manifest->isSabahParks();

        $authorityApprovals = $this->getAuthorityApprovals($manifest, $isSabahParks);

        $manifestFee = $manifest->manifestFee()
            ->whereStatus(1)
            ->get();

        $primaryFee = $manifestFee
            ->where("type", ManifestFee::TYPE_PRIMARY)
            ->first();

        $passenger = $manifest->guest->where("is_additional", "!=", 1)
            ->where("is_staff", "!=", 1)
            ->values()
            ->toArray();

        $additionalPassenger = $manifest->guest->where("is_additional", 1)
            ->where("is_staff", "!=", 1)
            ->values()
            ->toArray();

        // Merge all passengers into one array
        // $passenger = array_merge($primaryPassengers, $additionalPassengers);

        $staff = $manifest->guest->where("is_staff", 1)
            ->values()
            ->toArray();

        $template = "download-pdf.manifest-landscape";


        return Pdf::loadView($template, [
            "title" => "Receipt Manifest " . $manifest->form_number,
            "data" => [
                "manifest" => $manifest,
                "createdByUser" => $createdByUser,
                "arrInstructor" => $manifest->manifestBoatman->where("type", Boatman::TYPE_INSTRUCTOR),
                "arrDivemaster" => $manifest->manifestBoatman->where("type", Boatman::TYPE_DIVEMASTER),
                "arrGuide" => $manifest->manifestBoatman->where("type", Boatman::TYPE_GUIDE),
                "destination" => $manifest->manifestDestination->pluck("ref_destination_name")->implode(", "),
                "passenger" => $passenger,
                "staff" => $staff,
                "additionalPassenger" => $additionalPassenger ?? [],
                "manifest_fee" => [
                    "local_child" => $manifestFee->sum("local_child"),
                    "local_adult" => $manifestFee->sum("local_adult"),
                    "foreign_child" => $manifestFee->sum("foreign_child"),
                    "foreign_adult" => $manifestFee->sum("foreign_adult"),

                    "total" => number_format($manifestFee->sum("total"), 2),

                    "local_child_fee" => optional($primaryFee)->local_child_fee,
                    "local_adult_fee" => optional($primaryFee)->local_adult_fee,
                    "foreign_child_fee" => optional($primaryFee)->foreign_child_fee,
                    "foreign_adult_fee" => optional($primaryFee)->foreign_adult_fee,
                    "boat_fee" => optional($primaryFee)->boat_fee ?? 0,
                ],
                "qrcode" => $manifest->payment_status == Manifest::PAYMENT_STATUS_PAID
                    ? 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($manifest->form_number, 'QRCODE', 33, 33)
                    : false,
                "bypassApproval"     => $bypassApproval,
                "authorityApprovals" => $authorityApprovals,
                "isSabahParks"       => $isSabahParks,
            ]
        ])->setOption(['dpi' => 110]);
    }

    /**
     * Get the latest approvements for each authority role
     */
    private function getAuthorityApprovals(Manifest $manifest, bool $isSabahParks): array
    {
        $authorityRoles = [
            [
                'role_id'          => User::ROLE_JABATAN_PELABUHAN,
                'name'             => 'Jabatan Pelabuhan Dan Dermaga Sabah',
                'logo'             => public_path('assets/images/jabatan-pelabuhan-logo.png'),
                'stamp'            => public_path('assets/images/jabatan-pelabuhan-stamp.png'),
                'requires_approval' => true,
            ],
            [
                'role_id'          => User::ROLE_JABATAN_LAUT,
                'name'             => 'Jabatan Laut Malaysia',
                'logo'             => public_path('assets/images/jabatan-laut-logo.png'),
                'stamp'            => public_path('assets/images/jabatan-laut-stamp.png'),
                'requires_approval' => true,
            ],
            [
                'role_id'          => User::ROLE_PDRM,
                'name'             => 'Polis Diraja Malaysia',
                'logo'             => public_path('assets/images/pdrm-logo.png'),
                'stamp'            => public_path('assets/images/pdrm-stamp.png'),
                'requires_approval' => true,
            ],
            [
                'role_id'          => User::ROLE_SABAH_PARKS,
                'name'             => 'Sabah Parks',
                'logo'             => public_path('assets/images/sabah-parks-logo.jpeg'),
                'stamp'            => public_path('assets/images/jabatan-laut-stamp.png'),
                'requires_approval' => $isSabahParks,
            ],
        ];

        $latestApprovements = $manifest->approvement
            ->where('version', $manifest->version)
            ->sortByDesc('created_at')
            ->groupBy('role_id')
            ->map(fn($group) => $group->first());

        return collect($authorityRoles)->map(function ($authority) use ($latestApprovements) {
            $approvement = $latestApprovements->get($authority['role_id']);
            return [
                'authority_name'    => $authority['name'],
                'logo'              => $authority['logo'],
                'stamp'             => $authority['stamp'],
                'requires_approval' => $authority['requires_approval'],
                'status'            => $approvement ? $approvement->status : null,
                'status_text'       => $approvement ? $approvement->status_text : 'Pending',
                'user_name'         => $approvement ? $approvement->user->name : null,
                'date'              => $approvement ? $approvement->created_at->setTimezone('Asia/Kuala_Lumpur')->format('d/m/Y H:i') : null,
                'comments'          => $approvement ? $approvement->comments : null,
            ];
        })->toArray();
    }
}
