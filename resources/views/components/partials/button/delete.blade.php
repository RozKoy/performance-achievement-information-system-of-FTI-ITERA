@if (!isset($viewOnly))
    <button type="button" title="Hapus" data-id="{{ $id }}" onclick="pushDeleteId(this)" data-modal-target="{{ $modal }}" data-modal-toggle="{{ $modal }}" data-body='{!! json_encode($data) !!}' class="rounded-full bg-red-500 p-0.5 text-white hover:bg-red-400">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-5 sm:w-6">
            <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm-5-11h10v2H7v-2Z" />
        </svg>
    </button>
@else
    <button type="button" title="Hapus" class="pointer-events-none rounded-full bg-red-500 p-0.5 text-white" disabled>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-5">
            <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,22c-5.514,0-10-4.486-10-10S6.486,2,12,2s10,4.486,10,10-4.486,10-10,10Zm-5-11h10v2H7v-2Z" />
        </svg>
    </button>
@endif
