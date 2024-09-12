<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Service;
use App\Models\ServiceLainnya;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    function form()
    {
        $services = Service::all();
        $guests = Guest::select('name')->distinct()->orderBy('name')->get();

        $guests_this_year = Guest::whereYear('created_at', now()->year)->count();

        return view('welcome', compact('services', 'guests', 'guests_this_year'));
    }

    function storeImage($imageUri)
    {
        // Pecah data URI menjadi jenis konten dan data biner
        list($type, $data) = explode(';', $imageUri);
        list(, $data) = explode(',', $data);

        // Konversi data biner dari base64 ke format biner
        $binaryData = base64_decode($data);

        $path = 'guest-images/guest_' . uniqid() . '.' . explode('/', $type)[1];

        if (!Storage::put($path, $binaryData)) {
            throw new \Exception('Gagal menyimpan gambar.');
        }

        return $path;
    }

    function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string'],
            'hp' => ['required', 'string'],
            'layanan' => ['required'],
        ]);

        $data = new Guest();
        $data->service = $request->layanan;
        $data->name = $request->nama;
        $data->hp = $request->hp;
        $data->company = $request->instansi;
        $data->address = $request->alamat;
        $data->email = $request->email;
        $data->pangkat = $request->pangkat;
        $data->jabatan = $request->jabatan;
        $data->distance = $request->distance;

        try {
            $path = $this->storeImage($request->imageUri);

            $data->selfie = $path;
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }

        $data->save();

        if ($request->layanan === '8') {
            $new_service_lainnya = new ServiceLainnya();
            $new_service_lainnya->guest_id = $data->id;
            $new_service_lainnya->name = $request->layananCustom;
            $new_service_lainnya->save();
        }

        return redirect()->route('presensi.form')->with(['status' => 'new-guest-saved', 'name' => $data->name]);
    }
}
