<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaKegiatan;

use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranKegiatan;

class DeleteIndikatorKinerjaKegiatanSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return RedirectResponse
     */
    public function action(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): RedirectResponse
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id) {
            abort(404);
        }

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        DB::beginTransaction();

        try {
            $ikk->deleteOrTrashed();

            DB::commit();

            return _ControllerHelpers::Back()->with('success', 'Berhasil menghapus indikator kinerja kegiatan');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
