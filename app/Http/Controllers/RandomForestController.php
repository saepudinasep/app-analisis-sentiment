<?php

namespace App\Http\Controllers;

use App\Models\DataSentiment;
use App\Models\DataUji;
use App\Models\RandomForest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class RandomForestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $filePath = resource_path('json/' . $userName . '/Random Forest_classification_' . $userName . '.json');
        if (file_exists($filePath)) {
            $ambilData = File::get($filePath);
            // Mendekode JSON menjadi array asosiatif
            $data = json_decode($ambilData, true);
            // dd($data);
            // Ubah data agar sesuai dengan struktur yang diinginkan oleh jsGrid
            $jsGridData = [];
            $no = 1;
            foreach ($data as $xData) {
                $jsGridData[] = [
                    'no' => $no++,
                    'text' => $xData['text'],
                    'sentiment' => $xData['sentiment'],
                    'predict' => $xData['predict'],
                ];
            }
        } else {
            $jsGridData = false;
        }
        return view('random-forest', ['data' => $jsGridData]);
    }

    public function visual()
    {
        $namaUser = Auth::user()->name;
        $namaUser = preg_replace('/[^A-Za-z0-9\-_]/', '_', $namaUser);
        $filePath = resource_path('json/' . $namaUser . '/Random Forest_evaluation_' . $namaUser . '.json'); // Path menuju file JSON di direktori resources

        // Periksa apakah file JSON ada
        if (file_exists($filePath)) {
            // Baca data dari file JSON
            $data = json_decode(file_get_contents($filePath));
            $fileClassification = resource_path('json/' . $namaUser . '/Random Forest_classification_' . $namaUser . '.json');
            $Classification = json_decode(file_get_contents($fileClassification), true);
            $positifCount = 0;
            $negatifCount = 0;
            $netralCount = 0;
            foreach ($Classification as $xData) {
                $sentiment = $xData['sentiment'];
                if ($sentiment === "positif") {
                    $positifCount++;
                } elseif ($sentiment === "negatif") {
                    $negatifCount++;
                } elseif ($sentiment === "netral") {
                    $netralCount++;
                }
            }
            $jml_count = $positifCount + $negatifCount + $netralCount;
            // $positifCount = $data['sentiment'];

            // Persentase Sentimen Positif = (Jumlah Contoh Positif / Total Jumlah Contoh) * 100%
            $persentasePositif = number_format(($positifCount / $jml_count) * 100, 2);
            // Persentase Sentimen Negatif = (Jumlah Contoh Negatif / Total Jumlah Contoh) * 100%
            $persentaseNegatif = number_format(($negatifCount / $jml_count) * 100, 2);
            // Persentase Sentimen Netral = (Jumlah Contoh Netral / Total Jumlah Contoh) * 100%
            $persentaseNetral = number_format(($netralCount / $jml_count) * 100, 2);

            if ($data !== null) {
                $data->accuracy = number_format($data->accuracy * 100, 2); // Mengubah accuracy ke persentase
            }
        } else {
            return view('404'); // Sesuaikan dengan nama halaman "not found" Anda
        }

        return view('random-forest-visual', [
            'data' => $data ?? null, 'persentasePositif' => $persentasePositif, 'persentaseNegatif' => $persentaseNegatif, 'persentaseNetral' => $persentaseNetral
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
