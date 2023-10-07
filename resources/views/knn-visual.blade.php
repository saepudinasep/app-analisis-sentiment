@extends('layouts.mainlayout')

@section('title', 'Visualisasi KNN')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Confusion Matrix Klasifikasi KNN</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Precision</th>
                        <th>Recall</th>
                        <th>F1-Score</th>
                        <th>Support</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $lines = preg_split('/\n/', $data->classification_report, -1, PREG_SPLIT_NO_EMPTY);
                    @endphp
                    @foreach ($lines as $line)
                        @if (strpos($line, 'accuracy') === false &&
                                strpos($line, 'macro avg') === false &&
                                strpos($line, 'weighted avg') === false)
                            @php
                                $columns = preg_split('/\s+/', trim($line), -1, PREG_SPLIT_NO_EMPTY);
                            @endphp
                            @if (count($columns) >= 5)
                                <tr>
                                    <td>{{ $columns[0] }}</td>
                                    <td>{{ $columns[1] }}</td>
                                    <td>{{ $columns[2] }}</td>
                                    <td>{{ $columns[3] }}</td>
                                    <td>{{ $columns[4] }}</td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            Akurasi: {{ $data->accuracy }} %
        </div>
    </div>


    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Visualisasi Sentiment KNN</h3>
                </div>
                <div class="card-body">
                    <canvas id="knnChart"></canvas>
                </div>
            </div>
        </div>

        <script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>

        <script>
            var ctx = document.getElementById('knnChart').getContext('2d');
            var knnChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Positif (%)', 'Negatif (%)', 'Netral (%)'],
                    datasets: [{
                        data: [{{ $persentasePositif }}, {{ $persentaseNegatif }}, {{ $persentaseNetral }}],
                        backgroundColor: [
                            'rgba(0, 123, 255, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(210, 214, 222, 1)',
                        ],
                        borderColor: [
                            'rgba(0, 123, 255, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(210, 214, 222, 1)',
                        ],
                    }],
                },
                options: {
                    responsive: true,
                },
            });
        </script>

        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Akurasi Sentiment KNN</h3>
                </div>
                <div class="card-body">
                    <canvas id="akurasiChart"></canvas>
                </div>
            </div>
        </div>


        <script>
            var ctx = document.getElementById('akurasiChart').getContext('2d');
            var akurasi = {{ $data->accuracy }};
            var tidakAkurat = 100 - akurasi; // Menghitung persentase "Tidak Akurat"

            var akurasiChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Akurat (%)', 'Tidak Akurat (%)'],
                    datasets: [{
                        data: [akurasi, tidakAkurat],
                        backgroundColor: [
                            'rgba(0, 123, 255, 1)',
                            'rgba(220, 53, 69, 1)',
                        ],
                        borderColor: [
                            'rgba(0, 123, 255, 1)',
                            'rgba(220, 53, 69, 1)',
                        ],
                    }],
                },
                options: {
                    responsive: true,
                },
            });
        </script>

    </div>

@endsection
