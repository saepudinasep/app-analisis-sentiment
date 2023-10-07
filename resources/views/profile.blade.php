@extends('layouts.mainlayout')

@section('title', 'Profile')

@section('content')
    <div class="row">
        <div class="col-md-3">

            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ Auth::user()->photo ? asset('storage/image/' . Auth::user()->photo) : asset('vendor/adminlte/dist/img/avatar.png') }}"
                            alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center">{{ $data->name }}</h3>
                    <strong><i class="fas fa-envelope mr-1 mt-4"></i> email</strong>
                    <p class="text-muted">
                        {{ $data->email }}
                    </p>
                    <hr>
                </div>

            </div>

        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#settings" data-toggle="tab">Settings</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">

                        <div class="active tab-pane" id="settings">
                            <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="name" name="name" disabled
                                            autocomplete="off" value="{{ Auth::user()->name }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="email" name="email" disabled
                                            autocomplete="off" value="{{ Auth::user()->email }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputFile" class="col-sm-2 col-form-label">Photo</label>
                                    <div class="col-sm-10">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="photo" name="photo"
                                                accept=".jpeg,.jpg,.png">
                                            <label class="custom-file-label" for="photo">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
