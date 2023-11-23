<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class InstanceController extends Controller
{
    
    public function index(Request $request)
    { try {
            $instances = Instance::where('judis_id', $request->judis_id)
                ->orderBy('created_at', 'DESC')
                ->get(['ins_id','ins_nombre','judis_id']);
            return response()->json(['data' => $instances], 200);
        } catch (\Exception $e) {
            Log::error('Error en JudicialDistrictController: ' . $e->getMessage());
            return response()->json(['error' => 'Ha ocurrido un error en el servidor'], 500);
        }
    }
    
    protected function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $ins = \App\Models\Instance::create([   
                'ins_nombre' =>  strtoupper(trim($request->ins_nombre)),
                'judis_id' =>  strtoupper(trim($request->judis_id)),
            ]);
            DB::commit();
            return response()->json(['state' => 0, 'data' => $ins], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }

    protected function update(Request $request){
        try{
            DB::beginTransaction();
            $ins = \App\Models\Instance::find($request->ins_id);
            $ins->ins_nombre = strtoupper(trim($request->ins_nombre));
            $ins->judis_id = strtoupper(trim($request->judis_id));
            $ins->save();
            DB::commit();
            return \response()->json(['state' => 0], 200);

        }catch(Exception $e){
            DB::rollBack();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function destroy(Request $request){
        try {
            DB::beginTransaction();
            $ins = \App\Models\Instance::find($request->ins_id);
            $ins->delete();
           
            DB::commit();
            return \response()->json(['state' => 0, 200]);

        } catch (Exception $e) {
            DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }

    }
}


