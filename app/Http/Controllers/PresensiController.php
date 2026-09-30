<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Service;
use App\Models\ServiceLainnya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    // ====================================================
    // FUNGSI LAMA (BLADE LARAVEL)
    // ====================================================
    function form()
    {
        $services = Service::all();
        $guests = Guest::select('name')->distinct()->orderBy('name')->get();

        $guests_this_year = Guest::whereYear('created_at', now()->year)->count();

        return view('welcome', compact('services', 'guests', 'guests_this_year'));
    }

    function storeImage($imageUri)
    {
        list($type, $data) = explode(';', $imageUri);
        list(, $data) = explode(',', $data);
        $binaryData = base64_decode($data);
        $path = 'guest-images/guest_' . uniqid() . '.' . explode('/', $type)[1];

        if (!Storage::disk('public')->put($path, $binaryData)) {
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

        return redirect()->route('presensi.form')->with(['status' => 'new-guest-saved', 'name' => $data->name, 'id' => $data->id]);
    }

    function store_survey(Request $request)
    {
        $guest = Guest::find($request->guest_id);
        $guest->rating = $request->rating;
        $guest->save();

        return redirect()->route('presensi.form')->with(['status' => 'survey-saved', 'name' => $guest->name]);
    }


    // ====================================================
    // FUNGSI BARU UNTUK API NEXT.JS
    // ====================================================

    public function initDataApi()
    {
        $services = Service::all();
        $guests = Guest::select('name')->distinct()->orderBy('name')->get();
        $guests_this_year = Guest::whereYear('created_at', now()->year)->count();

        return response()->json([
            'services' => $services,
            'guests' => $guests->pluck('name'),
            'guests_this_year' => $guests_this_year
        ]);
    }

    // Fungsi Haversine untuk validasi ulang jarak di Backend
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            
        return round($earthRadius * $angle);
    }

    public function storeApi(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string'],
            'hp' => ['required', 'string'],
            'layanan' => ['required'],
            'imageUri' => ['required', 'string'], 
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['required', 'numeric'],
        ]);

        // Kunci validasi Backend: Hitung ulang secara server-side
        $bpomLat = -8.5877864;
        $bpomLng = 116.1157653;
        
        $backendDistance = $this->calculateDistance($bpomLat, $bpomLng, $request->latitude, $request->longitude);

        if ($backendDistance > 50) {
            return response()->json([
                'message' => 'Jarak terlalu jauh (' . $backendDistance . 'm). Anda terdeteksi berada di luar area BBPOM.'
            ], 403);
        }

        $data = new Guest();
        $data->service = $request->layanan;
        $data->name = $request->nama;
        $data->hp = $request->hp;
        $data->company = $request->instansi;
        $data->address = $request->alamat;
        $data->email = $request->email;
        $data->pangkat = $request->pangkat;
        $data->jabatan = $request->jabatan;
        
        // Simpan jarak yang dihitung secara valid oleh backend
        $data->distance = $backendDistance; 

        try {
            $path = $this->storeImage($request->imageUri);
            $data->selfie = $path;
        } catch (\Exception $th) {
            return response()->json(['message' => 'Gagal menyimpan foto: ' . $th->getMessage()], 400);
        }

        $data->save();

        if ($request->layanan === '8' && $request->filled('layananCustom')) {
            $new_service_lainnya = new ServiceLainnya();
            $new_service_lainnya->guest_id = $data->id;
            $new_service_lainnya->name = $request->layananCustom;
            $new_service_lainnya->save();
        }

        return response()->json([
            'message' => 'Data berhasil disimpan',
            'data' => [
                'id' => $data->id,
                'name' => $data->name
            ]
        ]);
    }

    public function storeSurveyApi(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'rating' => 'required|integer|min:1|max:3'
        ]);

        $guest = Guest::find($request->guest_id);
        $guest->rating = $request->rating;
        $guest->save();

        return response()->json(['message' => 'Survey berhasil disimpan']);
    }
}