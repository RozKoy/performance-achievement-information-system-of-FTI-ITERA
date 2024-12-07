@php
    /**
     * @optional @param link: route|string
     * @optional @param style: string
     *
     * @if link is set
     *
     * @optional @param viewOnly: mixed
     *
     * @el
     *
     *  @optional @param button: mixed
     *
     *  @if button is set
     *
     *  @optional @param onclick: string
     *
     *  @eif
     *
     * @eif
     */
@endphp

@if (isset($link))
    <a href="{{ url($link) }}" title="Ubah" class="{{ isset($style) ? $style : '' }} {{ isset($viewOnly) ? 'pointer-events-none' : 'hover:bg-yellow-400' }} rounded-full bg-yellow-500 p-0.5 text-white">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="{{ isset($viewOnly) ? '' : 'sm:w-6' }} aspect-square w-5">
            <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm1.379-15.621l-7.379,7.379v4.242h4.242l7.379-7.379c1.17-1.17,1.17-3.072,0-4.242s-3.072-1.17-4.242,0Zm-3.965,9.621h-1.414v-1.414l4.336-4.336,1.414,1.414-4.336,4.336Zm6.793-6.793l-1.043,1.043-1.414-1.414,1.043-1.043c.391-.391,1.023-.391,1.414,0s.39,1.024,0,1.414Z" />
        </svg>
    </a>
@else
    @if (isset($button))
        <button type="button" title="Tombol ubah" @isset($onclick) onclick="{{ $onclick }}" @endisset class="{{ isset($style) ? $style : '' }} rounded-full bg-yellow-500 p-0.5 text-white hover:bg-yellow-400">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-5 sm:w-6">
                <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm1.379-15.621l-7.379,7.379v4.242h4.242l7.379-7.379c1.17-1.17,1.17-3.072,0-4.242s-3.072-1.17-4.242,0Zm-3.965,9.621h-1.414v-1.414l4.336-4.336,1.414,1.414-4.336,4.336Zm6.793-6.793l-1.043,1.043-1.414-1.414,1.043-1.043c.391-.391,1.023-.391,1.414,0s.39,1.024,0,1.414Z" />
            </svg>
        </button>
    @else
        <button type="submit" title="Tombol ubah" class="{{ isset($style) ? $style : '' }} ml-auto flex items-center gap-1 rounded-lg bg-yellow-500 px-2 py-1.5 text-center text-xs text-white hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 max-sm:w-fit sm:text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-3 sm:w-4">
                <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm1.379-15.621l-7.379,7.379v4.242h4.242l7.379-7.379c1.17-1.17,1.17-3.072,0-4.242s-3.072-1.17-4.242,0Zm-3.965,9.621h-1.414v-1.414l4.336-4.336,1.414,1.414-4.336,4.336Zm6.793-6.793l-1.043,1.043-1.414-1.414,1.043-1.043c.391-.391,1.023-.391,1.414,0s.39,1.024,0,1.414Z" />
            </svg>
            Ubah
        </button>
    @endif
@endif
