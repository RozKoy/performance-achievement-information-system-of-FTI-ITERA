@php
    /**
     * @required @param message: string
     * @required @param id: string
     * @optional @param warning: mixed
     * @optional @param danger: mixed
     *
     * @if warning && danger are not set
     *
     * Status is success
     *
     * @eif
     */
@endphp

@php
    $id .= '-toast';
@endphp

<div id="{{ $id }}" class="{{ isset($danger) ? 'bg-red-200 outline-red-500' : (isset($warning) ? 'bg-orange-200 outline-orange-500' : 'bg-green-200 outline-green-500') }} flex w-full max-w-xs items-center gap-1.5 rounded-lg p-1.5 text-gray-500 outline outline-2 transition-all duration-200 max-2xl:text-sm max-lg:text-xs 2xl:max-w-sm" role="alert">
    <div class="{{ isset($danger) ? 'text-red-500' : (isset($warning) ? 'text-orange-500' : 'text-green-500') }} inline-flex flex-shrink-0 items-center justify-center rounded-lg bg-green-50 p-1">

        @if (isset($danger))
            <svg class="aspect-square w-4 lg:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z" />
            </svg>
        @else
            @if (isset($warning))
                <svg class="aspect-square w-4 lg:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z" />
                </svg>
            @else
                <svg class="aspect-square w-4 lg:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                </svg>
            @endif
        @endif

        <span class="sr-only">Check icon</span>
    </div>
    <div title="{{ $message }}" class="font-normal">{{ $message }}</div>
    <button type="button" class="-my-1.5 inline-flex items-center justify-center rounded-lg bg-white p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:ring-2 focus:ring-gray-300" onclick="removeToast('{{ $id }}', 250)" aria-label="Close">
        <span class="sr-only">Close</span>
        <svg class="aspect-square w-2.5 lg:w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
        </svg>
    </button>
</div>

@pushOnce('script')
    <script>
        function removeToast(id, timeout = 5000) {
            let component = document.getElementById(id);

            setTimeout(() => {
                component.remove();
            }, timeout);
        }
    </script>
@endPushOnce

@isset($withTimeout)
    @push('script')
        <script>
            removeToast("{{ $id }}");
        </script>
    @endpush
@endisset
