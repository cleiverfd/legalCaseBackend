<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Uuid;
class ArchivosController extends Controller
{   public function __construct()
    {
        $this->middleware('auth');
    }
    public function pdfprincipal(Request $request) {
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('/public/files/', $fileName);
    
        return response()->json(['message' => 'Archivo cargado con éxito','file'=>$fileName]);
    }
    public function traerpdfprincipal(Request $request) {
        $rutaArchivo = storage_path('app/public/files/' . $request->nombre);
        return response()->download($rutaArchivo);
    }
}
