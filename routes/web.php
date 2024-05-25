<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {

    // I brough the dashboard endpoint to the auth group, so we can evict an unecessary repetition
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'tasks' => (new TaskController())->all(),
            // statusList and priorityList are static properties that Im sending to the dashboard component to compose the options of the selectlist components in the modal
            // Even though it is a test project and these are static lists, I've make the decision to put all the relevant information on the api
            'statusList' => (new TaskController())->getStatusList(),
            'priorityList' => (new TaskController())->getPriorityList()
        ]);
    })->name('dashboard');

    Route::patch('/task/finish/{id}', [TaskController::class, 'finish'])->name('task.finish');
    Route::patch('/task/take/{id}', [TaskController::class, 'take'])->name('task.take');
    Route::post('/task', [TaskController::class, 'create'])->name('task.create');
    Route::patch('/task', [TaskController::class, 'update'])->name('task.update');
    Route::delete('/task', [TaskController::class, 'destroy'])->name('task.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
