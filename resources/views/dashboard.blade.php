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
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        TAMU HARI INI
                    </h2>
                    <p class="text-3xl font-bold text-indigo-600">{{ $guests_today }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 2 -->
                <div class="flex-1 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        SELAMAT DATANG DI BALAI BESAR POM DI MATARAM
                    </h2>
                </div>
            </div>

            
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 text-center mb-2">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    Total Seluruh Tamu Berdasarkan Keperluan
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Box 1 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 1)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-indigo-600">{{ $guests->where('service', 1)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 2 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 2)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-green-600">{{ $guests->where('service', 2)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 3 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 3)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-yellow-600">{{ $guests->where('service', 3)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 4 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 4)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-red-600">{{ $guests->where('service', 4)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 5 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 5)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-purple-600">{{ $guests->where('service', 5)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 6 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 6)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-blue-600">{{ $guests->where('service', 6)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 7 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 7)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-teal-600">{{ $guests->where('service', 7)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>

                <!-- Box 8 -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $services->where('id', 8)->first()->name }}</h2>
                    <p class="text-3xl font-bold text-emerald-600">{{ $guests->where('service', 8)->count() }}</p>
                    <p class="text-sm text-gray-500">Tamu</p>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
