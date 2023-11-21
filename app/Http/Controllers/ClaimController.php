<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\Request;

class ClaimController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $pretensiones = Claim::orderBy('created_at', 'DESC')->get();
        
        return response()->json(['data' => $pretensiones], 200);
    }

   
    public function store(Request $request)
    {
        
    }

    public function show(Request $request)
    {
        
    }

    public function update(Request $request)
    {
       
    }
    public function destroy(Request $request)
    {
      
    }
}
