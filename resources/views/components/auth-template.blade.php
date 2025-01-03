@extends('template')

@section('content')
    <div class="{{ isset($reverse) ? 'flex-row-reverse' : 'flex-row' }} flex min-h-screen w-screen bg-primary">
        <div class="relative flex min-h-full w-full items-center justify-center px-3 py-5">
            <div class="absolute z-0 h-full w-full overflow-hidden">
                <img src="{{ asset('storage/assets/authentication/line.svg') }}" alt="" class="absolute -left-8 -top-8 z-0">
                <img src="{{ asset('storage/assets/authentication/line.svg') }}" alt="" class="absolute -bottom-8 -right-8 z-0 rotate-180">
            </div>

            <div class="min-w-64 relative flex w-3/4 max-w-[750px] flex-col items-center rounded-lg bg-white p-10 max-2xl:w-[30rem] max-sm:w-10/12 max-sm:p-7 max-sm:text-sm max-[320px]:text-xs">
                <img src="{{ url(asset('storage/assets/fti-banner.svg')) }}" alt="fti-banner" class="mb-5 w-full">
                <h2 class="text-xl font-semibold uppercase max-sm:text-base" title="{{ $title }}">{{ $title }}</h2>
                <p class="text-slate-500" title="Selamat Datang Di Sistem Informasi Capaian Kinerja FTI ITERA">Selamat Datang Di SICAKI</p>

                <form method="POST" class="mt-4 flex w-full flex-col gap-2">
                    @csrf

                    {{ $slot }}

                </form>
            </div>
        </div>

        <div class="relative flex min-h-full w-full items-center justify-center bg-white px-3 py-5 max-lg:hidden">
            <div class="flex flex-col gap-7">
                <img src="{{ url(asset('storage/assets/fti-logo.png')) }}" alt="fti-banner" class="w-96">
                <h1 class="rounded-xl bg-primary py-1 text-center text-xl font-semibold text-white">
                    <span class="text-8xl">SICAKI</span><br> Sistem Informasi Capaian Kinerja
                </h1>
            </div>
        </div>
    </div>
@endsection
