<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaProgram;

use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;

class DeleteIndikatorKinerjaProgramSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function action(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id || $ikk->id !== $ps->indikatorKinerjaKegiatan->id || $ps->id !== $ikp->programStrategis->id) {
            abort(404);
        }

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        DB::beginTransaction();

        try {
            $ikp->deleteOrTrashed();

            DB::commit();

            return _ControllerHelpers::Back()->with('success', 'Berhasil menghapus indikator kinerja program');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
