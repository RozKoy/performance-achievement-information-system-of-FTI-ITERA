<button title="Tombol batal" type="button" @isset($onclick) onclick="{{ $onclick }}" @endisset class="ml-auto flex items-center gap-1 rounded-lg bg-red-500 px-2 py-1.5 text-center text-xs text-white hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-red-400 max-sm:w-fit sm:text-sm">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-3 sm:w-4">
        <g>
            <polygon points="15.293 7.293 12 10.586 8.707 7.293 7.293 8.707 10.586 12 7.293 15.293 8.707 16.707 12 13.414 15.293 16.707 16.707 15.293 13.414 12 16.707 8.707 15.293 7.293" />
            <path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm0,22A10,10,0,1,1,22,12,10.011,10.011,0,0,1,12,22Z" />
        </g>
    </svg>
</button>
