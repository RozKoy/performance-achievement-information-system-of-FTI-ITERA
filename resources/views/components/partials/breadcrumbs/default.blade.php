@php
    /**
     * @type Type [
     *      @required link: string
     *      @required name: string
     *      @optional params: string[]
     * ]
     *
     * @required @param breadCrumbs: Type[]
     */
@endphp

@php
    $dashboardRoute = auth()->user()->role === 'super admin' ? 'super-admin-dashboard' : 'admin-dashboard';
@endphp

<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-0 overflow-hidden text-sm rtl:space-x-reverse max-md:text-xs md:space-x-2">
        <li title="Halaman Beranda" class="inline-flex items-center">
            <a href="{{ url(route($dashboardRoute)) }}" class="{{ request()->routeIs($dashboardRoute) ? 'pointer-events-none text-primary/75' : 'text-gray-500 hover:text-primary' }} inline-flex items-center font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="me-1 h-3 w-3 md:me-2.5">
                    <path d="M22.849,7.68l-.869-.68h.021V2h-2v3.451L13.849,.637c-1.088-.852-2.609-.852-3.697,0L1.151,7.68c-.731,.572-1.151,1.434-1.151,2.363v13.957H9V15c0-.551,.448-1,1-1h4c.552,0,1,.449,1,1v9h9V10.043c0-.929-.42-1.791-1.151-2.363Zm-.849,14.32h-5v-7c0-1.654-1.346-3-3-3h-4c-1.654,0-3,1.346-3,3v7H2V10.043c0-.31,.14-.597,.384-.788L11.384,2.212c.363-.284,.869-.284,1.232,0l9,7.043c.244,.191,.384,.478,.384,.788v11.957Z" />
                </svg>
                Beranda
            </a>
        </li>

        @isset($breadCrumbs)
            @foreach ($breadCrumbs as $item)
                <li title="Halaman {{ $item['name'] }}" class="overflow-hidden">
                    <div class="flex items-center overflow-hidden">
                        <svg class="mx-0.5 h-3 w-3 text-gray-400 rtl:rotate-180 md:mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ url(route($item['link'], isset($item['params']) ? $item['params'] : [])) }}" class="{{ request()->routeIs($item['link']) ? 'pointer-events-none text-primary/75' : 'text-gray-500 hover:text-primary' }} ms-0 truncate font-medium md:ms-2">{{ $item['name'] }}</a>
                    </div>
                </li>
            @endforeach
        @endisset

    </ol>
</nav>
