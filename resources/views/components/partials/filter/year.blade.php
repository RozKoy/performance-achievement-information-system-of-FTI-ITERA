<form action="" class="*:rounded-xl *:border *:border-primary/80 *:px-1.5 *:py-0.5 flex snap-x items-center gap-1.5 overflow-x-auto p-2 text-primary max-md:text-sm max-sm:text-xs">
    @foreach ($years as $item)
        @if ($item === $year)
            <button name="year" title="{{ $item }}" value="{{ $item }}" class="snap-end border-transparent bg-primary/80 text-white hover:border-primary/80 hover:bg-white hover:text-primary focus:outline-primary" autofocus>{{ $item }}</button>
        @else
            <button name="year" title="{{ $item }}" value="{{ $item }}" class="snap-end hover:border-transparent hover:bg-primary/80 hover:text-white focus:outline-primary">{{ $item }}</button>
        @endif
    @endforeach
</form>
