<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\{
    PermissionResource
};

class LoginController extends Controller
{
    protected function credentials(Request $request)
    {
        $login = strtoupper($request->input($this->username()));
        $field = 'email';
        return [
            $field     => $login,
            'password' => $request->input('password')
        ];
    }
    public function username()
    {
        return 'login';
    }
    
    public function login(Request $request)
    {
        $credentials = $this->credentials($request);
        if (! Auth::attempt($credentials)) {
            return \response()->json(["message"=>"Usuario y/o contraseña es invalido","state"=>401],200);
        }

        $accesToken = Auth::user()->createToken('authTestToken')->accessToken;
        $user=[
            'id'=> Auth::user()->id,
            'name'=> Auth::user()->name,
            'email'=>Auth::user()->email,
            'token'=> $accesToken
        ];
         return \response()->json([
             'user'=> $user,
            
             'state'=> 200
         ],200);
    }

    public function salir(Request $request)
    {
        // /** cerrar sesio de vigilancia */
        // $session = \App\Models\SurveillanceSession::where('user_id',Auth::user()->id)->first();
        // if($session){
        //     $session->end = date('Y-m-d H:i:s');
        //     $session->state=0;
        //     $session->save();
        // }
        // Auth::user()->token()->revoke();
        // return \response()->json(['state'=>200,'user'=> Auth::user()],200);
    }
}
