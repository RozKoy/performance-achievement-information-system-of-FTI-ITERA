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
    <h2 title="Halaman {{ $text }}" class="text-xl font-semibold capitalize text-primary sm:text-2xl">{{ $text }}</h2>
</div>
