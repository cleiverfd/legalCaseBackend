<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\JudicialDistrictController;
use App\Http\Controllers\ProceedingController;
use App\Http\Controllers\LawyerController;

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
        Route::get('/', [LawyerController::class, 'index'])->name('lawyer.index');
        Route::post('/show', [LawyerController::class, 'show'])->name('lawyer.show');
        Route::post('/store', [LawyerController::class, 'store'])->name('lawyer.store');
        Route::post('/update', [LawyerController::class, 'update'])->name('lawyer.update');
        Route::post('/delete/{id}', [LawyerController::class, 'destroy'])->name('lawyer.destroy');

        // Audiencias por abogado
        Route::post('/audiencias', [LawyerController::class, 'audiencias'])->name('lawyer.audiencias');

        // Alertas por abogado
        Route::post('/alertas', [LawyerController::class, 'alertas'])->name('lawyer.alertas');

        // Expedientes por abogado
        Route::post('/expedientes', [LawyerController::class, 'expedientes'])->name('lawyer.expedientes');

        Route::post('/changeOfLawyer', [LawyerController::class, 'changeOfLawyer'])->name('lawyer.changeOfLawyer');
    });

    // Expedientes
    Route::prefix('proceeding')->group(function () {
        Route::get('/', 'App\Http\Controllers\ProceedingController@index')->name('proceeding.index');
        Route::get('/{id}', 'App\Http\Controllers\ProceedingController@show')->name('proceeding.show');
        Route::get('/{id}/show', 'App\Http\Controllers\ProceedingController@showupdate')->name('proceeding.showupdate');
        Route::post('/take', 'App\Http\Controllers\ProceedingController@take')->name('proceeding.take');
        Route::post('/update', 'App\Http\Controllers\ProceedingController@update')->name('proceeding.update');
        Route::post('/registrarcaso', 'App\Http\Controllers\ProceedingController@registrarcaso')->name('proceeding.registrarcaso');
        Route::post('/listarestado', 'App\Http\Controllers\ProceedingController@listarestado')->name('proceeding.listarestado');
        Route::post('/buscarPorId', 'App\Http\Controllers\ProceedingController@buscarPorId')->name('proceeding.buscarPorId');
        Route::post('/filterprocesal', 'App\Http\Controllers\ProceedingController@filterprocesal')->name('proceeding.filterprocesal');       
    });

    //  Distritos Judiciales
    Route::prefix('judicialdistrict')->group(function () {
        Route::get('/', [JudicialDistrictController::class, 'index'])->name('judicialdistrict.index');
        Route::get('/show', [JudicialDistrictController::class, 'show'])->name('judicialdistrict.show');
        Route::post('/store', [JudicialDistrictController::class, 'store'])->name('judicialdistrict.store');
        Route::put('/update', [JudicialDistrictController::class, 'update'])->name('judicialdistrict.update');
        Route::delete('/destroy', [JudicialDistrictController::class, 'destroy'])->name('judicialdistrict.destroy');
    });
    //instancias
    Route::prefix('instance')->group(function () {
        Route::get('/', 'App\Http\Controllers\InstanceController@index')->name('Instance.index');
        Route::post('/show', 'App\Http\Controllers\InstanceController@show')->name('Instance.show');
        Route::post('/store', 'App\Http\Controllers\InstanceController@store')->name('Instance.store');
        Route::post('/update', 'App\Http\Controllers\InstanceController@update')->name('Instance.update');
        Route::post('/destroy', 'App\Http\Controllers\InstanceController@destroy')->name('Instance.eliminar');
    });
    //especialidades
    Route::prefix('specialty')->group(function () {
        Route::get('/', 'App\Http\Controllers\SpecialtyController@index')->name('specialty.index');
        Route::post('/show', 'App\Http\Controllers\SpecialtyController@show')->name('specialty.show');
        Route::post('/store', 'App\Http\Controllers\SpecialtyController@store')->name('specialty.registrar');
        Route::post('/update', 'App\Http\Controllers\SpecialtyController@update')->name('specialty.update');
        Route::post('/destroy', 'App\Http\Controllers\SpecialtyController@destroy')->name('specialty.eliminar');
    });

    // Demandantes
    Route::prefix('demandante')->group(function () {
        Route::post('/', 'App\Http\Controllers\PersonController@index')->name('demandante.index');
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
        Route::post('/distritos', 'App\Http\Controllers\ReportController@distritos')->name('reportes.distritos');
    });
    // Generacion de Reportes  pdf
    Route::prefix('reportespfd')->group(function () {
        Route::get('/pdfabogados', 'App\Http\Controllers\ReportController@pdfabogados')->name('reportes.pdfabogados');
        Route::get('/pdfexptramite', 'App\Http\Controllers\ReportController@pdfexptramite')->name('reportes.pdfexptramite');
        Route::get('/pdfexpejecucion', 'App\Http\Controllers\ReportController@pdfexpejecucion')->name('reportes.pdfexpejecucion');
        Route::get('/pdfexps', 'App\Http\Controllers\ReportController@pdfexps')->name('reportes.pdfexps');
        Route::get('/pdfdemandantes', 'App\Http\Controllers\ReportController@pdfdemandantes')->name('reportes.pdfdemandantes');
        Route::get('/pdffechaaño', 'App\Http\Controllers\ReportController@pdffechaaño')->name('reportes.pdffechaaño');
        Route::get('/pdfmateria', 'App\Http\Controllers\ReportController@pdfmateria')->name('reportes.pdfmateria');
        Route::get('/pdfexpsabogado', 'App\Http\Controllers\ReportController@pdfexpsabogado')->name('reportes.pdfexpsabogado');
        Route::get('/pdfpretensiones', 'App\Http\Controllers\ReportController@pdfpretenciones')->name('reportes.pdfpretenciones');
        Route::get('/pdfejecuciones', 'App\Http\Controllers\ReportController@pdfejecuciones')->name('reportes.pdfejecuciones');
        Route::get('/pdfpretension', 'App\Http\Controllers\ReportController@pdfpretension')->name('reportes.pdfpretension');
        Route::get('/pdffechas', 'App\Http\Controllers\ReportController@pdffechas')->name('reportes.pdffechas');
        Route::get('/pdfdistrito', 'App\Http\Controllers\ReportController@pdfdistrito')->name('reportes.pdfdistrito');
    });

    // Audiencias
    Route::prefix('audiences')->group(function () {
        Route::get('/', 'App\Http\Controllers\AudienceController@index')->name('audiences.index');
        Route::post('/store', 'App\Http\Controllers\AudienceController@store')->name('audiences.store');
    });
    //guardar archivos
    Route::prefix('cargar')->group(function () {
        Route::post('/archivo', 'App\Http\Controllers\ArchivosController@pdfprincipal')->name('cargar.pdfprincipal');
        Route::post('/archivo/eje', 'App\Http\Controllers\ArchivosController@eje')->name('cargar.eje');
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
    Route::prefix('alerta')->group(function () {
        Route::get('/', 'App\Http\Controllers\AlertController@index')->name('alerta.index');
        Route::post('/store', 'App\Http\Controllers\AlertController@store')->name('mensajes.store');
    });
    //calendario
    Route::prefix('calendario')->group(function () {
        Route::get('/', 'App\Http\Controllers\CalendarioController@index')->name('calendario.index');
    });
    //Juzgados
    Route::prefix('juzgado')->group(function () {
        Route::post('/', 'App\Http\Controllers\CourtController@index')->name('juzgado.index');
        Route::post('/store', 'App\Http\Controllers\CourtController@store')->name('juzgado.store');
        Route::post('/destroy', 'App\Http\Controllers\CourtController@destroy')->name('juzgado.destroy');
        Route::post('/update', 'App\Http\Controllers\CourtController@update')->name('juzgado.update');
        Route::post('/favorite', 'App\Http\Controllers\CourtController@favorite')->name('juzgado.favorite');
    });
    //materias
    Route::prefix('subject')->group(function () {
        Route::get('/', 'App\Http\Controllers\SubjectController@index')->name('subject.index');
        Route::post('/show', 'App\Http\Controllers\SubjectController@show')->name('subject.show');
        Route::post('/store', 'App\Http\Controllers\SubjectController@registrar')->name('subject.registrar');
        Route::post('/update', 'App\Http\Controllers\SubjectController@update')->name('subject.update');
        Route::post('/destroy', 'App\Http\Controllers\SubjectController@eliminar')->name('subject.eliminar');
    });

    //pretensiones
    Route::prefix('claim')->group(function () {
        Route::get('/', 'App\Http\Controllers\ClaimController@index')->name('claim.index');
        Route::get('/', 'App\Http\Controllers\ClaimController@index')->name('claim.index');
        Route::post('/show', 'App\Http\Controllers\ClaimController@show')->name('claim.show');
        Route::post('/store', 'App\Http\Controllers\ClaimController@registrar')->name('claim.registrar');
        Route::post('/update', 'App\Http\Controllers\ClaimController@update')->name('claim.update');
        Route::post('/destroy', 'App\Http\Controllers\ClaimController@eliminar')->name('claim.eliminar');
    });
    Route::prefix('mail')->group(function () {
        Route::post('/', 'App\Http\Controllers\MailController@mail')->name('mail.mail');
    });
});
