<!-- Overlay -->
<div class="fixed flex inset-0 bg-black bg-opacity-90 items-center justify-center z-[9999]" id="survey-modal">

    <!-- Popup Modal -->
    <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
        <!-- Close Button -->
        <div class="absolute top-2 right-2 cursor-pointer text-gray-500 hover:text-gray-700"
            onclick="toggleSurveyModal()">✕</div>

        <!-- Popup Content -->
        <div class="text-center mt-8">
            <div class="text-gray-800 text-lg">
                Hay, {{ session('name') }} 👋
            </div>
            <h1 class="text-gray-800 text-xl font-semibold mb-4">Berikan penilaian Anda untuk Kami</h1>

            <form id="surveyForm" class="inline-block" method="POST" action="{{ route('presensi.store_survey') }}">
                @csrf
                <div class="flex justify-center gap-6 text-5xl mb-4">
                    <input type="hidden" name="guest_id" value="{{ session('id') }}" />
                    <label>
                        <input type="radio" name="rating" value="1" class="hidden peer" required />
                        <span
                            class="cursor-pointer peer-checked:scale-125 peer-checked:opacity-100 opacity-70 transition-transform flex flex-col gap-2">
                            <span>😞</span>
                            <span class="text-gray-800 text-base">Tidak Puas</span>
                        </span>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="2" class="hidden peer" />
                        <span
                            class="cursor-pointer peer-checked:scale-125 peer-checked:opacity-100 opacity-70 transition-transform flex flex-col gap-2">
                            <span>😐</span>
                            <span class="text-gray-800 text-base">Puas</span>
                        </span>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="3" class="hidden peer" />
                        <span
                            class="cursor-pointer peer-checked:scale-125 peer-checked:opacity-100 opacity-70 transition-transform flex flex-col gap-2">
                            <span>😄</span>
                            <span class="text-gray-800 text-base">Sangat Puas</span>
                        </span>
                    </label>
                </div>

                <button type="submit"
                    class="mt-2 bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                    Kirim Penilaian
                </button>
            </form>

            <p id="thanksMessage" class="hidden mt-4 text-green-600 text-lg font-medium">Terima kasih atas penilaian
                Anda! 💙</p>
        </div>
    </div>
</div>
