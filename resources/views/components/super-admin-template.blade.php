@extends('template')

@section('content')
    <aside id="logo-sidebar" class="fixed left-0 top-0 z-40 h-screen w-72 -translate-x-full bg-white pt-16 text-base transition-transform sm:translate-x-0" aria-label="Sidebar">
        <div class="h-full divide-y-2 divide-primary overflow-y-auto bg-white px-3 pb-4 text-primary">
            <ul class="space-y-2 py-1.5 font-medium">
                <li>
                    <x-partials.link.default route="super-admin-dashboard" name="beranda">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="M22.849,7.68l-.869-.68h.021V2h-2v3.451L13.849,.637c-1.088-.852-2.609-.852-3.697,0L1.151,7.68c-.731,.572-1.151,1.434-1.151,2.363v13.957H9V15c0-.551,.448-1,1-1h4c.552,0,1,.449,1,1v9h9V10.043c0-.929-.42-1.791-1.151-2.363Zm-.849,14.32h-5v-7c0-1.654-1.346-3-3-3h-4c-1.654,0-3,1.346-3,3v7H2V10.043c0-.31,.14-.597,.384-.788L11.384,2.212c.363-.284,.869-.284,1.232,0l9,7.043c.244,.191,.384,.478,.384,.788v11.957Z" />
                        </svg>
                    </x-partials.link.default>
                </li>
                <li>
                    <x-partials.link.default route="super-admin-achievement" name="capaian kinerja">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="m12.597,10.817l-2.759,2.702c-.32.32-.744.481-1.168.481-.427,0-.855-.162-1.181-.488l-1.45-1.393,1.386-1.442,1.241,1.192,2.533-2.48,1.399,1.429Zm-1.597,8.183h6v-2h-6v2Zm-4,0h2v-2h-2v2Zm4-12h6v-2h-6v2Zm-2-2h-2v2h2v-2Zm13-2v21H2V3c0-1.654,1.346-3,3-3h14c1.654,0,3,1.346,3,3Zm-2,0c0-.551-.449-1-1-1H5c-.551,0-1,.449-1,1v19h16V3Zm-7,10h4v-2h-1.958l-2.042,2Z" />
                        </svg>
                    </x-partials.link.default>
                </li>
            </ul>
            <ul class="space-y-2 py-1.5 font-medium">
                <li>
                    <x-partials.link.default route="super-admin-rs" name="rencana strategis">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="m24,3.668v.647l-2.837.841-.831,2.844h-.647l-.842-2.843-2.843-.842v-.647l2.844-.831.841-2.837h.647l.829,2.839,2.839.829Zm-19.5,1.332c1.381,0,2.5-1.119,2.5-2.5S5.881,0,4.5,0s-2.5,1.119-2.5,2.5,1.119,2.5,2.5,2.5Zm7.5,10h-4v-5h-2v5H2v-6c0-.551.448-1,1-1h5.283l6.741-4.148-1.049-1.703-6.259,3.852H3c-1.654,0-3,1.346-3,3v8h2v7h2v-7h6v3.051c-1.14.232-2,1.242-2,2.449v1.5h2v-1.5c0-.276.225-.5.5-.5h13.5v-2h-12v-5Zm-1-3.441l6.455-4.044-.222-.749-1.591-.471-4.642,2.916v2.349Z" />
                        </svg>
                    </x-partials.link.default>
                </li>
                <li>
                    <x-partials.link.default route="super-admin-iku" name="indikator kinerja utama">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="m12,2.5c0-1.381,1.119-2.5,2.5-2.5s2.5,1.119,2.5,2.5-1.119,2.5-2.5,2.5-2.5-1.119-2.5-2.5ZM3.5,10c1.381,0,2.5-1.119,2.5-2.5s-1.119-2.5-2.5-2.5-2.5,1.119-2.5,2.5,1.119,2.5,2.5,2.5Zm20.533,2.141v2.267l-13.825,9.593h-3.208v-4H2.987l-.033,2.531-.463,1.469H.393l.607-1.924v-8.076c0-1.24.745-2.336,1.898-2.791l5.696-1.134,3.21-3.112.004.004c.548-.591,1.325-.967,2.192-.967h5V0h1l4,2.5-3,1.875v3.625h-4v5h2v2.633l5.033-3.492Zm-11.033,7.656l4-2.796v-2h-4v4.796Zm0-10.796v4h2v-5h-1c-.551,0-1,.448-1,1ZM3,18h2v-5.182l-1.437.281c-.345.167-.563.513-.563.9v4Zm8,3.184v-10.655l-1.439,1.396-2.561.502v5.574h2v4.572l2-1.388Z" />
                        </svg>
                    </x-partials.link.default>
                </li>
            </ul>
            <ul class="space-y-2 py-1.5 font-medium">
                <li>
                    <x-partials.link.default route="super-admin-users" name="pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="M15,6c0-3.309-2.691-6-6-6S3,2.691,3,6s2.691,6,6,6,6-2.691,6-6Zm-6,4c-2.206,0-4-1.794-4-4s1.794-4,4-4,4,1.794,4,4-1.794,4-4,4Zm13,7c0-.552-.09-1.082-.256-1.579l1.82-1.049-.998-1.733-1.823,1.05c-.706-.797-1.662-1.368-2.743-1.589v-2.101h-2v2.101c-1.082,.221-2.037,.792-2.743,1.589l-1.823-1.05-.998,1.733,1.82,1.049c-.166,.497-.256,1.027-.256,1.579s.09,1.082,.256,1.579l-1.82,1.049,.998,1.733,1.823-1.05c.706,.797,1.662,1.368,2.743,1.589v2.101h2v-2.101c1.082-.221,2.037-.792,2.743-1.589l1.823,1.05,.998-1.733-1.82-1.049c.166-.497,.256-1.027,.256-1.579Zm-5,3c-1.654,0-3-1.346-3-3s1.346-3,3-3,3,1.346,3,3-1.346,3-3,3ZM5,14h3v2h-3c-1.654,0-3,1.346-3,3v5H0v-5c0-2.757,2.243-5,5-5Z" />
                        </svg>
                    </x-partials.link.default>
                </li>
                <li>
                    <x-partials.link.default route="super-admin-organization" name="organisasi">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="M5.5,7c1.93,0,3.5-1.57,3.5-3.5S7.43,0,5.5,0,2,1.57,2,3.5s1.57,3.5,3.5,3.5Zm0-5c.827,0,1.5,.673,1.5,1.5s-.673,1.5-1.5,1.5-1.5-.673-1.5-1.5,.673-1.5,1.5-1.5ZM14.5,7c1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5-3.5,1.57-3.5,3.5,1.57,3.5,3.5,3.5Zm0-5c.827,0,1.5,.673,1.5,1.5s-.673,1.5-1.5,1.5-1.5-.673-1.5-1.5,.673-1.5,1.5-1.5Zm7.5,15c0-.552-.09-1.082-.256-1.579l1.82-1.049-.998-1.733-1.823,1.05c-.706-.797-1.662-1.368-2.743-1.589v-2.101h-2v2.101c-1.082,.221-2.037,.792-2.743,1.589l-1.823-1.05-.998,1.733,1.82,1.049c-.166,.497-.256,1.027-.256,1.579s.09,1.082,.256,1.579l-1.82,1.049,.998,1.733,1.823-1.05c.706,.797,1.662,1.368,2.743,1.589v2.101h2v-2.101c1.082-.221,2.037-.792,2.743-1.589l1.823,1.05,.998-1.733-1.82-1.049c.166-.497,.256-1.027,.256-1.579Zm-5,3c-1.654,0-3-1.346-3-3s1.346-3,3-3,3,1.346,3,3-1.346,3-3,3Zm-6.049-9.65c-.26-.215-.588-.35-.951-.35-.827,0-1.5,.673-1.5,1.5,0,.487,.237,.917,.598,1.191-.32,.586-.57,1.215-.755,1.871-1.094-.588-1.843-1.733-1.843-3.062,0-1.93,1.57-3.5,3.5-3.5,1.028,0,1.948,.452,2.587,1.161-.591,.334-1.138,.735-1.636,1.189Zm-.951,5.65c-.036,.329-.059,.662-.059,1s.022,.671,.059,1h-3c-.551,0-1,.449-1,1v3h-2v-3c0-1.654,1.346-3,3-3h3Zm-5.287-6h-1.713c-.552,0-1,.448-1,1v3H0v-3c0-1.654,1.346-3,3-3h2.761c-.479,.579-.837,1.259-1.047,2Z" />
                        </svg>
                    </x-partials.link.default>
                </li>
            </ul>
            <ul class="space-y-2 py-1.5 font-medium">
                <li>
                    <a href="#" class="group flex items-center rounded-lg p-2 hover:bg-gray-100 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <g>
                                <path d="M23.259,16.2l-2.6-9.371A9.321,9.321,0,0,0,2.576,7.3L.565,16.35A3,3,0,0,0,3.493,20H7.1a5,5,0,0,0,9.8,0h3.47a3,3,0,0,0,2.89-3.8ZM12,22a3,3,0,0,1-2.816-2h5.632A3,3,0,0,1,12,22Zm9.165-4.395a.993.993,0,0,1-.8.395H3.493a1,1,0,0,1-.976-1.217l2.011-9.05a7.321,7.321,0,0,1,14.2-.372l2.6,9.371A.993.993,0,0,1,21.165,17.605Z" />
                            </g>
                        </svg>
                        <span class="ms-3 flex-1 whitespace-nowrap">Pengumuman</span>
                        <span class="ms-3 inline-flex h-3 w-3 items-center justify-center rounded-full bg-blue-100 p-3 text-sm font-medium text-blue-800">3</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <nav class="fixed top-0 z-40 w-full border-b-2 border-primary bg-white">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center rounded-lg p-1.5 text-sm text-primary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 sm:hidden sm:p-2">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="aspect-square w-4 sm:w-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    <a href="{{ url(route('super-admin-dashboard')) }}" title="Halaman beranda" class="group/logo ms-2 flex md:me-24">
                        <img src="{{ url(asset('storage/assets/fti-logo.png')) }}" class="me-1 h-6 sm:me-2 sm:h-8" alt="FTI Logo" />
                        <span class="self-center whitespace-nowrap border-l-2 border-gray-200 px-0.5 text-lg font-semibold text-primary before:content-['SICAKI'] group-hover/logo:underline sm:px-1.5 sm:text-2xl sm:before:content-['Sistem_Informasi_Capaian_Kinerja']"></span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="ms-3 flex items-center gap-2">
                        <p title="Editor Access" class="inline-flex items-center justify-center rounded-full bg-primary px-2 py-1 text-xs font-semibold uppercase text-white sm:text-sm">editor</p>
                        <div>
                            <button type="button" class="flex rounded-full fill-primary text-sm focus:ring-4 focus:ring-gray-300" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" class="h-6 w-6 sm:h-8 sm:w-8">
                                    <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm-4,21.164v-2.164c0-.552.449-1,1-1h6c.551,0,1,.448,1,1v2.164c-1.226.537-2.578.836-4,.836s-2.774-.299-4-.836Zm10-1.169v-.995c0-1.654-1.346-3-3-3h-6c-1.654,0-3,1.346-3,3v.995c-2.427-1.826-4-4.73-4-7.995C2,6.486,6.486,2,12,2s10,4.486,10,10c0,3.264-1.573,6.169-4,7.995Zm-6-13.995c-2.206,0-4,1.794-4,4s1.794,4,4,4,4-1.794,4-4-1.794-4-4-4Zm0,6c-1.103,0-2-.897-2-2s.897-2,2-2,2,.897,2,2-.897,2-2,2Z" />
                                </svg>

                            </button>
                        </div>
                        <div class="z-40 my-4 hidden list-none divide-y divide-gray-100 rounded bg-white text-base shadow shadow-primary" id="dropdown-user">
                            <div class="cursor-default px-4 py-3 text-sm text-primary" role="none">
                                <p role="none" title="Neil Sims">
                                    Neil Sims
                                </p>
                                <p class="truncate font-semibold" role="none" title="neil.sims@flowbite.com">
                                    neil.sims@flowbite.com
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ url(route('logout')) }}" title="Keluar" class="flex items-center justify-start gap-1 fill-red-500 px-4 py-2 text-sm text-red-500 hover:bg-gray-100" role="menuitem">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5">
                                            <g>
                                                <path d="M15,2.426v2.1a9,9,0,1,1-6,0v-2.1a11,11,0,1,0,6,0Z" />
                                                <rect x="11" width="2" height="8" />
                                            </g>
                                        </svg>
                                        Keluar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="p-4 sm:ml-72">
        <div class="mt-14 rounded-lg border-2 border-dashed border-primary p-4">

            {{ $slot }}

        </div>

        <footer class="pt-2 text-center max-md:text-xs">
            <p>Copyright &copy; {{ \Carbon\Carbon::now()->format('Y') }} Fakultas Teknologi Industri - ITERA</p>
        </footer>
    </div>
@endsection
