<?php

namespace App\Http\Controllers;

use App\Exports\GuestsExport;
use App\Models\Guest;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    function __invoke(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        $guests = Guest::with(['services', 'serviceLainnya']);

        if ($year) {
            $guests->whereYear('created_at', $year);
        }

        if ($month) {
            $guests->whereMonth('created_at', $month);
        }

        $guests = $guests->get();

        $services = Service::all();

        $guests_today = Guest::whereDate('created_at', Carbon::today())->count();

        $guestsTable = Guest::latest();

        if ($year) {
            $guestsTable->whereYear('created_at', $year);
        }

        if ($month) {
            $guestsTable->whereMonth('created_at', $month);
        }

        $guestsTable = $guestsTable->paginate(10)->appends($request->query());

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $chartQuery = Guest::join('services', 'guests.service', '=', 'services.id')
            ->select(
                DB::raw('YEAR(guests.created_at) as year'),
                DB::raw('MONTH(guests.created_at) as month'),
                DB::raw('count(*) as guests_total'),
                'services.name as service_name'
            );

        if ($year) {
            $chartQuery->whereYear('guests.created_at', $year);
        }

        if ($month) {
            $chartQuery->whereMonth('guests.created_at', $month);
        }

        $chartMode = $year && !$month ? 'month' : 'year';

        if ($chartMode === 'month') {
            $chartQuery->groupBy(DB::raw('YEAR(guests.created_at)'), DB::raw('MONTH(guests.created_at)'), 'services.name')
                ->orderBy(DB::raw('MONTH(guests.created_at)'));
        } else {
            $chartQuery->groupBy(DB::raw('YEAR(guests.created_at)'), DB::raw('MONTH(guests.created_at)'), 'services.name')
                ->orderBy(DB::raw('YEAR(guests.created_at)'));
        }

        $chartRaw = $chartQuery->get();

        if ($chartMode === 'month') {
            $chartData = $chartRaw->groupBy('month')->map(function ($items, $m) use ($monthNames) {
                return [
                    'label' => $monthNames[(int) $m] ?? $m,
                    'services' => $items->map(function ($item) {
                        return ['name' => $item->service_name, 'total' => $item->guests_total];
                    })
                ];
            })->values();
        } else {
            $chartData = $chartRaw->groupBy('year')->map(function ($items, $yr) {
                return [
                    'label' => (string) $yr,
                    'services' => $items->map(function ($item) {
                        return ['name' => $item->service_name, 'total' => $item->guests_total];
                    })
                ];
            })->values();
        }

        $chartData = $chartData->toArray();

        return view('dashboard', compact(
            'guests', 'services', 'guests_today', 'guestsTable',
            'chartData', 'chartMode', 'year'
        ));
    }


    function Guest_download(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');
        $filename = 'data-tamu';
        if ($year) $filename .= '-' . $year;
        if ($month) $filename .= '-bulan-' . $month;
        $filename .= '.xlsx';

        return Excel::download(new GuestsExport($year, $month), $filename);
    }

    public function downloadExcelApi(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');
        $filename = 'data-tamu';
        if ($year) $filename .= '-' . $year;
        if ($month) $filename .= '-bulan-' . $month;
        $filename .= '.xlsx';

        return Excel::download(new GuestsExport($year, $month), $filename);
    }

    // --- FUNGSI BARU UNTUK API NEXT.JS ---
    public function getDashboardApi(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        // 0. Ambil tahun-tahun sebelumnya yang memiliki data, dengan tahun sekarang sebagai yang paling akhir
        $currentYear = (int) Carbon::now()->format('Y');
        $yearsWithData = Guest::whereNotNull('created_at')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->map(fn($y) => (int) $y)
            ->toArray();

        if (!in_array($currentYear, $yearsWithData)) {
            $yearsWithData[] = $currentYear;
        }

        $available_years = array_values(array_filter($yearsWithData, fn($y) => $y <= $currentYear));
        sort($available_years);

        // 1. Total Guests (Berdasarkan filter)
        $guestsQuery = Guest::query();
        if ($year) $guestsQuery->whereYear('created_at', $year);
        if ($month) $guestsQuery->whereMonth('created_at', $month);
        $total_guests = $guestsQuery->count();

        // 2. Today Guests (Selalu hari ini)
        $today_guests = Guest::whereDate('created_at', Carbon::today())->count();

        // 3. Services Stats (Keperluan)
        $services = Service::all();
        $services_stats = [];
        foreach ($services as $service) {
            $q = clone $guestsQuery; 
            $services_stats[] = [
                'id' => $service->id,
                'name' => $service->name,
                'count' => $q->where('service', $service->id)->count(),
            ];
        }

        // 4. Table Data (Paginasi 10 Data per Halaman untuk Dashboard)
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);

        $guestsTable = clone $guestsQuery;
        $paginatedGuests = $guestsTable->with('services')->latest()->paginate($perPage, ['*'], 'page', $page);

        $table_data = $paginatedGuests->getCollection()->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'hp' => $g->hp,
                'company' => $g->company,
                'service_name' => $g->services->name ?? '-',
                'date' => $g->created_at->translatedFormat('l, d F Y'),
                'time' => $g->created_at->format('H:i'),
                'selfie' => $g->selfie
            ];
        });

        $pagination = [
            'current_page' => $paginatedGuests->currentPage(),
            'last_page' => $paginatedGuests->lastPage(),
            'per_page' => $paginatedGuests->perPage(),
            'total' => $paginatedGuests->total(),
            'from' => $paginatedGuests->firstItem() ?? 0,
            'to' => $paginatedGuests->lastItem() ?? 0,
        ];

        // 5. Chart Data (Format Khusus Chart.js)
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $chartQuery = Guest::join('services', 'guests.service', '=', 'services.id')
            ->select(
                DB::raw('YEAR(guests.created_at) as year'),
                DB::raw('MONTH(guests.created_at) as month'),
                DB::raw('count(*) as guests_total'),
                'services.name as service_name'
            );

        if ($year) $chartQuery->whereYear('guests.created_at', $year);
        if ($month) $chartQuery->whereMonth('guests.created_at', $month);

        $chartMode = $year && !$month ? 'month' : 'year';

        if ($chartMode === 'month') {
            $chartQuery->groupBy(DB::raw('YEAR(guests.created_at)'), DB::raw('MONTH(guests.created_at)'), 'services.name')
                ->orderBy(DB::raw('MONTH(guests.created_at)'));
        } else {
            $chartQuery->groupBy(DB::raw('YEAR(guests.created_at)'), DB::raw('MONTH(guests.created_at)'), 'services.name')
                ->orderBy(DB::raw('YEAR(guests.created_at)'));
        }

        $chartRaw = $chartQuery->get();

        $labels = [];
        $serviceMap = [];

        foreach ($chartRaw as $item) {
            $label = $chartMode === 'month' ? ($monthNames[(int)$item->month] ?? $item->month) : (string)$item->year;
            if (!in_array($label, $labels)) {
                $labels[] = $label;
            }
            $serviceMap[$item->service_name][$label] = $item->guests_total;
        }

        $colors = ['#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#06b6d4', '#14b8a6', '#84cc16'];
        $datasets = [];
        $colorIdx = 0;

        foreach ($services as $service) {
            $dataPoints = [];
            foreach ($labels as $lbl) {
                $dataPoints[] = $serviceMap[$service->name][$lbl] ?? 0;
            }
            
            $datasets[] = [
                'label' => $service->name,
                'data' => $dataPoints,
                'backgroundColor' => $colors[$colorIdx % count($colors)]
            ];
            $colorIdx++;
        }

        $chart_data = [
            'labels' => $labels,
            'datasets' => $datasets
        ];

        return response()->json([
            'total_guests' => $total_guests,
            'today_guests' => $today_guests,
            'services_stats' => $services_stats,
            'table_data' => $table_data,
            'pagination' => $pagination,
            'chart_data' => count($labels) > 0 ? $chart_data : null,
            'available_years' => $available_years
        ]);
    }
}