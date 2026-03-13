<?php
// routes/admin.php
use App\Http\Controllers\Admin\{UserController};
use App\Domains\Content\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Domains\Content\Http\Livewire\Admin\{ArticleTable, ArticleForm, ArticleShow};
use App\Livewire\Admin\Dashboard;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/dashboard', Dashboard::class)->name('dashboard');

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

// Rotas de artigos
Route::group([], function () {
    Route::middleware(['can:access admin panel'])->group(function () {
        Route::prefix('articles')->name('articles.')->group(function () {
            Route::get('/', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'index'])->name('index');
            Route::get('/create', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'create'])->name('create');
            Route::post('/', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'store'])->name('store');
            Route::get('/{article}', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'show'])->name('show');
            Route::get('/{article}/edit', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('edit');
            Route::put('/{article}', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'update'])->name('update');
            Route::delete('/{article}', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('destroy');

            // Ações adicionais
            Route::post('/{article}/publish', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'publish'])->name('publish');
            Route::post('/{article}/archive', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'archive'])->name('archive');
            Route::post('/{article}/restore', [App\Domains\Content\Http\Controllers\Admin\ArticleController::class, 'restore'])->name('restore');

            // Comentários
            Route::prefix('/{article}/comments')->name('comments.')->group(function () {
                Route::post('/{comment}/approve', [App\Domains\Content\Http\Controllers\Admin\CommentController::class, 'approve'])->name('approve');
                Route::post('/{comment}/spam', [App\Domains\Content\Http\Controllers\Admin\CommentController::class, 'markAsSpam'])->name('spam');
                Route::delete('/{comment}', [App\Domains\Content\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('destroy');
                Route::post('/bulk', [App\Domains\Content\Http\Controllers\Admin\CommentController::class, 'bulkAction'])->name('bulk');
            });
        });
    });
});

// Newsletter routes
Route::prefix('newsletters')->name('newsletters.')->group(function () {
    Route::get(
        '/subscribers',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'subscribers']
    )->name('subscribers');

    Route::get(
        '/subscribers/export',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'exportSubscribers']
    )->name('subscribers.export');

    Route::delete(
        '/subscribers/{subscriber}',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'destroySubscriber']
    )->whereNumber('subscriber')
        ->name('subscribers.destroy');

    Route::get(
        '/',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'index']
    )->name('index');

    Route::get(
        '/create',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'create']
    )->name('create');

    Route::post(
        '/',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'store']
    )->name('store');

    Route::get(
        '/{newsletter}',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'show']
    )->whereNumber('newsletter')
        ->name('show');

    Route::get(
        '/{newsletter}/edit',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'edit']
    )->whereNumber('newsletter')
        ->name('edit');

    Route::put(
        '/{newsletter}',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'update']
    )->whereNumber('newsletter')
        ->name('update');

    Route::delete(
        '/{newsletter}',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'destroy']
    )->whereNumber('newsletter')
        ->name('destroy');

    Route::post(
        '/{newsletter}/send',
        [\App\Http\Controllers\Admin\NewsletterController::class, 'send']
    )->whereNumber('newsletter')
        ->name('send');
});
