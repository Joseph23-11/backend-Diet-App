<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PersonalDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PersonalDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_kelamin' => 'required|in:pria,wanita',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'usia' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        $personalDetails = PersonalDetail::create([
            'user_id' => Auth::id(),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'berat_badan' => $request->input('berat_badan'),
            'tinggi_badan' => $request->input('tinggi_badan'),
            'usia' => $request->input('usia'),
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Personal details created successfully',
            'data' => $personalDetails,
        ], 201);
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = User::find(Auth::id());
    
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    
        $personalDetails = $user->personalDetail;
    
        if (!$personalDetails) {
            return response()->json([
                'success' => false,
                'message' => 'Personal details not found',
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $personalDetails,
        ]);
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    
        $personalDetail = $user->personalDetail;
        
        if (!$personalDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Personal detail not found',
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'jenis_kelamin' => 'nullable|in:pria,wanita',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'usia' => 'nullable|numeric',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        if ($request->has('jenis_kelamin')) {
            $personalDetail->jenis_kelamin = $request->input('jenis_kelamin');
        }
        
        if ($request->has('berat_badan')) {
            $personalDetail->berat_badan = $request->input('berat_badan');
        }
        
        if ($request->has('tinggi_badan')) {
            $personalDetail->tinggi_badan = $request->input('tinggi_badan');
        }
        
        if ($request->has('usia')) {
            $personalDetail->usia = $request->input('usia');
        }
        
        $personalDetail->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Personal detail updated successfully',
            'data' => $personalDetail,
        ]);
    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
