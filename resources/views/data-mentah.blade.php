@extends('layouts.mainlayout')

@section('title', 'Data Mentah')

@section('content')

    <div class="container mb-3">
        <div class="row">
            <div class="col-2 offset-10">
                <div class="btn-group w-100">
                    @if ($dataCount >= 3000)
                        <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal">
                            <i class="fas fa-trash"></i>
                            Delete Data
                        </a>

                        <!-- Modal Konfirmasi Delete -->
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
                            aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus Data</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Apakah Anda yakin akan menghapus data?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                                        <a href="/delete-data" class="btn btn-danger">Ya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="/import-excel" class="btn btn-success">
                            <i class="fas fa-upload"></i>
                            Import Excel
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal-success">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/data-mentah">
                        @csrf
                        <div class="form-group">
                            <label for="text">Text</label>
                            <textarea class="form-control" rows="3" placeholder="Enter ..." id="text" name="text" required></textarea>
                        </div>
                        <!-- Tambahkan input lain sesuai kebutuhan -->
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-default d-flex float-right"
                            data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Mentah (Awal)</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="jsGrid"></div>
            <span class="page"></span>
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
                    pageSize: 10,
                    pageButtonCount: 5,
                    data: data,
                    fields: [{
                            name: "no",
                            type: "number",
                            width: 5,
                            title: "No",
                            align: "center"
                        },
                        {
                            name: "text",
                            type: "text",
                            width: 100,
                            title: "Text"
                        }
                    ],

                    // Custom pagination menggunakan Bootstrap
                    pagerContainer: $(".page").addClass("text-center"),
                    pagerFormat: "{first} {prev} {pages} {next} {last} {pageIndex} of {pageCount}",
                    pagePrevText: "Prev",
                    pageNextText: "Next",
                    pageFirstText: "First",
                    pageLastText: "Last"
                });
            }

            $(document).ready(function() {
                const data = @json($data);
                initializeJSGrid(data);
            });
        </script>

    </div>

    <!-- Include Jquery -->
    {{-- <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.js') }}"></script> --}}
    <script src="{{ asset('vendor/adminlte/plugins/toastr/toastr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            @if (Session::has('status') && Session::has('message'))
                toastr.{{ Session::get('status') }}("{{ Session::get('message') }}");
            @endif
        });
    </script>
@endsection
