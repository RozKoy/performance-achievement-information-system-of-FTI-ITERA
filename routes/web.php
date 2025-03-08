<?php

// Authentication
use App\Http\Controllers\Authentication\ChangePasswordController;
use App\Http\Controllers\Authentication\ForgetPasswordController;
use App\Http\Controllers\Authentication\LogoutController;
use App\Http\Controllers\Authentication\LoginController;

// Rencana Strategis - Admin
use App\Http\Controllers\Admin\RencanaStrategis\HistoryRencanaStrategisAdminController;
use App\Http\Controllers\Admin\RencanaStrategis\HomeRencanaStrategisAdminController;
use App\Http\Controllers\Admin\RencanaStrategis\AddRencanaStrategisAdminController;
// Indikator Kinerja Utama - Admin
use App\Http\Controllers\Admin\IndikatorKinerjaUtama\UpdateUnitStatusIndikatorKinerjaUtamaAdminController;
use App\Http\Controllers\Admin\IndikatorKinerjaUtama\ImportTableDataIndikatorKinerjaUtamaAdminController;
use App\Http\Controllers\Admin\IndikatorKinerjaUtama\AddSingleDataIndikatorKinerjaUtamaAdminController;
use App\Http\Controllers\Admin\IndikatorKinerjaUtama\AddTableDataIndikatorKinerjaUtamaAdminController;
use App\Http\Controllers\Admin\IndikatorKinerjaUtama\HistoryIndikatorKinerjaUtamaAdminController;
use App\Http\Controllers\Admin\IndikatorKinerjaUtama\DetailIndikatorKinerjaUtamaAdminController;
use App\Http\Controllers\Admin\IndikatorKinerjaUtama\HomeIndikatorKinerjaUtamaAdminController;
// User - Admin
use App\Http\Controllers\Admin\User\CreateUserAdminController;
use App\Http\Controllers\Admin\User\DeleteUserAdminController;
use App\Http\Controllers\Admin\User\UpdateUserAdminController;
use App\Http\Controllers\Admin\User\HomeUserAdminController;

// Dashboard - Super Admin
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\Dashboard\HomeDashboardSuperAdminController;
use App\Http\Controllers\SuperAdmin\Dashboard\IndikatorKinerjaUtamaDashboardSuperAdminController;
use App\Http\Controllers\SuperAdmin\Dashboard\RencanaStrategisDashboardSuperAdminController;
// Rencana Strategis - Super Admin
use App\Http\Controllers\SuperAdmin\RencanaStrategis\ExcelExportRencanaStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\RencanaStrategis\EvaluationRencanaStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\RencanaStrategis\FormatRencanaStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\RencanaStrategis\DetailRencanaStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\RencanaStrategis\StatusRencanaStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\RencanaStrategis\TargetRencanaStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\RencanaStrategis\HomeRencanaStrategisSuperAdminController;
// Indikator Kinerja Utama - Super Admin
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\ExcelExportIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\EvaluationIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\ValidationIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\DetailIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\StatusIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\TargetIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\FormatIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\HomeIndikatorKinerjaUtamaSuperAdminController;
// Sasaran Strategis - Rencana Strategis - Super Admin
use App\Http\Controllers\SuperAdmin\SasaranStrategis\CreateSasaranStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\SasaranStrategis\DeleteSasaranStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\SasaranStrategis\UpdateSasaranStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\SasaranStrategis\HomeSasaranStrategisSuperAdminController;
// Kegiatan - Rencana Strategis - Super Admin
use App\Http\Controllers\SuperAdmin\Kegiatan\CreateKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\Kegiatan\DeleteKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\Kegiatan\UpdateKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\Kegiatan\HomeKegiatanSuperAdminController;
// Indikator Kinerja - Rencana Strategis - Super Admin
use App\Http\Controllers\SuperAdmin\IndikatorKinerja\CreateIndikatorKinerjaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerja\DeleteIndikatorKinerjaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerja\UpdateIndikatorKinerjaSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerja\HomeIndikatorKinerjaSuperAdminController;
// Sasaran Kegiatan - Indikator Kinerja Utama - Super Admin
use App\Http\Controllers\SuperAdmin\SasaranKegiatan\CreateSasaranKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\SasaranKegiatan\DeleteSasaranKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\SasaranKegiatan\UpdateSasaranKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\SasaranKegiatan\HomeSasaranKegiatanSuperAdminController;
// Indikator Kinerja Kegiatan - Indikator Kinerja Utama - Super Admin
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaKegiatan\CreateIndikatorKinerjaKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaKegiatan\DeleteIndikatorKinerjaKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaKegiatan\UpdateIndikatorKinerjaKegiatanSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaKegiatan\HomeIndikatorKinerjaKegiatanSuperAdminController;
// Program Strategis - Indikator Kinerja Utama - Super Admin
use App\Http\Controllers\SuperAdmin\ProgramStrategis\CreateProgramStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\ProgramStrategis\DeleteProgramStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\ProgramStrategis\UpdateProgramStrategisSuperAdminController;
use App\Http\Controllers\SuperAdmin\ProgramStrategis\HomeProgramStrategisSuperAdminController;
// Indikator Kinerja Program - Indikator Kinerja Utama - Super Admin
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaProgram\CreateIndikatorKinerjaProgramSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaProgram\DeleteIndikatorKinerjaProgramSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaProgram\UpdateIndikatorKinerjaProgramSuperAdminController;
use App\Http\Controllers\SuperAdmin\IndikatorKinerjaProgram\HomeIndikatorKinerjaProgramSuperAdminController;
// User - Super Admin
use App\Http\Controllers\SuperAdmin\User\CreateUserSuperAdminController;
use App\Http\Controllers\SuperAdmin\User\DeleteUserSuperAdminController;
use App\Http\Controllers\SuperAdmin\User\UpdateUserSuperAdminController;
use App\Http\Controllers\SuperAdmin\User\HomeUserSuperAdminController;
// Unit - Super Admin
use App\Http\Controllers\SuperAdmin\Unit\CreateUnitSuperAdminController;
use App\Http\Controllers\SuperAdmin\Unit\DeleteUnitSuperAdminController;
use App\Http\Controllers\SuperAdmin\Unit\UpdateUnitSuperAdminController;
use App\Http\Controllers\SuperAdmin\Unit\HomeUnitSuperAdminController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*
| -----------------------------------------------------------------
| AUTHENTICATION
| -----------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('masuk', [LoginController::class, 'view'])->name('login');
    Route::post('masuk', [LoginController::class, 'action']);

    Route::get('lupa-kata-sandi', [ForgetPasswordController::class, 'view'])->name('forget-password');
    Route::post('lupa-kata-sandi', [ForgetPasswordController::class, 'action']);

    Route::get('{token}/ubah-kata-sandi', [ChangePasswordController::class, 'view'])->name('change-password');
    Route::post('{token}/ubah-kata-sandi', [ChangePasswordController::class, 'action']);
});

Route::get('keluar', [LogoutController::class, 'action'])->middleware('auth')->name('logout');


/*
| -----------------------------------------------------------------
| SUPER ADMIN
| -----------------------------------------------------------------
*/

Route::prefix('super-admin')->middleware('superadmin')->group(function (): void {
    Route::prefix('beranda')->group(function (): void {
        Route::get('/', [HomeDashboardSuperAdminController::class, 'view'])->name('super-admin-dashboard');
        Route::get('iku/{year}', [DashboardController::class, 'iku'])->name('super-admin-dashboard-iku');
        Route::get('iku/{year}/export', [IndikatorKinerjaUtamaDashboardSuperAdminController::class, 'excelExport'])->name('super-admin-dashboard-iku-export');
        Route::get('rs/{year}/export', [RencanaStrategisDashboardSuperAdminController::class, 'excelExport'])->name('super-admin-dashboard-rs-export');
    });

    Route::prefix('capaian-kinerja')->group(function (): void {
        Route::get('/', function () {
            return redirect()->route('super-admin-achievement-rs');
        })->name('super-admin-achievement');

        Route::prefix('rencana-strategis')->group(function (): void {
            Route::get('/', [HomeRencanaStrategisSuperAdminController::class, 'view'])->name('super-admin-achievement-rs');
            Route::get('{ik}/detail', [DetailRencanaStrategisSuperAdminController::class, 'view'])->name('super-admin-achievement-rs-detail');
            Route::get('{year}/target', [TargetRencanaStrategisSuperAdminController::class, 'view'])->name('super-admin-achievement-rs-target');
            Route::get('export', [ExcelExportRencanaStrategisSuperAdminController::class, 'action'])->name('super-admin-achievement-rs-export');

            Route::middleware('editor')->group(function (): void {
                Route::post('{year}/target', [TargetRencanaStrategisSuperAdminController::class, 'action'])->name('super-admin-achievement-rs-target-add');
                Route::get('{period}/status', [StatusRencanaStrategisSuperAdminController::class, 'action'])->name('super-admin-achievement-rs-status');
                Route::post('{ik}/evaluation', [EvaluationRencanaStrategisSuperAdminController::class, 'action'])->name('super-admin-achievement-rs-evaluation');
            });
        });

        Route::prefix('indikator-kinerja-utama')->group(function (): void {
            Route::get('/', [HomeIndikatorKinerjaUtamaSuperAdminController::class, 'view'])->name('super-admin-achievement-iku');
            Route::get('{ikp}/detail', [DetailIndikatorKinerjaUtamaSuperAdminController::class, 'view'])->name('super-admin-achievement-iku-detail');
            Route::get('{year}/target', [TargetIndikatorKinerjaUtamaSuperAdminController::class, 'view'])->name('super-admin-achievement-iku-target');
            Route::get('export', [ExcelExportIndikatorKinerjaUtamaSuperAdminController::class, 'action'])->name('super-admin-achievement-iku-export');
            Route::get('{ikp}/export', [ExcelExportIndikatorKinerjaUtamaSuperAdminController::class, 'detailAction'])->name('super-admin-achievement-iku-detail-export');

            Route::middleware('editor')->group(function (): void {
                Route::post('{year}/target', [TargetIndikatorKinerjaUtamaSuperAdminController::class, 'action'])->name('super-admin-achievement-iku-target-add');
                Route::get('{period}/status', [StatusIndikatorKinerjaUtamaSuperAdminController::class, 'action'])->name('super-admin-achievement-iku-status');
                Route::post('{period}/deadline', [StatusIndikatorKinerjaUtamaSuperAdminController::class, 'setDeadline'])->name('super-admin-achievement-iku-deadline');
                Route::post('{ikp}/evaluation', [EvaluationIndikatorKinerjaUtamaSuperAdminController::class, 'action'])->name('super-admin-achievement-iku-evaluation');
                Route::post('{ikp}/validation', [ValidationIndikatorKinerjaUtamaSuperAdminController::class, 'action'])->name('super-admin-achievement-iku-detail-validation');
            });
        });
    });

    Route::prefix('rencana-strategis')->group(function (): void {
        Route::get('/', function () {
            return redirect()->route('super-admin-rs-ss');
        })->name('super-admin-rs');

        Route::middleware('editor')->group(function (): void {
            Route::post('duplicate', [FormatRencanaStrategisSuperAdminController::class, 'duplicate'])->name('super-admin-rs-duplicate');
            Route::post('import-excel', [FormatRencanaStrategisSuperAdminController::class, 'import'])->name('super-admin-rs-import');
        });

        Route::prefix('sasaran-strategis')->group(function (): void {
            Route::get('/', [HomeSasaranStrategisSuperAdminController::class, 'view'])->name('super-admin-rs-ss');

            Route::middleware('editor')->group(function (): void {
                Route::get('tambah', [CreateSasaranStrategisSuperAdminController::class, 'view'])->name('super-admin-rs-ss-add');
                Route::post('tambah', [CreateSasaranStrategisSuperAdminController::class, 'action']);

                Route::get('{ss}/ubah', [UpdateSasaranStrategisSuperAdminController::class, 'view'])->name('super-admin-rs-ss-edit');
                Route::put('{ss}/ubah', [UpdateSasaranStrategisSuperAdminController::class, 'action']);

                Route::get('{id}/hapus', [DeleteSasaranStrategisSuperAdminController::class, 'action']);
            });
        });

        Route::prefix('{ss}/kegiatan')->group(function ($route): void {
            Route::get('/', [HomeKegiatanSuperAdminController::class, 'view'])->name('super-admin-rs-k');

            Route::middleware('editor')->group(function (): void {
                Route::get('tambah', [CreateKegiatanSuperAdminController::class, 'view'])->name('super-admin-rs-k-add');
                Route::post('tambah', [CreateKegiatanSuperAdminController::class, 'action']);

                Route::get('{k}/ubah', [UpdateKegiatanSuperAdminController::class, 'view'])->name('super-admin-rs-k-edit');
                Route::put('{k}/ubah', [UpdateKegiatanSuperAdminController::class, 'action']);

                Route::get('{k}/hapus', [DeleteKegiatanSuperAdminController::class, 'action']);
            });
        });

        Route::prefix('{ss}/{k}/indikator-kinerja')->group(function ($route): void {
            Route::get('/', [HomeIndikatorKinerjaSuperAdminController::class, 'view'])->name('super-admin-rs-ik');

            Route::middleware('editor')->group(function (): void {
                Route::get('tambah', [CreateIndikatorKinerjaSuperAdminController::class, 'view'])->name('super-admin-rs-ik-add');
                Route::post('tambah', [CreateIndikatorKinerjaSuperAdminController::class, 'action']);

                Route::get('{ik}/ubah', [UpdateIndikatorKinerjaSuperAdminController::class, 'view'])->name('super-admin-rs-ik-edit');
                Route::put('{ik}/ubah', [UpdateIndikatorKinerjaSuperAdminController::class, 'action']);

                Route::get('{ik}/status', [UpdateIndikatorKinerjaSuperAdminController::class, 'statusToggle'])->name('super-admin-rs-ik-status');

                Route::get('{ik}/hapus', [DeleteIndikatorKinerjaSuperAdminController::class, 'action']);
            });
        });
    });


    Route::prefix('indikator-kinerja-utama')->group(function (): void {
        Route::get('/', function () {
            return redirect()->route('super-admin-iku-sk');
        })->name('super-admin-iku');

        Route::middleware('editor')->group(function (): void {
            Route::post('duplicate', [FormatIndikatorKinerjaUtamaSuperAdminController::class, 'duplicate'])->name('super-admin-iku-duplicate');
            Route::post('import', [FormatIndikatorKinerjaUtamaSuperAdminController::class, 'import'])->name('super-admin-iku-import');
        });

        Route::prefix('sasaran-kegiatan')->group(function (): void {
            Route::get('/', [HomeSasaranKegiatanSuperAdminController::class, 'view'])->name('super-admin-iku-sk');

            Route::middleware('editor')->group(function (): void {
                Route::get('tambah', [CreateSasaranKegiatanSuperAdminController::class, 'view'])->name('super-admin-iku-sk-add');
                Route::post('tambah', [CreateSasaranKegiatanSuperAdminController::class, 'action']);

                Route::get('{sk}/ubah', [UpdateSasaranKegiatanSuperAdminController::class, 'view'])->name('super-admin-iku-sk-edit');
                Route::put('{sk}/ubah', [UpdateSasaranKegiatanSuperAdminController::class, 'action']);

                Route::get('{sk}/hapus', [DeleteSasaranKegiatanSuperAdminController::class, 'action']);
            });
        });

        Route::prefix('{sk}/indikator-kinerja-kegiatan')->group(function ($route): void {
            Route::get('/', [HomeIndikatorKinerjaKegiatanSuperAdminController::class, 'view'])->name('super-admin-iku-ikk');

            Route::middleware('editor')->group(function (): void {
                Route::get('tambah', [CreateIndikatorKinerjaKegiatanSuperAdminController::class, 'view'])->name('super-admin-iku-ikk-add');
                Route::post('tambah', [CreateIndikatorKinerjaKegiatanSuperAdminController::class, 'action']);

                Route::get('{ikk}/ubah', [UpdateIndikatorKinerjaKegiatanSuperAdminController::class, 'view'])->name('super-admin-iku-ikk-edit');
                Route::put('{ikk}/ubah', [UpdateIndikatorKinerjaKegiatanSuperAdminController::class, 'action']);

                Route::get('{ikk}/hapus', [DeleteIndikatorKinerjaKegiatanSuperAdminController::class, 'action']);
            });
        });

        Route::prefix('{sk}/{ikk}/program-strategis')->group(function ($route): void {
            Route::get('/', [HomeProgramStrategisSuperAdminController::class, 'view'])->name('super-admin-iku-ps');

            Route::middleware('editor')->group(function (): void {
                Route::get('tambah', [CreateProgramStrategisSuperAdminController::class, 'view'])->name('super-admin-iku-ps-add');
                Route::post('tambah', [CreateProgramStrategisSuperAdminController::class, 'action']);

                Route::get('{ps}/ubah', [UpdateProgramStrategisSuperAdminController::class, 'view'])->name('super-admin-iku-ps-edit');
                Route::put('{ps}/ubah', [UpdateProgramStrategisSuperAdminController::class, 'action']);

                Route::get('{ps}/hapus', [DeleteProgramStrategisSuperAdminController::class, 'action']);
            });
        });

        Route::prefix('{sk}/{ikk}/{ps}/indikator-kinerja-program')->group(function ($route): void {
            Route::get('/', [HomeIndikatorKinerjaProgramSuperAdminController::class, 'view'])->name('super-admin-iku-ikp');

            Route::middleware('editor')->group(function (): void {
                Route::get('tambah', [CreateIndikatorKinerjaProgramSuperAdminController::class, 'view'])->name('super-admin-iku-ikp-add');
                Route::post('tambah', [CreateIndikatorKinerjaProgramSuperAdminController::class, 'action']);

                Route::get('{ikp}/ubah', [UpdateIndikatorKinerjaProgramSuperAdminController::class, 'view'])->name('super-admin-iku-ikp-edit');
                Route::put('{ikp}/ubah', [UpdateIndikatorKinerjaProgramSuperAdminController::class, 'action']);

                Route::get('{ikp}/status', [UpdateIndikatorKinerjaProgramSuperAdminController::class, 'statusToggle'])->name('super-admin-iku-ikp-status');

                Route::get('{ikp}/hapus', [DeleteIndikatorKinerjaProgramSuperAdminController::class, 'action']);
            });
        });
    });


    Route::prefix('pengguna')->middleware('editor')->group(function (): void {
        Route::get('/', [HomeUserSuperAdminController::class, 'view'])->name('super-admin-users');

        Route::get('tambah', [CreateUserSuperAdminController::class, 'view'])->name('super-admin-users-add');
        Route::post('tambah', [CreateUserSuperAdminController::class, 'action']);

        Route::get('{user}/ubah', [UpdateUserSuperAdminController::class, 'view'])->name('super-admin-users-edit');
        Route::put('{user}/ubah', [UpdateUserSuperAdminController::class, 'action']);

        Route::get('{user}/hapus', [DeleteUserSuperAdminController::class, 'action']);
    });


    Route::prefix('unit')->middleware('editor')->group(function (): void {
        Route::get('/', [HomeUnitSuperAdminController::class, 'view'])->name('super-admin-unit');

        Route::get('tambah', [CreateUnitSuperAdminController::class, 'view'])->name('super-admin-unit-add');
        Route::post('tambah', [CreateUnitSuperAdminController::class, 'action']);

        Route::get('{unit}/ubah', [UpdateUnitSuperAdminController::class, 'view'])->name('super-admin-unit-edit');
        Route::put('{unit}/ubah', [UpdateUnitSuperAdminController::class, 'action']);

        Route::get('{unit}/hapus', [DeleteUnitSuperAdminController::class, 'action']);
    });
});


/*
| -----------------------------------------------------------------
| ADMIN
| -----------------------------------------------------------------
*/

Route::prefix('/')->middleware('admin')->group(function (): void {
    Route::get('/', function () {
        return redirect()->route('admin-rs');
    })->name('admin-dashboard');

    Route::prefix('rencana-strategis')->group(function (): void {
        Route::get('/', [HomeRencanaStrategisAdminController::class, 'view'])->name('admin-rs');

        Route::post('{period}/{ik}', [AddRencanaStrategisAdminController::class, 'action'])->middleware('editor')->name('admin-rs-add');
    });

    Route::prefix('indikator-kinerja-utama')->group(function (): void {
        Route::get('/', [HomeIndikatorKinerjaUtamaAdminController::class, 'view'])->name('admin-iku');
        Route::get('{ikp}/detail', [DetailIndikatorKinerjaUtamaAdminController::class, 'view'])->name('admin-iku-detail');

        Route::put('{period}/{ikp}/data-table', [AddTableDataIndikatorKinerjaUtamaAdminController::class, 'addBulkData'])->middleware('editor')->name('admin-iku-data-table-bulk');
        Route::post('{period}/{ikp}/data-table', [AddTableDataIndikatorKinerjaUtamaAdminController::class, 'addData'])->middleware('editor')->name('admin-iku-data-table');

        Route::get('{ikp}/template', [ImportTableDataIndikatorKinerjaUtamaAdminController::class, 'template'])->middleware('editor')->name('admin-iku-template-download');
        Route::post('{period}/{ikp}/import', [ImportTableDataIndikatorKinerjaUtamaAdminController::class, 'import'])->middleware('editor')->name('admin-iku-data-table-import');

        Route::post('{period}/{ikp}/data-single', [AddSingleDataIndikatorKinerjaUtamaAdminController::class, 'action'])->middleware('editor')->name('admin-iku-data-single');

        Route::post('{ikp}/year-unit-status', [UpdateUnitStatusIndikatorKinerjaUtamaAdminController::class, 'yearStatusToggle'])->middleware('editor')->name('admin-iku-year-unit-status');
        Route::post('{period}/{ikp}/unit-status', [UpdateUnitStatusIndikatorKinerjaUtamaAdminController::class, 'statusToggle'])->middleware('editor')->name('admin-iku-unit-status');
    });

    Route::prefix('riwayat')->group(function (): void {
        Route::get('/', function () {
            return redirect()->route('admin-history-rs');
        })->name('admin-history');

        Route::prefix('rencana-strategis')->group(function (): void {
            Route::get('/', [HistoryRencanaStrategisAdminController::class, 'view'])->name('admin-history-rs');
        });

        Route::prefix('indikator-kinerja-utama')->group(function (): void {
            Route::get('/', [HistoryIndikatorKinerjaUtamaAdminController::class, 'view'])->name('admin-history-iku');
            Route::get('{ikp}/detail', [HistoryIndikatorKinerjaUtamaAdminController::class, 'detailView'])->name('admin-history-iku-detail');
        });
    });

    Route::prefix('pengguna')->middleware('editor')->group(function (): void {
        Route::get('/', [HomeUserAdminController::class, 'view'])->name('admin-users');

        Route::get('tambah', [CreateUserAdminController::class, 'view'])->name('admin-users-add');
        Route::post('tambah', [CreateUserAdminController::class, 'action']);

        Route::get('{id}/ubah', [UpdateUserAdminController::class, 'view'])->name('admin-users-edit');
        Route::put('{id}/ubah', [UpdateUserAdminController::class, 'action']);

        Route::get('{user}/hapus', [DeleteUserAdminController::class, 'action']);
    });
});
