<?php

use App\Http\Controllers\KnowledgeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KnowledgeController::class, 'index'])->name('home');
Route::post('/ask', [KnowledgeController::class, 'ask'])->name('ask');
