<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('menu.tamu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-auto">

                    <div class="mb-3">
                        <form class="flex h-fit flex-wrap">
                            <select name="numb_per_page" class="my-input mr-2 w-fit ml-auto" data-tooltip-target="tooltip-numb-per-page">
                                <option value="5" @if($numb_per_page=='5' ) selected @endif>5</option>
                                <option value="10" @if($numb_per_page=='10' ) selected @endif>10</option>
                                <option value="25" @if($numb_per_page=='25' ) selected @endif>25</option>
                                <option value="50" @if($numb_per_page=='50' ) selected @endif>50</option>
                                <option value="100" @if($numb_per_page=='100' ) selected @endif>100</option>
                            </select>

                            <div class="search-container flex justify-center items-center" x-data="{mode: 'tiket', statuses : []}" x-init="statuses = (await (await fetch('/status')).json()).data">
                                <select class="my-input mr-2" data-tooltip-target="tooltip-search-by" x-model="mode" id="search-mode" x-init="{{$validated['status'] ?? 'null'}} != null ? mode='status' : mode='tiket'"> <!-- init jika query string / $validated['status'] tidak null maka set mode ke status -->
                                    <option value="tiket">Tiket</option>
                                    <option value="status">Status</option>
                                </select>

                                <select name="status" class="my-input mr-2" x-show="mode=='status'">
                                    <option value="">==All==</option>
                                    <template x-for="status in statuses" :key="status.id">
                                        <option :value="status.id" x-text="status.name" :selected="{{$validated['status'] ?? 0}} == status.id"></option>
                                    </template>
                                </select>

                                <div class="inline-block my-input h-fit" x-show="mode=='tiket'">
                                    <input name="ticket" type="text" id="search" class="border-transparent focus:outline-none focus:ring-0 focus:border-transparent bg-transparent" placeholder="Cari berdasarkan tiket" @isset($validated['ticket'])value="{{ $validated['ticket'] }}" @endisset>
                                    <button type="submit" class="search bg-teal-300 hover:bg-teal-400 border border-teal-400 text-teal-50 py-1 px-2 rounded-full cursor-pointer hover:scale-105">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                        </form>

                        <!-- TOOLTIP -->
                        <div id="tooltip-numb-per-page" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-green-50 bg-green-500 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Jumlah per halaman
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>

                        <div id="tooltip-search-by" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-green-50 bg-green-500 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Pencarian berdasarkan
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                        <!-- TOOLTIP END -->
                    </div>

                    <table class="table table-auto w-full mb-4">
                        <thead>
                            <tr class="text-left [&>th]:border-b-2 bg-gray-200 odd:[&>th]:border-b-teal-400 even:[&>th]:border-b-teal-600 [&>th]:py-1 [&>th]:px-2 [&>th]:text-center ">
                                <th>No</th>
                                <th>Tiket</th>
                                <th>Yang Mengajukan</th>
                                <th>Fungsi</th>
                                <th>Kode Barang</th>
                                <th>NUP</th>
                                <th>Nama Barang</th>
                                <th>Petugas</th>
                                <th>Status TL</th>
                                <th>Tindak Lanjut</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr class="border-b odd:bg-gray-100 [&>td]:p-2 hover:bg-gray-300 [&>td]:text-center">
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {!! $data->links() !!}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            const forms = document.querySelectorAll('form')

            forms.forEach(form => {
                const submitBtn = form.querySelector('button[type="submit"]')

                if (submitBtn != null) { // pastikan submitBtn ada agar bisa ambil innertText nya
                    form.addEventListener('submit', function(e) {
                        submitBtn.setAttribute('disabled', true)

                        let msg = submitBtn.innerText + ' permohonan perbaikan ini ?'

                        // BYPASS UNTUK FORM SEARCH
                        if (submitBtn.classList.contains('search')) {
                            return
                        }

                        // UBAH PESAN KONFIRMASI SAAT HAPUS DATA
                        if (submitBtn.classList.contains('delete')) {
                            msg = 'Apakah Anda yakin ingin menghapus item ini ?';
                        }

                        // UBAH PESAN KONFIRMASI SAAT VERIFIKASI KATIM (LANGSUNG DARI TABEL BUKAN DARI SHOW DATA)
                        // AMBIL DARI TEXT TOOLTIP NYA
                        if (submitBtn.classList.contains('verif-katim')) {
                            const textDiv = submitBtn.nextElementSibling.textContent.trim();
                            msg = textDiv + ' permohonan perbaikan ini ?'
                        }

                        const isConfirmed = confirm(msg)
                        // jika konfirmasi ditolak maka batalkan submit
                        if (!isConfirmed) {
                            e.preventDefault()
                            submitBtn.removeAttribute('disabled')
                        }
                    })
                }
            });

            const searchBy = document.querySelector('select[name="status"]')
            const searchBtn = document.querySelector('button.search')
            const searchMode = document.querySelector('#search-mode')
            const searchInput = document.querySelector('input#search')

            searchBy.addEventListener('change', function(e) {
                searchBtn.click();
            })

            // HANDLE SAAT MODE DIUBAH KOSONGKAN VALUE YANG TIDAK DIGUNAKAN
            searchMode.addEventListener('change', function(e) {
                if (this.value == 'status') {
                    searchInput.value = ""
                } else if (this.value == 'tiket') {
                    searchBy.value = ""
                }
            })

            // setTimeout(() => {

            //     setMode()
            // }, 1000);

            // function setMode() {
            //     // Mendapatkan query string dari URL
            //     const queryString = window.location.search;
            //     const urlParams = new URLSearchParams(queryString);

            //     // Mendapatkan nilai dari parameter 'status'
            //     const statusQueryString = urlParams.get('status');

            //     // ubah mode ke status jika query string status tidak kosong
            //     if (statusQueryString != null) {
            //         searchMode.value = 'status'
            //     }
            // }


        })()
    </script>
    @endpush
</x-app-layout>