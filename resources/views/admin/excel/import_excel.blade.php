
@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3>IMPORT EXCEL</h3>
                </div>
                <div class="box-body">
                    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ url('excel/import') }}" class="form-horizontal" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}



                        @if ($errors->any())

                            <div class="alert alert-danger">

                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                                <ul>

                                    @foreach ($errors->all() as $error)

                                        <li>{{ $error }}</li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif



                        @if (Session::has('success'))

                            <div class="alert alert-success">

                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                                <p>{{ Session::get('success') }}</p>

                            </div>

                        @endif



                        <input type="file" name="import_file" />

                        <button class="btn btn-primary">Import File</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        @if (Session::has('success'))
        Lobibox.notify('success', {
            title: "",
            msg: "<?php echo Session::get("success"); ?>",
            size: 'mini',
            rounded: true
        });
        @endif
    </script>
@endsection

