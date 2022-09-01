<?php
$user = Session::get('auth');
$facilities = \App\Facility::select('id','name')
    ->where('referral_used','yes')
    ->where('province',$province)
    ->orderBy('name','asc')->get();

$province_name = \App\Province::select('description')->where('id',$province)->first()->description;
?>
@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px !important;
        }

        .refer {
            background-color: #55deff;
        }

        .discharged {
            background: #ffcd39;
        }
    </style>

    <div class="row col-md-12">
        <div class="box box-success">
            <div class="row">
                <div class="col-md-4">
                    <div class="box-header with-border">
                        <h3> COVID REPORT <br />
                            <small class="text-success">
                                {{ strtoupper($province_name) }} PROVINCE
                            </small>
                        </h3><br>
                        <form action="{{ asset('admin/report/covid').'/'.$province }}" method="POST" class="form-inline">
                            {{ csrf_field() }}
                            <div class="form-group-sm" style="margin-bottom: 10px;">
                                <input type="text" class="form-control" name="date_range" value="{{ date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)) }}" id="date_range">
                                <button type="submit" class="btn btn-success btn-sm btn-flat">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <section class="content" style="height: auto !important; min-height: 0px !important;margin-top: 10px;">
                        <div class="row">
                            {{-- change color to reflect number of covid cases --}}
                            <div class="col-md-3">
                                <div class="small-box {{ $bohol_bg }}">
                                    <div class="inner">
                                        <h3 class="text-center">{{ $bohol_cases }}</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>
                                    <h5 class="small-box-footer"> BOHOL </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box {{ $cebu_bg }}">
                                    <div class="inner">
                                        <h3 class="text-center">{{ $cebu_cases }}</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>
                                    <h5 class="small-box-footer"> CEBU </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box {{ $negros_bg }}">
                                    <div class="inner">
                                        <h3 class="text-center">{{ $negros_cases }}</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>
                                    <h5 class="small-box-footer"> NEGROS ORIENTAL </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box {{ $siquijor_bg }}">
                                    <div class="inner">
                                        <h3 class="text-center">{{ $siquijor_cases }}</h3>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>
                                    <h5 class="small-box-footer"> SIQUIJOR </h5>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h5>&emsp;<b>Legend:</b>&nbsp;&nbsp;
                        <span style="border: 1px solid black; background-color: #55deff;">&emsp;&nbsp;</span> During Referral &emsp;
                        <span style="border: 1px solid black; background-color: #ffcd39;">&emsp;&nbsp;</span> Discharged
                    </h5>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-fixed-header">
                    <thead class="header">
                        <tr class="bg-navy-active text-center">
                            <th></th>
                            <th></th>
                            <th class="text-center" colspan="5">CLINICAL STATUS</th>
                            <th class="text-center" colspan="8">SURVEILLANCE CATEGORY</th>
                        </tr>
                    </thead>
                    <thead class="header bg-gray-light">
                        <tr>
                            <th></th>
                            <th class="text-center" style="width: 50%"> <b>FACILITY NAME</b> </th>
                            <th class="text-center" colspan="1"> <small><i> Asymptomatic </i></small> </th>
                            <th class="text-center" colspan="1"> <small><i> Mild </i></small> </th>
                            <th class="text-center" colspan="1"> <small><i> Moderate </i></small> </th>
                            <th class="text-center" colspan="1"> <small><i> Severe </i></small> </th>
                            <th class="text-center" colspan="1"> <small><i> Critical </i></small> </th>
                            <th class="text-center" colspan="2"> <small><i> Contact (PUM) </i></small> </th>
                            <th class="text-center" colspan="2"> <small><i> Suspect </i></small> </th>
                            <th class="text-center" colspan="2"> <small><i> Probable </i></small> </th>
                            <th class="text-center" colspan="2"> <small><i> Confirmed </i></small> </th>
                        </tr>
                    </thead>
                    <?php
                    $count = 1;
                    ?>
                    @foreach($facilities as $faci)
                        <?php
                            $case = \App\Http\Controllers\admin\ReportCtrl::getNumberOfCases($faci->id);
                        ?>
                        @if(!$case->empty)
                            <tr class="text-center">
                                <td>{{ $count++ }}</td>
                                <td style="overflow-wrap: break-word; width:50%;"> {{ $faci->name }}</td>
                                <td class="refer"> {{ $case->asymp }} </td>
                                <td class="refer"> {{ $case->mild }} </td>
                                <td class="refer"> {{ $case->moderate }} </td>
                                <td class="refer"> {{ $case->severe }} </td>
                                <td class="refer"> {{ $case->critical }} </td>
                                <td class="refer"> {{ $case->refer_contact }}  </td>
                                <td class="discharged"> {{ $case->dis_contact }} </td>
                                <td class="refer"> {{ $case->refer_suspect }} </td>
                                <td class="discharged"> {{ $case->dis_suspect }} </td>
                                <td class="refer"> {{ $case->refer_probable }} </td>
                                <td class="discharged"> {{ $case->dis_probable }} </td>
                                <td class="refer"> {{ $case->refer_confirmed }} </td>
                                <td class="discharged"> {{ $case->dis_confirmed }} </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.table-fixed-header').fixedHeader();
        $('#date_range').daterangepicker();
    </script>
@endsection

