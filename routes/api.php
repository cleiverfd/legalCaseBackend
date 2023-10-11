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
Route::prefix('/user')->group(function () {
    Route::post('/login', 'App\Http\Controllers\LoginController@login');
    Route::post('/logout', 'App\Http\Controllers\LoginController@salir');
});
/*DEPARTAMENTOS*/
Route::middleware(['auth:api'])->group(function () {
    Route::prefix('department')->group(function () {
        Route::get('/', 'App\Http\Controllers\DepartmentController@index')->name('department.index');
        Route::get('/{id}/show', 'App\Http\Controllers\DepartmentController@show')->name('department.show');
        Route::post('/provincias', 'App\Http\Controllers\DepartmentController@provincias')->name('department.provincias');
        Route::post('/distritos', 'App\Http\Controllers\DepartmentController@distritos')->name('department.distritos');
    });
    /*ABOGRADOS*/
    Route::prefix('lawyer')->group(function () {
        Route::get('/', 'App\Http\Controllers\LawyerController@index')->name('lawyer.index');
        Route::get('/{id}/show', 'App\Http\Controllers\LawyerController@show')->name('lawyer.show');
        Route::post('/registrar', 'App\Http\Controllers\LawyerController@registrar')->name('lawyer.registrar');
    });
    Route::prefix('proceeding')->group(function () {
        Route::get('/', 'App\Http\Controllers\ProceedingController@index')->name('proceeding.index');

        Route::post('/registrarcaso', 'App\Http\Controllers\ProceedingController@registrarcaso')->name('proceeding.registrarcaso');
        // Route::post('/parteprocesal', 'App\Http\Controllers\ProceedingController@parteprocesal')->name('proceeding.parteprocesal');
        // Route::post('/asignarabogado', 'App\Http\Controllers\ProceedingController@asignarabogado')->name('proceeding.asignarabogado');

        Route::get('/{id}', 'App\Http\Controllers\ProceedingController@show')->name('proceeding.show');

    });
    //  DISTRITOS JUDICIALES
    Route::prefix('judicialdistrict')->group(function () {
        Route::get('/', 'App\Http\Controllers\JudicialDistrictController@index')->name('judicialdistrict.index');
        Route::post('/instancias', 'App\Http\Controllers\JudicialDistrictController@instancia')->name('judicialdistrict.instancia');
        Route::post('/especialidades', 'App\Http\Controllers\JudicialDistrictController@especialidad')->name('judicialdistrict.especilidad');
    });
    //demandantes
    Route::prefix('demandante')->group(function () {
        Route::get('detalledemandante/{doc}', 'App\Http\Controllers\PersonController@detalledemandante')->
        name('demandante.detalledemandante');

    });
});
