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
/*DEPARTAMENTOS*/
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('department')->group(function () {
        Route::get('/', 'App\Http\Controllers\DepartmentController@index')->name('department.index');
         Route::get('/{id}/show', 'App\Http\Controllers\DepartmentController@show')->name('department.show');
       
    });
    /*ABOGRADOS*/ 
    Route::prefix('lawyer')->group(function () {
        Route::get('/', 'App\Http\Controllers\LawyerController@index')->name('lawyer.index');
        Route::get('/{id}/show', 'App\Http\Controllers\LawyerController@show')->name('lawyer.show');
    });
    Route::prefix('proceeding')->group(function () {
        Route::post('/datosgeneralesexpediente', 'App\Http\Controllers\ProceedingController@datosgeneralesexpediente')->name('proceeding.datosgeneralesexpediente');
        Route::post('/parteprocesal', 'App\Http\Controllers\ProceedingController@parteprocesal')->name('proceeding.parteprocesal');
        Route::post('/asignarabogado', 'App\Http\Controllers\ProceedingController@asignarabogado')->name('proceeding.asignarabogado');
        
        
    });
});