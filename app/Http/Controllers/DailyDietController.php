<?php

namespace App\Http\Controllers;

use App\Models\Lunch;
use App\Models\Snack;
use App\Models\Dinner;
use App\Models\Breakfast;
use App\Models\DailySport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyDietController extends Controller
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

        $breakfasts = Breakfast::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();
            
        $lunches = Lunch::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

        $dinners = Dinner::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

        $snacks = Snack::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

        $dailySports = DailySport::where('user_id', $user_id)
            ->whereDate('created_at', today())
            ->get();

            
        $total_kalori_breakfast = $breakfasts->sum('kalori');
        $total_kalori_lunch = $lunches->sum('kalori');
        $total_kalori_dinner = $dinners->sum('kalori');
        $total_kalori_snack = $snacks->sum('kalori');
        $total_kalori_dailySport = $dailySports->sum('kalori');

        // Hitung total kalori harian
        $total_kalori_daily = ($total_kalori_breakfast + $total_kalori_lunch + $total_kalori_dinner + $total_kalori_snack) - $total_kalori_dailySport;

        // dd($total_kalori_daily);

        return response()->json([
            'success' => true,
            'message' => 'Daily Diet data retrieved successfully',
            'data' => [
                'total_kalori_breakfast' => $total_kalori_breakfast,
                'total_kalori_lunch' => $total_kalori_lunch,
                'total_kalori_dinner' => $total_kalori_dinner,
                'total_kalori_snack' => $total_kalori_snack,
                'total_kalori_daily_sport' => $total_kalori_dailySport,
                'total_kalori_daily' => $total_kalori_daily,
            ],
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
