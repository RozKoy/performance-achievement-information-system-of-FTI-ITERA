<form class="m-0 flex w-full">
    @foreach (request()->query() as $index => $item)
        @if ($index !== 'search')
            <input type="hidden" name="{{ $index }}" value="{{ $item }}">
        @endif
    @endforeach
    <div class="relative flex-1 text-primary">
        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-3 sm:w-4" aria-hidden="true">
                <g>
                    <path d="M24,22.586l-6.262-6.262a10.016,10.016,0,1,0-1.414,1.414L22.586,24ZM10,18a8,8,0,1,1,8-8A8.009,8.009,0,0,1,10,18Z" />
                </g>
            </svg>
        </div>
        <input type="search" name="search" title="Pencarian" oninvalid="this.setCustomValidity('Pencarian wajib diisi')" oninput={{ request()->query('search') !== null ? 'clearSearch(this)' : "this.setCustomValidity('')" }} class="block w-full flex-1 rounded-l-lg border-2 !border-slate-100 px-2 py-1.5 !ps-8 text-xs text-primary focus:!border-primary focus:ring-0 sm:px-2.5 sm:ps-10 sm:text-sm" value="{{ request()->query('search') }}" placeholder="Cari..." />
    </div>
    <button type="submit" title="Tombol cari" class="rounded-r-lg !bg-primary/80 px-2 text-xs font-medium text-white hover:!bg-primary/70 focus:outline-none focus:ring-2 focus:ring-primary sm:px-4 sm:text-sm">Cari</button>
</form>

@pushIf(request()->query('search') !== null, 'script')
@pushOnce('script')
    <script>
        function clearSearch(element) {
            if (element.value === '') {
                element.form.submit();
            } else {
                element.setCustomValidity('');
            }
        }
    </script>
@endPushOnce
@endPushIf
