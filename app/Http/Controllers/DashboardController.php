<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function __invoke() {
        $guests = Guest::with(['services', 'serviceLainnya'])->get();
        $services = Service::all();

        $guests_today = Guest::whereDate('created_at', Carbon::today())->count();
        
        return view('dashboard', compact('guests', 'services', 'guests_today'));
    }
}
