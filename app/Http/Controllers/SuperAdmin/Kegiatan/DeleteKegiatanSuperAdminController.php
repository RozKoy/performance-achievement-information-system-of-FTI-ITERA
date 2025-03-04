<?php

namespace App\Http\Controllers\SuperAdmin\Kegiatan;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranStrategis;
use App\Models\Kegiatan;

class DeleteKegiatanSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @return RedirectResponse
     */
    public function action(SasaranStrategis $ss, Kegiatan $k): RedirectResponse
    {
        if ($ss->id !== $k->sasaranStrategis->id) {
            abort(404);
        }

        $ss = SasaranStrategis::currentOrFail($ss->id);

        DB::beginTransaction();

        try {
            $k->deleteOrTrashed();

            DB::commit();

            return _ControllerHelpers::Back();
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
