<div title="@foreach ($data as $item)@if ($loop->iteration !== 1){{ '|' }}@endif {{ $item }} @endforeach" class="flex w-fit items-center gap-0.5 whitespace-nowrap rounded-xl bg-primary/20 p-1.5 text-xs text-primary sm:gap-1 sm:text-sm">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="aspect-square w-3.5 sm:w-4">
        <path d="M12,24C5.383,24,0,18.617,0,12S5.383,0,12,0s12,5.383,12,12-5.383,12-12,12Zm0-22C6.486,2,2,6.486,2,12s4.486,10,10,10,10-4.486,10-10S17.514,2,12,2Zm3.397,13.803l-2.397-4.076V5h-2v7.272l2.673,4.544,1.725-1.014Z" />
    </svg>
    <p>
        @foreach ($data as $item)
            @if ($loop->iteration !== 1)
                {{ ' | ' }}
            @endif
            {{ $item }}
        @endforeach
    </p>
</div>
