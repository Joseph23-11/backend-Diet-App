<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerubahanBerat;
use Illuminate\Support\Facades\Auth;

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

        // Memeriksa apakah data perubahan berat badan kosong
        if ($perubahanBerats->isEmpty()) {
            return response()->json(['error' => 'No weight change data found.'], 404);
        }

        // Membuat matriks X dan vektor y dari data perubahan berat badan
        $X = [];
        $y = [];

        foreach ($perubahanBerats as $index => $perubahanBerat) {
            $X[] = [1, $index + 1];
            $y[] = $perubahanBerat->berat_sekarang; // Ganti "nilai" dengan atribut yang sesuai dalam model PerubahanBerat
        }

        $count = count($y);

        // Menghitung matriks X_transpose
        $X_transpose = $X;
        if (!is_array($X_transpose[0])) {
            $X_transpose = [$X_transpose];
        } else {
            $X_transpose = array_map(null, ...$X_transpose);
        }


        // Menghitung matriks X_transpose_X
        $X_transpose_X = [];
        $X_transpose = is_array($X_transpose) ? $X_transpose : [$X_transpose];
        $countXTranspose = count($X_transpose);
        for ($i = 0; $i < $countXTranspose; $i++) {
            $row = [];
            for ($j = 0; $j < $countXTranspose; $j++) {
                $sum = 0;
                for ($k = 0; $k < $count; $k++) {
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

        $jumlahData = $count; // Jumlah data yang ingin ditambahkan
        $prediksiBeratSekarang = $c[0] + $c[1] * ($count + 1);

        // Menghitung perubahan berat badan
        $perubahanBerat = $y[$count - 1] - $y[$count - 2];

        // Menambahkan prediksi perubahan berat badan ke prediksi berat sekarang
        $prediksiBeratSekarang += $perubahanBerat;

        return response()->json(['prediksi_berat_sekarang' => $prediksiBeratSekarang]);
    }

    // Fungsi untuk mengalikan matriks dengan vektor
    function matrixVectorMultiplication($matrix, $vector)
    {
        $result = [];
        $m = count($matrix);

        if (is_array($matrix[0])) {
            $n = count($matrix[0]);
        } else {
            $n = count($matrix);
        }

        // Debugging statements
        echo 'Matrix Dimensions: ' . $m . ' x ' . $n . "\n";
        echo 'Vector Dimensions: ' . count($vector) . "\n";


        for ($i = 0; $i < $m; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                if (is_array($vector)) {
                    $sum += $matrix[$i][$j] * $vector[$j];
                } else {
                    $sum += $matrix[$i][$j] * $vector;
                }
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
