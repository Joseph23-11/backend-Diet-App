<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Snack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SnackController extends Controller
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

        // Mendapatkan data snack berdasarkan user_id dan created_at hari ini
        $snacks = Snack::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

        // Loop melalui setiap objek snack
        foreach ($snacks as $snack) {
            // Mendapatkan data makanan berdasarkan food_id
            $food = Food::findOrFail($snack->food_id);
            
            // Menambahkan nama makanan ke objek snack
            $snack->nama_makanan = $food->nama_makanan;
        }

        return response()->json([
            'success' => true,
            'message' => 'Snack data retrieved successfully',
            'data' => $snacks,
        ], 200);
    }

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

        // Menyimpan data snack baru
        $snack = Snack::create([
            'user_id' => $user_id,
            'food_id' => $request->input('food_id'),
            'porsi_makanan' => $porsi_makanan,
            'kalori' => $kalori,
            'protein' => $protein,
            'lemak' => $lemak,
            'karbohidrat' => $karbohidrat,
        ]);

        // Mendapatkan nama makanan terkait
        $snack->nama_makanan = $food->nama_makanan;

        return response()->json([
            'success' => true,
            'message' => 'Snack data created successfully',
            'data' => $snack,
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
        // Mencari data Snack berdasarkan ID
        $snack = Snack::find($id);

        // Jika data tidak ditemukan, kirimkan respons error
        if (!$snack) {
            return response()->json([
                'success' => false,
                'message' => 'Snack data not found',
            ], 404);
        }

        // Menghapus data Snack
        $snack->delete();

        return response()->json([
            'success' => true,
            'message' => 'Snack data deleted successfully',
        ], 200);
    }
}
