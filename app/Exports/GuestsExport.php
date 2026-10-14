<?php

namespace App\Exports;

use App\Models\Guest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuestsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $year;
    protected $month;

    public function __construct($year = null, $month = null)
    {
        $this->year = $year;
        $this->month = $month;
    }

    /**
     * Ambil data tamu dengan relasi service menggunakan query.
     */
    public function query()
    {
        $query = Guest::with('services');
        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }
        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }
        return $query->latest();
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
            'Tanggal',
            'Waktu'
        ];
    }

    /**
     * Format data yang akan diekspor.
     */
    public function map($guest): array
    {
        return [
            $guest->name ?? '-',
            $guest->hp ?? '-',
            $guest->company ?? '-',
            $guest->services->name ?? '-',
            $guest->created_at ? $guest->created_at->translatedFormat('d F Y') : '-',
            $guest->created_at ? $guest->created_at->format('H:i') . ' WITA' : '-'
        ];
    }
}
