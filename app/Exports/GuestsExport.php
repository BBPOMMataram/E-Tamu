<?php

namespace App\Exports;

use App\Models\Guest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuestsExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Ambil data tamu dengan relasi service menggunakan query.
     */
    public function query()
    {
        return Guest::with('service'); // Pakai with() karena pakai FromQuery
    }

    /**
     * Judul kolom di Excel.
     */
    public function headings(): array
    {
        return [
            'Nama',
            'HP',
            'Instansi',
            'Keperluan', // Nama Service
            'Tanggal'
        ];
    }

    /**
     * Format data yang akan diekspor.
     */
    public function map($guest): array
    {
        return [
            $guest->name,
            $guest->hp,
            $guest->company,
            $guest->service , // Nama service
            $guest->created_at->format('d F Y')
        ];
    }
}
