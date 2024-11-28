<?php

use App\Http\Controllers\IndikatorKinerjaKegiatanController;
use App\Http\Controllers\IndikatorKinerjaProgramController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\ProgramStrategisController;
use App\Http\Controllers\SasaranStrategisController;
use App\Http\Controllers\SasaranKegiatanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IKUController;
use App\Http\Controllers\RSController;
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

Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/masuk', 'loginView')->name('login');
        Route::post('/masuk', 'login');

        Route::get('/lupa-kata-sandi', 'forgetPasswordView')->name('forget-password');
        Route::post('/lupa-kata-sandi', 'forgetPassword');

        Route::get('/{token}/ubah-kata-sandi', 'changePasswordView')->name('change-password');
        Route::post('/{token}/ubah-kata-sandi', 'changePassword');
    });

    Route::get('/keluar', 'logout')->middleware('auth')->name('logout');
});



/*
| -----------------------------------------------------------------
| SUPER ADMIN
| -----------------------------------------------------------------
*/

Route::prefix('/super-admin')->middleware('superadmin')->group(function () {
    Route::prefix('/beranda')->controller(DashboardController::class)->group(function () {
        Route::get('/', 'home')->name('super-admin-dashboard');
        Route::get('/iku/{year}', 'iku')->name('super-admin-dashboard-iku');
        Route::get('/iku/{year}/export', 'exportIKU')->name('super-admin-dashboard-iku-export');
        Route::get('/rs/{year}/export', 'exportRS')->name('super-admin-dashboard-rs-export');
    });

    Route::prefix('/capaian-kinerja')->group(function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-achievement-rs');
        })->name('super-admin-achievement');

        Route::prefix('/rencana-strategis')->controller(RSController::class)->group(function () {
            Route::get('/', 'homeView')->name('super-admin-achievement-rs');
            Route::get('/{ik}/detail', 'detailView')->name('super-admin-achievement-rs-detail');
            Route::get('/{year}/target', 'targetView')->name('super-admin-achievement-rs-target');
            Route::get('/export', 'exportRS')->name('super-admin-achievement-rs-export');

            Route::middleware('editor')->group(function () {
                Route::post('/{year}/target', 'addTarget')->name('super-admin-achievement-rs-target-add');
                Route::get('/{period}/status', 'statusToggle')->name('super-admin-achievement-rs-status');
                Route::post('/{ik}/evaluation', 'addEvaluation')->name('super-admin-achievement-rs-evaluation');
            });
        });

        Route::prefix('/indikator-kinerja-utama')->controller(IKUController::class)->group(function () {
            Route::get('/', 'homeView')->name('super-admin-achievement-iku');
            Route::get('/{ikp}/detail', 'detailView')->name('super-admin-achievement-iku-detail');
            Route::get('/{year}/target', 'targetView')->name('super-admin-achievement-iku-target');
            Route::get('/export', 'exportIKU')->name('super-admin-achievement-iku-export');
            Route::get('/{ikp}/export', 'detailExportIKU')->name('super-admin-achievement-iku-detail-export');

            Route::middleware('editor')->group(function () {
                Route::post('/{year}/target', 'addTarget')->name('super-admin-achievement-iku-target-add');
                Route::get('/{period}/status', 'statusToggle')->name('super-admin-achievement-iku-status');
                Route::post('/{ikp}/evaluation', 'addEvaluation')->name('super-admin-achievement-iku-evaluation');
            });
        });
    });

    Route::prefix('/rencana-strategis')->group(function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-rs-ss');
        })->name('super-admin-rs');

        Route::post('/import-excel', [RSController::class, 'RSImport'])->middleware('editor')->name('super-admin-rs-import');

        Route::prefix('/sasaran-strategis')->controller(SasaranStrategisController::class)->group(function () {
            Route::get('/', 'homeView')->name('super-admin-rs-ss');

            Route::middleware('editor')->group(function () {
                Route::get('/tambah', 'addView')->name('super-admin-rs-ss-add');
                Route::post('/tambah', 'add');

                Route::get('/{ss}/ubah', 'editView')->name('super-admin-rs-ss-edit');
                Route::put('/{ss}/ubah', 'edit');

                Route::get('/{id}/hapus', 'delete');
            });
        });

        Route::prefix('/{ss}/kegiatan')->controller(KegiatanController::class)->group(function ($route) {
            Route::get('/', 'homeView')->name('super-admin-rs-k');

            Route::middleware('editor')->group(function () {
                Route::get('/tambah', 'addView')->name('super-admin-rs-k-add');
                Route::post('/tambah', 'add');

                Route::get('/{k}/ubah', 'editView')->name('super-admin-rs-k-edit');
                Route::put('/{k}/ubah', 'edit');

                Route::get('/{k}/hapus', 'delete');
            });
        });

        Route::prefix('/{ss}/{k}/indikator-kinerja')->controller(IndikatorKinerjaController::class)->group(function ($route) {
            Route::get('/', 'homeView')->name('super-admin-rs-ik');

            Route::middleware('editor')->group(function () {
                Route::get('/tambah', 'addView')->name('super-admin-rs-ik-add');
                Route::post('/tambah', 'add');

                Route::get('/{ik}/ubah', 'editView')->name('super-admin-rs-ik-edit');
                Route::put('/{ik}/ubah', 'edit');

                Route::get('/{ik}/status', 'statusToggle')->name('super-admin-rs-ik-status');

                Route::get('/{ik}/hapus', 'delete');
            });
        });
    });


    Route::prefix('/indikator-kinerja-utama')->group(function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-iku-sk');
        })->name('super-admin-iku');

        Route::prefix('/sasaran-kegiatan')->controller(SasaranKegiatanController::class)->group(function () {
            Route::get('/', 'homeView')->name('super-admin-iku-sk');

            Route::middleware('editor')->group(function () {
                Route::get('/tambah', 'addView')->name('super-admin-iku-sk-add');
                Route::post('/tambah', 'add');

                Route::get('/{sk}/ubah', 'editView')->name('super-admin-iku-sk-edit');
                Route::put('/{sk}/ubah', 'edit');

                Route::get('/{sk}/hapus', 'delete');
            });
        });

        Route::prefix('/{sk}/indikator-kinerja-kegiatan')->controller(IndikatorKinerjaKegiatanController::class)->group(function ($route) {
            Route::get('/', 'homeView')->name('super-admin-iku-ikk');

            Route::middleware('editor')->group(function () {
                Route::get('/tambah', 'addView')->name('super-admin-iku-ikk-add');
                Route::post('/tambah', 'add');

                Route::get('/{ikk}/ubah', 'editView')->name('super-admin-iku-ikk-edit');
                Route::put('/{ikk}/ubah', 'edit');

                Route::get('/{ikk}/hapus', 'delete');
            });
        });

        Route::prefix('/{sk}/{ikk}/program-strategis')->controller(ProgramStrategisController::class)->group(function ($route) {
            Route::get('/', 'homeView')->name('super-admin-iku-ps');

            Route::middleware('editor')->group(function () {
                Route::get('/tambah', 'addView')->name('super-admin-iku-ps-add');
                Route::post('/tambah', 'add');

                Route::get('/{ps}/ubah', 'editView')->name('super-admin-iku-ps-edit');
                Route::put('/{ps}/ubah', 'edit');

                Route::get('/{ps}/hapus', 'delete');
            });
        });

        Route::prefix('/{sk}/{ikk}/{ps}/indikator-kinerja-program')->controller(IndikatorKinerjaProgramController::class)->group(function ($route) {
            Route::get('/', 'homeView')->name('super-admin-iku-ikp');

            Route::middleware('editor')->group(function () {
                Route::get('/tambah', 'addView')->name('super-admin-iku-ikp-add');
                Route::post('/tambah', 'add');

                Route::get('/{ikp}/ubah', 'editView')->name('super-admin-iku-ikp-edit');
                Route::put('/{ikp}/ubah', 'edit');

                Route::get('/{ikp}/status', 'statusToggle')->name('super-admin-iku-ikp-status');

                Route::get('/{ikp}/hapus', 'delete');
            });
        });
    });


    Route::prefix('/pengguna')->controller(UsersController::class)->middleware('editor')->group(function () {
        Route::get('/', 'homeView')->name('super-admin-users');

        Route::get('/tambah', 'addView')->name('super-admin-users-add');
        Route::post('/tambah', 'add');

        Route::get('/{user}/ubah', 'editView')->name('super-admin-users-edit');
        Route::put('/{user}/ubah', 'edit');

        Route::get('/{user}/hapus', 'delete');
    });


    Route::prefix('/unit')->controller(UnitsController::class)->middleware('editor')->group(function () {
        Route::get('/', 'homeView')->name('super-admin-unit');

        Route::get('/tambah', 'addView')->name('super-admin-unit-add');
        Route::post('/tambah', 'add');

        Route::get('/{unit}/ubah', 'editView')->name('super-admin-unit-edit');
        Route::put('/{unit}/ubah', 'edit');

        Route::get('/{unit}/hapus', 'delete');
    });
});


/*
| -----------------------------------------------------------------
| ADMIN
| -----------------------------------------------------------------
*/

Route::prefix('/')->middleware('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin-rs');
    })->name('admin-dashboard');

    Route::prefix('/rencana-strategis')->controller(RSController::class)->group(function () {
        Route::get('/', 'homeViewAdmin')->name('admin-rs');

        Route::post('/{period}/{ik}', 'addAdmin')->middleware('editor')->name('admin-rs-add');
    });

    Route::prefix('/indikator-kinerja-utama')->controller(IKUController::class)->group(function () {
        Route::get('/', 'homeViewAdmin')->name('admin-iku');
        Route::get('/{ikp}/detail', 'detailViewAdmin')->name('admin-iku-detail');

        Route::put('/{period}/{ikp}/data-table', 'bulkAddData')->middleware('editor')->name('admin-iku-data-table-bulk');
        Route::post('/{period}/{ikp}/data-table', 'addDataTable')->middleware('editor')->name('admin-iku-data-table');

        Route::post('/{period}/{ikp}/import', 'ikpTableDataImport')->middleware('editor')->name('admin-iku-data-table-import');
        Route::get('/{ikp}/template', 'ikpExcelTemplate')->middleware('editor')->name('admin-iku-template-download');

        Route::post('/{period}/{ikp}/data-single', 'addDataSingle')->middleware('editor')->name('admin-iku-data-single');
        // Route::get('/{ikp}/detail/{achievement}/hapus', 'delete')->middleware('editor');
    });

    Route::prefix('/riwayat')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin-history-rs');
        })->name('admin-history');

        Route::controller(RSController::class)->group(function () {
            Route::get('/rencana-strategis', 'historyAdmin')->name('admin-history-rs');
        });

        Route::controller(IKUController::class)->group(function () {
            Route::get('/indikator-kinerja-utama', 'historyAdmin')->name('admin-history-iku');
            Route::get('/indikator-kinerja-utama/{ikp}/detail', 'historyDetailAdmin')->name('admin-history-iku-detail');
        });
    });

    Route::prefix('/pengguna')->controller(UsersController::class)->middleware('editor')->group(function () {
        Route::get('/', 'homeViewAdmin')->name('admin-users');

        Route::get('/tambah', 'addViewAdmin')->name('admin-users-add');
        Route::post('/tambah', 'addAdmin');

        Route::get('/{id}/ubah', 'editViewAdmin')->name('admin-users-edit');
        Route::put('/{id}/ubah', 'editAdmin');

        Route::get('/{user}/hapus', 'deleteAdmin');
    });
});
