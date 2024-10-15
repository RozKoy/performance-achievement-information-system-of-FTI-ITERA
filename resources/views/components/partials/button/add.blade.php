<button type="{{ isset($submit) ? 'submit' : 'button' }}" @if (!isset($submit) && (isset($href) || isset($route))) onclick="moveTo('{{ url(isset($href) ? route($href) : $route) }}')" @endif @disabled(isset($viewOnly)) title="Tombol tambah" class="{{ isset($style) ? $style : '' }} {{ isset($viewOnly) ? 'py-0.5 px-1.5 pointer-events-none' : 'sm:text-sm py-1.5 px-2 hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-400' }} flex items-center gap-1 rounded-lg bg-green-500 text-center text-xs text-white max-sm:w-fit">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="{{ isset($viewOnly) ? '' : 'sm:w-4' }} aspect-square w-3">
        <path d="m12 0a12 12 0 1 0 12 12 12.013 12.013 0 0 0 -12-12zm0 22a10 10 0 1 1 10-10 10.011 10.011 0 0 1 -10 10zm1-11h4v2h-4v4h-2v-4h-4v-2h4v-4h2z" />
    </svg>
    {{ isset($text) ? $text : 'Tambah' }}
</button>

@if (!isset($submit) && !isset($viewOnly) && (isset($href) || isset($route)))
    @pushOnce('script')
        <script>
            function moveTo(link) {
                window.location.href = link;
            }
        </script>
    @endPushOnce
@endif
