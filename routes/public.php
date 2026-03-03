<?php
// routes/public.php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Domains\Content\Http\Controllers\Public\ArticleController;
use App\Domains\Content\Http\Controllers\Public\CommentController;
use App\Http\Controllers\NewsletterSubscriptionController;

Route::get('/', function () {
    return view('public.index');
})->name('home');

// Rotas para páginas estáticas
Route::view('/sobre', 'public.about')->name('about');

// Rotas públicas para conteúdo
Route::prefix('conteudo')->name('articles.')->group(function () {
    // Listagem geral (padrão: artigos)
    Route::get('/', [ArticleController::class, 'index'])->name('index');
    
    // Listagem por tipo (artigos, vídeos, jornais)
    Route::get('/{type}', [ArticleController::class, 'index'])->name('type');
    
    // Item individual
    Route::get('/{type}/{slug}', [ArticleController::class, 'show'])->name('show');
});

// Redirecionamentos amigáveis (opcional)
Route::redirect('/artigos', '/conteudo/article')->name('articles.redirect');
Route::redirect('/videos', '/conteudo/video')->name('videos.redirect');
Route::redirect('/jornais', '/conteudo/newspaper')->name('newspapers.redirect');

Route::prefix('comments')->group(function () {
    Route::get('/article/{articleId}', [CommentController::class, 'index']);
    Route::post('/', [CommentController::class, 'store']);
});

Route::post('/newsletter/subscribe', 
    [NewsletterSubscriptionController::class, 'subscribe']
)->name('newsletter.subscribe');

Route::get('/newsletter/unsubscribe', [NewsletterSubscriptionController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe');