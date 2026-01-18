<?php

namespace App\Jobs;

use App\Imports\QarorlarImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ImportQarorExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle(): void
    {
        // Import Excel file asynchronously
        Excel::import(new QarorlarImport, $this->filePath);

        // Clean up temporary file after import
        if (File::exists($this->filePath)) {
            File::delete($this->filePath);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Clean up temporary file on failure
        if (File::exists($this->filePath)) {
            File::delete($this->filePath);
        }

        // Log the failure
        \Log::error('Excel import failed', [
            'file' => $this->filePath,
            'error' => $exception->getMessage(),
        ]);
    }
}
