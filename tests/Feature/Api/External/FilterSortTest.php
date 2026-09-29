<?php

namespace Tests\Feature\Api\External;

use App\Models\Boat;
use App\Models\Boatman;
use App\Models\Company;
use App\Models\Guest;
use App\Models\RefActivity;
use App\Models\RefDestination;
use App\Models\RefNationality;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterSortTest extends TestCase
{
    use RefreshDatabase;

    private function fetch(string $uri)
    {
        return $this->withHeader('Authorization', 'Bearer ' . config('external_api.token'))
            ->getJson($uri);
    }

    public function test_reference_lists_filter_by_partial_title_and_exact_code()
    {
        foreach ([
            ['/api/external/destinations', RefDestination::class],
            ['/api/external/activities', RefActivity::class],
            ['/api/external/nationalities', RefNationality::class],
        ] as [$uri, $model]) {
            $alpha = $model::factory()->create(['title' => 'Alpha Island', 'code' => 'X_00001']);
            $model::factory()->create(['title' => 'Bravo Reef', 'code' => 'X_00002']);

            $this->fetch($uri . '?filter[title]=alpha')
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.id', $alpha->id);

            $this->fetch($uri . '?filter[code]=X_00002')
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'Bravo Reef');

            $this->fetch($uri . '?filter[id]=' . $alpha->id . '&sort=-title')
                ->assertOk()
                ->assertJsonCount(1, 'data');

            $this->fetch($uri . '?sort=-title')
                ->assertOk()
                ->assertJsonPath('data.0.title', 'Bravo Reef');
        }
    }

    public function test_destinations_filter_never_exposes_departure_points()
    {
        RefDestination::factory()->departure()->create(['title' => 'Jetty Alpha']);

        $this->fetch('/api/external/destinations?filter[title]=alpha')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    private function company(string $name): Company
    {
        return Company::create(['user_id' => 1, 'name' => $name]);
    }

    public function test_boats_filter_by_company_and_partial_number()
    {
        $c1 = $this->company('Coral Breeze');
        $c2 = $this->company('Blue Lagoon');
        $a = Boat::create(['company_id' => $c1->id, 'number' => 'SA/P11/0513']);
        Boat::create(['company_id' => $c2->id, 'number' => 'SA/P12/0510']);

        $this->fetch('/api/external/boats?filter[company_id]=' . $c1->id)
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $a->id);

        $this->fetch('/api/external/boats?filter[number]=P12&sort=-number')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.number', 'SA/P12/0510');
    }

    public function test_boats_return_company_and_nested_crew()
    {
        $company = $this->company('Coral Breeze');
        $boat = Boat::create(['company_id' => $company->id, 'number' => 'SA/P11/0513', 'name' => 'Sea Explorer', 'license' => 'LIC-1']);
        Boatman::create(['company_id' => $company->id, 'boat_id' => $boat->id, 'name' => 'Ahmad', 'type' => 1, 'seaman_card_no' => 'SC-1']);
        Boat::create(['company_id' => $company->id, 'number' => 'SA/P12/0510', 'name' => 'Empty']);

        $response = $this->fetch('/api/external/boats?sort=number')->assertOk();

        $response->assertJsonPath('data.0.name', 'Sea Explorer')
            ->assertJsonPath('data.0.license', 'LIC-1')
            ->assertJsonPath('data.0.company', ['id' => $company->id, 'name' => 'Coral Breeze'])
            ->assertJsonPath('data.0.boatman.0.name', 'Ahmad')
            ->assertJsonPath('data.0.boatman.0.type_label', 'Boatman')
            ->assertJsonPath('data.0.boatman.0.seaman_card_no', 'SC-1')
            ->assertJsonPath('data.1.boatman', []);

        $this->assertArrayNotHasKey('company', $response->json('data.0.boatman.0'));
    }

    public function test_a_deleted_company_does_not_break_the_boat_and_boatmen_lists()
    {
        $company = $this->company('Gone Resort');
        $boat = Boat::create(['company_id' => $company->id, 'number' => 'SA/P11/0513']);
        Boatman::create(['company_id' => $company->id, 'boat_id' => $boat->id, 'name' => 'Ahmad', 'type' => 1]);
        $company->delete();

        $this->fetch('/api/external/boats')
            ->assertOk()
            ->assertJsonPath('data.0.company', null)
            ->assertJsonPath('data.0.boatman.0.name', 'Ahmad');

        $this->fetch('/api/external/boatmen')
            ->assertOk()
            ->assertJsonPath('data.0.company', null);
    }

    public function test_boatmen_return_company_and_type_label()
    {
        $company = $this->company('Coral Breeze');
        Boatman::create(['company_id' => $company->id, 'name' => 'Faizal', 'type' => 3]);

        $this->fetch('/api/external/boatmen')
            ->assertOk()
            ->assertJsonPath('data.0.type_label', 'Instructor')
            ->assertJsonPath('data.0.boat_id', null)
            ->assertJsonPath('data.0.company', ['id' => $company->id, 'name' => 'Coral Breeze']);
    }

    public function test_boatmen_filter_by_multiple_types_and_name()
    {
        $company = $this->company('Coral Breeze');
        foreach ([[1, 'Ahmad'], [3, 'Faizal'], [4, 'Diana'], [5, 'Guntur']] as [$type, $name]) {
            Boatman::create(['company_id' => $company->id, 'name' => $name, 'type' => $type]);
        }

        $this->fetch('/api/external/boatmen?filter[type]=3,4,5')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.name', 'Diana');

        $this->fetch('/api/external/boatmen?filter[name]=ahm')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->fetch('/api/external/boatmen?filter[type]=3,4,5&sort=-type')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Guntur');
    }

    public function test_guests_filter_and_sort()
    {
        $base = [
            'manifest_id' => 1, 'nationality_id' => 1, 'nationality_name' => 'Malaysian',
            'activity_name' => 'Snorkeling', 'age' => 30, 'gender' => 'F',
            'next_of_kin' => 'n', 'emergency_contact' => 'e',
        ];
        Guest::create(['name' => 'Siti Aminah', 'ic_no' => 'A1'] + $base);
        Guest::create(['name' => 'John Smith', 'ic_no' => 'B2', 'gender' => 'M', 'age' => 50] + $base);

        $this->fetch('/api/external/guests?filter[gender]=M')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'John Smith');

        $this->fetch('/api/external/guests?filter[name]=siti')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->fetch('/api/external/guests?sort=-age')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'John Smith')
            ->assertJsonPath('meta.total', 2);
    }

    public function test_unsupported_filter_and_sort_are_rejected_with_the_error_envelope()
    {
        foreach (['destinations', 'activities', 'nationalities', 'boats', 'boatmen', 'guests'] as $endpoint) {
            $this->fetch("/api/external/{$endpoint}?filter[bogus]=1")
                ->assertStatus(400)
                ->assertJson(['success' => false, 'data' => null]);

            $this->fetch("/api/external/{$endpoint}?sort=bogus")
                ->assertStatus(400)
                ->assertJson(['success' => false, 'data' => null]);
        }
    }
}
