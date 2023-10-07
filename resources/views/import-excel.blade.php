@extends('layouts.mainlayout')

@section('title', 'Import Excel')

@section('content')
    <form class="form-horizontal" enctype="multipart/form-data" action="importExcel" method="post">
        @csrf
        <div class="form-group row">
            <div class="col-4">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="file" name="file" accept=".xls,.xlsx">
                    <label class="custom-file-label" for="file">Choose file</label>
                </div>
            </div>
            <button type="submit" class="btn btn-success" id="uploadButton">Upload</button>
        </div>
    </form>
    <h3>
        <a href="{{ asset('template/template_excel.xlsx') }}" download="template_excel.xlsx" class="text-sm">
            Download Template Excel
        </a>
    </h3>

    @if (isset($data))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Excel Anda</h3>
                <form action="/simpan-data" method="post">
                    @csrf
                    <input type="text" hidden value="{{ $fileName }}" name="fileName">
                    <button class="btn btn-primary float-right" type="submit">Simpan</button>
                </form>
            </div>

            <div class="card-body">
                <div id="jsGrid"></div>
            </div>

        </div>


        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>
        <script>
            function initializeJSGrid(data) {
                $("#jsGrid").jsGrid({
                    width: "100%",
                    height: "400px",
                    sorting: true,
                    paging: true,
                    data: data,
                    fields: [{
                            name: "no",
                            type: "number",
                            width: 50,
                            title: "No"
                        },
                        {
                            name: "text",
                            type: "text",
                            width: 100,
                            title: "Text"
                        }
                    ]
                });
            }

            $(document).ready(function() {
                const data = @json($data);
                initializeJSGrid(data);
            });
        </script>

        <!-- Include Jquery -->
    @endif

    {{-- <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.js') }}"></script> --}}
    @if (Session::has('status') && Session::has('message'))
        <script src="{{ asset('vendor/adminlte/plugins/toastr/toastr.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                toastr.{{ Session::get('status') }}("{{ Session::get('message') }}");
            });
        </script>
    @endif
@endsection
