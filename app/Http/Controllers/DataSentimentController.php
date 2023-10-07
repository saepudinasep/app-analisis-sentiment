<?php

namespace App\Http\Controllers;

use App\Models\DataSentiment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class DataSentimentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $filePath = resource_path('json/' . $userName . '/data_sentiment_' . $userName . '.json');
        if (file_exists($filePath)) {
            $ambilData = File::get($filePath);
            // Mendekode JSON menjadi array asosiatif
            $data = json_decode($ambilData, true);
        } else {
            $data = false;
        }
        return view('data-sentiment', ['data' => $data]);
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
