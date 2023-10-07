@extends('layouts.mainlayout')

@section('title', '404 Error Page')

@section('content')

    <div class="error-page">
        <h2 class="headline text-warning"> 404</h2>
        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>
            <p>
                Anda tidak mempunyai data untuk di visualisasi/klasifikasi
                Kembali ke <a href="/">dashboard</a> atau <a href="/data-mentah">import data</a>.
            </p>
        </div>

    </div>
@endsection
