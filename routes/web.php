<?php

use App\Http\Controllers\UnitsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Artisan;
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

        Route::view('/rencana-strategis', 'super-admin.achievement.rs.home')->name('super-admin-achievement-rs');
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
            'prefix' => '/sasaran-strategis'
        ], function () {
            Route::view('/', 'super-admin.rs.ss.home')->name('super-admin-rs-ss');
            Route::view('/tambah', 'super-admin.rs.ss.add')->name('super-admin-rs-ss-add');
            Route::view('/{id}/ubah', 'super-admin.rs.ss.edit')->name('super-admin-rs-ss-edit');
        });

        Route::group([
            'prefix' => '/{ss}/kegiatan'
        ], function () {
            Route::view('/', 'super-admin.rs.k.home')->name('super-admin-rs-k');
            Route::view('/tambah', 'super-admin.rs.k.add')->name('super-admin-rs-k-add');
            Route::view('/{id}/ubah', 'super-admin.rs.k.edit')->name('super-admin-rs-k-edit');
        });

        Route::group([
            'prefix' => '/{ss}/{k}/indikator-kinerja'
        ], function () {
            Route::view('/', 'super-admin.rs.ik.home')->name('super-admin-rs-ik');
            Route::view('/tambah', 'super-admin.rs.ik.add')->name('super-admin-rs-ik-add');
            Route::view('/{id}/ubah', 'super-admin.rs.ik.edit')->name('super-admin-rs-ik-edit');
        });
    });


    Route::group([
        'prefix' => '/indikator-kinerja-utama'
    ], function () {
        Route::get('/', function () {
            return redirect()->route('super-admin-iku-sk');
        })->name('super-admin-iku');

        Route::group([
            'prefix' => '/sasaran-kegiatan'
        ], function () {
            Route::view('/', 'super-admin.iku.sk.home')->name('super-admin-iku-sk');
            Route::view('/tambah', 'super-admin.iku.sk.add')->name('super-admin-iku-sk-add');
            Route::view('/{id}/ubah', 'super-admin.iku.sk.edit')->name('super-admin-iku-sk-edit');
        });

        Route::group([
            'prefix' => '/{sk}/indikator-kinerja-kegiatan'
        ], function () {
            Route::view('/', 'super-admin.iku.ikk.home')->name('super-admin-iku-ikk');
            Route::view('/tambah', 'super-admin.iku.ikk.add')->name('super-admin-iku-ikk-add');
            Route::view('/{id}/ubah', 'super-admin.iku.ikk.edit')->name('super-admin-iku-ikk-edit');
        });

        Route::group([
            'prefix' => '/{sk}/{ikk}/program-strategis'
        ], function () {
            Route::view('/', 'super-admin.iku.ps.home')->name('super-admin-iku-ps');
            Route::view('/tambah', 'super-admin.iku.ps.add')->name('super-admin-iku-ps-add');
            Route::view('/{id}/ubah', 'super-admin.iku.ps.edit')->name('super-admin-iku-ps-edit');
        });

        Route::group([
            'prefix' => '/{sk}/{ikk}/{ps}/indikator-kinerja-program'
        ], function () {
            Route::view('/', 'super-admin.iku.ikp.home')->name('super-admin-iku-ikp');
            Route::view('/tambah', 'super-admin.iku.ikp.add')->name('super-admin-iku-ikp-add');
            Route::view('/{id}/ubah', 'super-admin.iku.ikp.edit')->name('super-admin-iku-ikp-edit');
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

    Route::view('/rencana-strategis', 'admin.rs.home')->name('admin-rs');

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
        'prefix' => '/pengguna'
    ], function () {
        Route::view('/', 'admin.users.home')->name('admin-users');
        Route::view('/tambah', 'admin.users.add')->name('admin-users-add');
        Route::view('/{id}/ubah', 'admin.users.edit')->name('admin-users-edit');
    });
});