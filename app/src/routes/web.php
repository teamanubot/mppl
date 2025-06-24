<?php

use App\Livewire\CekStatus;
use App\Livewire\Daftar;
use App\Livewire\ShowHomePage;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use App\Livewire\Akun\AkunLogin;
use App\Livewire\Akun\AkunRegister;
use App\Livewire\ProductPage;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\InvoiceController;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/
Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/
Route::get('/', ShowHomePage::class )->name('home');
Route::get('/daftar', \App\Livewire\Daftar::class)->name('daftar');
Route::get('/order', ProductPage::class )->name('order');
Route::get('/cek-status', CekStatus::class )->name('cek-status');
Route::get('/login', AkunLogin::class)->name('akun.login');
Route::get('/register', AkunRegister::class)->name('akun.register');
Route::get('/invoice/{id}', [InvoiceController::class, 'download'])->name('invoice.download');

Route::post('/logout-akun', function () {
    Auth::guard('akun')->logout();
    return redirect('/');
})->name('akun.logout');

Route::middleware('auth:akun')->group(function () {
    Route::get('/profil', function () {
        return view('/'); // contoh halaman setelah login
    })->name('akun.profil');
});