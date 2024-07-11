@php
    $rs = isset($admin) ? 'admin-history-rs' : 'super-admin-achievement-rs';
    $iku = isset($admin) ? 'admin-history-iku' : 'super-admin-achievement-iku';
@endphp

<div class="*:flex-1 *:rounded-lg *:p-1 *:border-2 *:border-transparent *:bg-primary/80 flex gap-2.5 text-center text-white max-md:text-sm max-sm:text-xs">
    <a href="{{ url(route($rs)) }}" title="Halaman Rencana Strategis" class="{{ request()->routeIs($rs) ? 'font-semibold outline outline-2 outline-offset-2 outline-primary/80' : '' }} hover:border-primary/80 hover:bg-white hover:text-primary">Rencana Strategis</a>
    <a href="{{ url(route($iku)) }}" title="Halaman Indikator Kinerja Utama" class="{{ request()->routeIs($iku) ? 'font-semibold outline outline-2 outline-offset-2 outline-primary/80' : '' }} hover:border-primary/80 hover:bg-white hover:text-primary">Indikator Kinerja Utama</a>
</div>
