<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DavomatController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FoydalanuvchiController;
use App\Http\Controllers\GuruhController;
use App\Http\Controllers\TalabaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Kollej Davomat Tizimi
|--------------------------------------------------------------------------
*/

// Asosiy sahifa - login sahifasiga yo'naltirish
Route::get('/', function () {
    return redirect()->route('login');
});

// ===== AUTH ROUTES =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===== AUTHENTICATED ROUTES =====
Route::middleware('auth')->group(function () {

    // Dashboard - barcha foydalanuvchilar uchun
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [DashboardController::class, 'refreshStats'])->name('dashboard.refresh');
    Route::get('/dashboard/stats/{period}', [DashboardController::class, 'getStatsByPeriod'])->name('dashboard.stats');

    // ===== ADMIN ROUTES =====
    Route::middleware('admin')->group(function () {

        // Guruhlar CRUD
        Route::resource('guruhlar', GuruhController::class)->parameters([
            'guruhlar' => 'guruh'
        ]);;
        Route::get('/guruhlar/{guruh}/talabalar', [GuruhController::class, 'getTalabalar'])->name('guruhlar.talabalar');

        // Talabalar CRUD
        Route::resource('talabalar', TalabaController::class)->parameters([
            'talabalar' => 'talaba'
        ]);
        Route::post('/talabalar/{talaba}/ketdi', [TalabaController::class, 'markAsLeft'])->name('talabalar.ketdi');
        Route::post('/talabalar/{talaba}/mark-left', [TalabaController::class, 'markAsLeft'])->name('talabalar.mark-left');
        Route::post('/talabalar/{talaba}/transfer', [TalabaController::class, 'transfer'])->name('talabalar.transfer');

        // Foydalanuvchilar boshqaruvi
        Route::resource('foydalanuvchilar', FoydalanuvchiController::class)->except(['show'])->parameters([
            'foydalanuvchilar' => 'foydalanuvchi'
        ]);
        Route::post('/foydalanuvchilar/{foydalanuvchi}/toggle-status', [FoydalanuvchiController::class, 'toggleStatus'])
            ->name('foydalanuvchilar.toggle-status');

        // Davomat tahrirlash (faqat admin)
        Route::get('/davomat/{davomat}/edit', [DavomatController::class, 'edit'])->name('davomat.edit');
        Route::put('/davomat/{davomat}', [DavomatController::class, 'update'])->name('davomat.update');
        Route::delete('/davomat/{davomat}', [DavomatController::class, 'destroy'])->name('davomat.destroy');

        // Export
        Route::get('/export', [ExportController::class, 'index'])->name('export.index');
        Route::post('/export/excel', [ExportController::class, 'exportCSV'])->name('export.csv');
        Route::get('/export/guruh/{guruh}', [ExportController::class, 'guruhHisoboti'])->name('export.guruh');
    });

    // ===== DAVOMAT OLUVCHI ROUTES =====
    Route::middleware('davomat.oluvchi')->group(function () {
        Route::get('/davomat/olish', [DavomatController::class, 'olish'])->name('davomat.olish');
        Route::post('/davomat/saqlash', [DavomatController::class, 'saqlash'])->name('davomat.saqlash');
        Route::get('/davomat/mening-tarixim', [DavomatController::class, 'meningTarixim'])->name('davomat.mening-tarixim');
        Route::get('/davomat/guruh-davomat', [DavomatController::class, 'getGuruhDavomat'])->name('davomat.guruh-davomat');
    });

    // ===== ADMIN VA DAVOMAT OLUVCHI UCHUN =====
    Route::middleware('role:admin,davomat_oluvchi')->group(function () {
        Route::get('/davomat/tarixi', [DavomatController::class, 'tarixi'])->name('davomat.tarixi');
    });

    // ===== BARCHA FOYDALANUVCHILAR UCHUN (Read-only) =====
    // Guruhlarni ko'rish
    Route::get('/guruhlar-korish', [GuruhController::class, 'index'])->name('guruhlar.korish');
    Route::get('/guruhlar-korish/{guruh}', [GuruhController::class, 'show'])->name('guruhlar.korish.show');

    // Talabalarni ko'rish
    Route::get('/talabalar-korish', [TalabaController::class, 'index'])->name('talabalar.korish');
    Route::get('/talabalar-korish/{talaba}', [TalabaController::class, 'show'])->name('talabalar.korish.show');
});

