@isset($success)
    <div id="{{ $id }}-toast" class="flex w-full max-w-xs items-center rounded-lg bg-green-100 p-3 text-gray-500 outline outline-2 outline-green-500 max-2xl:text-sm max-lg:text-xs 2xl:max-w-sm" role="alert">
        <div class="inline-flex flex-shrink-0 items-center justify-center rounded-lg bg-green-50 p-2 text-green-500">
            <svg class="aspect-square w-4 lg:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 font-normal">{{ $message }}</div>
        <button type="button" class="-mx-1.5 -my-1.5 ms-auto inline-flex items-center justify-center rounded-lg bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:ring-2 focus:ring-gray-300" data-dismiss-target="#{{ $id }}-toast" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="aspect-square w-2.5 lg:w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
@endisset

@isset($danger)
    <div id="{{ $id }}-toast" class="flex w-full max-w-xs items-center rounded-lg bg-red-100 p-3 text-gray-500 outline outline-2 outline-red-500 max-2xl:text-sm max-lg:text-xs 2xl:max-w-sm" role="alert">
        <div class="inline-flex flex-shrink-0 items-center justify-center rounded-lg bg-red-50 p-2 text-red-500">
            <svg class="aspect-square w-4 lg:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" />
            </svg>
            <span class="sr-only">Error icon</span>
        </div>
        <div class="ms-3 font-normal">{{ $message }}</div>
        <button type="button" class="-mx-1.5 -my-1.5 ms-auto inline-flex items-center justify-center rounded-lg bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:ring-2 focus:ring-gray-300" data-dismiss-target="#{{ $id }}-toast" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="aspect-square w-2.5 lg:w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
@endisset

@isset($warning)
    <div id="{{ $id }}-toast" class="flex w-full max-w-xs items-center rounded-lg bg-orange-100 p-3 text-gray-500 outline outline-2 outline-orange-500 max-2xl:text-sm max-lg:text-xs 2xl:max-w-sm" role="alert">
        <div class="inline-flex flex-shrink-0 items-center justify-center rounded-lg bg-orange-50 p-2 text-orange-500">
            <svg class="aspect-square w-4 lg:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z" />
            </svg>
            <span class="sr-only">Warning icon</span>
        </div>
        <div class="ms-3 font-normal">{{ $message }}</div>
        <button type="button" class="-mx-1.5 -my-1.5 ms-auto inline-flex items-center justify-center rounded-lg bg-white p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:ring-2 focus:ring-gray-300" data-dismiss-target="#{{ $id }}-toast" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="aspect-square w-2.5 lg:w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
        </button>
    </div>
@endisset

@pushOnce('script')
    <script>
        function removeToast(id) {
            let component = document.getElementById(id);
            let button = component.querySelector('button');

            setTimeout(() => {
                button.click();
            }, 3000);

            setTimeout(() => {
                component.remove();
            }, 3250);
        }
    </script>
@endPushOnce

@push('script')
    <script>
        removeToast("{{ $id . '-toast' }}");
    </script>
@endpush
