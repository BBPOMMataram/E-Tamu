<?php

namespace App\Http\Controllers;

use App\Exports\GuestsExport;
use App\Models\Guest;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
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


    function Guest_download()
    {
        return Excel::download(new GuestsExport, 'guests.xlsx');
    }
}
