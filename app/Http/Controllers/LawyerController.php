<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{
    LawyerResource
};
use Uuid;

class LawyerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Obtener todos los datos
    protected function index(Request $request){
        $Lawyer= \App\Models\Lawyer::OrderBy('created_at','DESC')->with('persona')->get();
        // $data = LawyerResource::collection($Lawyer);

        return \response()->json(['data'=>$Lawyer],200);
    }   
}
