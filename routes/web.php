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

    Route::get('/indikator-kinerja-utama', function () {
        return 'Halaman Indikator Kinerja Utama';
    })->name('super-admin-iku');


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
