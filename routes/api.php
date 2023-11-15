<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\JudicialDistrictController;
use App\Http\Controllers\ProceedingController;

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
        Route::post('/show', 'App\Http\Controllers\LawyerController@show')->name('lawyer.show');
        Route::post('/registrar', 'App\Http\Controllers\LawyerController@registrar')->name('lawyer.registrar');
        Route::post('/update', 'App\Http\Controllers\LawyerController@update')->name('lawyer.update');
        Route::post('eliminar/{id}', 'App\Http\Controllers\LawyerController@eliminar')->name('lawyer.eliminar');
       //audiencias  por abogado
        Route::post('/audiencias', 'App\Http\Controllers\LawyerController@audiencias')->name('lawyer.audiencias');
        //alertas por abogado
        Route::post('/alertas', 'App\Http\Controllers\LawyerController@alertas')->name('lawyer.alertas');
        //expedientes por abogado
        Route::post('/expedientes', 'App\Http\Controllers\LawyerController@expedientes')->name('lawyer.expedientes');
    });

    // Expedientes
    Route::prefix('proceeding')->group(function () {
        Route::get('/', 'App\Http\Controllers\ProceedingController@index')->name('proceeding.index');
        Route::get('/{id}', 'App\Http\Controllers\ProceedingController@show')->name('proceeding.show');
        Route::get('/{id}/show', 'App\Http\Controllers\ProceedingController@showupdate')->name('proceeding.showupdate');
        Route::post('/update', 'App\Http\Controllers\ProceedingController@update')->name('proceeding.update');
        Route::post('/registrarcaso', 'App\Http\Controllers\ProceedingController@registrarcaso')->name('proceeding.registrarcaso');
        Route::post('/listarestado', 'App\Http\Controllers\ProceedingController@listarestado')->name('proceeding.listarestado');
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
    Route::prefix('demandado')->group(function () {
        Route::get('/', 'App\Http\Controllers\PersonController@indexdemandados')->name('demandado.indexdemandados');
        Route::get('/detalledemandado/{doc}', 'App\Http\Controllers\PersonController@detalledemandado')->name('demandado.detalledemandado');
        Route::get('/historial/{doc}', 'App\Http\Controllers\PersonController@getHistoryByDocument')->name('demandado.gethistorybydocument');
        Route::post('/expedientes', 'App\Http\Controllers\PersonController@traerexpedientesDemandado')->name('demandado.traerexpedientesDemandado');
    
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
        Route::post('/exprecientes', 'App\Http\Controllers\ReportController@exprecientes')->name('reportes.exprecientes');
        Route::get('/pdfabogados', 'App\Http\Controllers\ReportController@pdfabogados')->name('reportes.pdfabogados');
        Route::get('/pdfexptramite', 'App\Http\Controllers\ReportController@pdfexptramite')->name('reportes.pdfexptramite');
        Route::get('/pdfexpejecucion', 'App\Http\Controllers\ReportController@pdfexpejecucion')->name('reportes.pdfexpejecucion');
        Route::get('/pdfexps', 'App\Http\Controllers\ReportController@pdfexps')->name('reportes.pdfexps');
        Route::get('/pdfdemandantes', 'App\Http\Controllers\ReportController@pdfdemandantes')->name('reportes.pdfdemandantes');
        Route::get('/pdffechaaño', 'App\Http\Controllers\ReportController@pdffechaaño')->name('reportes.pdffechaaño');
        Route::get('/pdfmateria', 'App\Http\Controllers\ReportController@pdfmateria')->name('reportes.pdfmateria');
    });

    // Audiencias
    Route::prefix('audiences')->group(function () {
        Route::get('/', 'App\Http\Controllers\AudienceController@index')->name('audiences.index');
        Route::post('/store', 'App\Http\Controllers\AudienceController@store')->name('audiences.store');
    });
    //guardar archivos
    Route::prefix('cargar')->group(function () {
        Route::post('/archivo', 'App\Http\Controllers\ArchivosController@pdfprincipal')->name('cargar.pdfprincipal');
    });
    //llevar excel a la Bd
    Route::prefix('excel')->group(function () {
        Route::post('/cargar', 'App\Http\Controllers\ExcelController@index')->name('excel.index');
    });
    Route::prefix('traer')->group(function () {
        Route::get('/archivo', 'App\Http\Controllers\ArchivosController@traerpdfprincipal')->name('traer.traerpdfprincipal');
    });

    //mandar mensajes  a celular
    Route::prefix('mensajes')->group(function () {
        Route::get('/', 'App\Http\Controllers\WhatsappController@index')->name('mensajes.index');
    });

    //Alertas
    Route::prefix('alerta')->group(function(){
        Route::get('/', 'App\Http\Controllers\AlertController@index')->name('alerta.index');
        Route::post('/store', 'App\Http\Controllers\AlertController@store')->name('mensajes.store');
    });
     //calendario
     Route::prefix('calendario')->group(function(){
        Route::get('/', 'App\Http\Controllers\CalendarioController@index')->name('calendario.index');
    });
    //Juzgados
    Route::prefix('juzgado')->group(function(){
        Route::get('/', 'App\Http\Controllers\CourtController@index')->name('juzgado.index');
        Route::post('/store', 'App\Http\Controllers\CourtController@store')->name('juzgado.store');
        Route::post('/destroy', 'App\Http\Controllers\CourtController@destroy')->name('juzgado.destroy');
    
    });
});
