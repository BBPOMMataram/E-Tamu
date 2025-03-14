<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // DB::table('services')->insert([
        //     ['id' => 1, 'name' => 'Pengujian Barang Bukti Narkoba'],
        //     ['id' => 2, 'name' => 'Pengujian Obat & Makanan'],
        //     ['id' => 3, 'name' => 'Informasi & Pengaduan'],
        //     ['id' => 4, 'name' => 'Sertifikasi'],
        //     ['id' => 5, 'name' => 'Wajib Lapor'],
        //     ['id' => 6, 'name' => 'Kunjungan'],
        //     ['id' => 7, 'name' => 'Keperluan Pribadi'],
        //     ['id' => 8, 'name' => 'Keperluan Lain'],
        // ]);
    }
}
