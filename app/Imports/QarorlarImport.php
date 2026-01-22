<?php

namespace App\Imports;

use App\Models\Qaror;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QarorlarImport implements SkipsOnError, ToModel, WithHeadingRow
{
    use SkipsErrors;

    public function model(array $row)
    {
        // Validate row data
        $validator = Validator::make($row, [
            'number' => 'required|string|max:50',
            'title' => 'required|string|max:500',
            'created_date' => 'nullable|date',
            'pdf_path' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Excel import row validation failed', [
                'row' => $row,
                'errors' => $validator->errors()->toArray(),
            ]);

            return null; // Skip invalid rows
        }

        $validated = $validator->validated();

        // Clean up pdf_path - remove /storage/ prefix if present
        $pdfPath = $validated['pdf_path'] ?? null;
        if ($pdfPath) {
            // Remove leading /storage/ to store just "qarorlar/1765.pdf"
            $pdfPath = preg_replace('#^/?storage/#', '', $pdfPath);
        }

        return Qaror::updateOrCreate(
            // 🔑 MATCH — HUJJAT RAQAMI
            ['number' => $validated['number']],

            // 🔄 UPDATE / CREATE
            [
                'title' => $validated['title'],
                'created_date' => $validated['created_date'] ?? null,
                'pdf_path' => $pdfPath,
            ]
        );
    }
}
