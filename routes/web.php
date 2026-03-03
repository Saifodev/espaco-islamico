<?php
// routes/web.php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\InvitationController;

// Rotas de convite (públicas)
Route::get('/invitation/accept/{token}', [InvitationController::class, 'showAcceptForm'])->name('invitation.accept');
Route::post('/invitation/accept', [InvitationController::class, 'accept'])->name('invitation.accept.post');

require __DIR__ . '/auth.php';
require __DIR__ . '/public.php';

// Route::middleware(['auth', 'role:admin|editor'])->prefix('admin')
Route::middleware(['auth'])->prefix('admin')
    ->name('admin.')
    ->group(function () {
        require __DIR__ . '/admin.php';
    });

// require __DIR__ . '/dev.php';
