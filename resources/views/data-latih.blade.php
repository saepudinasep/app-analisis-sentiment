@extends('layouts.mainlayout')

@section('title', 'Data Latih (Trial)')

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Latih (Trial)</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
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
                        },
                        {
                            name: "sentiment",
                            type: "text",
                            width: 5,
                            title: "Sentiment",
                            align: "center"
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

@endsection
