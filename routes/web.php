<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteRapidoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\RelatorioPerdasController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.authenticate');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::resource('clientes', ClienteController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/clientes/rapido', [ClienteRapidoController::class, 'store'])
        ->name('clientes.rapido');

    Route::resource('encomendas', EncomendaController::class)
        ->only(['index', 'store', 'update']);

    Route::patch('/encomendas/{encomenda}/status', [EncomendaController::class, 'status'])
        ->name('encomendas.status');


    Route::get('/relatorios/perdas', [RelatorioPerdasController::class, 'index'])
        ->name('relatorios.perdas');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::redirect('/', '/dashboard');