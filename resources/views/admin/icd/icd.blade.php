@extends('layouts.app')

@section('content')
    <style>
        .center-align {
            text-align: center;
        }

        .file-upload {
            background-color: #ffffff;
            width: 400px;
            margin: 0 auto;
            padding: 20px;
        }

        .file-upload-btn {
            width: 100%;
            margin: 0;
            color: #fff;
            background: #1FB264;
            border: none;
            padding: 50px;
            border-radius: 4px;
            border-bottom: 4px solid #15824B;
            transition: all .2s ease;
            outline: none;
            text-transform: uppercase;
            font-weight: 700;
        }

        .file-upload-btn:hover {
            background: #1AA059;
            color: #ffffff;
            transition: all .2s ease;
            cursor: pointer;
        }

        .file-upload-btn:active {
            border: 0;
            transition: all .2s ease;
        }

        .file-upload-content {
            display: none;
            text-align: center;
        }

        .file-upload-input {
            position: absolute;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            outline: none;
            opacity: 0;
            cursor: pointer;
        }

        .image-upload-wrap {
            margin-top: 20px;
            border: 4px dashed #1FB264;
            position: relative;
        }

        .image-dropping,
        .image-upload-wrap:hover {
            background-color: #1FB264;
            border: 4px dashed #ffffff;
        }

        .image-title-wrap {
            padding: 0 15px 15px 15px;
            color: #222;
        }

        .drag-text {
            text-align: center;
        }

        .drag-text h3 {
            font-weight: 100;
            text-transform: uppercase;
            color: #15824B;
            padding: 60px 0;
        }

        .remove-image {
            width: 150px;
            margin: 0;
            color: #fff;
            background: #cd4535;
            border: none;
            padding: 10px;
            border-radius: 4px;
            border-bottom: 4px solid #b02818;
            transition: all .2s ease;
            outline: none;
            text-transform: uppercase;
            font-weight: 700;
        }

        .remove-image:hover {
            background: #c13b2a;
            color: #ffffff;
            transition: all .2s ease;
            cursor: pointer;
        }

        .remove-image:active {
            border: 0;
            transition: all .2s ease;
        }
    </style>

    <div class="row">
        <title>ICD-10 Codes</title>
        <div class="box">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    List of ICD-10 Codes    
                </h1>
                <ol class="breadcrumb form-inline my-2 my-lg-0">
                    <form action="{{ asset('admin/icd/search') }}" method="GET">
                        {{ csrf_field() }}
                        <input type="search" class="form-control" name="keyword" style="width: 50%;">
                        <button type="submit" class="btn btn-success btn-sm btn-flat"><i class="fa fa-search"></i> Search</button>
                        <button type="button" class="btn btn-primary btn-sm btn-flat" href="#import_icd" data-toggle="modal">
                            <i class="fa fa-file-excel-o"></i> Import
                        </button>
                    </form>
                </ol><br>
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <section class="col-lg-12">
                        <div class="box">
                            <div class="box-body no-padding table-responsive float-left">
                                <table class="table table-striped table-hover" data-pagination="true" >
                                    <tr>
                                        <th class="center-align">Code</th>
                                        <th class="center-align">Description</th>
                                        <th class="center-align">Group</th>
                                        <th class="center-align">Case Rate</th>
                                        <th class="center-align">Professional Fee</th>
                                        <th class="center-align">Health Care Fee</th>
                                        <th class="center-align">Source</th>
                                    </tr>
                                    @foreach($icd as $row)
                                        <tr>
                                            <td>{{ $row->code }}</td>
                                            <td>{{ $row->description }}</td>
                                            <td>{{ $row->group }}</td>
                                            <td>{{ $row->case_rate }}</td>
                                            <td>{{ $row->professional_fee }}</td>
                                            <td>{{ $row->health_care_fee }}</td>
                                            <td>{{ $row->source }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                                <div style="text-align: right;">
                                    {!! $icd->links() !!}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </div>
    @include('admin.excel.import_excel')
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
