<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademicCalendarController;

Route::get('/', [AcademicCalendarController::class, 'index'])->name('academic-calendar.index');
Route::get('/login', [AcademicCalendarController::class, 'showLoginForm'])->name('academic-calendar.login');
Route::post('/scrape', [AcademicCalendarController::class, 'scrape'])->name('academic-calendar.scrape');
