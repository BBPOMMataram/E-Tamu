<?php

namespace App\Http\Controllers;

use App\Http\Resources\GuestResource;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    function index(Request $request) {
        $validated = $request->validate([
            'name' => ['string', 'nullable'],
        ]);

        $data = Guest::with('service')
            ->latest();

        if (isset($validated["name"])) {
            $data = $data->where('name', $validated["name"]);
        }

        $numb_per_page = $request['numb_per_page'] ?? 10;
        $data = $data->paginate($numb_per_page)->appends(array_merge($validated, ['numb_per_page' => $numb_per_page]));
        $indexNumber = (request()->input('page', 1) - 1) * $numb_per_page;

        return view('guest.index', compact('data', 'indexNumber', 'validated', 'numb_per_page'));
    }
    
    function new_guest(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string'],
            'hp' => ['required', 'string'],
            'service' => ['required'],
        ]);

        $data = new Guest();
        $data->name = $request->name;
        $data->hp = $request->hp;
        $data->service = $request->service;
        $data->company = $request->company;
        $data->address = $request->address;
        $data->email = $request->email;
        $data->pangkat = $request->pangkat;
        $data->jabatan = $request->jabatan;
        $data->selfie = $request->file("selfie");

        try {
            $folderName = 'guest-images';
            // $path = Storage::put($folderName, $data->selfie);
            $path = $request->file("selfie")->store($folderName);
            $data->selfie = $path;
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }

        $data->save();

        // $data->service = $data->serviceType->name;
        // GuestArrived::dispatch(['data' => $data]);

        return response()->json(['status' => 1, 'msg' => 'Saved', 'data' => $data]);
    }

    function get_all_guests()
    {
        $data = Guest::all();
        return response()->json($data);
    }

    // FUNGSI UNTUK FITUR AUTOFILL DATA TAMU SAAT MENGISI KOLOM NAMA
    public function getByName($name)
    {
        $guest = Guest::with('serviceLainnya')
        ->where('name', $name)->orderBy('created_at', 'desc')->first();
        return response()->json($guest);
    }

    function get_guests(Request $request) {
        $year = $request->query("year");
        $month = $request->query("month");

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $query = Guest::join('services', 'guests.service', '=', 'services.id')
            ->select(
                DB::raw('YEAR(guests.created_at) as year'),
                DB::raw('MONTH(guests.created_at) as month'),
                DB::raw('count(*) as guests_total'),
                'services.name as service_name'
            );

        if ($year) {
            $query->whereYear('guests.created_at', $year);
        }

        if ($month) {
            $query->whereMonth('guests.created_at', $month);
        }

        $groupBy = [$year && !$month ? 'month' : 'year'];

        $guests = $query->groupBy(
                array_merge($groupBy, ['services.name'])
            )
            ->orderByRaw($groupBy[0] === 'year' ? 'YEAR(guests.created_at)' : 'MONTH(guests.created_at)')
            ->get();

        if ($year && !$month) {
            $data = [
                'mode' => 'month',
                'items' => $guests->groupBy('month')->map(function ($items, $m) use ($monthNames) {
                    return [
                        'label' => $monthNames[(int) $m] ?? $m,
                        'services' => $items->map(function ($item) {
                            return [
                                'service_name' => $item->service_name,
                                'total' => $item->guests_total,
                            ];
                        })
                    ];
                })->values()
            ];
        } else {
            $data = [
                'mode' => 'year',
                'items' => $guests->groupBy('year')->map(function ($items, $yr) {
                    return [
                        'label' => (string) $yr,
                        'services' => $items->map(function ($item) {
                            return [
                                'service_name' => $item->service_name,
                                'total' => $item->guests_total,
                            ];
                        })
                    ];
                })->values()
            ];
        }

        return response()->json($data);
    }
}
