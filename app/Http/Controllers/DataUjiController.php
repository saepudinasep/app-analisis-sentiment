<?php

namespace App\Http\Controllers;

use App\Models\DataUji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DataUjiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $filePath = resource_path('json/' . $userName . '/test_data_' . $userName . '.json');
        if (file_exists($filePath)) {
            $ambilData = File::get($filePath);
            // Mendekode JSON menjadi array asosiatif
            $data = json_decode($ambilData, true);
            // dd($data);
            // Ubah data agar sesuai dengan struktur yang diinginkan oleh jsGrid
            $jsGridData = [];
            foreach ($data['X_test'] as $index => $xTest) {
                $yTest = $data['y_test'][$index];
                $sentiment = '';

                if ($yTest == -1) {
                    $sentiment = 'negatif';
                } elseif ($yTest == 1) {
                    $sentiment = 'positif';
                } elseif ($yTest == 0) {
                    $sentiment = 'netral';
                }

                $jsGridData[] = [
                    'no' => $index + 1,
                    'text' => $xTest,
                    'sentiment' => $sentiment,
                ];
            }
            // dd($jsGridData);
        } else {
            $jsGridData = false;
        }
        return view('data-uji', ['data' => $jsGridData]);
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
