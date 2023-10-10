<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Uuid;

class JudicialDistrictController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(Request $request)
    {
        $judicial = \App\Models\JudicialDistrict::OrderBy('created_at', 'DESC')->get();
        

        return \response()->json(['data' => $judicial], 200);
    }
    protected function instancia(Request $request)
    {
        $instances = \App\Models\Instance::where('judis_id', $request->judis_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        return \response()->json(['data' => $instances], 200);
    }
    protected function especialidad(Request $request)
    {
        $specialties = \App\Models\Specialty::where('ins_id', $request->ins_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        return \response()->json(['data' => $specialties], 200);
    }
}
