<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\CoverLetterController;

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
    return view('index');
});
Route::get('/resume', function () {
    return view('resume');
});
Route::get('/services', function () {
    return view('services');
});
Route::get('/coverletter', function () {
    return view('coverletter');
});
Route::get('/portfolio', function () {
    return view('portfolio');
});
Route::get('/career', function () {
    return view('career');
});
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});
Route::get('/refund-policy', function () {
    return view('refund-policy');
});
Route::get('/terms', function () {
    return view('terms');
});
Route::get('/faq', function () {
    return view('faq');
});
Route::get('/about', function () {
    return view('about');
});
Route::get('/contact', function () {
    return view('contact');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/cover-letter-template', [CoverLetterController::class, 'show'])->name('cover-letter-template.show');
    Route::post('/cover-letter-template', [CoverLetterController::class, 'store'])->name('cover-letter-template.store');
    Route::post('/profile/update', [CoverLetterController::class, 'updateProfile'])->name('profile.update');

    Route::get('/resume-build', [PricingController::class, 'show'])->name('resume-build');
    Route::get('/resume-template', [ResumeController::class, 'show'])->name('resume-template');
    Route::get('/portfolio-template', function () {
        return view('portfolio-template');
    })->name('portfolio-template');
    Route::get('/career-template', function () {
        return view('career-template');
    })->name('career-template');
    Route::post('/resume-template', [ResumeController::class, 'store'])->name('resume-template.store');
    Route::get('/resume/preview/{template}', [ResumeController::class, 'preview'])->name('resume.preview');
    Route::get('/resume/fullscreen-preview/{template}', [ResumeController::class, 'fullscreenPreview'])->name('resume.fullscreen.preview');
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/status/{merchantOrderId}', [PaymentController::class, 'paymentStatus'])->name('payment.status');
    Route::get('/payment/success', function () {
        return view('payment-success');
    })->name('payment.success');
    Route::get('/payment/failure', function () {
        return view('payment-failure');
    })->name('payment.failure');
});

Route::post('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
});
