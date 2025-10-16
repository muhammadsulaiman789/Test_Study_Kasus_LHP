<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SummaryController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/add-transaction', action: [DashboardController::class, 'addTransaction'])->name('transaction.add');
Route::get('/summary', [SummaryController::class, 'index'])->name('summary');
Route::get('/tank/{type}', [DashboardController::class, 'sourceDestinationTank']);
Route::get('/export-json', [SummaryController::class, 'exportJson'])->name('export.json');
