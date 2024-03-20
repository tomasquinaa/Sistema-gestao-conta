<?php

use App\Http\Controllers\ContaController;
use App\Http\Controllers\SendEmailContaController;
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

// Aula 13

Route::get('/', function () {
    return view('welcome');
});

// CONTAS
Route::get('/index-contas', [ContaController::class, 'index'])->name('conta.index');
Route::get('/create-contas', [ContaController::class, 'create'])->name('conta.create');
Route::post('/store-contas', [ContaController::class, 'store'])->name('conta.store');
Route::get('/show-contas/{conta}', [ContaController::class, 'show'])->name('conta.show');
Route::get('/edit-contas/{conta}', [ContaController::class, 'edit'])->name('conta.edit');
Route::put('/update-contas/{conta}', [ContaController::class, 'update'])->name('conta.update');
Route::delete('/destroy-contas/{conta}', [ContaController::class, 'destroy'])->name('conta.destroy');

Route::get('/change-situation-conta/{conta}', [ContaController::class, 'changeSituation'])->name('conta.change-situation');

Route::get('/gerar-pdf-conta', [ContaController::class, 'gerarPdf'])->name('conta.gerar-pdf');

// gerar CSV - Excel
Route::get('/gerar-csv-conta', [ContaController::class, 'gerarCsv'])->name('conta.gerar-csv');

// gerar Word 
Route::get('/gerar-word-conta', [ContaController::class, 'gerarWord'])->name('conta.gerar-word');

// Enviar Email

Route::get('/send-email-pendente-conta', [SendEmailContaController::class, 'sendEmailPendenteConta'])->name('conta.send-email-pendente');
