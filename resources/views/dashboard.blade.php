<x-app-layout>
    <x-slot name="header">
        <div class="flex">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <span class="ml-auto text-green-500 font-bold text-2xl">{{ $guests->count() }} Total Tamu</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-2 mb-6">
                <!-- Box 1 -->
                <div class="bg-gradient-to-r from-lime-500 to-green-500  shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        TAMU HARI INI
                    </h2>
                    <p class="text-3xl font-bold text-white">{{ $guests_today }}</p>
                    <p class="text-sm text-gray-200">Tamu</p>
                </div>

                <!-- Box 2 -->
                <div
                    class="flex-1 bg-gradient-to-r from-blue-400 to-green-500 shadow-md rounded-lg p-6 text-center text-white">
                    <h2 class="text-xl font-semibold">
                        SELAMAT DATANG DI BALAI BESAR POM DI MATARAM
                    </h2>
                    <div class="filter flex justify-center gap-2 mt-2">
                        <form>
                            <select name="year" id="year" class="rounded text-gray-700">
                                <option value="">Pilih Tahun</option>
                                <option value="2023" @if (request()->query('year') === '2023') selected @endif>2023</option>
                                <option value="2024" @if (request()->query('year') === '2024') selected @endif>2024</option>
                                <option value="2025" @if (request()->query('year') === '2025') selected @endif>2025</option>
                            </select>
                            <select name="month" id="month" class="rounded text-gray-700">
                                <option value="">Pilih Bulan</option>
                                <option value="1" @if (request()->query('month') === '1') selected @endif>Januari
                                </option>
                                <option value="2" @if (request()->query('month') === '2') selected @endif>Februari
                                </option>
                                <option value="3" @if (request()->query('month') === '3') selected @endif>Maret</option>
                                <option value="4" @if (request()->query('month') === '4') selected @endif>April</option>
                                <option value="5" @if (request()->query('month') === '5') selected @endif>Mei</option>
                                <option value="6" @if (request()->query('month') === '6') selected @endif>Juni</option>
                                <option value="7" @if (request()->query('month') === '7') selected @endif>Juli</option>
                                <option value="8" @if (request()->query('month') === '8') selected @endif>Agustus
                                </option>
                                <option value="9" @if (request()->query('month') === '9') selected @endif>September
                                </option>
                                <option value="10" @if (request()->query('month') === '10') selected @endif>Oktober
                                </option>
                                <option value="11" @if (request()->query('month') === '11') selected @endif>November
                                </option>
                                <option value="12" @if (request()->query('month') === '12') selected @endif>Desember
                                </option>
                            </select>
                            <button class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                </div>
            </div>


            <div class="bg-gray-800 text-white shadow-md rounded-lg p-4 text-center mb-2">
                <h2 class="text-xl font-semibold">
                    Total Tamu Berdasarkan Keperluan
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Box 1 -->
                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 1)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 1)->count() }}</p>
                    <p class="text-sm text-indigo-200">Tamu</p>
                </div>

                <!-- Box 2 -->
                <div class="bg-gradient-to-r from-green-500 to-teal-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 2)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 2)->count() }}</p>
                    <p class="text-sm text-teal-200">Tamu</p>
                </div>

                <!-- Box 3 -->
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 3)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 3)->count() }}</p>
                    <p class="text-sm text-orange-200">Tamu</p>
                </div>

                <!-- Box 4 -->
                <div class="bg-gradient-to-r from-red-500 to-pink-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 4)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 4)->count() }}</p>
                    <p class="text-sm text-pink-200">Tamu</p>
                </div>

                <!-- Box 5 -->
                <div class="bg-gradient-to-r from-purple-500 to-blue-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 5)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 5)->count() }}</p>
                    <p class="text-sm text-blue-200">Tamu</p>
                </div>

                <!-- Box 6 -->
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 6)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 6)->count() }}</p>
                    <p class="text-sm text-cyan-200">Tamu</p>
                </div>

                <!-- Box 7 -->
                <div class="bg-gradient-to-r from-teal-500 to-emerald-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 7)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 7)->count() }}</p>
                    <p class="text-sm text-emerald-200">Tamu</p>
                </div>

                <!-- Box 8 -->
                <div class="bg-gradient-to-r from-emerald-500 to-lime-500 shadow-lg rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-white">
                        {{ $services->where('id', 8)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-white">{{ $guests->where('service', 8)->count() }}</p>
                    <p class="text-sm text-lime-200">Tamu</p>
                </div>
            </div>

            <div class="bg-gray-800 text-white shadow-md rounded-lg p-4 text-center mb-4">
                <h2 class="text-xl font-semibold">
                    Tabel Data Tamu
                </h2>
            </div>

            @auth
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="overflow-x-auto" id="guesttable">
                    <table class="min-w-full border-collapse w-full">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Nama</th>
                                <th class="py-3 px-6 text-left">HP</th>
                                <th class="py-3 px-6 text-left">Instansi</th>
                                <th class="py-3 px-6 text-left">Keperluan</th>
                                <th class="py-3 px-6 text-left">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach ($guestsTable as $guest)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $guest->name }}</td>
                                    <td class="py-3 px-6 text-left">{{ $guest->hp }}</td>
                                    <td class="py-3 px-6 text-left">{{ $guest->company }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ $services->where('id', $guest->service)->first()->name ?? '-' }}</td>
                                    <td class="py-3 px-6 text-left">{{ $guest->created_at->format('d F Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="m-4">{{ $guestsTable->links() }}</div>

                <button class="bg-blue-500 text-blue-100 py-2 px-4 m-4 rounded">
                    <a href="{{ route('guest.download') }}">Download xlsx</a>
                </button>
            </div>
            @endauth
            @guest
            <div class="bg-white shadow-md rounded-lg p-4 text-center">
                <h2 class="text-xl font-semibold hover:text-blue-500">
                    <a href="{{ route('login') }}">
                        Silahkan login untuk melihat data tamu
                    </a>
                </h2>
            </div>   
            @endguest
        </div>
    </div>
</x-app-layout>
