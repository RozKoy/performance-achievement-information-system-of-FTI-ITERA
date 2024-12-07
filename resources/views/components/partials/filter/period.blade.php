@php
    /**
     * @type Type [
     *      @required title: string
     *      @required value: string
     * ]
     *
     * @required @param periods: Type[]
     * @required @param period: string
     */
@endphp

<form class="*:whitespace-nowrap *:flex-1 *:border *:border-primary/80 *:rounded-xl *:py-0.5 *:px-1 flex flex-wrap gap-2 text-primary max-md:text-sm max-sm:text-xs">

    @if (request()->query('year') !== null)
        <input type="hidden" name="year" value="{{ request()->query('year') }}">
    @endif

    @foreach ($periods as $item)
        <button title="{{ $item['title'] }}" name="period" value="{{ $item['value'] }}" class="{{ $item['value'] === $period ? 'outline outline-1 outline-offset-2 outline-primary/80 hover:border-transparent hover:bg-primary/80 hover:text-white' : 'hover:border-transparent hover:bg-primary/80 hover:text-white' }}">
            {{ $item['title'] }}
        </button>
    @endforeach

</form>
