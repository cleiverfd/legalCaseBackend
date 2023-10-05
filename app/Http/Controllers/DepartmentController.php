<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Resources\{
    DepartmentResource
};
use Uuid;

class DepartmentController extends Controller
{
    //contructor
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Obtener todos los datos
    protected function index(Request $request)
    {
        $department = \App\Models\Department::OrderBy('created_at', 'DESC')->get();
        $data = DepartmentResource::collection($department);

        return \response()->json(['data' => $data], 200);
    }
    protected function show($id)
    {
        $department = \App\Models\Department::select('dep_id', 'dep_nombre')->find($id);
        return $department;
    }
    protected function provincias(Request $request)
    {
        $provinces = \App\Models\Province::where('dep_id', $request->dep_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        return \response()->json(['data' => $provinces], 200);
    }
    protected function distritos(Request $request)
    {
        $districts = \App\Models\District::where('pro_id', $request->pro_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        return \response()->json(['data' => $districts], 200);
    }
}
