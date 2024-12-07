@php
    /**
     * @required @param text: string
     * @optional @param previousRoute: route|string
     * @optional @param previous: string
     * @optional @param tooltip: mixed
     * @optional @param back: mixed
     *
     * @optional @param slot: child
     */
@endphp

<div class="flex items-center gap-2 max-md:flex-wrap">

    @if (isset($back) || isset($previous) || isset($previousRoute))
        <a href="{{ isset($back) ? url()->previous() : url(isset($previous) ? route($previous) : $previousRoute) }}" title="Tombol kembali" class="rounded-lg p-1 text-primary hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 sm:p-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-4 sm:w-5">
                <g>
                    <path d="M24,24H22a7.008,7.008,0,0,0-7-7H10.17v6.414L.877,14.121a3,3,0,0,1,0-4.242L10.17.586V7H15a9.01,9.01,0,0,1,9,9ZM8.17,5.414,2.291,11.293a1,1,0,0,0,0,1.414L8.17,18.586V15H15a8.989,8.989,0,0,1,7,3.349V16a7.008,7.008,0,0,0-7-7H8.17Z" />
                </g>
            </svg>
        </a>
    @endif

    <h2 title="Halaman {{ $text }}" class="text-xl font-semibold capitalize text-primary sm:text-2xl">
        {{ $text }}

        @if ($tooltip ?? false)
            <button title="Bantuan" data-popover-target="popover-description" data-popover-placement="bottom-start" data-popover-trigger="click" type="button">
                <svg class="aspect-square w-5 text-gray-400 hover:text-gray-500 max-sm:w-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Show information</span>
            </button>
        @endif

    </h2>

    @if ($tooltip ?? false)
        <div data-popover id="popover-description" role="tooltip" class="invisible absolute z-50 inline-block w-96 max-w-full rounded-lg border border-primary bg-white text-sm text-gray-500 opacity-0 shadow-sm transition-opacity duration-300 max-sm:text-xs">
            <div class="space-y-2 p-3">
                {{ $slot }}
            </div>
        </div>
    @endif

</div>
