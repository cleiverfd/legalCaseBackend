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
      protected function index(Request $request){
          $department= \App\Models\Department::OrderBy('created_at','DESC')->get();
          $data = DepartmentResource::collection($department);
  
          return \response()->json(['data'=>$data],200);
      }
  
      //Insertar datos
    //   protected function store(Request $request){
    //       try{
    //           \DB::beginTransaction();
    //           $category=\App\Models\Category::create([
    //               'name' => strtoupper(trim($request->name)),
    //               'state' => 1,
    //           ]);
    //           $data[]=$category;
    //           $resp=CategoriesResource::collection($data);
    //           \DB::commit();
    //           return \response()->json(['state'=>0,'data'=>$resp],200);
    //       }catch(Exception $e){
    //           \DB::rollback();
    //           return ['state' => '1', 'exception' => (string) $e];
  
    //       }
  
    //   }
  
      //Ver un dato en especifico
      protected function show($id){
          $department = \App\Models\Department::select('dep_id','dep_nombre')->find($id);
          return $department;
      }
  
      //Editar
    //   protected function update(Request $request){
    //       try{
    //           \DB::beginTransaction();
    //       $category=\App\Models\Category::find($request->id);
    //       $category->name = strtoupper(trim($request->name));
    //       $category->state = $request->state; 
    //       $category->save();
    //       $data[]=$category;
    //       \DB::commit();
    //           return \response()->json(['state'=>0,'data'=> CategoriesResource::collection($data)],200);
    //       }catch(Exception $e){
    //           \DB::rollback();
    //           return ['state' => '1', 'exception' => (string) $e];
    //       }
    //   }
      
      //funcion para el eliminado lógico
    //   protected function destroy(Request $request){
    //       try{
    //      \DB::beginTransaction();
    //      $category=\App\Models\Category::where('id',$request->id)->delete();
    //      \DB::commit();
    //      return \response()->json($request->id,200);
  
    //      }catch(Exception $e){
    //          \DB::rollback();
    //          return ['state' => '1', 'exception' => (string) $e];
    //      }
    //  }
  
}
