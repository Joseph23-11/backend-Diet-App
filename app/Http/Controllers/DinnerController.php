<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Dinner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DinnerController extends Controller
{
    public function index()
    {
        // Mendapatkan ID pengguna yang sedang login
        $user_id = Auth::id();

        // Mendapatkan data dinner berdasarkan user_id dan created_at hari ini
        $dinners = Dinner::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Dinner data retrieved successfully',
            'data' => $dinners,
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

        // Menyimpan data dinner baru
        $dinner = Dinner::create([
            'user_id' => $user_id,
            'food_id' => $request->input('food_id'),
            'porsi_makanan' => $porsi_makanan,
            'kalori' => $kalori,
            'protein' => $protein,
            'lemak' => $lemak,
            'karbohidrat' => $karbohidrat,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dinner data created successfully',
            'data' => $dinner,
        ], 201);
    }

    public function destroy($id)
    {
        // Mencari data Dinner berdasarkan ID
        $dinner = Dinner::find($id);

        // Jika data tidak ditemukan, kirimkan respons error
        if (!$dinner) {
            return response()->json([
                'success' => false,
                'message' => 'Dinner data not found',
            ], 404);
        }

        // Menghapus data Dinner
        $dinner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dinner data deleted successfully',
        ], 200);
    }
}
