<?php

namespace App\Http\Controllers;

use App\Exports\GuestsExport;
use App\Models\Guest;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $guestsTable = Guest::latest()->paginate(10); //guest untuk table pagination

        return view('dashboard', compact('guests', 'services', 'guests_today', 'guestsTable'));
    }


    function Guest_download()
    {
        return Excel::download(new GuestsExport, 'guests.xlsx');
    }
}
