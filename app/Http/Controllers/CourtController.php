<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Uuid;

class CourtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index()
    {
        try {
            $juzgados =\App\Models\Court::orderBy('created_at', 'DESC')->get();;
            return response()->json(['state' => 0, 'data' => $juzgados], 200);
        } catch (\Exception $e) {
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }


    protected function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $court = \App\Models\Court::create([
                'co_nombre' =>  strtoupper(trim($request->co_nombre)),
            ]);
            DB::commit();
            return response()->json(['state' => 0, 'data' => $court], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }
}
