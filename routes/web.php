<?php

use App\Http\Controllers\AdminDinas\ComplaintController as AdminDinasComplaintController;
use App\Http\Controllers\AdminDinas\ReportController;
use App\Http\Controllers\AgenController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'role:superadmin,admin_dinas'])->group(function () {

    Route::get('/dashboard', [ComplaintController::class, 'index'])->name('dashboard');
    Route::get('/complaints/stats', [ComplaintController::class, 'getComplaintStats'])
        ->name('complaints.stats');

    //FIX: Letakkan route yang lebih spesifik DULU
    Route::prefix('complaints')->name('complaints.')->group(function () {
        // Route dengan action spesifik harus di atas
        Route::post('/{id}/update-status', [ComplaintController::class, 'updateStatus'])
            ->name('update-status');
        Route::get('/{complaint}/history', [ComplaintController::class, 'showHistory'])
            ->name('history');
        Route::get('/{complaint}/edit', [ComplaintController::class, 'edit'])
            ->name('edit');
        Route::put('/{complaint}', [ComplaintController::class, 'update'])
            ->name('update');
        Route::delete('/{complaint}', [ComplaintController::class, 'destroy'])
            ->name('destroy');
        Route::get('/{complaint}', [ComplaintController::class, 'show'])
            ->name('show');
        Route::get('/{id}/cetak-history', [ComplaintController::class, 'cetakHistory'])
            ->name('cetak-history');

        Route::get('/complaint-photo/{fileId}', [ComplaintController::class, 'showPhoto'])->name('photo');
        Route::get('/{complaint}/photo', [ComplaintController::class, 'showComplaintPhoto'])->name('show.photo');   
    });
    Route::prefix('categories')->name('admin_dinas.categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::delete('/{agencyCategory}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Superadmin only routes
    Route::middleware('role:superadmin')->group(function () {
        Route::get('reports', [ComplaintController::class, 'reportAll'])->name('reports.all');

        Route::get('/reports/users', [ComplaintController::class, 'reportUsers'])
            ->name('reports.users');
        Route::get('/reports/users/{username}', [ComplaintController::class, 'userComplaintsDetail'])
            ->name('reports.users.detail');
        Route::get('/reports/pelapor_pdf', [ComplaintController::class, 'generateUserReportPdf'])
            ->name('reports.pelapor_pdf');
        Route::get('/reports/cetak-pdf', [ComplaintController::class, 'cetakPdf'])
            ->name('reports.cetak-pdf');
        
        Route::get('/reports/agents', [ComplaintController::class, 'reportAgents'])
            ->name('reports.agents');

        // Admin Pegawai Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
            Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
            Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
            Route::get('/pegawai/{id}', [PegawaiController::class, 'show'])->name('pegawai.show');
            Route::get('/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
            Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
            Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
            Route::post('/pegawai/{id}/reset-password', [PegawaiController::class, 'resetPassword'])
                ->name('pegawai.reset-password');

            //agen user 
            Route::get('/agen-dinas', [AgenController::class, 'index'])->name('agen-dinas.index');
            Route::get('/agen-dinas/cetak-pdf', [AgenController::class, 'cetakPdf'])->name('agen-dinas.cetak-pdf'); 
        });
        
    });
    Route::middleware('role:admin_dinas')->group(function () {
        Route::prefix('admin_dinas')->name('admin_dinas.')->group(function () {
            Route::get('/complaints', [AdminDinasComplaintController::class, 'index'])
                ->name('complaints.index');
            Route::get('/complaints/{id}', [AdminDinasComplaintController::class, 'show'])
                ->name('complaints.show');
            Route::get('/complaint/{id}/edit', [AdminDinasComplaintController::class, 'edit'])->name('complaints.edit');
            Route::put('/complaints/{id}', [AdminDinasComplaintController::class, 'update'])->name('complaints.update');
            Route::delete('/complaints/{id}', [AdminDinasComplaintController::class, 'destroy'])->name('complaints.destroy');
            Route::post('/complaints/{id}/update-status', [AdminDinasComplaintController::class, 'updateStatus'])
                ->name('complaints.update-status');

            Route::get('/reports.generate-pdf', [ReportController::class, 'generatePdf'])->name('reports.generate-pdf');
            Route::post('/generate-custom-pdf', [ReportController::class, 'generateCustomPdf'])->name('generate-custom-pdf');
        });
    });
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    });
});

require __DIR__ . '/auth.php';
