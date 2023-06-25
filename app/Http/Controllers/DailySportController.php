<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sport;
use App\Models\DailySport;
use Illuminate\Http\Request;
use App\Models\PersonalDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DailySportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mendapatkan ID pengguna yang sedang login
        $user_id = Auth::id();
    
        // Mendapatkan data breakfast berdasarkan user_id dan created_at hari ini
        $dailySports = DailySport::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();
    
        return response()->json([
            'success' => true,
            'message' => 'Daily Sport data retrieved successfully',
            'data' => $dailySports,
        ], 200);
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
            'sport_id' => 'required',
            'durasi' => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Mendapatkan user yang sedang login
        $user_id = Auth::id();

        // Mendapatkan data makanan berdasarkan food_id
        $sport = Sport::findOrFail($request->input('sport_id'));
    
        $durasi = $request->input('durasi');
        list($jam, $menit, $detik) = explode(':', $durasi);
    
        $jam = (int)$jam;
        $menit = (int)$menit;
        $detik = (int)$detik;
    
        $totalDetik = ($jam * 3600) + ($menit * 60) + $detik;
        $jamDecimal = $totalDetik / 3600;
    
        $kalori = $jamDecimal * $sport->kalori;
    
        // Pembulatan hasil kalori menjadi dua desimal
        $kalori = round($kalori, 2);

        // dd($sport);
    
        // Menyimpan data dailySport baru
        $dailySport = DailySport::create([
            'user_id' => $user_id,
            'sport_id' => $request->input('sport_id'),
            'durasi' => $durasi,
            'kalori' => $kalori,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Daily Sport data created successfully',
            'data' => $dailySport,
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
        // Mencari data Dailysport berdasarkan ID
        $dailysport = DailySport::find($id);

        // Jika data tidak ditemukan, kirimkan respons error
        if (!$dailysport) {
            return response()->json([
                'success' => false,
                'message' => 'Dailysport data not found',
            ], 404);
        }
    
        // Menghapus data Dailysport
        $dailysport->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Dailysport data deleted successfully',
        ], 200);
    }
}
