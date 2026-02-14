<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\HomePageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AiGuideController;



Route::get('/', [HomePageController::class, 'home']);
Route::get('/about', [HomePageController::class, 'about']);
Route::get('/destinations', [HomePageController::class, 'destinations']);
Route::get('/contact', [HomePageController::class, 'contact']);
Route::get('/destination/{id}', [HomePageController::class, 'single'])->name('destination.single')->where('destination', '[0-9]+');

Route::get('/register', function () {
    return view('register');
});


Route::get('/search', [SearchController::class, 'handleSearch'])->name('search');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/ask-ai-guide', [AiGuideController::class, 'askGuide'])->name('ai.ask');

