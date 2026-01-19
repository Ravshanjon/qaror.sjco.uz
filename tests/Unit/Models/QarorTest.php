<?php

namespace Tests\Unit\Models;

use App\Models\Qaror;
use Tests\TestCase;

class QarorTest extends TestCase
{
    /** @test */
    public function it_has_fillable_attributes(): void
    {
        $fillable = (new Qaror())->getFillable();

        $this->assertContains('published_id', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('pdf_path', $fillable);
        $this->assertContains('created_date', $fillable);
        $this->assertContains('number', $fillable);
        $this->assertContains('file', $fillable);
        $this->assertContains('text', $fillable);
        $this->assertContains('views', $fillable);
    }

    /** @test */
    public function it_casts_created_date_to_date(): void
    {
        $qaror = Qaror::factory()->create([
            'created_date' => '2024-05-15',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $qaror->created_date);
    }

    /** @test */
    public function it_can_use_order_by_number_scope(): void
    {
        Qaror::factory()->create(['number' => 50]);
        Qaror::factory()->create(['number' => 200]);
        Qaror::factory()->create(['number' => 100]);

        $qarorlar = Qaror::orderByNumber()->get();

        $this->assertEquals(200, $qarorlar[0]->number);
        $this->assertEquals(100, $qarorlar[1]->number);
        $this->assertEquals(50, $qarorlar[2]->number);
    }

    /** @test */
    public function it_handles_numeric_string_sorting_correctly(): void
    {
        // Test edge case: string "9" vs "10"
        Qaror::factory()->create(['number' => 9]);
        Qaror::factory()->create(['number' => 10]);
        Qaror::factory()->create(['number' => 100]);

        $qarorlar = Qaror::orderByNumber()->get();

        // Should be: 100, 10, 9 (not 9, 100, 10)
        $this->assertEquals(100, $qarorlar[0]->number);
        $this->assertEquals(10, $qarorlar[1]->number);
        $this->assertEquals(9, $qarorlar[2]->number);
    }

    /** @test */
    public function it_has_factory(): void
    {
        $qaror = Qaror::factory()->create();

        $this->assertInstanceOf(Qaror::class, $qaror);
        $this->assertDatabaseHas('qarors', [
            'id' => $qaror->id,
        ]);
    }
}
