<?php

namespace App\Http\Controllers\SuperAdmin\SasaranStrategis;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranStrategis;

class DeleteSasaranStrategisSuperAdminController extends Controller
{
    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function action(string $id): RedirectResponse
    {
        $ss = SasaranStrategis::currentOrFail($id);

        DB::beginTransaction();

        try {
            $ss->deleteOrTrashed();

            DB::commit();

            return _ControllerHelpers::Back();
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
