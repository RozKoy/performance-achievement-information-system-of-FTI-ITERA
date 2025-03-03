<?php

namespace App\Http\Controllers\Admin\IndikatorKinerjaUtama;

use App\Http\Requests\IndikatorKinerjaUtama\ImportRequest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Imports\IKPTableDataSheets;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Exports\IKUExport;

class ImportTableDataIndikatorKinerjaUtamaAdminController extends Controller
{
    /**
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return BinaryFileResponse
     */
    public function template(IndikatorKinerjaProgram $ikp): BinaryFileResponse
    {
        if ($ikp->status !== 'aktif' || $ikp->mode !== 'table') {
            abort(404);
        }

        $collection = collect();

        $columns = $ikp->columns()
            ->orderBy('number')
            ->pluck('name');

        $collection->add($columns->toArray());
        $collection->add([]);
        $collection->add(['Mulai isi data dari baris ke-2, jangan hapus baris 1']);

        return Excel::download(
            new IKUExport($collection->toArray()),
            Str::replace(['/', '\\'], '-', (string) $ikp->name) . ' - template.xlsx'
        );
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\ImportRequest $request
     * @param string $period
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function import(ImportRequest $request, string $period, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        if ($ikp->status !== 'aktif' || $ikp->mode !== 'table') {
            abort(404);
        }

        $user = auth()->user();

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $currentDate = Carbon::now();

        $periodInstance = $year->periods()
            ->whereDate('deadline', '>=', $currentDate)
            ->where('period', $period)
            ->where('status', true)
            ->firstOrFail();

        Excel::import(
            new IKPTableDataSheets($ikp, $periodInstance->id, $user->unit->id),
            $request->file('file')
        );

        return _ControllerHelpers::Back()->with('success', 'Berhasil import data tabel (*Silahkan periksa kembali)');
    }
}
