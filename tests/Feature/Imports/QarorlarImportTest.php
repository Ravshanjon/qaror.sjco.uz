<?php

namespace Tests\Feature\Imports;

use App\Imports\QarorlarImport;
use App\Models\Qaror;
use Tests\TestCase;

class QarorlarImportTest extends TestCase
{
    /** @test */
    public function it_creates_new_qaror_from_valid_row(): void
    {
        $import = new QarorlarImport();

        $row = [
            'number' => '123',
            'title' => 'Test Qarori',
            'created_date' => '2024-01-15',
            'pdf_path' => 'pdfs/test.pdf',
        ];

        $qaror = $import->model($row);

        $this->assertInstanceOf(Qaror::class, $qaror);
        $this->assertEquals('123', $qaror->number);
        $this->assertEquals('Test Qarori', $qaror->title);
    }

    /** @test */
    public function it_updates_existing_qaror_by_number(): void
    {
        // Create existing qaror
        Qaror::factory()->create([
            'number' => 456,
            'title' => 'Old Title',
        ]);

        $import = new QarorlarImport();

        $row = [
            'number' => '456',
            'title' => 'Updated Title',
            'created_date' => '2025-01-15',
            'pdf_path' => null,
        ];

        $import->model($row);

        $this->assertEquals(1, Qaror::count());
        $this->assertEquals('Updated Title', Qaror::where('number', 456)->first()->title);
    }

    /** @test */
    public function it_skips_invalid_rows(): void
    {
        $import = new QarorlarImport();

        // Missing required 'number' field
        $row = [
            'title' => 'Test without number',
        ];

        $result = $import->model($row);

        $this->assertNull($result);
        $this->assertEquals(0, Qaror::count());
    }

    /** @test */
    public function it_validates_title_max_length(): void
    {
        $import = new QarorlarImport();

        $row = [
            'number' => '789',
            'title' => str_repeat('a', 600), // Exceeds 500 max
            'created_date' => '2024-01-15',
        ];

        $result = $import->model($row);

        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_nullable_fields(): void
    {
        $import = new QarorlarImport();

        $row = [
            'number' => '999',
            'title' => 'Minimal Qaror',
            'created_date' => null,
            'pdf_path' => null,
        ];

        $qaror = $import->model($row);

        $this->assertInstanceOf(Qaror::class, $qaror);
        $this->assertNull($qaror->created_date);
        $this->assertNull($qaror->pdf_path);
    }

    /** @test */
    public function it_validates_date_format(): void
    {
        $import = new QarorlarImport();

        $row = [
            'number' => '111',
            'title' => 'Test',
            'created_date' => 'invalid-date',
        ];

        $result = $import->model($row);

        $this->assertNull($result);
    }
}
