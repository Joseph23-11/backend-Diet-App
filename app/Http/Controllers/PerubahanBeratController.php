<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonalDetail;
use App\Models\PerubahanBerat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class PerubahanBeratController extends Controller
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

        // Ambil personal_detail_id berdasarkan user_id
        $personalDetail = PersonalDetail::where('user_id', $user_id)->first();

        // Cek apakah ada data PerubahanBerat yang terkait dengan personal_detail_id
        if ($personalDetail) {
            // Ambil semua data PerubahanBerat berdasarkan personal_detail_id
            $perubahanBerat = PerubahanBerat::where('personal_detail_id', $personalDetail->id)
                ->orderBy('created_at', 'asc')
                ->get();

            // Buat array response
            $response = [
                'perubahan_berat' => $perubahanBerat,
            ];

            return response()->json($response);
        }

        return response()->json(['message' => 'Tabel perubahan_berats tidak ditemukan'], 404);
    }

    public function store(Request $request)
    {
        // Mendapatkan ID pengguna yang sedang login
        $user_id = Auth::id();

        // Ambil personal_detail_id berdasarkan user_id
        $personalDetail = PersonalDetail::where('user_id', $user_id)->first();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'berat_sekarang' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Buat data PerubahanBerat baru
        $perubahanBerat = new PerubahanBerat();
        $perubahanBerat->personal_detail_id = $personalDetail->id;
        $perubahanBerat->berat_sekarang = $request->berat_sekarang;

        // Ambil berat_sebelumnya terakhir jika ada, jika tidak ambil dari personal_detail
        $beratSebelumnya = PerubahanBerat::where('personal_detail_id', $personalDetail->id)
            ->orderBy('created_at', 'desc')
            ->value('berat_sekarang');

        if ($beratSebelumnya !== null) {
            $perubahanBerat->berat_sebelumnya = $beratSebelumnya;
        } else {
            $personalDetailModel = PersonalDetail::find($personalDetail->id);
            $perubahanBerat->berat_sebelumnya = $personalDetailModel->berat_badan;
        }

        // Hitung jumlah_pengurangan
        $jumlahPengurangan = $perubahanBerat->berat_sekarang - $perubahanBerat->berat_sebelumnya;
        $perubahanBerat->jumlah_pengurangan = $jumlahPengurangan;

        // Simpan perubahan
        $perubahanBerat->save();

        // Buat response
        $response = [
            'message' => 'Data Perubahan Berat berhasil disimpan',
            'perubahan_berat' => $perubahanBerat,
        ];

        return response()->json($response, 201);
    }

    public function destroy($id)
    {
        // Mencari data PerubahanBerat berdasarkan ID
        $perubahanBerat = PerubahanBerat::find($id);

        // Jika data tidak ditemukan, kirimkan respons error
        if (!$perubahanBerat) {
            return response()->json([
                'success' => false,
                'message' => 'Perubahan Berat data not found',
            ], 404);
        }

        // Menghapus data PerubahanBerat
        $perubahanBerat->delete();

        return response()->json([
            'success' => true,
            'message' => 'PerubahanBerat data deleted successfully',
        ], 200);
    }
}
