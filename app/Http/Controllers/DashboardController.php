<?php

namespace App\Http\Controllers;

use App\Models\DataMentah;
use App\Models\DataSentiment;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $filePath = resource_path('json/' . $userName . '/' . 'data_' . $userName . '.json');

        if (file_exists($filePath)) {
            $ambilData = File::get($filePath);
            // Mendekode JSON menjadi array asosiatif
            $data = json_decode($ambilData, true);
            if ($data) {
                // Menghitung jumlah elemen dalam array
                $count = count($data);
            }
        } else {
            $count = 0;
        }
        // dd($ambilData);
        return view('home', ['count' => $count]);
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
