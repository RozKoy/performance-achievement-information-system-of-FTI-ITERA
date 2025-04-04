@extends('template')

@section('content')
    <aside id="admin-sidebar" class="fixed left-0 top-0 z-40 h-screen w-72 -translate-x-full bg-white pt-16 text-base transition-transform sm:translate-x-0" aria-label="Sidebar">
        <div class="h-full divide-y-2 divide-primary overflow-y-auto bg-white px-3 pb-4 text-primary">
            <ul class="space-y-2 py-1.5 font-medium">
                <li>
                    <x-partials.link.sidebar route="admin-rs" name="rencana strategis">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="m24,3.668v.647l-2.837.841-.831,2.844h-.647l-.842-2.843-2.843-.842v-.647l2.844-.831.841-2.837h.647l.829,2.839,2.839.829Zm-19.5,1.332c1.381,0,2.5-1.119,2.5-2.5S5.881,0,4.5,0s-2.5,1.119-2.5,2.5,1.119,2.5,2.5,2.5Zm7.5,10h-4v-5h-2v5H2v-6c0-.551.448-1,1-1h5.283l6.741-4.148-1.049-1.703-6.259,3.852H3c-1.654,0-3,1.346-3,3v8h2v7h2v-7h6v3.051c-1.14.232-2,1.242-2,2.449v1.5h2v-1.5c0-.276.225-.5.5-.5h13.5v-2h-12v-5Zm-1-3.441l6.455-4.044-.222-.749-1.591-.471-4.642,2.916v2.349Z" />
                        </svg>
                    </x-partials.link.sidebar>
                </li>
                <li>
                    <x-partials.link.sidebar route="admin-iku" name="indikator kinerja utama">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="m12,2.5c0-1.381,1.119-2.5,2.5-2.5s2.5,1.119,2.5,2.5-1.119,2.5-2.5,2.5-2.5-1.119-2.5-2.5ZM3.5,10c1.381,0,2.5-1.119,2.5-2.5s-1.119-2.5-2.5-2.5-2.5,1.119-2.5,2.5,1.119,2.5,2.5,2.5Zm20.533,2.141v2.267l-13.825,9.593h-3.208v-4H2.987l-.033,2.531-.463,1.469H.393l.607-1.924v-8.076c0-1.24.745-2.336,1.898-2.791l5.696-1.134,3.21-3.112.004.004c.548-.591,1.325-.967,2.192-.967h5V0h1l4,2.5-3,1.875v3.625h-4v5h2v2.633l5.033-3.492Zm-11.033,7.656l4-2.796v-2h-4v4.796Zm0-10.796v4h2v-5h-1c-.551,0-1,.448-1,1ZM3,18h2v-5.182l-1.437.281c-.345.167-.563.513-.563.9v4Zm8,3.184v-10.655l-1.439,1.396-2.561.502v5.574h2v4.572l2-1.388Z" />
                        </svg>
                    </x-partials.link.sidebar>
                </li>
            </ul>
            <ul class="space-y-2 py-1.5 font-medium">
                <li>
                    <x-partials.link.sidebar route="admin-history" name="riwayat">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                            <path d="m12.597,10.817l-2.759,2.702c-.32.32-.744.481-1.168.481-.427,0-.855-.162-1.181-.488l-1.45-1.393,1.386-1.442,1.241,1.192,2.533-2.48,1.399,1.429Zm-1.597,8.183h6v-2h-6v2Zm-4,0h2v-2h-2v2Zm4-12h6v-2h-6v2Zm-2-2h-2v2h2v-2Zm13-2v21H2V3c0-1.654,1.346-3,3-3h14c1.654,0,3,1.346,3,3Zm-2,0c0-.551-.449-1-1-1H5c-.551,0-1,.449-1,1v19h16V3Zm-7,10h4v-2h-1.958l-2.042,2Z" />
                        </svg>
                    </x-partials.link.sidebar>
                </li>
            </ul>

            @if (auth()->user()->isEditor())
                <ul class="space-y-2 py-1.5 font-medium">
                    <li>
                        <x-partials.link.sidebar route="admin-users" name="pengguna">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 transition duration-75" fill="currentColor">
                                <path d="M15,6c0-3.309-2.691-6-6-6S3,2.691,3,6s2.691,6,6,6,6-2.691,6-6Zm-6,4c-2.206,0-4-1.794-4-4s1.794-4,4-4,4,1.794,4,4-1.794,4-4,4Zm13,7c0-.552-.09-1.082-.256-1.579l1.82-1.049-.998-1.733-1.823,1.05c-.706-.797-1.662-1.368-2.743-1.589v-2.101h-2v2.101c-1.082,.221-2.037,.792-2.743,1.589l-1.823-1.05-.998,1.733,1.82,1.049c-.166,.497-.256,1.027-.256,1.579s.09,1.082,.256,1.579l-1.82,1.049,.998,1.733,1.823-1.05c.706,.797,1.662,1.368,2.743,1.589v2.101h2v-2.101c1.082-.221,2.037-.792,2.743-1.589l1.823,1.05,.998-1.733-1.82-1.049c.166-.497,.256-1.027,.256-1.579Zm-5,3c-1.654,0-3-1.346-3-3s1.346-3,3-3,3,1.346,3,3-1.346,3-3,3ZM5,14h3v2h-3c-1.654,0-3,1.346-3,3v5H0v-5c0-2.757,2.243-5,5-5Z" />
                            </svg>
                        </x-partials.link.sidebar>
                    </li>
                </ul>
            @endif

            <ul class="space-y-2 py-1.5 font-medium">
                <li>
                    <div class="flex justify-end">
                        <a href="https://drive.google.com/file/d/1jGHbYurerzD_9HlQi4cn6oqUjrJuqhvY/view?usp=sharing" target="_blank" title="Manual Book" class="flex items-center gap-1 underline">
                            Manual Book
                            <svg class="aspect-square w-2.5 max-md:w-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                        </a>
                    </div>
                </li>
            </ul>

        </div>
    </aside>

    <nav class="fixed top-0 z-40 w-full border-b-2 border-primary bg-white">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="admin-sidebar" data-drawer-toggle="admin-sidebar" aria-controls="admin-sidebar" type="button" class="inline-flex items-center rounded-lg p-1.5 text-sm text-primary hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 sm:hidden sm:p-2">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="aspect-square w-4 sm:w-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                        </svg>
                    </button>
                    <a href="{{ url(route('admin-dashboard')) }}" title="Halaman beranda" class="group/logo ms-2 flex md:me-24">
                        <img src="{{ url(asset('storage/assets/fti-logo.png')) }}" class="me-1 h-6 sm:me-2 sm:h-8" alt="FTI Logo" />
                        <span class="self-center whitespace-nowrap border-l-2 border-gray-200 px-0.5 text-lg font-semibold text-primary before:content-['SICAKI'] group-hover/logo:underline sm:px-1.5 sm:text-2xl sm:before:content-['Sistem_Informasi_Capaian_Kinerja']"></span>
                    </a>
                </div>

                <div class="flex items-center">
                    <div class="ms-3 flex items-center gap-2">
                        <p title="Teknik Informatika | Editor Access" class="inline-flex items-center justify-center truncate rounded-full bg-primary px-2 py-1 text-xs font-semibold uppercase text-white max-[400px]:hidden lg:text-sm 2xl:text-base">{{ auth()->user()->unit->name }}</p>
                        <div>
                            <button type="button" title="Tombol profil" class="flex rounded-full fill-primary text-sm focus:ring-4 focus:ring-gray-300" aria-expanded="false" data-dropdown-toggle="user-menu">
                                <span class="sr-only">Open user menu</span>
                                <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" class="h-6 w-6 sm:h-8 sm:w-8">
                                    <path d="m12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm-4,21.164v-2.164c0-.552.449-1,1-1h6c.551,0,1,.448,1,1v2.164c-1.226.537-2.578.836-4,.836s-2.774-.299-4-.836Zm10-1.169v-.995c0-1.654-1.346-3-3-3h-6c-1.654,0-3,1.346-3,3v.995c-2.427-1.826-4-4.73-4-7.995C2,6.486,6.486,2,12,2s10,4.486,10,10c0,3.264-1.573,6.169-4,7.995Zm-6-13.995c-2.206,0-4,1.794-4,4s1.794,4,4,4,4-1.794,4-4-1.794-4-4-4Zm0,6c-1.103,0-2-.897-2-2s.897-2,2-2,2,.897,2,2-.897,2-2,2Z" />
                                </svg>

                            </button>
                        </div>
                        <div class="z-40 my-4 hidden list-none divide-y divide-gray-100 rounded bg-white text-base shadow shadow-primary" id="user-menu">
                            <div class="cursor-default px-4 py-3 text-sm text-primary *:max-w-40 *:overflow-hidden *:truncate sm:*:max-w-60 2xl:*:max-w-96" role="none">
                                <p role="none" title="Neil Sims">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="font-semibold" role="none" title="neil.sims@flowbite.com">
                                    {{ auth()->user()->email }}
                                </p>
                                <p class="rounded-xl bg-primary px-1 italic text-white" role="none" title="Teknik Informatika">
                                    {{ auth()->user()->unit->name }}
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

    <x-Partials.Info.Time />

    <div class="p-4 sm:ml-72">
        <div class="mt-14 rounded-lg border-2 border-dashed border-primary p-4">
            <div class="flex flex-col gap-5 2xl:mx-auto 2xl:min-w-[75vw] 2xl:max-w-max">

                {{ $slot }}

            </div>
        </div>

        <footer class="pt-2 text-center max-md:text-xs">
            <p>Copyright &copy; {{ \Carbon\Carbon::now()->format('Y') }} Fakultas Teknologi Industri - ITERA</p>
        </footer>
    </div>

    @pushIf($errors->has('error'), 'notification')
    <x-partials.toast.default id="login-error" message="{{ $errors->get('error')[0] ?? 'Gagal' }}" withTimeout danger />
    @endPushIf
@endsection
