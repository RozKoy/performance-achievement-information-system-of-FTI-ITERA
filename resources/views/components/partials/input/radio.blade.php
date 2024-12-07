@php
    /**
     * @required @param title: string
     * @required @param value: string
     * @required @param name: string
     * @required @param id: string
     * @optional @param required: mixed
     * @optional @param checked: mixed
     */
@endphp

<input type="radio" title="{{ $title }}" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" oninvalid="this.setCustomValidity('Mohon pilih salah satu')" onchange="this.setCustomValidity('')" class="rounded-md border-0 bg-primary/25 checked:bg-primary/80 focus:ring-primary/90" @isset($checked) checked @endisset @isset($required) required @endisset>
