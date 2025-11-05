<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocenteIdoneidadController;
use App\Http\Controllers\ImportacionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas protegidas por autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DocenteIdoneidadController::class, 'dashboard'])
        ->name('dashboard');

    // Gestión de perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Docentes - Idoneidad
    Route::prefix('docentes')->name('docentes.')->group(function () {
        Route::get('/', [DocenteIdoneidadController::class, 'index'])->name('index');
        Route::get('/{docente}', [DocenteIdoneidadController::class, 'show'])->name('show');
    });

    // Importación de archivos
    Route::prefix('importacion')->name('importacion.')->group(function () {
        Route::get('/', [ImportacionController::class, 'index'])->name('index');
        Route::post('/importar', [ImportacionController::class, 'importar'])->name('importar');
        Route::get('/plantilla', [ImportacionController::class, 'descargarPlantilla'])->name('plantilla');
    });
});

require __DIR__.'/auth.php';