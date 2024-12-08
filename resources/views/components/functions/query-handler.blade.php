@php
    /**
     * @required @param data: string[]
     */
@endphp

<div class="hidden">

    @foreach ($data as $item)
        @if (request()->query($item))
            <input type="hidden" name="{{ $item }}" value="{{ request()->query($item) }}">
        @endif
    @endforeach

</div>
