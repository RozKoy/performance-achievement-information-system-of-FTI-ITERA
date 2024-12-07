@php
    /**
     * @required @param route: string
     * @required @param title: string
     * @required @param name: string
     * @optional @param center: mixed
     * @optional @param right: mixed
     * @optional @param left: mixed
     */
@endphp

<a href="{{ url(route($route)) }}" title="Halaman {{ $title }}" class="{{ isset($left) ? 'self-start' : '' }} {{ isset($center) ? 'self-center' : '' }} {{ isset($right) ? 'self-end' : '' }} w-fit capitalize text-primary underline hover:text-primary/90">
    {{ $name }}
</a>
