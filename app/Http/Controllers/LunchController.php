<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Lunch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LunchController extends Controller
{
    public function index()
    {
        // Mendapatkan ID pengguna yang sedang login
        $user_id = Auth::id();

        // Mendapatkan data lunch berdasarkan user_id dan created_at hari ini
        $lunches = Lunch::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

        // Loop melalui setiap objek lunch
        foreach ($lunches as $lunch) {
            // Mendapatkan data makanan berdasarkan food_id
            $food = Food::findOrFail($lunch->food_id);
            
            // Menambahkan nama makanan ke objek lunch
            $lunch->nama_makanan = $food->nama_makanan;
        }

        return response()->json([
            'success' => true,
            'message' => 'Lunch data retrieved successfully',
            'data' => $lunches,
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

        // Menyimpan data lunch baru
        $lunch = Lunch::create([
            'user_id' => $user_id,
            'food_id' => $request->input('food_id'),
            'porsi_makanan' => $porsi_makanan,
            'kalori' => $kalori,
            'protein' => $protein,
            'lemak' => $lemak,
            'karbohidrat' => $karbohidrat,
        ]);

        // Mendapatkan nama makanan terkait
        $lunch->nama_makanan = $food->nama_makanan;

        return response()->json([
            'success' => true,
            'message' => 'Lunch data created successfully',
            'data' => $lunch,
        ], 201);
    }

    public function destroy($id)
    {
        // Mencari data Lunch berdasarkan ID
        $lunch = Lunch::find($id);

        // Jika data tidak ditemukan, kirimkan respons error
        if (!$lunch) {
            return response()->json([
                'success' => false,
                'message' => 'Lunch data not found',
            ], 404);
        }

        // Menghapus data Lunch
        $lunch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lunch data deleted successfully',
        ], 200);
    }
}
