<?php

namespace Tests\Feature\Api;

use App\Models\Manifest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityListFilterTest extends TestCase
{
    use RefreshDatabase;

    private const COMPANY_ID = 5;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['id' => User::ROLE_AGENT, 'name' => 'Agent', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'autho-my-dashboard-list', 'guard_name' => 'web']));

        $user = User::factory()->create(['company_id' => self::COMPANY_ID]);
        $user->assignRole($role);
        Sanctum::actingAs($user);

        $this->manifest('FRM-001', 'Coral Breeze', 'SA/P11/0513', '2026-09-01', Manifest::STATUS_PENDING);
        $this->manifest('FRM-002', 'Coral Breeze', 'SA/P12/0510', '2026-09-10', Manifest::STATUS_APPROVED);
        $this->manifest('FRM-003', 'Blue Lagoon', 'SA/P13/0001', '2026-09-20', Manifest::STATUS_APPROVED);
        // Another company's manifest: never visible to this agent, whatever the filter.
        $this->manifest('FRM-004', 'Coral Breeze', 'SA/P11/0513', '2026-09-05', Manifest::STATUS_APPROVED, 99);
    }

    private function manifest(string $form, string $company, string $boat, string $date, int $status, int $companyId = self::COMPANY_ID): void
    {
        DB::table('manifest')->insert([
            'uuid' => (string) Str::uuid(),
            'user_id' => 1, 'form_number' => $form, 'form_date' => $date, 'sequence' => '1',
            'departure_date' => $date, 'departure_time' => '08:00:00',
            'departure_id' => 1, 'departure_name' => 'Semporna', 'type' => 1,
            'company_id' => $companyId, 'company_name' => $company,
            'boat_number' => $boat, 'boatman_name' => '', 'boatman_mate_no' => '', 'seaman_no' => '',
            'boatman_ic_no' => '', 'assistant_name' => '', 'assistant_mate_no' => '', 'assistant_ic_no' => '',
            'is_final' => 1, 'status' => $status, 'payment_status' => Manifest::PAYMENT_STATUS_PAID,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function forms(string $query = ''): array
    {
        $response = $this->getJson('/api/activity' . $query)->assertOk();

        return collect($response->json('data.list'))
            ->map(function ($item) {
                return str_replace(' (DRAFT)', '', $item['form_number']);
            })->all();
    }

    public function test_default_order_is_newest_departure_first_and_scoped_to_the_company()
    {
        $this->assertSame(['FRM-003', 'FRM-002', 'FRM-001'], $this->forms());
    }

    public function test_filter_search_matches_company_boat_or_form_number()
    {
        $this->assertSame(['FRM-002', 'FRM-001'], $this->forms('?filter[search]=coral'));
        $this->assertSame(['FRM-003'], $this->forms('?filter[search]=P13'));
        $this->assertSame(['FRM-002'], $this->forms('?filter[search]=FRM-002'));
    }

    public function test_filter_status_accepts_one_or_many_values()
    {
        $this->assertSame(['FRM-001'], $this->forms('?filter[status]=0'));
        $this->assertSame(['FRM-003', 'FRM-002'], $this->forms('?filter[status]=1'));
        $this->assertSame(['FRM-003', 'FRM-002', 'FRM-001'], $this->forms('?filter[status]=0,1'));
    }

    public function test_filter_departure_date_exact_and_range()
    {
        $this->assertSame(['FRM-002'], $this->forms('?filter[departure_date]=2026-09-10'));
        $this->assertSame(['FRM-003', 'FRM-002'], $this->forms('?filter[departure_date_from]=2026-09-10'));
        $this->assertSame(['FRM-002', 'FRM-001'], $this->forms('?filter[departure_date_to]=2026-09-10'));
        $this->assertSame(['FRM-002'], $this->forms('?filter[departure_date_from]=2026-09-02&filter[departure_date_to]=2026-09-11'));
    }

    public function test_filters_combine()
    {
        $this->assertSame(['FRM-002'], $this->forms('?filter[search]=coral&filter[status]=1'));
    }

    public function test_sort_ascending_and_descending()
    {
        $this->assertSame(['FRM-001', 'FRM-002', 'FRM-003'], $this->forms('?sort=departure_date'));
        $this->assertSame(['FRM-003', 'FRM-002', 'FRM-001'], $this->forms('?sort=-form_number'));
    }

    public function test_meta_date_range_ignores_the_date_filters_but_not_the_others()
    {
        $meta = $this->getJson('/api/activity?filter[departure_date]=2026-09-10')->assertOk()->json('data.meta');
        $this->assertSame(['2026-09-01', '2026-09-20'], [$meta['min_date'], $meta['max_date']]);

        $meta = $this->getJson('/api/activity?filter[status]=1')->assertOk()->json('data.meta');
        $this->assertSame(['2026-09-10', '2026-09-20'], [$meta['min_date'], $meta['max_date']]);
    }

    public function test_unsupported_filter_and_sort_are_rejected()
    {
        $this->getJson('/api/activity?filter[bogus]=1')->assertStatus(400);
        $this->getJson('/api/activity?sort=bogus')->assertStatus(400);
    }

    public function test_invalid_filter_values_are_rejected()
    {
        $this->getJson('/api/activity?filter[departure_date]=tomorrow')->assertStatus(422);
        $this->getJson('/api/activity?filter[status]=abc')->assertStatus(422);
    }

    public function test_legacy_search_fields_still_work()
    {
        $this->assertSame(['FRM-002', 'FRM-001'], $this->forms('?search_fields[]=search&search_values[]=coral'));
        $this->assertSame(['FRM-003', 'FRM-002'], $this->forms('?search_fields[]=status&search_values[]=1'));
        $this->assertSame(['FRM-002'], $this->forms('?search_fields[]=departure_date&search_values[]=2026-09-10'));
        $this->assertSame(['FRM-001', 'FRM-002', 'FRM-003'], $this->forms('?order_type=asc'));
        // Unknown legacy fields were always ignored
        $this->assertSame(['FRM-003', 'FRM-002', 'FRM-001'], $this->forms('?search_fields[]=bogus&search_values[]=x'));
    }
}
