<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

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
        $guest = Guest::where('name', $name)->first();
        return response()->json($guest);
    }
}
