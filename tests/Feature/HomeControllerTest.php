<?php

namespace Tests\Feature;

use App\Models\Qaror;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    /** @test */
    public function it_displays_home_page_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('qarorlar');
    }

    /** @test */
    public function it_displays_paginated_qarorlar(): void
    {
        // Create 30 qarorlar
        Qaror::factory()->count(30)->create();

        $response = $this->get('/');

        $response->assertStatus(200);

        // Default pagination: 25 items per page
        $qarorlar = $response->viewData('qarorlar');
        $this->assertEquals(25, $qarorlar->count());
        $this->assertEquals(30, $qarorlar->total());
    }

    /** @test */
    public function it_orders_qarorlar_by_number_descending(): void
    {
        Qaror::factory()->create(['number' => 100]);
        Qaror::factory()->create(['number' => 50]);
        Qaror::factory()->create(['number' => 200]);

        $response = $this->get('/');

        $qarorlar = $response->viewData('qarorlar');

        // Should be ordered: 200, 100, 50
        $this->assertEquals(200, $qarorlar->first()->number);
        $this->assertEquals(50, $qarorlar->last()->number);
    }

    /** @test */
    public function it_handles_empty_qarorlar_list(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $qarorlar = $response->viewData('qarorlar');
        $this->assertEquals(0, $qarorlar->count());
    }

    /** @test */
    public function it_respects_custom_pagination_config(): void
    {
        config(['qaror.items_per_page' => 10]);

        Qaror::factory()->count(15)->create();

        $response = $this->get('/');

        $qarorlar = $response->viewData('qarorlar');
        $this->assertEquals(10, $qarorlar->count());
    }
}
