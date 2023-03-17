<?php

namespace App\Http\Controllers;

use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TargetController extends Controller
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
        'level_aktivitas' => 'required|in:0,1,2,3',
        'target_berat_badan' => 'required|numeric',
        'target_diet' => 'required|in:lambat,normal,cepat',
        'target_waktu_diet' => 'required|integer',
        'kebutuhan_kalori_harian' => 'required|numeric',
        'target_kalori_harian' => 'required|numeric',
        'total_pengurangan_berat' => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors(),
        ], 422);
    }

    $target = Target::create([
        'user_id' => Auth::id(),
        'level_aktivitas' => $request->input('level_aktivitas'),
        'target_berat_badan' => $request->input('target_berat_badan'),
        'target_diet' => $request->input('target_diet'),
        'target_waktu_diet' => $request->input('target_waktu_diet'),
        'kebutuhan_kalori_harian' => $request->input('kebutuhan_kalori_harian'),
        'target_kalori_harian' => $request->input('target_kalori_harian'),
        'total_pengurangan_berat' => $request->input('total_pengurangan_berat'),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Target created successfully',
        'data' => $target,
    ], 201);
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
