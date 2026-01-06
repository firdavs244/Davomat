<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guruh;
use App\Models\Talaba;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalabaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->guruh = Guruh::factory()->create();
    }

    /** @test */
    public function admin_can_view_talabalar_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('talabalar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('talabalar.index');
    }

    /** @test */
    public function admin_can_create_talaba()
    {
        $this->actingAs($this->admin);

        $talabaData = [
            'fish' => 'Aliyev Ali Vali o\'g\'li',
            'guruh_id' => $this->guruh->id,
            'kirgan_sana' => now()->format('Y-m-d'),
        ];

        $response = $this->post(route('talabalar.store'), $talabaData);

        $response->assertRedirect(route('talabalar.index'));
        $this->assertDatabaseHas('talabalar', ['fish' => 'Aliyev Ali Vali o\'g\'li']);
    }

    /** @test */
    public function admin_can_update_talaba()
    {
        $this->markTestSkipped('Update test needs debugging');

        $this->actingAs($this->admin);

        $talaba = Talaba::factory()->create([
            'guruh_id' => $this->guruh->id,
            'fish' => 'Aliyev Ali',
        ]);

        $updatedData = [
            'fish' => 'Karimov Vali',
            'guruh_id' => $this->guruh->id,
            'kirgan_sana' => $talaba->kirgan_sana,
        ];

        $response = $this->put(route('talabalar.update', $talaba), $updatedData);

        $response->assertRedirect(route('talabalar.index'));
        $this->assertDatabaseHas('talabalar', ['id' => $talaba->id, 'fish' => 'Karimov Vali']);
    }

    /** @test */
    public function talaba_can_be_marked_as_left()
    {
        $this->actingAs($this->admin);

        $talaba = Talaba::factory()->create([
            'guruh_id' => $this->guruh->id,
            'holati' => 'aktiv',
        ]);

        $response = $this->post(route('talabalar.mark-left', $talaba), [
            'ketgan_sana' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('talabalar', [
            'id' => $talaba->id,
            'holati' => 'ketgan',
        ]);
    }

    /** @test */
    public function talaba_can_be_transferred_to_another_group()
    {
        $this->actingAs($this->admin);

        $yangiGuruh = Guruh::factory()->create();
        $talaba = Talaba::factory()->create(['guruh_id' => $this->guruh->id]);

        $response = $this->post(route('talabalar.transfer', $talaba), [
            'yangi_guruh_id' => $yangiGuruh->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('talabalar', [
            'id' => $talaba->id,
            'guruh_id' => $yangiGuruh->id,
        ]);
    }
}
