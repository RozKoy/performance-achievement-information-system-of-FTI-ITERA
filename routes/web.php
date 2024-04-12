<?php

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

Route::get('/', function () {
    return view('welcome');
});

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

Route::group([
    'prefix' => '/super-admin'
], function () {
    Route::get('/beranda', function () {
        return view('super-admin.home');
    })->name('super-admin-dashboard');

    Route::get('/capaian-kinerja', function () {
        return 'Halaman Capaian Kinerja';
    })->name('super-admin-achievement');

    Route::get('/rencana-strategis', function () {
        return 'Halaman Rencana Strategis';
    })->name('super-admin-rs');


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
            'prefix' => '/sasaran-kinerja'
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
            'prefix' => '/{sk}/{ikk}/data-dukung'
        ], function () {
            Route::view('/', 'super-admin.iku.dd.home')->name('super-admin-iku-dd');
            Route::view('/tambah', 'super-admin.iku.dd.add')->name('super-admin-iku-dd-add');
            Route::view('/{id}/ubah', 'super-admin.iku.dd.edit')->name('super-admin-iku-dd-edit');
        });
    });


    Route::group([
        'prefix' => '/pengguna'
    ], function () {
        Route::view('/', 'super-admin.users.home')->name('super-admin-users');
        Route::view('/tambah', 'super-admin.users.add')->name('super-admin-users-add');
        Route::view('/{id}/ubah', 'super-admin.users.edit')->name('super-admin-users-edit');
    });


    Route::group([
        'prefix' => '/organisasi'
    ], function () {
        Route::view('/', 'super-admin.organization.home')->name('super-admin-organization');
        Route::view('/tambah', 'super-admin.organization.add')->name('super-admin-organization-add');
        Route::view('/{id}/ubah', 'super-admin.organization.edit')->name('super-admin-organization-edit');
    });
});
