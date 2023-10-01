<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::prefix('/user')->group( function (){
    Route::post('/login','App\Http\Controllers\LoginController@login');    
    Route::post('/logout','App\Http\Controllers\LoginController@salir');
});
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('department')->group(function () {
        Route::get('/', 'App\Http\Controllers\DepartmentController@index')->name('department.index');
        // Route::post('/store', 'App\Http\Controllers\DepartmentController@store')->name('department.store');
        Route::get('/{id}/show', 'App\Http\Controllers\DepartmentController@show')->name('department.show');
        // Route::post('/update', 'App\Http\Controllers\DepartmentController@update')->name('department.update');
        // Route::post('/destroy', 'App\Http\Controllers\DepartmentController@destroy')->name('department.destroy');
        
    });
});