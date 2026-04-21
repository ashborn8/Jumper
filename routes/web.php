<?php

use App\Interfaces\Http\Controllers\ChirpController;
use App\Interfaces\Http\Controllers\DashboardController;
use App\Interfaces\Http\Controllers\UserDiscoveryController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::redirect('chirp', 'jumper');
    Route::get('jumper', [ChirpController::class, 'index'])->name('jumper.index');
    Route::post('jumper', [ChirpController::class, 'store'])->name('jumper.store');
    Route::patch('jumper/{chirp}', [ChirpController::class, 'update'])->name('jumper.update');
    Route::delete('jumper/{chirp}', [ChirpController::class, 'destroy'])->name('jumper.destroy');

    Route::get('people', [UserDiscoveryController::class, 'index'])->name('people.index');
    Route::post('people/{user}/follow', [UserDiscoveryController::class, 'follow'])->name('people.follow');
    Route::delete('people/{user}/follow', [UserDiscoveryController::class, 'unfollow'])->name('people.unfollow');
});

require __DIR__.'/settings.php';
