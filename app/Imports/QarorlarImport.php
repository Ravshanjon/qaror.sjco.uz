<?php

namespace App\Imports;
use App\Models\Qaror;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QarorlarImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return Qaror::updateOrCreate(
            // 🔑 MATCH — HUJJAT RAQAMI
            ['number' => $row['number']],

            // 🔄 UPDATE / CREATE
            [
                'title'        => $row['title'],
                'created_date' => $row['created_date'],
                'pdf_path'     => $row['pdf_path'] ?? null,
            ]
        );
    }
}
