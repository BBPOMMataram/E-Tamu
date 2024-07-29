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
        $guests = Guest::paginate();
    
        $year = $request->query("year");
        if ($year) {
            $guests = Guest::whereYear('guests.created_at', $year)
                ->join('services', 'guests.service', '=', 'services.id')
                ->select(
                    DB::raw('MONTH(guests.created_at) as month'),
                    DB::raw('count(*) as guests_total'),
                    'services.name as service_name'
                )
                ->groupBy(DB::raw('MONTH(guests.created_at)'), 'services.name')
                ->get();
    
            // Mengubah data menjadi format yang diinginkan
            $data = [
                'year' => $year,
                'guests' => $guests->groupBy('month')->map(function ($items, $month) {
                    return [
                        'month' => date("F", mktime(0, 0, 0, $month, 10)), // Mengubah angka bulan menjadi nama bulan
                        'services' => $items->map(function ($item) {
                            return [
                                'service_name' => $item->service_name,
                                'total' => $item->guests_total,
                            ];
                        })
                    ];
                })->values()
            ];
    
            return response()->json($data);
        }
    
        return GuestResource::collection($guests);
    }
}
