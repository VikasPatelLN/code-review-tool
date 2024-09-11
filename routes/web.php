<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('welcome');


Route::get('/get-files-and-content', [App\Http\Controllers\GitHubController::class, 'getFilesAndContent'])->name('file-content');

Route::get('/ai-code-review', [App\Http\Controllers\RiskGPTController::class, 'aiCodeReview'])->name('file-content');
