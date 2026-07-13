<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Umkm;
use App\Models\Village;

class UmkmManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $village;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->village = Village::create([
            'name' => 'Sungailiat',
            'population' => 50000,
            'area_km2' => 20.5,
            'density' => 2439.02,
        ]);
    }

    public function test_admin_can_create_umkm()
    {
        $token = auth('api')->login($this->admin);

        $payload = [
            'name' => 'Warung Tes',
            'owner' => 'Budi',
            'category' => 'Makanan',
            'village_id' => $this->village->id,
            'address' => 'Jl. Pemuda No 1',
            'latitude' => -1.8889,
            'longitude' => 106.1038,
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/umkms', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Warung Tes');

        $this->assertDatabaseHas('umkms', ['name' => 'Warung Tes', 'category' => 'Makanan']);
    }

    public function test_can_filter_umkm_by_category()
    {
        Umkm::create([
            'name' => 'Warung A',
            'owner' => 'A',
            'category' => 'Makanan',
            'village_id' => $this->village->id,
            'address' => 'A',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        Umkm::create([
            'name' => 'Toko B',
            'owner' => 'B',
            'category' => 'Minuman',
            'village_id' => $this->village->id,
            'address' => 'B',
            'latitude' => 0,
            'longitude' => 0,
        ]);

        $response = $this->getJson('/api/umkms?category=Makanan');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Warung A', $data[0]['name']);
    }

    public function test_dashboard_stats_accessible_by_admin()
    {
        $token = auth('api')->login($this->admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'total_umkm',
                         'by_potential',
                         'recent_umkms'
                     ]
                 ]);
    }
}
