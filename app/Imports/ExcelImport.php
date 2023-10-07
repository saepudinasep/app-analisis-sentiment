<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\File;

class ExcelImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        // Konversi data dari Collection ke array asosiatif
        $data = [];

        foreach ($collection as $row) {
            $data[] = $row->toArray();
        }

        return $data;
        // // Simpan data dalam format JSON
        // $json_data = json_encode($data, JSON_PRETTY_PRINT);

        // // Simpan data JSON ke dalam file
        // $jsonFilePath = resource_path('json/exported_data.json');
        // File::put($jsonFilePath, $json_data);
    }
}
