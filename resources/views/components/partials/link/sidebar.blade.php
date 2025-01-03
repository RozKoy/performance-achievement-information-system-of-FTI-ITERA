@php
    /**
     * @required @param route: string
     * @required @param name: string
     *
     * @optional @param slot: child
     */
@endphp

<a href="{{ url(route($route)) }}" title="Halaman {{ $name }}" class="{{ request()->routeIs($route) || request()->routeIs($route . '-*') ? 'bg-primary hover:bg-opacity-25 text-white hover:text-primary' : 'hover:bg-gray-100 hover:text-gray-700' }} group flex items-center rounded-lg p-2">

    {{ $slot }}

    <span class="ms-3 flex-1 capitalize">{{ $name }}</span>

    @if (request()->routeIs($route) || request()->routeIs($route . '-*'))
        <div class="h-5 w-1.5 rounded bg-white group-hover:bg-primary"></div>
    @endif
</a>
