<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ImportQarorExcelJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ImportQarorExcelJobTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    /** @test */
    public function it_can_be_dispatched(): void
    {
        Queue::fake();

        ImportQarorExcelJob::dispatch('/tmp/test.xlsx');

        Queue::assertPushed(ImportQarorExcelJob::class);
    }

    /** @test */
    public function it_has_correct_properties(): void
    {
        $job = new ImportQarorExcelJob('/tmp/test.xlsx');

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(300, $job->timeout);
    }

    /** @test */
    public function it_processes_excel_import(): void
    {
        Excel::fake();

        // Create temporary file
        $filePath = storage_path('app/test-import.xlsx');
        File::put($filePath, 'fake content');

        $job = new ImportQarorExcelJob($filePath);
        $job->handle();

        Excel::assertImported($filePath);
    }

    /** @test */
    public function it_deletes_file_after_successful_import(): void
    {
        Excel::fake();

        $filePath = storage_path('app/test-import.xlsx');
        File::put($filePath, 'fake content');

        $this->assertTrue(File::exists($filePath));

        $job = new ImportQarorExcelJob($filePath);
        $job->handle();

        $this->assertFalse(File::exists($filePath));
    }

    /** @test */
    public function it_deletes_file_on_failure(): void
    {
        $filePath = storage_path('app/test-import.xlsx');
        File::put($filePath, 'fake content');

        $job = new ImportQarorExcelJob($filePath);
        $job->failed(new \Exception('Test failure'));

        $this->assertFalse(File::exists($filePath));
    }

    /** @test */
    public function it_logs_error_on_failure(): void
    {
        \Log::shouldReceive('error')
            ->once()
            ->with('Excel import failed', \Mockery::type('array'));

        $job = new ImportQarorExcelJob('/tmp/nonexistent.xlsx');
        $job->failed(new \Exception('File not found'));
    }
}
