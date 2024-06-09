<div id="{{ $id }}" tabindex="-1" class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden md:inset-0">
    <div class="relative max-h-full w-full max-w-md p-4">
        <div class="relative rounded-lg bg-white shadow shadow-primary">
            <button type="button" title="Tutup" onclick="popDeleteId()" class="absolute end-2.5 top-3 ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-primary hover:bg-gray-200 hover:text-primary/80" data-modal-hide="{{ $id }}">
                <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 text-center md:p-5">
                <svg class="mx-auto mb-4 aspect-square w-10 text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="text-sm font-semibold text-primary sm:text-base">Anda akan menghapus data dibawah ini</h3>
                <div id="data-body" class="my-2.5 flex flex-col items-start overflow-auto whitespace-nowrap rounded-lg bg-red-50 p-2 text-left text-xs text-red-400 sm:text-sm"></div>
                <div class="inline-flex flex-wrap items-center gap-1.5">
                    <button id="delete-confirm" type="button" onclick="deleteItemById(this)" class="rounded-lg bg-red-500 px-2 py-1.5 text-xs text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 sm:text-sm">
                        Iya, hapus
                    </button>
                    <button data-modal-hide="{{ $id }}" type="button" onclick="popDeleteId()" class="rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm text-primary hover:bg-gray-100 hover:text-primary/80 focus:outline-none focus:ring-2 focus:ring-gray-100">Tidak, batalkan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@pushOnce('script')
    <script>
        function pushDeleteId(component) {
            let id = component.getAttribute('data-id');
            let data = JSON.parse(component.getAttribute('data-body'));
            let keys = Object.keys(data);
            let info = '';
            keys.forEach(item => {
                info += `<p><span class="capitalize font-semibold">${ item }</span> = ${ data[item] }</p>`;
            });
            document.getElementById('data-body').innerHTML = info;
            document.getElementById('delete-confirm').setAttribute('data-id', id);
        }

        function deleteItemById(component) {
            let id = component.getAttribute('data-id');
            window.location.href = `${ window.location.origin }${ window.location.pathname }/${ id }/hapus`;
        }

        function popDeleteId() {
            document.getElementById('delete-confirm').removeAttribute('data-id');
            document.getElementById('data-body').innerHTML = '';
        }
    </script>
@endPushOnce
