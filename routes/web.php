<?php

use App\Http\Controllers\IndikatorKinerjaKegiatanController;
use App\Http\Controllers\IndikatorKinerjaProgramController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\ProgramStrategisController;
use App\Http\Controllers\SasaranStrategisController;
use App\Http\Controllers\SasaranKegiatanController;
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

// Route::get('/', function () {
//     return view('welcome');
// });


/*
| -----------------------------------------------------------------
| AUTHENTICATION
| -----------------------------------------------------------------
*/

Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/masuk', 'loginView')->name('login');
        Route::post('/masuk', 'login');
        Route::get('/lupa-kata-sandi', function () {
            return view('authentication.forget-password');
        })->name('forget-password');
        Route::get('/ubah-kata-sandi', function () {
            return view('authentication.change-password');
        })->name('change-password');
    });

    Route::get('/keluar', 'logout')->middleware('auth')->name('logout');
});



/*
| -----------------------------------------------------------------
| SUPER ADMIN
| -----------------------------------------------------------------
*/

Route::prefix('/super-admin')->middleware('superadmin')->group(function () {
    Route::get('/beranda', function () {
        return view('super-admin.home');
    })->name('super-admin-dashboard');


    Route::prefix('/capaian-kinerja')->group(function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-achievement-rs');
        })->name('super-admin-achievement');

        Route::prefix('/rencana-strategis')->controller(RSController::class)->group(function () {
            Route::get('/', 'homeView')->name('super-admin-achievement-rs');
            Route::get('/{ik}/detail', 'detailView')->name('super-admin-achievement-rs-detail');
            Route::get('/{year}/target', 'targetView')->name('super-admin-achievement-rs-target');

            Route::middleware('editor')->group(function () {
                Route::post('/{ik}/{unit}/target', 'addTarget')->name('super-admin-achievement-rs-target-add');
                Route::get('/{period}/status', 'statusToggle')->name('super-admin-achievement-rs-status');
                Route::post('/{ik}/evaluation', 'addEvaluation')->name('super-admin-achievement-rs-evaluation');
            });
        });

        Route::prefix('/indikator-kinerja-utama')->controller(IKUController::class)->group(function () {
            Route::get('/', 'homeView')->name('super-admin-achievement-iku');
            Route::get('/{ikp}/detail', 'detailView')->name('super-admin-achievement-iku-detail');
            Route::get('/{year}/target', 'targetView')->name('super-admin-achievement-iku-target');

            Route::middleware('editor')->group(function () {
                Route::post('/{ikp}/{unit}/target', 'addTarget')->name('super-admin-achievement-iku-target-add');
                Route::get('/{period}/status', 'statusToggle')->name('super-admin-achievement-iku-status');
                Route::post('/{ikp}/evaluation', 'addEvaluation')->name('super-admin-achievement-iku-evaluation');
            });
        });
    });

    Route::prefix('/rencana-strategis')->group(function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-rs-ss');
        })->name('super-admin-rs');

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


    Route::prefix('/pengguna')->controller(UsersController::class)->group(function () {
        Route::get('/', 'homeView')->name('super-admin-users');

        Route::middleware('editor')->group(function () {
            Route::get('/tambah', 'addView')->name('super-admin-users-add');
            Route::post('/tambah', 'add');

            Route::get('/{user}/ubah', 'editView')->name('super-admin-users-edit');
            Route::put('/{user}/ubah', 'edit');

            Route::get('/{user}/hapus', 'delete');
        });
    });


    Route::prefix('/unit')->controller(UnitsController::class)->group(function () {
        Route::get('/', 'homeView')->name('super-admin-unit');

        Route::middleware('editor')->group(function () {
            Route::get('/tambah', 'addView')->name('super-admin-unit-add');
            Route::post('/tambah', 'add');

            Route::get('/{unit}/ubah', 'editView')->name('super-admin-unit-edit');
            Route::put('/{unit}/ubah', 'edit');

            Route::get('/{unit}/hapus', 'delete');
        });
    });
});


/*
| -----------------------------------------------------------------
| ADMIN
| -----------------------------------------------------------------
*/

Route::prefix('/')->middleware('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.home');
    })->name('admin-dashboard');

    Route::prefix('/rencana-strategis')->controller(RSController::class)->group(function () {
        Route::get('/', 'homeViewAdmin')->name('admin-rs');
        Route::post('/{period}/{ik}', 'addAdmin')->middleware('editor')->name('admin-rs-add');
    });

    Route::get('/indikator-kinerja-utama', [IKUController::class, 'homeViewAdmin'])->name('admin-iku');
    Route::get('/indikator-kinerja-utama/{id}/detail', [IKUController::class, 'detailViewAdmin'])->name('admin-iku-detail');
    Route::post('/indikator-kinerja-utama/{period}/{id}/data', [IKUController::class, 'addData'])->name('admin-iku-data');

    Route::prefix('/riwayat')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin-history-rs');
        })->name('admin-history');

        Route::controller(RSController::class)->group(function () {
            Route::get('/rencana-strategis', 'historyAdmin')->name('admin-history-rs');
        });

        Route::controller(IKUController::class)->group(function () {
            Route::view('/indikator-kinerja-utama', 'admin.history.iku.home')->name('admin-history-iku');
            Route::view('/indikator-kinerja-utama/{id}/detail', 'admin.history.iku.detail')->name('admin-history-iku-detail');
        });
    });

    Route::prefix('/pengguna')->controller(UsersController::class)->group(function () {
        Route::get('/', 'homeViewAdmin')->name('admin-users');

        Route::middleware('editor')->group(function () {
            Route::get('/tambah', 'addViewAdmin')->name('admin-users-add');
            Route::post('/tambah', 'addAdmin');

            Route::get('/{id}/ubah', 'editViewAdmin')->name('admin-users-edit');
            Route::put('/{id}/ubah', 'editAdmin');

            Route::get('/{user}/hapus', 'deleteAdmin');
        });
    });
});
