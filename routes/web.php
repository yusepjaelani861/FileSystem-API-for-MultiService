<?php

use App\Http\Controllers\APIController;
use App\Models\Resize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/c/{file_id}', function ($file_id, Request $request) {
    $resize = Resize::where('file_id', $file_id)->where('width', $request->input('width'))->first();

    return $resize;
});