<ol class="flex w-full items-center gap-2 rounded-lg border border-gray-200 bg-white p-3 text-center text-xs font-medium text-gray-400 shadow-sm sm:text-sm">

    @foreach ($stepper as $item)
        <li class="{{ isset($item['status']) ? 'text-primary' : '' }} flex cursor-default items-center gap-1.5 overflow-hidden">
            <span class="{{ isset($item['status']) ? 'border-primary' : 'border-gray-400' }} flex aspect-square w-5 shrink-0 items-center justify-center rounded-full border text-xs">
                {{ $loop->iteration }}
            </span>

            <div class="overflow-hidden">
                <p class="truncate" title="{{ $item['name'] }}">{{ $item['name'] }}</p>
            </div>

            @if (!$loop->last)
                <svg class="aspect-square w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 9 4-4-4-4M1 9l4-4-4-4" />
                </svg>
            @endif

        </li>
    @endforeach

</ol>
