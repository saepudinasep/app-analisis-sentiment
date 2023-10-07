<?php

namespace App\Http\Controllers;

use App\Imports\ExcelImport;
use App\Models\DataMentah;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
// use Excel;



class DataMentahController extends Controller
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
                $Count = count($data);
            }
        } else {
            $data = false;
            $Count = 0;
        }
        return view('data-mentah', ['data' => $data, 'dataCount' => $Count]);
    }

    public function importExcel()
    {
        return view('import-excel');
    }

    public function storeExcel(Request $request)
    {
        $file = $request->file('file');

        $this->validate($request, [
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Nama file JSON yang akan digunakan
        $timestamp = time();
        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $fileName = "data-$timestamp-$userName.json";
        $filePath = resource_path('json/' . $userName . '/'  . $fileName);

        // Inisialisasi nomor yang akan digunakan
        $counter = 1;

        if (file_exists($filePath)) {
            // File JSON sudah ada, baca isi file untuk mendapatkan nomor terakhir
            $jsonContents = file_get_contents($filePath);
            $existingData = json_decode($jsonContents, true);

            // Cari nomor terakhir dalam data yang ada
            $lastItem = end($existingData);

            // Periksa jika $lastItem adalah sebuah array dan memiliki kunci "no"
            if (is_array($lastItem) && isset($lastItem['no'])) {
                $counter = $lastItem['no'] + 1; // Nomor berikutnya setelah nomor terakhir
            }
        }

        $data = Excel::toArray([], $file);

        if (count($data) > 0) {
            $headerRow = $data[0][0]; // Get the first row as the header row

            // Check if the header row contains the required columns
            if (in_array('no', $headerRow) && in_array('text', $headerRow)) {
                $jsonArray = [];
                $headerSkipped = false;

                foreach ($data[0] as $row) {
                    if (!$headerSkipped) {
                        // Lewati baris header (indeks 0)
                        $headerSkipped = true;
                        continue;
                    }

                    $item = [
                        "no" => $counter, // Gunakan nomor yang telah dihitung
                        "text" => $row[1]
                    ];

                    $jsonArray[] = $item;
                    $counter++;
                }

                // Konversi ke JSON
                $jsonResult = json_encode($jsonArray, JSON_PRETTY_PRINT);

                // Simpan data ke file JSON
                file_put_contents($filePath, $jsonResult);

                return redirect()->route('showJsonData')->with(['fileName' => $fileName]);
            } else {
                // The uploaded file doesn't contain the required columns
                Session::flash('status', 'error');
                Session::flash('message', 'File yang diunggah harus berisi kolom "no" dan "text".');
                return redirect('/import-excel');
            }
        } else {
            // The uploaded file is empty or couldn't be read
            Session::flash('status', 'error');
            Session::flash('message', 'File yang diunggah kosong atau tidak dapat diproses.');
            return redirect('/import-excel');
        }
    }

    public function showJsonData()
    {
        $fileName = session('fileName');
        // return $fileName;
        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $filePath = resource_path('json/' . $userName . '/' . $fileName);
        $dataAwal = File::get($filePath);
        $data = json_decode($dataAwal, true);
        // $data = json_decode(File::get($filePath), true);
        return view('import-excel', ['data' => $data, 'fileName' => $fileName]);
        // dd($data);
        // return "Berhasil Melewati route ini";
    }

    public function simpanData(Request $request)
    {
        $fileName = $request->fileName;
        $existingData = [];
        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $data = ['namaUser' => $userName];
        $count = json_decode(file_get_contents(resource_path('json/' . $userName . '/' . $fileName)), true);

        $existingFileName = resource_path('json/' . $userName . '/' . 'data_' . $userName . '.json');
        $mergedData = [];

        if (file_exists($existingFileName)) {
            $existingData = json_decode(file_get_contents($existingFileName), true);
            if (is_array($existingData)) {
                $newDataFileName = resource_path('json/' . $userName . '/' . $fileName);

                if (file_exists($newDataFileName)) {
                    $newData = json_decode(file_get_contents($newDataFileName), true);

                    // Cari nomor terakhir dalam data yang ada
                    $lastItem = end($existingData);

                    // Periksa jika $lastItem adalah sebuah array dan memiliki kunci "no"
                    if (is_array($lastItem) && isset($lastItem['no'])) {
                        $counter = $lastItem['no'] + 1; // Nomor berikutnya setelah nomor terakhir
                    }

                    // Gunakan nomor yang telah dihitung untuk data yang baru
                    foreach ($newData as &$item) {
                        $item['no'] = $counter;
                        $counter++;
                    }

                    // Gabungkan data lama dan data baru
                    $mergedData = array_merge($existingData, $newData);
                } else {
                    // Jika file kedua tidak ada, gunakan data dari file pertama saja
                    $mergedData = $existingData;
                }
            }
        } else {
            // Jika file pertama tidak ada, gunakan data dari file kedua sebagai file pertama
            $newDataFileName = resource_path('json/' . $userName . '/' . $fileName);

            if (file_exists($newDataFileName)) {
                $mergedData = json_decode(file_get_contents($newDataFileName), true);
            }
        }

        // Memeriksa apakah jumlah data dalam $existingData tidak lebih dari 1000
        if (is_array($count)) {
            if (count($count) <= 1000) {

                if (file_put_contents($existingFileName, json_encode($mergedData))) {
                    if (file_exists($newDataFileName)) {
                        unlink($newDataFileName);
                    }
                    // Mengonversi data menjadi JSON
                    $jsonData = json_encode($data, JSON_FORCE_OBJECT);
                    $jsonData = str_replace("'", '"', $jsonData);

                    $scriptPath = base_path('resources/python/data_training.py');
                    // Escapce shell argument untuk mengatasi spasi atau karakter khusus
                    $escapedScriptPath = escapeshellarg($scriptPath);
                    // Eksekusi perintah Python dengan shell_exec
                    shell_exec("python $escapedScriptPath 2>&1 " . escapeshellarg($jsonData));

                    $PathModel = base_path('resources/python/modelling.py');
                    // Escapce shell argument untuk mengatasi spasi atau karakter khusus
                    $escapedPathModel = escapeshellarg($PathModel);
                    // Eksekusi perintah Python dengan shell_exec
                    shell_exec("python $escapedPathModel 2>&1 " . escapeshellarg($jsonData));

                    Session::flash('status', 'success');
                    Session::flash('message', 'Data Berhasil diTambahkan');

                    // Redirect kembali ke halaman daftar
                    return redirect('/data-mentah');
                } else {
                    return "Gagal Merge";
                }
            } else {
                // Jika jumlah data lebih dari 1000, maka lakukan tindakan sesuai kebijakan Anda
                // Contoh: Tampilkan pesan kesalahan atau lakukan tindakan lain
                if (file_exists($newDataFileName)) {
                    unlink($newDataFileName);
                }
                Session::flash('status', 'error');
                Session::flash('message', 'Jumlah data dalam file melebihi 1000. Penggabungan tidak diizinkan.');

                // Redirect kembali ke halaman daftar
                return redirect('/import-excel');
                // echo '<script>toastr.error("Jumlah data dalam file melebihi 1000. Penggabungan tidak diizinkan.")</script>';
            }
        }
    }


    public function delete()
    {

        $userName = Auth::user()->name;
        $userName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $userName);
        $data = resource_path('json/' . $userName . 'data_' . $userName . '.json'); // data
        unlink($data);
        $sentiment = resource_path('json/' . $userName . '/data_sentiment_' . $userName . '.json'); // sentiment
        unlink($sentiment);
        $train = resource_path('json/' . $userName . '/train_data_' . $userName . '.json'); // train
        unlink($train);
        $test = resource_path('json/' . $userName . '/test_data_' . $userName . '.json'); // test
        unlink($test);
        $knn_classification = resource_path('json/' . $userName . '/KNN_classification_' . $userName . '.json'); // knn-clasification
        unlink($knn_classification);
        $knn_evaluation = resource_path('json/' . $userName . '/KNN_evaluation_' . $userName . '.json'); // knn-evaluation
        unlink($knn_evaluation);
        $logistic_classification = resource_path('json/' . $userName . '/Logistic Regression_classification_' . $userName . '.json'); // logistic-clasification
        unlink($logistic_classification);
        $logistic_evaluation = resource_path('json/' . $userName . '/Logistic Regression_evaluation_' . $userName . '.json'); // logistic-evaluation
        unlink($logistic_evaluation);
        $naive_classification = resource_path('json/' . $userName . '/Naive Bayes_classification_' . $userName . '.json'); // naive-clasification
        unlink($naive_classification);
        $naive_evaluation = resource_path('json/' . $userName . '/Naive Bayes_evaluation_' . $userName . '.json'); // naive-evaluation
        unlink($naive_evaluation);
        $random_classification = resource_path('json/' . $userName . '/Random Forest_classification_' . $userName . '.json'); // random-forest-clasification
        unlink($random_classification);
        $random_evaluation = resource_path('json/' . $userName . '/Random Forest_evaluation_' . $userName . '.json'); // random-forest-evaluation
        unlink($random_evaluation);
        $svm_classification = resource_path('json/' . $userName . '/SVM_classification_' . $userName . '.json'); // svm-clasification
        unlink($svm_classification);
        $svm_evaluation = resource_path('json/' . $userName . '/SVM_evaluation_' . $userName . '.json'); // svm-evaluation
        // unlink($filePath);
        unlink($svm_evaluation);
        // if ($delete === 0) {
        Session::flash('status', 'success');
        Session::flash('message', 'Data Berhasil diDelete');

        // Redirect kembali ke halaman daftar
        return redirect('/data-mentah');
        // }
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
    // public function store(Request $request)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'text' => 'required|string|max:255',
    //         // tambahkan validasi lain sesuai kebutuhan
    //     ]);

    //     // Ambil data yang sudah ada dari file JSON
    //     $data = json_decode(file_get_contents(resource_path('json/data.json')), true);

    //     // Tambahkan data baru
    //     $data[] = [
    //         'content' => $request->input('text'),
    //         // tambahkan data lain sesuai kebutuhan
    //     ];

    //     // Simpan data kembali ke file JSON
    //     file_put_contents(resource_path('json/data.json'), json_encode($data));

    //     // Jalankan perintah Artisan
    //     $import_dataMentah = Artisan::call('app:import-data-mentah');

    //     if ($import_dataMentah === 0) {

    //         Session::flash('status', 'success');
    //         Session::flash('message', 'Data Berhasil diTambahkan');

    //         // Redirect kembali ke halaman daftar
    //         return redirect('/data-mentah');
    //     } else {
    //         return "Terjadi kesalahan saat menjalankan perintah.";
    //     }
    // }

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
