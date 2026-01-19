<?php

namespace Tests\Feature;

use App\Models\Qaror;
use Tests\TestCase;

class PdfControllerTest extends TestCase
{
    /** @test */
    public function it_displays_pdf_viewer_for_existing_qaror(): void
    {
        $qaror = Qaror::factory()->create(['number' => 123]);

        $response = $this->get("/pdfs/123");

        $response->assertStatus(200);
        $response->assertViewIs('pdf-viewer');
        $response->assertViewHas('qaror', $qaror);
    }

    /** @test */
    public function it_increments_views_counter_on_each_visit(): void
    {
        $qaror = Qaror::factory()->create([
            'number' => 456,
            'views' => 10,
        ]);

        $this->get("/pdfs/456");

        $this->assertEquals(11, $qaror->fresh()->views);

        $this->get("/pdfs/456");

        $this->assertEquals(12, $qaror->fresh()->views);
    }

    /** @test */
    public function it_returns_404_for_non_existent_qaror(): void
    {
        $response = $this->get("/pdfs/999999");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_handles_qaror_with_zero_views(): void
    {
        $qaror = Qaror::factory()->create([
            'number' => 789,
            'views' => 0,
        ]);

        $this->get("/pdfs/789");

        $this->assertEquals(1, $qaror->fresh()->views);
    }

    /** @test */
    public function it_handles_string_number_parameter(): void
    {
        $qaror = Qaror::factory()->create(['number' => 100]);

        $response = $this->get("/pdfs/100");

        $response->assertStatus(200);
        $response->assertViewHas('qaror');
    }
}
