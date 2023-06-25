<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sport;
use Illuminate\Http\Request;
use App\Models\PersonalDetail;
use Illuminate\Support\Facades\Auth;

class SportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mendapatkan user yang sedang login
        $user = User::find(Auth::id());
    
        // Mendapatkan personal detail dari user yang sedang login
        $personalDetail = PersonalDetail::where('user_id', $user->id)->first();
    
        // Menentukan range berat_badan
        if ($personalDetail->berat_badan >= 54 && $personalDetail->berat_badan <= 64) {
            // Range berat 54kg - 64kg
            $sports = Sport::where('berat', 130)->get();
        } elseif ($personalDetail->berat_badan >= 65 && $personalDetail->berat_badan <= 75) {
            // Range berat 65kg - 75kg
            $sports = Sport::where('berat', 155)->get();
        } elseif ($personalDetail->berat_badan >= 75.5 && $personalDetail->berat_badan <= 85.5) {
            // Range berat 75.5kg - 85.5kg
            $sports = Sport::where('berat', 180)->get();
        } elseif ($personalDetail->berat_badan >= 85.6 && $personalDetail->berat_badan <= 98.6) {
            // Range berat 85.6kg - 98.6kg
            $sports = Sport::where('berat', 205)->get();
        } else {
            // Berat_badan tidak masuk dalam range yang ditentukan
            $sports = collect();
        }

    
        return response()->json([
            'success' => true,
            'data' => $sports,
        ]);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
