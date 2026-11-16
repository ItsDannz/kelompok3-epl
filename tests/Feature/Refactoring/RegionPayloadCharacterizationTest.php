<?php

namespace Tests\Feature\Refactoring;

use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

// RF-003: Ekstraksi resolver wilayah; jalur create/update dan POS tetap mempertahankan kontraknya.
class RegionPayloadCharacterizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);
        $user = User::where('email', 'arya@gmail.com')->firstOrFail();
        $user->markEmailAsVerified();
        $this->actingAs($user);
        Province::insert(['code' => '11', 'name' => 'Provinsi RF']);
        City::insert(['code' => '1101', 'name' => 'Kota RF', 'province_code' => '11']);
        District::insert(['code' => '110101', 'name' => 'Kecamatan RF', 'city_code' => '1101']);
        Village::insert(['code' => '1101010001', 'name' => 'Desa RF', 'district_code' => '110101']);
    }

    public static function endpoints(): array
    {
        return ['customer create' => ['customers', false], 'customer update' => ['customers', true],
            'member create' => ['members', false], 'member update' => ['members', true]];
    }

    #[DataProvider('endpoints')]
    public function test_region_codes_and_names_are_saved_on_all_form_paths(string $module, bool $update): void
    {
        $payload = ['name' => 'RF Region', 'no_telp' => '900001', 'address' => 'Alamat uji',
            'province_id' => '11', 'regency_id' => '1101', 'district_id' => '110101', 'village_id' => '1101010001',
            'is_loyalty_member' => $module === 'members', 'loyalty_tier' => 'regular'];
        if ($update) {
            $customer = Customer::create(['name' => 'Before', 'no_telp' => '900001', 'address' => 'Before']);
            $response = $this->put(route($module.'.update', $customer), $payload);
        } else {
            $response = $this->post(route($module.'.store'), $payload);
        }
        $response->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('customers', ['no_telp' => '900001', 'name' => 'RF Region',
            'province_id' => '11', 'province_name' => 'Provinsi RF', 'regency_id' => '1101', 'regency_name' => 'Kota RF',
            'district_id' => '110101', 'district_name' => 'Kecamatan RF', 'village_id' => '1101010001', 'village_name' => 'Desa RF']);
    }

    public function test_pos_without_optional_region_keeps_null_codes_and_names(): void
    {
        $this->postJson(route('customers.storeAjax'), ['name' => 'POS RF', 'no_telp' => '900002', 'address' => 'Uji'])
            ->assertOk()->assertJsonPath('success', true)->assertJsonPath('customer.province_id', null);
        $this->assertDatabaseHas('customers', ['no_telp' => '900002', 'province_name' => null, 'village_id' => null, 'village_name' => null]);
    }

    public function test_unknown_region_codes_keep_the_codes_and_null_names(): void
    {
        $this->post(route('customers.store'), ['name' => 'Unknown RF', 'no_telp' => '900003', 'address' => 'Uji',
            'province_id' => 'XX', 'regency_id' => 'XX', 'district_id' => 'XX', 'village_id' => 'XX'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('customers', ['no_telp' => '900003', 'province_id' => 'XX', 'province_name' => null, 'village_id' => 'XX', 'village_name' => null]);
    }
}
