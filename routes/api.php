<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\JudicialDistrictController;
use App\Http\Controllers\ProceedingController;

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

Route::middleware(['auth:api'])->group(function () {

    // Departamentos
    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
        Route::get('/{id}/show', [DepartmentController::class, 'show'])->name('department.show');
        Route::post('/provincias', [DepartmentController::class, 'provincias'])->name('department.provincias');
        Route::post('/distritos', [DepartmentController::class, 'distritos'])->name('department.distritos');
    });

    // Abogados
    Route::prefix('lawyer')->group(function () {
        Route::get('/', 'App\Http\Controllers\LawyerController@index')->name('lawyer.index');
        Route::get('/{id}/show', 'App\Http\Controllers\LawyerController@show')->name('lawyer.show');
        Route::post('/registrar', 'App\Http\Controllers\LawyerController@registrar')->name('lawyer.registrar');
    });

    // Expedientes
    Route::prefix('proceeding')->group(function () {
        Route::get('/', 'App\Http\Controllers\ProceedingController@index')->name('proceeding.index');
        Route::get('/{id}', 'App\Http\Controllers\ProceedingController@show')->name('proceeding.show');
        Route::post('/registrarcaso', 'App\Http\Controllers\ProceedingController@registrarcaso')->name('proceeding.registrarcaso');
    });

    //  Distritos Judiciales
    Route::prefix('judicialdistrict')->group(function () {
        Route::get('/', [JudicialDistrictController::class, 'index'])->name('judicialdistrict.index');

        Route::post('/instancias', [JudicialDistrictController::class, 'instancia'])->name('judicialdistrict.instancia');

        Route::post('/especialidades', [JudicialDistrictController::class, 'especialidad'])->name('judicialdistrict.especilidad');
    });

    // Demandantes
    Route::prefix('demandante')->group(function () {
        Route::get('/', 'App\Http\Controllers\PersonController@index')->name('demandante.index');
        Route::get('/detalledemandante/{doc}', 'App\Http\Controllers\PersonController@detalledemandante')->name('demandante.detalledemandante');
        Route::post('/expedientes', 'App\Http\Controllers\PersonController@traerexpedientes')->name('demandante.traerexpedientes');

        // Nuevas rutas para obtener información por documento
        Route::get('/direccion/{doc}', 'App\Http\Controllers\PersonController@getAddressByDocument')->name('demandante.getaddressbydocument');
        Route::get('/historial/{doc}', 'App\Http\Controllers\PersonController@getHistoryByDocument')->name('demandante.gethistorybydocument');
        Route::get('/pagos/{doc}', 'App\Http\Controllers\PersonController@getPaymentsByDocument')->name('demandante.getpaymentsbydocument');
    });


    // Historial de Comunicaciones
    Route::prefix('history')->group(function () {
        Route::get('/', 'App\Http\Controllers\HistoryController@index')->name('history.index');
        Route::post('/store', 'App\Http\Controllers\HistoryController@store')->name('history.store');
        Route::get('data/{doc}', 'App\Http\Controllers\HistoryController@data')->name('history.data');
    });

    // Historial de Pagos
    Route::prefix('payment')->group(function () {
        Route::get('/', 'App\Http\Controllers\PaymentController@index')->name('payment.index');
        Route::post('/store', 'App\Http\Controllers\PaymentController@store')->name('payment.store');
    });

    // Generacion de Reportes
    Route::prefix('reportes')->group(function () {
        Route::post('/inicio', 'App\Http\Controllers\ReportController@inicio')->name('reportes.inicio');
        Route::get('/exprecientes', 'App\Http\Controllers\ReportController@getRecentProceedings')->name('reportes.getRecentProceedings');
        Route::get('/pdfabogados', 'App\Http\Controllers\ReportController@pdfabogados')->name('reportes.pdfabogados');
        Route::get('/pdfexptramite', 'App\Http\Controllers\ReportController@pdfexptramite')->name('reportes.pdfexptramite');
        Route::get('/pdfexpejecucion', 'App\Http\Controllers\ReportController@pdfexpejecucion')->name('reportes.pdfexpejecucion');
        Route::get('/pdfexps', 'App\Http\Controllers\ReportController@pdfexps')->name('reportes.pdfexps');
        Route::get('/pdfdemandantes', 'App\Http\Controllers\ReportController@pdfdemandantes')->name('reportes.pdfdemandantes');
    });

    // Audiencias
    Route::prefix('audiences')->group(function () {
        Route::get('/', 'App\Http\Controllers\AudienceController@index')->name('audiences.index');
        Route::post('/store', 'App\Http\Controllers\AudienceController@store')->name('audiences.store');
    });
});
