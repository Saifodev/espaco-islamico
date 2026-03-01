<?php
// routes/admin.php
use App\Http\Controllers\Admin\{UserController, ArticleController};
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Rotas de perfil
Route::group([], function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas de usuários
Route::resource('users', UserController::class);
Route::post('/users/{user}/resend-invite', [UserController::class, 'resendInvite'])->name('users.resend-invite');
Route::post('/users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
Route::post('/users/leave-impersonate', [UserController::class, 'leaveImpersonate'])->name('users.leave-impersonate');
Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');