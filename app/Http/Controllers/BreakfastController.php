<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Breakfast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BreakfastController extends Controller
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
        $breakfasts = Breakfast::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

        // Loop melalui setiap objek breakfast
        foreach ($breakfasts as $breakfast) {
            // Mendapatkan data makanan berdasarkan food_id
            $food = Food::findOrFail($breakfast->food_id);

            // Menambahkan nama makanan ke objek breakfast
            $breakfast->nama_makanan = $food->nama_makanan;
        }

        return response()->json([
            'success' => true,
            'message' => 'Breakfast data retrieved successfully',
            'data' => $breakfasts,
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
            'food_id'       => 'required',
            'porsi_makanan' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Mendapatkan ID pengguna yang sedang login
        $user_id = Auth::id();

        // Mendapatkan data makanan berdasarkan food_id
        $food = Food::findOrFail($request->input('food_id'));

        // Menghitung nilai kalori, protein, lemak, dan karbohidrat berdasarkan porsi makanan
        $porsi_makanan = $request->input('porsi_makanan');
        $kalori = $food->kalori * $porsi_makanan;
        $protein = $food->protein * $porsi_makanan;
        $lemak = $food->lemak * $porsi_makanan;
        $karbohidrat = $food->karbohidrat * $porsi_makanan;

        // Menyimpan data breakfast baru
        $breakfast = Breakfast::create([
            'user_id' => $user_id,
            'food_id' => $request->input('food_id'),
            'porsi_makanan' => $porsi_makanan,
            'kalori' => $kalori,
            'protein' => $protein,
            'lemak' => $lemak,
            'karbohidrat' => $karbohidrat,
        ]);

        // Mendapatkan nama makanan terkait
        $breakfast->nama_makanan = $food->nama_makanan;

        return response()->json([
            'success' => true,
            'message' => 'Breakfast data created successfully',
            'data' => $breakfast,
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
        $validator = Validator::make($request->all(), [
            'porsi_makanan' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Mencari data Breakfast berdasarkan ID
        $breakfast = Breakfast::find($id);

        // Jika data tidak ditemukan, kirimkan respons error
        if (!$breakfast) {
            return response()->json([
                'success' => false,
                'message' => 'Breakfast data not found',
            ], 404);
        }

        // Mendapatkan data makanan berdasarkan food_id
        $food = Food::findOrFail($breakfast->food_id);

        // Menghitung nilai kalori, protein, lemak, dan karbohidrat berdasarkan porsi makanan
        $porsi_makanan = $request->input('porsi_makanan');
        $kalori = $food->kalori * $porsi_makanan;
        $protein = $food->protein * $porsi_makanan;
        $lemak = $food->lemak * $porsi_makanan;
        $karbohidrat = $food->karbohidrat * $porsi_makanan;

        // Mengupdate data breakfast
        $breakfast->porsi_makanan = $porsi_makanan;
        $breakfast->kalori = $kalori;
        $breakfast->protein = $protein;
        $breakfast->lemak = $lemak;
        $breakfast->karbohidrat = $karbohidrat;
        $breakfast->save();

        // Mendapatkan nama makanan terkait
        $breakfast->nama_makanan = $food->nama_makanan;

        return response()->json([
            'success' => true,
            'message' => 'Breakfast data updated successfully',
            'data' => $breakfast,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Mencari data Breakfast berdasarkan ID
        $breakfast = Breakfast::find($id);

        // Jika data tidak ditemukan, kirimkan respons error
        if (!$breakfast) {
            return response()->json([
                'success' => false,
                'message' => 'Breakfast data not found',
            ], 404);
        }

        // Menghapus data Breakfast
        $breakfast->delete();

        return response()->json([
            'success' => true,
            'message' => 'Breakfast data deleted successfully',
        ], 200);
    }
}
