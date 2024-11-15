<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getProvince(): JsonResponse
    {
        $provinsi = Address::select('provinsi')
            ->distinct()
            ->orderBy('provinsi')
            ->get();

        return response()->json($provinsi);
    }
    public function getKabupaten($provinsi): JsonResponse
    {
        $kabupaten = DB::table('addresses')
            ->select('kabupaten')
            ->where('provinsi', $provinsi)
            ->distinct()
            ->orderBy('kabupaten')
            ->get();

        return response()->json($kabupaten);
    }

    public function getKecamatan($kabupaten): JsonResponse
    {
        $kecamatan = DB::table('addresses')
            ->select('kecamatan')
            ->where('kabupaten', $kabupaten)
            ->distinct()
            ->orderBy('kecamatan')
            ->get();

        return response()->json($kecamatan);
    }

    public function getKelurahan($kecamatan): JsonResponse
    {
        $kelurahan = DB::table('addresses')
            ->select('kelurahan')
            ->where('kecamatan', $kecamatan)
            ->distinct()
            ->orderBy('kelurahan')
            ->get();

        return response()->json($kelurahan);
    }

    public function getKodePos($kelurahan): JsonResponse
    {
        $kodepos = DB::table('addresses')
            ->select('kodepos')
            ->where('kelurahan', $kelurahan)
            ->distinct()
            ->orderBy('kodepos')
            ->get();

        return response()->json($kodepos);
    }
}
