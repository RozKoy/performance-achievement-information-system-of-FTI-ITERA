@php
    /**
     * @required @param dataNumber: string
     * @required @param dataText: string
     * @required @param title: string
     */
@endphp

<div class="flex flex-wrap items-center gap-3">
    <h3 title="{{ $title }}" class="text-lg font-semibold capitalize text-primary sm:text-xl">{{ $title }} :</h3>
    <div class="*:px-2.5 *:py-1 *:truncate flex min-w-[60%] flex-1 items-center overflow-hidden rounded-lg border border-slate-200 text-primary max-sm:text-sm">
        <p title="{{ $dataNumber }}" class="max-w-20 bg-primary/80 text-center text-white">{{ $dataNumber }}</p>
        <p title="{{ $dataText }}" class="flex-1 bg-primary/10">{{ $dataText }}</p>
    </div>
</div>
