<?php

use App\Http\Controllers\IndikatorKinerjaKegiatanController;
use App\Http\Controllers\IndikatorKinerjaProgramController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\ProgramStrategisController;
use App\Http\Controllers\RencanaStrategisController;
use App\Http\Controllers\SasaranStrategisController;
use App\Http\Controllers\SasaranKegiatanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UsersController;
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


// Authentication
Route::get('/masuk', function () {
    return view('authentication.login');
})->name('login');

Route::get('/lupa-kata-sandi', function () {
    return view('authentication.forget-password');
})->name('forget-password');

Route::get('/ubah-kata-sandi', function () {
    return view('authentication.change-password');
})->name('change-password');

Route::get('/keluar', function () {
    return 'Anda Telah Keluar';
})->name('logout');


// Super Admin
Route::group([
    'prefix' => '/super-admin'
], function () {
    Route::get('/beranda', function () {
        return view('super-admin.home');
    })->name('super-admin-dashboard');


    Route::group([
        'prefix' => '/capaian-kinerja'
    ], function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-achievement-rs');
        })->name('super-admin-achievement');

        Route::get('/rencana-strategis', [RencanaStrategisController::class, 'homeView'])->name('super-admin-achievement-rs');
        Route::view('/rencana-strategis/{id}/detail', 'super-admin.achievement.rs.detail')->name('super-admin-achievement-rs-detail');
        Route::view('/indikator-kinerja-utama', 'super-admin.achievement.iku.home')->name('super-admin-achievement-iku');
        Route::view('/indikator-kinerja-utama/{id}/detail', 'super-admin.achievement.iku.detail')->name('super-admin-achievement-iku-detail');
    });

    Route::group([
        'prefix' => '/rencana-strategis'
    ], function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-rs-ss');
        })->name('super-admin-rs');

        Route::group([
            'prefix' => '/sasaran-strategis',
            'controller' => SasaranStrategisController::class
        ], function () {
            Route::get('/', 'homeView')->name('super-admin-rs-ss');
            Route::get('/tambah', 'addView')->name('super-admin-rs-ss-add');
            Route::post('/tambah', 'add');
            Route::get('/{id}/ubah', 'editView')->name('super-admin-rs-ss-edit');
            Route::put('/{id}/ubah', 'edit');
        });

        Route::group([
            'prefix' => '/{ss}/kegiatan',
            'controller' => KegiatanController::class
        ], function ($route) {
            Route::get('/', 'homeView')->name('super-admin-rs-k');
            Route::get('/tambah', 'addView')->name('super-admin-rs-k-add');
            Route::post('/tambah', 'add');
            Route::get('/{id}/ubah', 'editView')->name('super-admin-rs-k-edit');
            Route::put('/{id}/ubah', 'edit');
        });

        Route::group([
            'prefix' => '/{ss}/{k}/indikator-kinerja',
            'controller' => IndikatorKinerjaController::class
        ], function ($route) {
            Route::get('/', 'homeView')->name('super-admin-rs-ik');
            Route::get('/tambah', 'addView')->name('super-admin-rs-ik-add');
            Route::post('/tambah', 'add');
            Route::get('/{id}/ubah', 'editView')->name('super-admin-rs-ik-edit');
            Route::put('/{id}/ubah', 'edit');
            Route::get('/{id}/status', 'statusToggle')->name('super-admin-rs-ik-status');
        });
    });


    Route::group([
        'prefix' => '/indikator-kinerja-utama'
    ], function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-iku-sk');
        })->name('super-admin-iku');

        Route::group([
            'prefix' => '/sasaran-kegiatan',
            'controller' => SasaranKegiatanController::class
        ], function () {
            Route::get('/', 'homeView')->name('super-admin-iku-sk');
            Route::get('/tambah', 'addView')->name('super-admin-iku-sk-add');
            Route::post('/tambah', 'add');
            Route::get('/{id}/ubah', 'editView')->name('super-admin-iku-sk-edit');
            Route::put('/{id}/ubah', 'edit');
        });

        Route::group([
            'prefix' => '/{sk}/indikator-kinerja-kegiatan',
            'controller' => IndikatorKinerjaKegiatanController::class
        ], function ($route) {
            Route::get('/', 'homeView')->name('super-admin-iku-ikk');
            Route::get('/tambah', 'addView')->name('super-admin-iku-ikk-add');
            Route::post('/tambah', 'add');
            Route::get('/{id}/ubah', 'editView')->name('super-admin-iku-ikk-edit');
            Route::put('/{id}/ubah', 'edit');
        });

        Route::group([
            'prefix' => '/{sk}/{ikk}/program-strategis',
            'controller' => ProgramStrategisController::class
        ], function ($route) {
            Route::get('/', 'homeView')->name('super-admin-iku-ps');
            Route::get('/tambah', 'addView')->name('super-admin-iku-ps-add');
            Route::post('/tambah', 'add');
            Route::get('/{id}/ubah', 'editView')->name('super-admin-iku-ps-edit');
            Route::put('/{id}/ubah', 'edit');
        });

        Route::group([
            'prefix' => '/{sk}/{ikk}/{ps}/indikator-kinerja-program',
            'controller' => IndikatorKinerjaProgramController::class
        ], function ($route) {
            Route::get('/', 'homeView')->name('super-admin-iku-ikp');
            Route::get('/tambah', 'addView')->name('super-admin-iku-ikp-add');
            Route::post('/tambah', 'add');
            Route::get('/{id}/ubah', 'editView')->name('super-admin-iku-ikp-edit');
            Route::put('/{id}/ubah', 'edit');
            Route::get('/{id}/status', 'statusToggle')->name('super-admin-iku-ikp-status');
        });
    });


    Route::group([
        'prefix' => '/pengguna',
        'controller' => UsersController::class
    ], function () {
        Route::get('/', 'homeView')->name('super-admin-users');
        Route::get('/tambah', 'addView')->name('super-admin-users-add');
        Route::post('/tambah', 'add');
        Route::get('/{id}/ubah', 'editView')->name('super-admin-users-edit');
        Route::put('/{id}/ubah', 'edit');
    });


    Route::group([
        'prefix' => '/unit',
        'controller' => UnitsController::class
    ], function () {
        Route::get('/', 'homeView')->name('super-admin-unit');
        Route::get('/tambah', 'addView')->name('super-admin-unit-add');
        Route::post('/tambah', 'add');
        Route::get('/{id}/ubah', 'editView')->name('super-admin-unit-edit');
        Route::put('/{id}/ubah', 'edit');
    });
});


// Admin
Route::group([
    'prefix' => '/'
], function () {
    Route::get('/', function () {
        return view('admin.home');
    })->name('admin-dashboard');

    Route::get('/rencana-strategis', [RencanaStrategisController::class, 'homeViewAdmin'])->name('admin-rs');

    Route::view('/indikator-kinerja-utama', 'admin.iku.home')->name('admin-iku');

    Route::group([
        'prefix' => '/riwayat'
    ], function () {
        Route::get('/', function () {
            return redirect()->route('admin-history-rs');
        })->name('admin-history');

        Route::view('/rencana-strategis', 'admin.history.rs.home')->name('admin-history-rs');
        Route::view('/indikator-kinerja-utama', 'admin.history.iku.home')->name('admin-history-iku');
        Route::view('/indikator-kinerja-utama/{id}/detail', 'admin.history.iku.detail')->name('admin-history-iku-detail');
    });

    Route::group([
        'prefix' => '/pengguna',
        'controller' => UsersController::class
    ], function () {
        Route::get('/', 'homeViewAdmin')->name('admin-users');
        Route::get('/tambah', 'addViewAdmin')->name('admin-users-add');
        Route::post('/tambah', 'addAdmin');
        Route::get('/{id}/ubah', 'editViewAdmin')->name('admin-users-edit');
        Route::put('/{id}/ubah', 'editAdmin');
    });
});