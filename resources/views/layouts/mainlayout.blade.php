<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analisis Sentiment | @yield('title')</title>
    {{-- <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/jsgrid/jsgrid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/jsgrid/jsgrid-theme.min.css') }}"> --}}
    {{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" /> --}}

    <!-- Include AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.css') }}">
    <!-- Include FontAwesome -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/fontawesome-free/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/plugins/toastr/toastr.min.css') }}">


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>

    <style>
        .different-values {
            color: red;
            /* Change the color to your desired value */
        }
    </style>
    {{-- <link rel="stylesheet" href="../../plugins/jsgrid/jsgrid.min.css">
    <link rel="stylesheet" href="../../plugins/jsgrid/jsgrid-theme.min.css"> --}}
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <div class="user-panel d-flex m-3">
                        <div class="image">
                            <img src="{{ Auth::user()->photo ? asset('storage/image/' . Auth::user()->photo) : asset('vendor/adminlte/dist/img/avatar.png') }}"
                                class="img-circle elevation-2" alt="User Image">
                        </div>
                        <div class="info">
                            <a href="/" class="d-block">{{ Auth::user()->name }}</a>
                        </div>
                    </div>
                </li>

            </ul>
        </nav>


        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <a href="/home" class="brand-link">
                <img src="{{ asset('vendor/adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Analisis Sentiment</span>
            </a>

            <div class="sidebar">

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">

                        <li class="nav-item">
                            <a href="/home" class="nav-link @if (request()->is('dashboard')) active @endif">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/data-mentah" class="nav-link @if (request()->is('data-mentah')) active @endif">
                                <i class="nav-icon fas fa-pencil-alt"></i>
                                <p>
                                    Data Mentah (Awal)
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/data-sentiment" class="nav-link @if (request()->is('data-sentiment')) active @endif">
                                <i class="nav-icon fas fa-highlighter"></i>
                                <p>
                                    Data with Sentiment
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/data-latih" class="nav-link @if (request()->is('data-latih')) active @endif">
                                <i class="nav-icon fas fa-random"></i>
                                <p>
                                    Data Latih (Trial)
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/data-uji" class="nav-link @if (request()->is('data-uji')) active @endif">
                                <i class="nav-icon fas fa-compress-alt"></i>
                                <p>
                                    Data Uji (Test)
                                </p>
                            </a>
                        </li>
                        <li class="nav-item @if (request()->is('naive-bayes') ||
                                request()->is('svm') ||
                                request()->is('random-forest') ||
                                request()->is('knn') ||
                                request()->is('logistic-regression')) menu-open @endif">
                            <a href="#" class="nav-link @if (request()->is('naive-bayes') ||
                                    request()->is('svm') ||
                                    request()->is('random-forest') ||
                                    request()->is('knn') ||
                                    request()->is('logistic-regression')) active @endif">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>
                                    Klasifikasi
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/naive-bayes"
                                        class="nav-link @if (request()->is('naive-bayes')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Naive Bayes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/svm" class="nav-link @if (request()->is('svm')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>SVM</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/random-forest"
                                        class="nav-link @if (request()->is('random-forest')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Random Forest</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/knn"
                                        class="nav-link  @if (request()->is('knn')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>KNN</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/logistic-regression"
                                        class="nav-link  @if (request()->is('logistic-regression')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Logistic Regression</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item @if (request()->is('naive-bayes-visual') ||
                                request()->is('svm-visual') ||
                                request()->is('random-forest-visual') ||
                                request()->is('knn-visual') ||
                                request()->is('logistic-regression-visual')) menu-open @endif">
                            <a href="#" class="nav-link @if (request()->is('naive-bayes-visual') ||
                                    request()->is('svm-visual') ||
                                    request()->is('random-forest-visual') ||
                                    request()->is('knn-visual') ||
                                    request()->is('logistic-regression-visual')) active @endif">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>
                                    Visualisasi
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="/naive-bayes-visual"
                                        class="nav-link @if (request()->is('naive-bayes-visual')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Naive Bayes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/svm-visual"
                                        class="nav-link @if (request()->is('svm-visual')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>SVM</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/random-forest-visual"
                                        class="nav-link @if (request()->is('random-forest-visual')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Random Forest</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/knn-visual"
                                        class="nav-link @if (request()->is('knn-visual')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>KNN</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/logistic-regression-visual"
                                        class="nav-link @if (request()->is('logistic-regression-visual')) active @endif">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Logistic Regression</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="/profile" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Profile
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/logout" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>

            </div>

        </aside>

        <div class="content-wrapper">

            <div class="content-header" style="margin-top: 80px">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">Analisis Sentiment</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>


            <div class="content">
                <div class="container-fluid">
                    @yield('content')

                </div>
            </div>

        </div>



        <footer class="main-footer">

            <div class="float-right d-none d-sm-inline">
                Lakukan Apa Yang Kamu Sukai.
            </div>

            <strong>Copyright &copy; 2023 <a href="https://adminlte.io">Asep Saepudin</a>.</strong> All rights
            reserved.
        </footer>
    </div>

    <!-- Include Jquery -->
    {{-- <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script> --}}
    <!-- Include bootstrap JS -->
    <script src="{{ asset('vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Include custom File JS -->
    <script src="{{ asset('vendor/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- Include AdminLTE JS -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    <!-- Include Chart JS -->
    {{-- <script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script> --}}


    <script>
        $(function() {
            bsCustomFileInput.init();
        });
    </script>
</body>

</html>
