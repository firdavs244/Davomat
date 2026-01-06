<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guruh;
use App\Models\Talaba;
use App\Models\Davomat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DavomatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->davomatOluvchi = User::factory()->create([
            'role' => 'davomat_oluvchi',
            'is_active' => true,
        ]);

        $this->guruh = Guruh::factory()->create();
        $this->talaba = Talaba::factory()->create([
            'guruh_id' => $this->guruh->id,
            'holati' => 'aktiv',
        ]);
    }

    /** @test */
    public function davomat_oluvchi_can_view_davomat_olish_page()
    {
        $this->actingAs($this->davomatOluvchi);

        $response = $this->get(route('davomat.olish'));

        $response->assertStatus(200);
        $response->assertViewIs('davomat.olish');
    }

    /** @test */
    public function davomat_oluvchi_can_save_davomat()
    {
        $this->actingAs($this->davomatOluvchi);

        $davomatData = [
            'guruh_id' => $this->guruh->id,
            'sana' => now()->format('Y-m-d'),
            'para' => '1',
            'davomat' => [
                $this->talaba->id => 'bor',
            ],
        ];

        $response = $this->post(route('davomat.saqlash'), $davomatData);

        $response->assertRedirect();
        $this->assertDatabaseHas('davomat', [
            'talaba_id' => $this->talaba->id,
            'guruh_id' => $this->guruh->id,
            'para_1' => 'bor',
        ]);
    }

    /** @test */
    public function admin_can_update_davomat()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin);

        $davomat = Davomat::factory()->create([
            'talaba_id' => $this->talaba->id,
            'guruh_id' => $this->guruh->id,
            'sana' => now()->format('Y-m-d'),
            'para_1' => 'bor',
            'xodim_id' => $this->davomatOluvchi->id,
        ]);

        $response = $this->put(route('davomat.update', $davomat), [
            'para_1' => 'yoq',
            'para_2' => 'bor',
            'para_3' => 'bor',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('davomat', [
            'id' => $davomat->id,
            'para_1' => 'yoq',
        ]);
    }

    /** @test */
    public function koruvchi_cannot_take_davomat()
    {
        $koruvchi = User::factory()->create(['role' => 'koruvchi', 'is_active' => true]);

        $this->actingAs($koruvchi);

        $response = $this->post(route('davomat.saqlash'), [
            'guruh_id' => $this->guruh->id,
            'sana' => now()->format('Y-m-d'),
            'para' => '1',
            'davomat' => [$this->talaba->id => 'bor'],
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function davomat_oluvchi_cannot_update_davomat()
    {
        $this->actingAs($this->davomatOluvchi);

        $davomat = Davomat::factory()->create([
            'talaba_id' => $this->talaba->id,
            'guruh_id' => $this->guruh->id,
            'xodim_id' => $this->davomatOluvchi->id,
        ]);

        $response = $this->put(route('davomat.update', $davomat), [
            'para_1' => 'yoq',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function davomat_tarixi_can_be_viewed()
    {
        $this->actingAs($this->davomatOluvchi);

        Davomat::factory()->create([
            'talaba_id' => $this->talaba->id,
            'guruh_id' => $this->guruh->id,
            'xodim_id' => $this->davomatOluvchi->id,
        ]);

        $response = $this->get(route('davomat.tarixi'));

        $response->assertStatus(200);
        $response->assertViewIs('davomat.tarixi');
    }
}
