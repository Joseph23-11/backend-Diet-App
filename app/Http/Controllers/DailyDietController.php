<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Lunch;
use App\Models\Snack;
use App\Models\Sport;
use App\Models\Dinner;
use App\Models\Target;
use App\Models\Breakfast;
use App\Models\DailySport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $total_kalori_breakfast = ceil($breakfasts->sum('kalori'));
        $total_kalori_lunch = ceil($lunches->sum('kalori'));
        $total_kalori_dinner = ceil($dinners->sum('kalori'));
        $total_kalori_snack = ceil($snacks->sum('kalori'));
        $total_kalori_dailySport = ceil($dailySports->sum('kalori'));


        // Mengambil data target hari diet
        $target = Target::where('user_id', $user_id)->first();
        $budget_kalori_harian = $target->budget_kalori_harian;

        // Hitung total kalori harian
        $total_kalori_daily = $budget_kalori_harian + $total_kalori_dailySport - ($total_kalori_breakfast + $total_kalori_lunch + $total_kalori_dinner + $total_kalori_snack);

        // Pastikan total kalori harian tidak lebih kecil dari 0
        if ($total_kalori_daily < 0) {
            $total_kalori_daily = 0;
        }

        // Ubah total kalori menjadi bilangan bulat
        $total_kalori_daily = intval($total_kalori_daily);

        return response()->json([
            'success' => true,
            'message' => 'Daily Diet data retrieved successfully',
            'data' => [
                'breakfasts' => $breakfasts,
                'lunches' => $lunches,
                'dinners' => $dinners,
                'snacks' => $snacks,
                'dailySports' => $dailySports,
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

    /**
     * Display a listing of the resource based on the given date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
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

        // Ambil tanggal dari request
        $date = $request->input('date');

        // Mengubah format tanggal sesuai dengan database (format: Y-m-d)
        $formattedDate = date('Y-m-d', strtotime($date));

        $breakfasts = Breakfast::where('user_id', $user_id)
            ->whereDate('created_at', $formattedDate)
            ->get();

        // Loop melalui setiap objek breakfast
        foreach ($breakfasts as $breakfast) {
            // Mendapatkan data makanan berdasarkan food_id
            $food = Food::findOrFail($breakfast->food_id);

            // Menambahkan nama makanan ke objek breakfast
            $breakfast->nama_makanan = $food->nama_makanan;
        }

        $lunches = Lunch::where('user_id', $user_id)
            ->whereDate('created_at', $formattedDate)
            ->get();

        // Loop melalui setiap objek breakfast
        foreach ($lunches as $lunch) {
            // Mendapatkan data makanan berdasarkan food_id
            $food = Food::findOrFail($lunch->food_id);

            // Menambahkan nama makanan ke objek lunch
            $lunch->nama_makanan = $food->nama_makanan;
        }

        $dinners = Dinner::where('user_id', $user_id)
            ->whereDate('created_at', $formattedDate)
            ->get();

        // Loop melalui setiap objek breakfast
        foreach ($dinners as $dinner) {
            // Mendapatkan data makanan berdasarkan food_id
            $food = Food::findOrFail($dinner->food_id);

            // Menambahkan nama makanan ke objek dinner
            $dinner->nama_makanan = $food->nama_makanan;
        }

        $snacks = Snack::where('user_id', $user_id)
            ->whereDate('created_at', $formattedDate)
            ->get();

        // Loop melalui setiap objek breakfast
        foreach ($snacks as $snack) {
            // Mendapatkan data makanan berdasarkan food_id
            $food = Food::findOrFail($snack->food_id);

            // Menambahkan nama makanan ke objek snack
            $snack->nama_makanan = $food->nama_makanan;
        }

        $dailySports = DailySport::where('user_id', $user_id)
            ->whereDate('created_at', $formattedDate)
            ->get();

        // Loop melalui setiap objek daily sport
        foreach ($dailySports as $dailySport) {
            // Mendapatkan data olahraga berdasarkan sport_id
            $sport = Sport::findOrFail($dailySport->sport_id);

            // Menambahkan nama olahraga ke objek daily sport
            $dailySport->nama_olahraga = $sport->nama_olahraga;
        }

        $total_kalori_breakfast = ceil($breakfasts->sum('kalori'));
        $total_kalori_lunch = ceil($lunches->sum('kalori'));
        $total_kalori_dinner = ceil($dinners->sum('kalori'));
        $total_kalori_snack = ceil($snacks->sum('kalori'));
        $total_kalori_dailySport = ceil($dailySports->sum('kalori'));

        // Mengambil data target hari diet
        $target = Target::where('user_id', $user_id)->first();
        $budget_kalori_harian = $target->budget_kalori_harian;

        // Hitung total kalori harian
        $total_kalori_daily = $budget_kalori_harian + $total_kalori_dailySport - ($total_kalori_breakfast + $total_kalori_lunch + $total_kalori_dinner + $total_kalori_snack);

        // Pastikan total kalori harian tidak lebih kecil dari 0
        if ($total_kalori_daily < 0) {
            $total_kalori_daily = 0;
        }

        // Ubah total kalori menjadi bilangan bulat
        $total_kalori_daily = intval($total_kalori_daily);

        return response()->json([
            'success' => true,
            'message' => 'Daily Diet data retrieved successfully',
            'data' => [
                'breakfasts' => $breakfasts,
                'lunches' => $lunches,
                'dinners' => $dinners,
                'snacks' => $snacks,
                'dailySports' => $dailySports,
                'total_kalori_breakfast' => $total_kalori_breakfast,
                'total_kalori_lunch' => $total_kalori_lunch,
                'total_kalori_dinner' => $total_kalori_dinner,
                'total_kalori_snack' => $total_kalori_snack,
                'total_kalori_daily_sport' => $total_kalori_dailySport,
                'total_kalori_daily' => $total_kalori_daily,
            ],
        ], 200);
    }

    public function status()
    {
        // Mendapatkan ID pengguna yang sedang login
        $user_id = Auth::id();

        // Mengambil data target hari diet
        $target = Target::where('user_id', $user_id)->first();
        $target_hari_diet = $target->target_hari_diet;
        $budget_kalori_harian = $target->budget_kalori_harian;

        // Menghitung total kalori harian
        $breakfasts = Breakfast::where('user_id', $user_id)->pluck('created_at');
        $lunches = Lunch::where('user_id', $user_id)->pluck('created_at');
        $dinners = Dinner::where('user_id', $user_id)->pluck('created_at');
        $snacks = Snack::where('user_id', $user_id)->pluck('created_at');
        $dailySports = DailySport::where('user_id', $user_id)->pluck('created_at');

        $dates = collect(array_merge($breakfasts->toArray(), $lunches->toArray(), $dinners->toArray(), $snacks->toArray(), $dailySports->toArray()))
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique();

        $status_hari_diet = $target_hari_diet;

        foreach ($dates as $date) {
            $total_kalori_daily = Breakfast::where('user_id', $user_id)
                ->whereDate('created_at', $date)
                ->sum('kalori')
                + Lunch::where('user_id', $user_id)
                ->whereDate('created_at', $date)
                ->sum('kalori')
                + Dinner::where('user_id', $user_id)
                ->whereDate('created_at', $date)
                ->sum('kalori')
                + Snack::where('user_id', $user_id)
                ->whereDate('created_at', $date)
                ->sum('kalori')
                - DailySport::where('user_id', $user_id)
                ->whereDate('created_at', $date)
                ->sum('kalori');

            if ($total_kalori_daily <= $budget_kalori_harian) {
                $status_hari_diet -= 1;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status hari diet retrieved successfully',
            'data' => [
                'status_hari_diet' => $status_hari_diet,
            ],
        ], 200);
    }
}
