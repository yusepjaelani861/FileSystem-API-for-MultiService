<?php

use App\Http\Controllers\APIController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

# POST Method
Route::post('/upload', [APIController::class, 'upload']);
Route::post('/rename', [APIController::class, 'rename']);
Route::post('/delete', [APIController::class, 'delete']);


# GET Method
Route::get('/list', [APIController::class, 'list']);
Route::get('/search', [APIController::class, 'search']);
Route::get('/filter', [APIController::class, 'filter']);
Route::get('/download/{file_id}', [APIController::class, 'download']);


Route::get('/image/{file_id}', [APIController::class, 'getImage']);
