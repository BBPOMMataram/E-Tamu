<!-- Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-[9999]" id="popup-overlay">
    
    <!-- Popup Modal -->
    <div class="bg-white p-6 rounded-lg shadow-lg w-80 relative">
        <!-- Close Button -->
        <div class="absolute top-2 right-2 cursor-pointer text-gray-500 hover:text-gray-700" onclick="togglePopup()">✕</div>

        <!-- Popup Content -->
        <div class="text-center">
            <img src="{{ asset('images/popup-img.png') }}" alt="">
        </div>
    </div>
</div>

<!-- Button to Open Popup -->
{{-- <div class="text-center mt-6">
    <button onclick="togglePopup()" class="bg-blue-500 text-white px-4 py-2 rounded">Open Popup</button>
</div> --}}

<script>
    function togglePopup() {
        const overlay = document.getElementById('popup-overlay');
        overlay.classList.toggle('hidden');
    }
</script>
