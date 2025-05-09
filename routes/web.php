<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LiveStreamController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InterviewController;
use App\Http\Middleware\AdminMiddleware;

// Rotas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

// Rotas para a galeria
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/news/{news}', [GalleryController::class, 'showNewsImages'])->name('gallery.news');

// Rota pública para obter a transmissão ativa
Route::get('/live-stream/active', [LiveStreamController::class, 'getActive']);

// Adicione esta rota antes das rotas com parâmetros
Route::get('/transmissoes', [LiveStreamController::class, 'listAll'])->name('live-streams.list');

Route::get('/entrevistas', [InterviewController::class, 'listAll'])->name('interviews.list');

// Rotas protegidas por autenticação
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rotas de notícias para usuários autenticados
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
    
    // Rotas para aprovadores e administradores
    Route::post('/news/{news}/approve', [NewsController::class, 'approve'])->name('news.approve');
});

// Rotas administrativas com namespace completo e sem usar prefix/group
Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/admin/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Rotas para live streams (adicionar estas)
    Route::get('/admin/live-streams', [LiveStreamController::class, 'index'])->name('live-streams.index');
    Route::post('/admin/live-streams', [LiveStreamController::class, 'store'])->name('live-streams.store');
    Route::get('/admin/live-streams/{id}', [LiveStreamController::class, 'show'])->name('live-streams.show');
    Route::put('/admin/live-streams/{id}', [LiveStreamController::class, 'update'])->name('live-streams.update');
    Route::delete('/admin/live-streams/{id}', [LiveStreamController::class, 'destroy'])->name('live-streams.destroy');
    Route::put('/admin/live-streams/{id}/activate', [LiveStreamController::class, 'activate'])->name('live-streams.activate');

    // Rota para gerenciar entrevistas
    Route::get('/admin/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::post('/admin/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::get('/admin/interviews/{id}', [InterviewController::class, 'show'])->name('interviews.show');
    Route::put('/admin/interviews/{id}', [InterviewController::class, 'update'])->name('interviews.update');
    Route::delete('/admin/interviews/{id}', [InterviewController::class, 'destroy'])->name('interviews.destroy');
    Route::put('/admin/interviews/{id}/toggle-featured', [InterviewController::class, 'toggleFeatured'])->name('interviews.toggle-featured');
});

// Rotas públicas com parâmetros (DEVE VIR DEPOIS das rotas específicas)
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Adicione junto com suas outras rotas
Route::post('/ckeditor/upload', [App\Http\Controllers\CKEditorController::class, 'upload'])
    ->name('ckeditor.upload')
    ->middleware('web');  // Adiciona explicitamente o middleware 'web'

// Adicione estas novas rotas ao arquivo de rotas

// Rotas para perfil pessoal (protegidas por autenticação)
Route::middleware(['auth'])->group(function () {
    // Perfil próprio
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Rotas públicas para equipe e perfis públicos
Route::get('/equipe', [ProfileController::class, 'listReporters'])->name('team.index');
Route::get('/equipe/{username}', [ProfileController::class, 'showPublic'])->name('profile.public');

// Rota para interações (likes/comentários)
Route::post('/profile/{user}/interaction', [ProfileController::class, 'addInteraction'])->name('profile.interaction');