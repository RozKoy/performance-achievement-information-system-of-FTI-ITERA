<button type="{{ isset($submit) ? 'submit' : 'button' }}" @if (!isset($submit) && isset($href)) onclick="moveTo('{{ url(route($href)) }}')" @endif title="Tombol tambah" class="ml-auto flex items-center gap-1 rounded-lg bg-green-500 px-2 py-1.5 text-center text-xs text-white hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-400 max-sm:w-fit sm:text-sm">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="aspect-square w-3 sm:w-4">
        <path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10zm1-11h4v2h-4v4h-2v-4h-4v-2h4v-4h2z" />
    </svg>
    Tambah
</button>

@if (!isset($submit) && isset($href))
    @pushOnce('script')
        <script>
            function moveTo(link) {
                window.location.href = link;
            }
        </script>
    @endPushOnce
@endif
