@php
    /**
     * @required @param title: string
     * @required @param for: string
     * @optional @param required: mixed
     */
@endphp

<label for="{{ $for }}" title="{{ ucfirst($title) }}" class="@isset($required) after:content-['*'] after:text-red-500 @endisset max-sm:text-sm">
    {{ ucwords($title) }}
</label>
