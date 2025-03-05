<?php

namespace App\Http\Controllers\SuperAdmin\ProgramStrategis;

use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;

class DeleteProgramStrategisSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @return RedirectResponse
     */
    public function action(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps): RedirectResponse
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id || $ikk->id !== $ps->indikatorKinerjaKegiatan->id) {
            abort(404);
        }

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        DB::beginTransaction();

        try {
            $ps->deleteOrTrashed();

            DB::commit();

            return _ControllerHelpers::Back()->with('success', 'Berhasil menghapus program strategis');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
