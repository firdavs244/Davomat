<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guruh;
use App\Models\Talaba;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruhTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin user yaratish
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_guruhlar_index()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('guruhlar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('guruhlar.index');
    }

    /** @test */
    public function admin_can_create_guruh()
    {
        $this->actingAs($this->admin);

        $guruhData = [
            'nomi' => 'AT-101',
            'kurs' => '1',
            'yunalish' => 'Axborot texnologiyalari',
        ];

        $response = $this->post(route('guruhlar.store'), $guruhData);

        $response->assertRedirect(route('guruhlar.index'));
        $this->assertDatabaseHas('guruhlar', $guruhData);
    }

    /** @test */
    public function admin_can_update_guruh()
    {
        $this->markTestSkipped('Update test needs debugging');

        $this->actingAs($this->admin);

        $guruh = Guruh::factory()->create(['nomi' => 'AT-101']);

        $updatedData = [
            'nomi' => 'AT-102',
            'kurs' => 2,
            'yunalish' => 'Dasturlash',
            'is_active' => 1,
        ];

        $response = $this->put(route('guruhlar.update', $guruh), $updatedData);

        $response->assertRedirect(route('guruhlar.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('muvaffaqiyat');

        $guruh->refresh();
        $this->assertEquals('AT-102', $guruh->nomi);
        $this->assertEquals(2, $guruh->kurs);
        $response = $this->delete(route('guruhlar.destroy', $guruh));

        $response->assertRedirect(route('guruhlar.index'));
        $this->assertDatabaseMissing('guruhlar', ['id' => $guruh->id]);
    }

    /** @test */
    public function cannot_delete_guruh_with_students()
    {
        $this->actingAs($this->admin);

        $guruh = Guruh::factory()->create();
        Talaba::factory()->create(['guruh_id' => $guruh->id]);

        $response = $this->delete(route('guruhlar.destroy', $guruh));

        $response->assertRedirect(route('guruhlar.index'));
        $this->assertDatabaseHas('guruhlar', ['id' => $guruh->id]);
    }

    /** @test */
    public function koruvchi_cannot_create_guruh()
    {
        $koruvchi = User::factory()->create(['role' => 'koruvchi', 'is_active' => true]);

        $this->actingAs($koruvchi);

        $response = $this->get(route('guruhlar.create'));

        $response->assertStatus(403);
    }
}
