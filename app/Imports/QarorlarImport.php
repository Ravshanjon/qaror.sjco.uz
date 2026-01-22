<?php

namespace App\Imports;

use App\Models\Qaror;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QarorlarImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Get values, converting number to string if needed
        $number = isset($row['number']) ? (string) $row['number'] : null;
        $title = $row['title'] ?? null;
        $createdDate = $row['created_date'] ?? null;
        $pdfPath = $row['pdf_path'] ?? null;

        // Skip empty rows
        if (empty($number) || empty($title)) {
            Log::warning('Excel import row skipped - missing required fields', [
                'row' => $row,
            ]);

            return null;
        }

        // Clean up pdf_path - remove /storage/ prefix if present
        if ($pdfPath) {
            $pdfPath = preg_replace('#^/?storage/#', '', $pdfPath);
        }

        return Qaror::updateOrCreate(
            ['number' => $number],
            [
                'title' => $title,
                'created_date' => $createdDate,
                'pdf_path' => $pdfPath,
            ]
        );
    }
}
