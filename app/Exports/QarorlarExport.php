<?php

namespace App\Exports;

use App\Models\Qaror;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class QarorlarExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Qaror::query()->orderByRaw('CAST(number AS UNSIGNED) DESC');
    }

    public function headings(): array
    {
        return ['ID', 'Nomlanishi', 'Qaror raqami', 'Sana', 'Ko\'rishlar'];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->title,
            $row->number,
            $row->created_date?->format('d.m.Y'),
            $row->views,
        ];
    }
}
