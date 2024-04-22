<div class="fixed bottom-5 right-5 z-20 rounded-full">
    <div>
        <button type="button" title="Tombol informasi waktu" class="flex animate-bounce rounded-full bg-primary/90 fill-white p-0.5 text-sm shadow-sm shadow-primary/80 duration-1000 hover:animate-none hover:bg-primary/100 focus:animate-none focus:bg-primary/100 focus:ring-2 focus:ring-primary/90" aria-expanded="false" data-dropdown-toggle="time-information">
            <span class="sr-only">Open time information</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="aspect-square w-7 sm:w-8">
                <g>
                    <path d="M12,24A12,12,0,1,1,24,12,12.013,12.013,0,0,1,12,24ZM12,2A10,10,0,1,0,22,12,10.011,10.011,0,0,0,12,2Z" />
                    <path d="M13,15H11v-.743a3.954,3.954,0,0,1,1.964-3.5,2,2,0,0,0,1-2.125,2.024,2.024,0,0,0-1.6-1.595A2,2,0,0,0,10,9H8a4,4,0,1,1,5.93,3.505A1.982,1.982,0,0,0,13,14.257Z" />
                    <rect x="11" y="17" width="2" height="2" />
                </g>
            </svg>
        </button>
    </div>
    <div class="*:whitespace-nowrap z-40 my-4 hidden list-none divide-y divide-gray-100 rounded bg-white shadow shadow-primary" id="time-information">
        <div class="*:my-1 cursor-default px-4 py-2.5 text-sm text-primary" role="none">
            <p role="none" title="Rencana strategis">
                Rencana Strategis
            </p>
            <x-partials.badge.time :data="$rs" />
        </div>
        <div class="*:my-1 cursor-default px-4 py-2.5 text-sm text-primary" role="none">
            <p role="none" title="Indikator kinerja utama">
                Indikator Kinerja Utama
            </p>
            <x-partials.badge.time :data="$iku" />
        </div>
    </div>
</div>
