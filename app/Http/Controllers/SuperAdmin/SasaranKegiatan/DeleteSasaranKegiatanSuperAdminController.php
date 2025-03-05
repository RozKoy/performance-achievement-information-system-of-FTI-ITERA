<?php

namespace App\Http\Controllers\SuperAdmin\SasaranKegiatan;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranKegiatan;

class DeleteSasaranKegiatanSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranKegiatan $sk
     * @return RedirectResponse
     */
    public function action(SasaranKegiatan $sk): RedirectResponse
    {
        $sk = SasaranKegiatan::currentOrFail($sk->id);

        DB::beginTransaction();

        try {
            $sk->deleteOrTrashed();

            DB::commit();

            return _ControllerHelpers::Back()->with('success', 'Berhasil menghapus sasaran kegiatan');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
