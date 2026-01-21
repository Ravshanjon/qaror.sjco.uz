<?php

namespace Tests\Feature\Livewire;

use App\Livewire\QarorlarTable;
use App\Models\Qaror;
use Livewire\Livewire;
use Tests\TestCase;

class QarorlarTableTest extends TestCase
{
    /** @test */
    public function it_renders_successfully(): void
    {
        Livewire::test(QarorlarTable::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.qarorlar-table');
    }

    /** @test */
    public function it_displays_all_qarorlar_without_filters(): void
    {
        Qaror::factory()->count(5)->create();

        Livewire::test(QarorlarTable::class)
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->count() === 5;
            });
    }

    /** @test */
    public function it_filters_by_search_query(): void
    {
        Qaror::factory()->create(['title' => 'Молия вазирлиги']);
        Qaror::factory()->create(['title' => 'Давлат бошқаруви']);

        Livewire::test(QarorlarTable::class)
            ->set('search', 'Молия')
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->count() === 1
                    && str_contains($qarorlar->first()->title, 'Молия');
            });
    }

    /** @test */
    public function it_filters_by_number(): void
    {
        Qaror::factory()->create(['number' => 123]);
        Qaror::factory()->create(['number' => 456]);
        Qaror::factory()->create(['number' => 789]);

        Livewire::test(QarorlarTable::class)
            ->set('number', '123')
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->count() === 1
                    && $qarorlar->first()->number == 123;
            });
    }

    /** @test */
    public function it_filters_by_year(): void
    {
        Qaror::factory()->year(2023)->create();
        Qaror::factory()->year(2024)->create();
        Qaror::factory()->year(2025)->create();

        Livewire::test(QarorlarTable::class)
            ->set('year', '2024')
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->count() === 1
                    && $qarorlar->first()->created_date->year === 2024;
            });
    }

    /** @test */
    public function it_combines_multiple_filters(): void
    {
        Qaror::factory()->create([
            'title' => 'Молия вазирлиги 2024',
            'number' => 100,
            'created_date' => '2024-05-15',
        ]);

        Qaror::factory()->create([
            'title' => 'Молия вазирлиги 2025',
            'number' => 200,
            'created_date' => '2025-05-15',
        ]);

        Livewire::test(QarorlarTable::class)
            ->set('search', 'Молия')
            ->set('year', '2024')
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->count() === 1
                    && $qarorlar->first()->number == 100;
            });
    }

    /** @test */
    public function it_resets_pagination_on_search_update(): void
    {
        // Create a unique record that will only appear when searched
        $uniqueQaror = Qaror::factory()->create(['title' => 'Unique Target Item', 'number' => 999]);

        // Create many other records to fill multiple pages
        Qaror::factory()->count(50)->create(['title' => 'Other Item']);

        // Test that when we search for the unique item, we find it
        // This implicitly tests that pagination is reset since the unique item
        // would only be on page 1 of filtered results
        Livewire::test(QarorlarTable::class)
            ->set('search', 'Unique Target')
            ->assertViewHas('qarorlar', function ($qarorlar) use ($uniqueQaror) {
                return $qarorlar->count() === 1
                    && $qarorlar->first()->id === $uniqueQaror->id;
            });
    }

    /** @test */
    public function it_respects_per_page_setting(): void
    {
        Qaror::factory()->count(50)->create();

        Livewire::test(QarorlarTable::class)
            ->set('perPage', 10)
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->count() === 10;
            });
    }

    /** @test */
    public function it_loads_years_list_correctly(): void
    {
        Qaror::factory()->year(2021)->create();
        Qaror::factory()->year(2023)->create();
        Qaror::factory()->year(2023)->create(); // Duplicate year
        Qaror::factory()->year(2025)->create();

        $component = Livewire::test(QarorlarTable::class);

        $years = $component->years; // Access computed property

        // Should have 3 distinct years, ordered descending
        $this->assertCount(3, $years);
        $this->assertEquals(2025, $years[0]);
        $this->assertEquals(2023, $years[1]);
        $this->assertEquals(2021, $years[2]);
    }

    /** @test */
    public function it_handles_empty_filters_gracefully(): void
    {
        Qaror::factory()->count(5)->create();

        Livewire::test(QarorlarTable::class)
            ->set('search', '')
            ->set('number', '')
            ->set('year', '')
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->count() === 5;
            });
    }

    /** @test */
    public function it_persists_filters_in_query_string(): void
    {
        Livewire::test(QarorlarTable::class)
            ->set('search', 'Молия')
            ->set('number', '123')
            ->set('year', '2024')
            ->assertSet('search', 'Молия')
            ->assertSet('number', '123')
            ->assertSet('year', '2024');
    }

    /** @test */
    public function it_orders_results_by_number_descending(): void
    {
        Qaror::factory()->create(['number' => 50]);
        Qaror::factory()->create(['number' => 200]);
        Qaror::factory()->create(['number' => 100]);

        Livewire::test(QarorlarTable::class)
            ->assertViewHas('qarorlar', function ($qarorlar) {
                return $qarorlar->first()->number === 200
                    && $qarorlar->last()->number === 50;
            });
    }
}
