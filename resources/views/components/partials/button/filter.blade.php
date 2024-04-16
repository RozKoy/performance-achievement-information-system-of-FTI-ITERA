<button type="button" onclick="filterToggle()" class="ml-auto flex items-center gap-1 rounded-lg bg-orange-500 px-2 py-1.5 text-center text-xs text-white hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400 max-sm:w-fit sm:text-sm">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="aspect-square w-3 sm:w-4">
        <path d="m15 24-6-4.5v-5.12l-8-9v-2.38a3 3 0 0 1 3-3h16a3 3 0 0 1 3 3v2.38l-8 9zm-4-5.5 2 1.5v-6.38l8-9v-1.62a1 1 0 0 0 -1-1h-16a1 1 0 0 0 -1 1v1.62l8 9z" />
    </svg>
    Filter
</button>

@pushOnce('script')
    <script>
        function filterToggle() {
            document.getElementById('filter').classList.toggle('hidden');
            document.getElementById('filter').classList.toggle('flex');
        }
    </script>
@endPushOnce
