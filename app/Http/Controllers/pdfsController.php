<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Uuid;
use PDF;
class pdfsController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');
    }
    protected function expedientestramite(Request $request)
    {
        return response()->json(['state' => 0]);  
    }
}
