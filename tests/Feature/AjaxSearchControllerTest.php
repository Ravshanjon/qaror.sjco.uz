<?php

namespace Tests\Feature;

use App\Models\Qaror;
use Tests\TestCase;

class AjaxSearchControllerTest extends TestCase
{
    /** @test */
    public function it_searches_qarorlar_by_title(): void
    {
        Qaror::factory()->create(['title' => 'Молия вазирлиги тўғрисида', 'number' => 1]);
        Qaror::factory()->create(['title' => 'Ўзбекистон Республикаси Конституцияси', 'number' => 2]);
        Qaror::factory()->create(['title' => 'Давлат бошқаруви тизими', 'number' => 3]);

        $response = $this->getJson('/qarorlar/ajax-search?q=Молия');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Молия вазирлиги тўғрисида']);
    }

    /** @test */
    public function it_returns_empty_array_for_empty_query(): void
    {
        Qaror::factory()->count(5)->create();

        $response = $this->getJson('/qarorlar/ajax-search?q=');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    /** @test */
    public function it_limits_results_to_20_items(): void
    {
        // Create 30 qarorlar with similar title
        Qaror::factory()->count(30)->create([
            'title' => 'Test Qarori',
        ]);

        $response = $this->getJson('/qarorlar/ajax-search?q=Test');

        $response->assertStatus(200);
        $this->assertCount(20, $response->json());
    }

    /** @test */
    public function it_orders_results_by_number_descending(): void
    {
        Qaror::factory()->create(['title' => 'Молия', 'number' => 50]);
        Qaror::factory()->create(['title' => 'Молия', 'number' => 200]);
        Qaror::factory()->create(['title' => 'Молия', 'number' => 100]);

        $response = $this->getJson('/qarorlar/ajax-search?q=Молия');

        $results = $response->json();
        $this->assertEquals(200, $results[0]['number']);
        $this->assertEquals(100, $results[1]['number']);
        $this->assertEquals(50, $results[2]['number']);
    }

    /** @test */
    public function it_returns_only_required_fields(): void
    {
        Qaror::factory()->create(['title' => 'Test', 'number' => 1]);

        $response = $this->getJson('/qarorlar/ajax-search?q=Test');

        $result = $response->json()[0];

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('number', $result);
        $this->assertArrayHasKey('created_date', $result);

        // Should NOT include these fields
        $this->assertArrayNotHasKey('pdf_path', $result);
        $this->assertArrayNotHasKey('text', $result);
        $this->assertArrayNotHasKey('views', $result);
    }

    /** @test */
    public function it_validates_query_parameter(): void
    {
        $response = $this->getJson('/qarorlar/ajax-search?q=' . str_repeat('a', 300));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('q');
    }

    /** @test */
    public function it_handles_case_insensitive_search(): void
    {
        Qaror::factory()->create(['title' => 'МоЛиЯ вАзИрЛиГи']);

        $response = $this->getJson('/qarorlar/ajax-search?q=молия');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    /** @test */
    public function it_trims_whitespace_from_query(): void
    {
        Qaror::factory()->create(['title' => 'Молия']);

        $response = $this->getJson('/qarorlar/ajax-search?q=  Молия  ');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    /** @test */
    public function it_respects_rate_limiting(): void
    {
        // Make 60 requests (the limit)
        for ($i = 0; $i < 60; $i++) {
            $response = $this->getJson('/qarorlar/ajax-search?q=test');
            $response->assertStatus(200);
        }

        // 61st request should be rate limited
        $response = $this->getJson('/qarorlar/ajax-search?q=test');
        $response->assertStatus(429); // Too Many Requests
    }
}
