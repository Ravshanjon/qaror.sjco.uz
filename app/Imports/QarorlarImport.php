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

    /**
     * 5 xonali unique published_id
     */
    protected function generatePublishedId(): int
    {
        do {
            $id = random_int(10000, 99999);
        } while (Qaror::where('published_id', $id)->exists());

        return $id;
    }
}
