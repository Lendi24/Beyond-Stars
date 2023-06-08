<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|   
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware(['signed', 'throttle:6,1']);




Route::prefix('/v1')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/register',      [AuthController::class, 'register'])->middleware('guest');
        Route::post('/login',         [AuthController::class, 'login'])->middleware('guest');

        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('/logout',      [AuthController::class, 'logout']);
        });

        Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
            return $request->user();
        });
    });



    Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
        Route::prefix('/group')->group(function () {
            Route::post('/', [GroupController::class, 'create']);
            Route::get('/', [GroupController::class, 'index']);
            Route::get('/{group_id}', [GroupController::class, 'show']);
            Route::get('/members/{group_id}', [GroupController::class, 'showMembers']);
            Route::post('/join/{group_id}', [GroupController::class, 'join']);
            Route::delete('/{group_id}', [GroupController::class, 'delete']);
            Route::post('/leave/{group_id}', [GroupController::class, 'leave']);
            Route::post('/{group_id}', [GroupController::class, 'update']);
        });

        Route::prefix('/user')->group(function () {
            Route::get('/groups', [UserController::class, 'groups']);
            Route::post('/update', [UserController::class, 'update']);
            Route::post('password/email', [PasswordResetController::class, 'forgotPassword']);
        });

        Route::prefix('/categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
        });


        Route::prefix('/event')->group(function () {
            Route::get('/', [EventController::class, 'index']);
            Route::get('/group', [EventController::class, 'groupIndex']);
            Route::post('/', [EventController::class, 'create']);
            Route::post('/join/{event_id}', [EventController::class, 'join']);
            Route::post('/leave/{event_id}', [EventController::class, 'leave']);
            Route::get('/public', [EventController::class, 'publicIndex']);
            Route::post('/{event_id}', [EventController::class, 'update']);
            Route::get('/{event_id}', [EventController::class, 'show']);
            Route::delete('/{event_id}', [EventController::class, 'destroy']);
        });
    });
});


Route::get(
    '/test',
    function () {
        return 'test';
    }
);
