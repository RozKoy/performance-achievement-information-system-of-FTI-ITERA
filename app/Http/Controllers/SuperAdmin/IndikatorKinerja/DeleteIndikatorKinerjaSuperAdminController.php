<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerja;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\IndikatorKinerja;
use App\Models\SasaranStrategis;
use App\Models\Kegiatan;

class DeleteIndikatorKinerjaSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @param \App\Models\IndikatorKinerja $ik
     * @return RedirectResponse
     */
    public function action(SasaranStrategis $ss, Kegiatan $k, IndikatorKinerja $ik): RedirectResponse
    {
        if ($ss->id !== $k->sasaranStrategis->id && $k->id !== $ik->kegiatan->id) {
            abort(404);
        }

        $ss = SasaranStrategis::currentOrFail($k->sasaranStrategis->id);

        DB::beginTransaction();

        try {
            $ik->deleteOrTrashed();

            DB::commit();

            return _ControllerHelpers::Back()->with('success', 'Berhasil menghapus indikator kinerja');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
