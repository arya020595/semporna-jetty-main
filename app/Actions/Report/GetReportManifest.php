<?php

namespace App\Actions\Report;

use Illuminate\Support\Collection;
use App\Models\Manifest;
use App\Models\ManifestFee;
use App\Models\RefDestination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetReportManifest
{
    protected $columns;

    protected User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Execute the action.
     *
     * Methodology aligned with Power BI:
     * - Base filters: status=1 (approved), payment_status=1 (paid), not soft-deleted
     * - Departure: Jetty Pelancong (CODE_SEMPORNA) automatically includes Seafest Jetty (CODE_SEAFEST)
     *   because Power BI groups both under "Jetty Pelancong" (departure_id 7 or 8)
     * - Passenger/gender counts: pre-aggregated from guests table per manifest_id
     * - Fee amounts: pre-aggregated from manifest_fee table per manifest_id
     * - Both use LEFT JOIN to avoid Cartesian product and to count all manifests
     *   even if they have no fee/guest records (data integrity edge cases)
     * - Test/dummy records excluded (matching Power BI exclusion filters)
     *
     * @param  array  $filters
     * @return Collection
     */
    public function execute(array $filters)
    {
        // --- Departure ID resolution ------------------------------------------
        // Power BI groups Jetty Pelancong (CODE_SEMPORNA) and Seafest Jetty
        // (CODE_SEAFEST) together under "Jetty Pelancong". We replicate that
        // behavior: when the user selects Jetty Pelancong, also include Seafest.
        $departureIds = $this->resolveDepartureIds($filters['departure_id'] ?? null);

        // --- Pre-aggregate manifest_fee per manifest (1 row per manifest_id) --
        // Avoids Cartesian product when also joining guest data.
        // Only sum PAID fee records; LEFT JOIN so manifests with no PAID fee
        // record still appear (handled via COALESCE in outer SELECT).
        $feeSubquery = DB::table('manifest_fee')
            ->select(
                'manifest_id',
                DB::raw('SUM(local_adult) as local_adult'),
                DB::raw('SUM(local_child) as local_child'),
                DB::raw('SUM(foreign_adult) as foreign_adult'),
                DB::raw('SUM(foreign_child) as foreign_child'),
                DB::raw('SUM(
                    local_adult_fee   * local_adult   +
                    local_child_fee   * local_child   +
                    foreign_adult_fee * foreign_adult +
                    foreign_child_fee * foreign_child
                ) as charge_passenger'),
                DB::raw('SUM(boat_fee) as charge_boat_fee'),
            )
            ->where('status', ManifestFee::STATUS_PAID)
            ->whereNull('deleted_at')
            ->groupBy('manifest_id');

        // --- Pre-aggregate guest gender per manifest (1 row per manifest_id) --
        // Without this, LEFT JOIN guests creates M rows per manifest, causing
        // fee values to be summed M times (M = number of guests per manifest).
        $guestSubquery = DB::table('guests')
            ->select(
                'manifest_id',
                DB::raw("SUM(CASE WHEN gender = 'M' THEN 1 ELSE 0 END) as male_count"),
                DB::raw("SUM(CASE WHEN gender = 'F' THEN 1 ELSE 0 END) as female_count"),
            )
            ->where('is_staff', 0)
            ->whereNull('deleted_at')
            ->groupBy('manifest_id');

        // --- Main query -------------------------------------------------------
        $query = Manifest::query()
            ->leftJoinSub($feeSubquery, 'mf', function ($join) {
                $join->on('mf.manifest_id', '=', 'manifest.id');
            })
            ->leftJoinSub($guestSubquery, 'g', function ($join) {
                $join->on('g.manifest_id', '=', 'manifest.id');
            })
            // Primary status filters (matching Power BI: status=1, payment_status=1)
            ->where('manifest.status', Manifest::STATUS_APPROVED)
            ->where('manifest.payment_status', Manifest::PAYMENT_STATUS_PAID)
            ->whereNull('manifest.deleted_at')
            // Exclude test/dummy records (matching Power BI exclusion filters)
            // ->whereNotIn('manifest.company_name', ['PT. Company Dummy', 'Justin Company'])
            // ->whereNotIn('manifest.boatman_name', ['airiel', 'Amiirul Hamizan', 'hamizan', '201961005713'])
            // ->where(function ($q) {
            //     $q->whereNull('manifest.assistant_name')
            //         ->orWhere('manifest.assistant_name', '!=', 'Habri Azizi');
            // })
            // Date range filter
            ->when($filters['start'] ?? false, function ($q) use ($filters) {
                return $q->where('manifest.departure_date', '>=', $filters['start']);
            })
            ->when($filters['end'] ?? false, function ($q) use ($filters) {
                return $q->where('manifest.departure_date', '<=', $filters['end']);
            })
            // Departure filter (Jetty Pelancong automatically includes Seafest)
            ->when(!empty($departureIds), function ($q) use ($departureIds) {
                return $q->whereIn('manifest.departure_id', $departureIds);
            })
            // Jetty operator: restrict to their own jetty group
            ->when($this->user->hasRole(User::ROLE_OPERATOR_JETTY), function ($q) {
                $operatorDepartureIds = $this->resolveDepartureIds($this->user->jetty_id);
                return $q->whereIn('manifest.departure_id', $operatorDepartureIds);
            })
            ->select(
                'manifest.departure_date',
                'manifest.departure_name',

                // Gender counts from guest table (actual passengers)
                DB::raw("SUM(COALESCE(g.male_count, 0))   as total_male"),
                DB::raw("SUM(COALESCE(g.female_count, 0)) as total_female"),

                // Passenger category counts from manifest_fee (billing-based)
                DB::raw("SUM(COALESCE(mf.local_adult, 0))   as total_local_adult"),
                DB::raw("SUM(COALESCE(mf.local_child, 0))   as total_local_child"),
                DB::raw("SUM(COALESCE(mf.foreign_adult, 0)) as total_foreign_adult"),
                DB::raw("SUM(COALESCE(mf.foreign_child, 0)) as total_foreign_child"),

                // Revenue from manifest_fee (billing-based)
                DB::raw("SUM(COALESCE(mf.charge_passenger, 0)) as total_charge_passenger"),
                DB::raw("SUM(COALESCE(mf.charge_boat_fee, 0))  as total_charge_boat_fee"),

                // Manifest and company counts (DISTINCT prevents double-counting)
                DB::raw("COUNT(DISTINCT manifest.id)         as total_manifest"),
                DB::raw("COUNT(DISTINCT manifest.company_id) as total_unique_companies"),
            )
            ->groupBy('manifest.departure_date', 'manifest.departure_name')
            ->orderBy('manifest.departure_date');

        return $query->get();
    }

    /**
     * Resolve departure IDs for filtering.
     *
     * When Jetty Pelancong (CODE_SEMPORNA) is selected, Seafest Jetty
     * (CODE_SEAFEST) is automatically included because Power BI groups
     * both under the same "Jetty Pelancong" category.
     *
     * @param int|null $departureId
     * @return array
     */
    protected function resolveDepartureIds(?int $departureId): array
    {
        if (!$departureId) {
            return [];
        }

        return [$departureId];
    }

    public function getColumns()
    {
        return $this->columns;
    }
}
