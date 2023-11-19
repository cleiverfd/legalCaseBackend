<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JudicialDistrict;
use App\Models\Instance;
use App\Models\Specialty;
use Illuminate\Support\Facades\Log;

class JudicialDistrictController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $judicialDistricts =JudicialDistrict::orderBy('created_at', 'DESC')
            ->get(['judis_id', 'judis_nombre']);

        return response()->json(['data' => $judicialDistricts], 200);
    }


    public function instancia(Request $request)
    {
        try {
            // $request->validate([
            //     'judis_id' => 'required|exists:judicial_districts,judis_id',
            // ]);

            $instances = Instance::where('judis_id', $request->judis_id)
                ->orderBy('created_at', 'DESC')
                ->get(['ins_id','ins_nombre','judis_id']);

            return response()->json(['data' => $instances], 200);
        } catch (\Exception $e) {
            Log::error('Error en JudicialDistrictController: ' . $e->getMessage());
            return response()->json(['error' => 'Ha ocurrido un error en el servidor'], 500);
        }
    }


    public function especialidad(Request $request)
    {
        // $request->validate([
        //     'ins_id' => 'required|exists:instances,id',
        // ]);

        $specialties = Specialty::where('ins_id', $request->ins_id)
            ->orderBy('created_at', 'DESC')
            ->get(['esp_id','esp_nombre','ins_id']);

        return response()->json(['data' => $specialties], 200);
    }
    //CRUD
    protected function show(Request $request)
    {
        $JD = JudicialDistrict::where('judis_id', $request->judis_id)->first();
        return \response()->json(['data' => $JD], 200);
    }

    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();
           $JD = JudicialDistrict::create([
                'judis_nombre' => strtoupper(trim($request->judis_nombre)),
            ]);
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $JD], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $JD = JudicialDistrict::find($request->judis_id);
            $JD->judis_nombre = strtoupper(trim($request->judis_nombre));
            $JD->save();
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => 'actulizado correcto'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function eliminar(Request $request)
    {
        try {
            \DB::beginTransaction();

            $JD = JudicialDistrict::find($request->judis_id);
            $JD->delete();
            \DB::commit();

            return \response()->json(['state'=>0,'data' => 'eliminado'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return \response()->json(['message' => 'Error al eliminar ', 'exception' => $e->getMessage()], 500);
        }
    }
}
