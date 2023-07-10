<?php

namespace App\Http\Controllers;

use App\Models\PerubahanBerat;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PrediksiController extends Controller
{
    public function index()
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mendapatkan personal_detail_id berdasarkan user_id
        $personalDetailId = $user->personalDetail->id;

        // Mengambil data perubahan berat badan berdasarkan personal_detail_id
        $perubahanBerats = PerubahanBerat::where('personal_detail_id', $personalDetailId)->get();

        // Memeriksa apakah data perubahan berat badan kosong atau hanya terdiri dari satu elemen
        if ($perubahanBerats->isEmpty() || $perubahanBerats->count() < 2) {
            return response()->json(['error' => 'Insufficient weight change data. At least 2 data points are required.'], 404);
        }

        // Membuat matriks X dan vektor y dari data perubahan berat badan
        $X = [];
        $y = [];

        foreach ($perubahanBerats as $index => $perubahanBerat) {
            $X[] = [1, $index + 1];
            $y[] = $perubahanBerat->berat_sekarang;
        }

        // Menghitung matriks X_transpose
        $X_transpose = array_map(null, ...$X);

        // Menghitung matriks X_transpose_X
        $X_transpose_X = [];
        for ($i = 0; $i < count($X_transpose); $i++) {
            $row = [];
            for ($j = 0; $j < count($X_transpose); $j++) {
                $sum = 0;
                for ($k = 0; $k < count($X); $k++) {
                    $sum += $X[$k][$i] * $X[$k][$j];
                }
                $row[] = $sum;
            }
            $X_transpose_X[] = $row;
        }

        // Menghitung vektor parameter c
        $X_transpose_X_inverse = $this->inverseMatrix($X_transpose_X);
        $X_transpose_y = $this->matrixVectorMultiplication($X_transpose, $y);
        $c = $this->matrixVectorMultiplication($X_transpose_X_inverse, $X_transpose_y);

        // Mengambil data target berat badan
        $targetBeratBadan = Target::where('user_id', Auth::id())->pluck('target_berat_badan')->first();

        // Menghitung hari ke berapa titik berat badan ideal terpenuhi (intersep dengan garis regresi linear)
        $hariKeX = ($targetBeratBadan - $c[0]) / $c[1];
        $hariKeXRounded = ceil($hariKeX);

        // Membuat array titik-titik garis regresi linear
        $regressionLine = [];
        $minX = 1;
        $maxX = $hariKeXRounded;

        for ($index = 0; $index < $maxX; $index++) {
            $x = $index + 1;
            $y = $c[0] + $c[1] * $x;
            $yFormatted = number_format($y, 1);
            $regressionLine[] = [
                'hari' => date('Y-m-d', strtotime($perubahanBerats[0]->created_at . ' + ' . $index . ' days')),
                'berat' => $yFormatted
            ];
        }

        // Menghitung prediksi berat sekarang
        $prediksiBeratSekarang = $c[0] + $c[1] * ($perubahanBerats->count() + 1);
        $prediksiBeratSekarangFormatted = number_format($prediksiBeratSekarang, 1);

        // Menambahkan tanggal prediksi berat sekarang
        $tanggalPrediksi = date('Y-m-d', strtotime($perubahanBerats[1]->created_at . ' + ' . $perubahanBerats->count() . ' days'));

        // Cek apakah perubahan berat badan naik
        $lastWeightChange = $perubahanBerats->last();
        $currentWeight = $lastWeightChange->berat_sekarang;
        $previousWeight = $perubahanBerats[$perubahanBerats->count() - 2]->berat_sekarang;

        if ($currentWeight > $previousWeight) {
            $hariKeXRounded = ceil($hariKeX) + 1;
        }

        $response = [
            'prediksi_berat_sekarang' => [
                'tanggal' => $tanggalPrediksi,
                'berat' => $prediksiBeratSekarangFormatted
            ],
            'hari_target' => [
                'tanggal' => date('Y-m-d', strtotime($perubahanBerats[0]->created_at . ' + ' . $hariKeXRounded . ' days')),
                'hari' => $hariKeXRounded
            ],
            'regression_line' => $regressionLine,
        ];

        return response()->json($response);
    }

    // Fungsi untuk mengalikan matriks dengan vektor
    function matrixVectorMultiplication($matrix, $vector)
    {
        $result = [];
        $m = count($matrix);
        $n = count($matrix[0]);

        for ($i = 0; $i < $m; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $matrix[$i][$j] * $vector[$j];
            }
            $result[] = $sum;
        }

        return $result;
    }

    // Fungsi untuk menghitung invers matriks menggunakan metode adjoin
    function inverseMatrix($matrix)
    {
        $n = count($matrix);

        // Menghitung determinan matriks
        $determinant = $this->determinant($matrix);

        // Mengecek apakah matriks memiliki invers (determinan tidak sama dengan 0)
        if ($determinant === 0) {
            return null; // Matriks tidak memiliki invers
        }

        // Menghitung matriks adjoin
        $adjointMatrix = $this->adjointMatrix($matrix);

        // Menghitung matriks invers
        $inverseMatrix = $this->scalarMatrixMultiplication($adjointMatrix, 1 / $determinant);

        return $inverseMatrix;
    }

    // Fungsi untuk menghitung determinan matriks
    function determinant($matrix)
    {
        $n = count($matrix);

        if ($n === 1) {
            return $matrix[0][0];
        }

        $determinant = 0;

        for ($j = 0; $j < $n; $j++) {
            $submatrix = [];
            for ($k = 1; $k < $n; $k++) {
                $row = [];
                for ($l = 0; $l < $n; $l++) {
                    if ($l !== $j) {
                        $row[] = $matrix[$k][$l];
                    }
                }
                $submatrix[] = $row;
            }
            $cofactor = ($j % 2 === 0) ? 1 : -1;
            $determinant += $cofactor * $matrix[0][$j] * $this->determinant($submatrix);
        }

        return $determinant;
    }

    // Fungsi untuk menghitung matriks adjoin
    function adjointMatrix($matrix)
    {
        $n = count($matrix);
        $adjointMatrix = [];

        for ($i = 0; $i < $n; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                $cofactor = (($i + $j) % 2 === 0) ? 1 : -1;
                $submatrix = [];
                for ($k = 0; $k < $n; $k++) {
                    if ($k !== $i) {
                        $subrow = [];
                        for ($l = 0; $l < $n; $l++) {
                            if ($l !== $j) {
                                $subrow[] = $matrix[$k][$l];
                            }
                        }
                        $submatrix[] = $subrow;
                    }
                }
                $adjointMatrix[$i][$j] = $cofactor * $this->determinant($submatrix);
            }
        }

        return $adjointMatrix;
    }

    // Fungsi untuk mengalikan matriks dengan skalar
    function scalarMatrixMultiplication($matrix, $scalar)
    {
        $result = [];
        $n = count($matrix);

        for ($i = 0; $i < $n; $i++) {
            $row = [];
            for ($j = 0; $j < $n; $j++) {
                $row[] = $matrix[$i][$j] * $scalar;
            }
            $result[] = $row;
        }

        return $result;
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
