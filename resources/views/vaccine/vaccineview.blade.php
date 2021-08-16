@extends('layouts.app')

@section('content')
<style>
    @media (min-width: 0px) {
        .modal_w {
            width: 80%;
        }
    }
</style>


<div class="box box-success">
    <div class="box-header">
        <form action="{{ asset('vaccine/vaccineview').'/'.$province_id }}" method="GET">
            {{ csrf_field() }}
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
                        <select name="typeof_vaccine_filter" id="typeof_vaccine_filter" class="select2">
                            <option value="">Select Type of Vaccine</option>
                            <option value="Sinovac" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                            <option value="Astrazeneca" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                            <option value="Pfizer" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Pfizer')echo 'selected';} ?> >Pfizer</option>
                            <option value="SputnikV" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'SputnikV')echo 'selected';} ?>>SputnikV</option>
                           <option value="Moderna" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Moderna')echo 'selected';} ?> >Moderna</option>
                            <option value="Johnson" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Johnson')echo 'selected';} ?> >Janssen</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="muncity_filter" id="muncity_filter" class="select2">
                            <option value="">Select Municipality</option>
                            @foreach($muncity as $row)
                                <option value="{{ $row->id }}" <?php if(isset($muncity_filter)){if($muncity_filter == $row->id)echo 'selected';} ?> >{{ $row->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                            <select name="priority_filter" id="priority_filter" class="select2">
                                <option value="">Select Priority</option>
                                <option value="a1" <?php if(isset($priority_filter)){if($priority_filter == 'a1')echo 'selected';} ?> >A1</option>
                                <option value="a2" <?php if(isset($priority_filter)){if($priority_filter == 'a2')echo 'selected';} ?> >A2</option>
                                <option value="a3" <?php if(isset($priority_filter)){if($priority_filter == 'a3')echo 'selected';} ?> >A3</option>
                                <option value="a4" <?php if(isset($priority_filter)){if($priority_filter == 'a4')echo 'selected';} ?> >A4</option>
                                <option value="a5" <?php if(isset($priority_filter)){if($priority_filter == 'a5')echo 'selected';} ?> >A5</option>
                                <option value="b1" <?php if(isset($priority_filter)){if($priority_filter == 'b1')echo 'selected';} ?> >B1</option>
                                <option value="b2" <?php if(isset($priority_filter)){if($priority_filter == 'b2')echo 'selected';} ?> >B2</option>
                                <option value="b3" <?php if(isset($priority_filter)){if($priority_filter == 'b3')echo 'selected';} ?> >B3</option>
                                <option value="b4" <?php if(isset($priority_filter)){if($priority_filter == 'b4')echo 'selected';} ?> >B4</option>
                                <option value="b5" <?php if(isset($priority_filter)){if($priority_filter == 'b5')echo 'selected';} ?> disabled >B5</option>
                                <option value="b6" <?php if(isset($priority_filter)){if($priority_filter == 'b6')echo 'selected';} ?> disabled >B6</option>
                                <option value="c" <?php if(isset($priority_filter)){if($priority_filter == 'c')echo 'selected';} ?> disabled >C</option>
                            </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="date_range" placeholder="Enter date range.." name="date_range" value="{{ date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)) }}">
                    </div>
                    <div class="col-md-8">
                     <span class="input-group-btn">
                        <button type="submit" class="btn btn-success" onclick="loadPage()"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ asset('vaccine/export/excel') }}" type="button" class="btn btn-danger"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                        <a href="{{ asset('vaccine/vaccineview').'/'.$province_id }}" type="button" class="btn btn-warning" onclick="loadPage()"><i class="fa fa-eye"></i> View All</a>
                         <!--
                        <button type="button" class="btn btn-primary" onclick="newVaccinated()"><i class="fa fa-eyedropper"></i> New Vaccinated</button>
                        -->
                    </span>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-red">
                <span class="info-box-icon"><i class="ion ion-erlenmeyer-flask-bubbles"></i></span>
                <div class="info-box-content">
                        <span class="info-box-text">SINOVAC</span>
                        <span class="info-box-number">+{{$sinovac_allocated}}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 50%"></div>
                    </div>
                    <span class="progress-description">
                         {{ $sinovac_completion }}% Goal Completion
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-2 col-xs-6">
            <!-- small box -->
            <div class="info-box bg-yellow">
                <span class="info-box-icon"><i class="ion ion-erlenmeyer-flask-bubbles"></i></span>
                <div class="info-box-content">
                        <span class="info-box-text">ASTRAZENECA</span>
                        <span class="info-box-number">+{{ $astra_allocated }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 50%"></div>
                    </div>
                        <span class="progress-description">
                            {{ $astra_completion }}% Goal Completion
                        </span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <div class="info-box bg-aqua">
                <span class="info-box-icon"><i class="ion ion-erlenmeyer-flask-bubbles"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">PFIZER</span>
                    <span class="info-box-number">+{{ $pfizer_allocated }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 50%"></div>
                    </div>
                    <span class="progress-description">
                        {{ $pfizer_completion }}% Goal Completion
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="ion ion-erlenmeyer-flask-bubbles"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">SPUTNIKV</span>
                    <span class="info-box-number">+{{ $sputnikv_allocated }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 50%"></div>
                    </div>
                    <span class="progress-description">
                        {{ $sputnikv_completion }}% Goal Completion
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <div class="info-box bg-purple">
                <span class="info-box-icon"><i class="ion ion-erlenmeyer-flask-bubbles"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">MODERNA</span>
                    <span class="info-box-number">+{{ $moderna_allocated }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 50%"></div>
                    </div>
                    <span class="progress-description">
                        {{ $moderna_completion }}% Goal Completion
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-6">
            <div class="info-box" style="background: #1d94ff;">
                <span class="info-box-icon"><i class="ion ion-erlenmeyer-flask-bubbles"  style="color: white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="color: white">Janssen</span>
                    <span class="info-box-number"  style="color: white">+{{ $johnson_allocated }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 50%"></div>
                    </div>
                    <span class="progress-description" style="color: white">
                        {{ $johnson_completion }}% Goal Completion
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="row" style="padding-left: 1%;padding-right: 1%">
            <div class="col-md-2">
                <div class="info-box" style="background-color: #d0fffe">
                    <span class="info-box-icon" style="background-color: #d0fffe" ><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(A1)</b> </span>
                        <span class="info-box-number">+{{ number_format($a1_target) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a1_completion }}%; background-color: #514f4f;"></div>
                        </div>
                        <span class="progress-description">
                            {{ $a1_completion }}% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #e4ffde" >
                    <span class="info-box-icon" style="background-color: #e4ffde" ><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(A2)</b> </span>
                        <span class="info-box-number">+{{ number_format($a2_target) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a2_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                            {{ $a2_completion }}% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #d0fffe">
                    <span class="info-box-icon" style="background-color: #d0fffe"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(A3)</b> </span>
                        <span class="info-box-number">+{{ number_format($a3_target) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a3_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                            {{ $a3_completion }}% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #e4ffde">
                    <span class="info-box-icon" style="background-color: #e4ffde"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(A4)</b> </span>
                        <span class="info-box-number">+{{ number_format($a4_target) }}</span>
                        <div class="progress">
                        <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                            {{ $a4_completion }}% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #d0fffe">
                    <span class="info-box-icon" style="background-color: #d0fffe"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(A5)</b> </span>
                        <span class="info-box-number">+{{ number_format($a5_target) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                            {{ $a5_completion }}% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #e4ffde">
                    <span class="info-box-icon" style="background-color: #e4ffde"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(B1)</b> </span>
                        <span class="info-box-number">+{{ number_format($b1_target) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                             {{ $b1_completion }}% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #d0fffe">
                    <span class="info-box-icon" style="background-color: #d0fffe"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(B2)</b> </span>
                        <span class="info-box-number">+{{ number_format($b2_target) }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                             {{ $b2_completion }}% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #e4ffde">
                    <span class="info-box-icon" style="background-color: #e4ffde"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(B3)</b> </span>
                        <span class="info-box-number">+{{ number_format($b3_target) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                            {{ $b3_completion }}% Goal Completion
                    </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #d0fffe">
                    <span class="info-box-icon" style="background-color: #d0fffe"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(B4)</b> </span>
                        <span class="info-box-number">+{{ number_format($b4_target) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                            {{ $b4_completion }}% Goal Completion
                       </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #e4ffde">
                    <span class="info-box-icon" style="background-color: #e4ffde"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(B5)</b> </span>
                        <span class="info-box-number">+0</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                        0% Goal Completion
                    </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #d0fffe">
                    <span class="info-box-icon" style="background-color: #d0fffe"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(B6)</b> </span>
                        <span class="info-box-number">+0</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                        0% Goal Completion
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-2">
                <div class="info-box" style="background-color: #e4ffde">
                    <span class="info-box-icon" style="background-color: #e4ffde"><i class="ion ion-ios-medkit-outline"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text" style="font-size: 9.5pt;">Target vaccination  <b>(C)</b> </span>
                        <span class="info-box-number">+0</span>

                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $a4_completion }}%; background-color: #514f4f"></div>
                        </div>
                        <span class="progress-description">
                        0% Goal Completion
                    </span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
        @if(count($data)>0)
            @foreach($data as $row)
                <?php

                $vaccine = \App\VaccineAccomplished::where("muncity_id",$row->id)->orderBy("date_first","asc")->first();

                $global_elipop_a1 = \App\Muncity::select(DB::raw("sum(COALESCE(a1,0)) as a1"))->where("province_id",$province_id)->where("id",$row->id)->first()->a1;
                $global_elipop_a2 = \App\Muncity::select(DB::raw("sum(COALESCE(a2,0)) as a2"))->where("province_id",$province_id)->where("id",$row->id)->first()->a2;
                $global_elipop_a3 = \App\Muncity::select(DB::raw("sum(COALESCE(a3,0)) as a3"))->where("province_id",$province_id)->where("id",$row->id)->first()->a3;
                $global_elipop_a4 = \App\Muncity::select(DB::raw("sum(COALESCE(a4,0)) as a4"))->where("province_id",$province_id)->where("id",$row->id)->first()->a4;
                $global_elipop_a5 = \App\Muncity::select(DB::raw("sum(COALESCE(a5,0)) as a5"))->where("province_id",$province_id)->where("id",$row->id)->first()->a5;
                $global_elipop_b1 = \App\Muncity::select(DB::raw("sum(COALESCE(b1,0)) as b1"))->where("province_id",$province_id)->where("id",$row->id)->first()->b1;
                $global_elipop_b2 = \App\Muncity::select(DB::raw("sum(COALESCE(b2,0)) as b2"))->where("province_id",$province_id)->where("id",$row->id)->first()->b2;
                $global_elipop_b3 = \App\Muncity::select(DB::raw("sum(COALESCE(b3,0)) as b3"))->where("province_id",$province_id)->where("id",$row->id)->first()->b3;
                $global_elipop_b4 = \App\Muncity::select(DB::raw("sum(COALESCE(b4,0)) as b4"))->where("province_id",$province_id)->where("id",$row->id)->first()->b4;

                $total_epop_svac_a1 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_a1 :0; //E_POP_A1_SINOVAC
                $total_epop_svac_a2 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_a2 :0; //E_POP_A2_SINOVAC
                $total_epop_svac_a3 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_a3 :0; //E_POP_A3_SINOVAC
                $total_epop_svac_a4 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_a4 :0; //E_POP_A4_SINOVAC
                $total_epop_svac_a5 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_a5 :0; //E_POP_A5_SINOVAC
                $total_epop_svac_b1 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_b1 :0; //E_POP_B1_SINOVAC
                $total_epop_svac_b2 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_b2 :0; //E_POP_B2_SINOVAC
                $total_epop_svac_b3 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_b3 :0; //E_POP_B3_SINOVAC
                $total_epop_svac_b4 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $global_elipop_b4 :0; //E_POP_B4_SINOVAC
                $total_epop_svac =  $total_epop_svac_a1 + $total_epop_svac_a2 + $total_epop_svac_a3 + $total_epop_svac_a4 +
                    $total_epop_svac_a5 + $total_epop_svac_b1 + $total_epop_svac_b2 + $total_epop_svac_b3 + $total_epop_svac_b4;  //TOTAL_E_POP_SINOVAC

                //ASTRAZENECA
                $total_epop_astra_a1 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_a1 :0; //E_POP_A1_ASTRA
                $total_epop_astra_a2 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_a2 :0; //E_POP_A2_ASTRA
                $total_epop_astra_a3 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_a3 :0; //E_POP_A3_ASTRA
                $total_epop_astra_a4 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_a4 :0; //E_POP_A4_ASTRA
                $total_epop_astra_a5 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_a5 :0; //E_POP_A5_ASTRA
                $total_epop_astra_b1 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_b1 :0; //E_POP_B1_ASTRA
                $total_epop_astra_b2 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_b2 :0; //E_POP_B2_ASTRA
                $total_epop_astra_b3 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_b3 :0; //E_POP_B3_ASTRA
                $total_epop_astra_b4 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $global_elipop_b4 :0; //E_POP_B4_ASTRA
                $total_epop_astra = $total_epop_astra_a1 + $total_epop_astra_a2 + $total_epop_astra_a3 + $total_epop_astra_a4 +  $total_epop_astra_a5 +
                                    $total_epop_astra_b1 + $total_epop_astra_b2 + $total_epop_astra_b3 + $total_epop_astra_b4 ;  //TOTAL_E_POP_ASTRA

                $total_epop_overall = $row->a1 + $row->a2 + $row->a3 + $row->a4 + $row->a5 + $row->b1 + $row->b2 + $row->b3 + $row->b4 + $row->b5;

                //PFIZER
                $total_epop_pfizer_a1 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_a1 : 0; //E_POP_A1_PFIZER
                $total_epop_pfizer_a2 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_a2 : 0; //E_POP_A2_PFIZER
                $total_epop_pfizer_a3 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_a3 : 0; //E_POP_A3_PFIZER
                $total_epop_pfizer_a4 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_a4 : 0; //E_POP_A4_PFIZER
                $total_epop_pfizer_a5 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_a5 : 0; //E_POP_A5_PFIZER
                $total_epop_pfizer_b1 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_b1 : 0; //E_POP_B1_PFIZER
                $total_epop_pfizer_b2 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_b2 : 0; //E_POP_B2_PFIZER
                $total_epop_pfizer_b3 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_b3 : 0; //E_POP_B3_PFIZER
                $total_epop_pfizer_b4 = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $global_elipop_b4 : 0; //E_POP_B4_PFIZER
                $total_epop_pfizer = $total_epop_pfizer_a1 + $total_epop_pfizer_a2 + $total_epop_pfizer_a3 + $total_epop_pfizer_a4 +  $total_epop_pfizer_a5 +
                                     $total_epop_pfizer_b1 + $total_epop_pfizer_b2 + $total_epop_pfizer_b3 + $total_epop_pfizer_b4; //TOTAL_E_POP_PFIZER

                //SPUTNIK V
                $total_epop_sputnikv_a1 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_a1 : 0; //E_POP_A1_SPUTNIKV
                $total_epop_sputnikv_a2 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_a2 : 0; //E_POP_A2_SPUTNIKV
                $total_epop_sputnikv_a3 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_a3 : 0; //E_POP_A3_SPUTNIKV
                $total_epop_sputnikv_a4 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_a4 : 0; //E_POP_A4_SPUTNIKV
                $total_epop_sputnikv_a5 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_a5 : 0; //E_POP_A5_SPUTNIKV
                $total_epop_sputnikv_b1 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_b1 : 0; //E_POP_B1_SPUTNIKV
                $total_epop_sputnikv_b2 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_b2 : 0; //E_POP_B2_SPUTNIKV
                $total_epop_sputnikv_b3 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_b3 : 0; //E_POP_B3_SPUTNIKV
                $total_epop_sputnikv_b4 = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $global_elipop_b4 : 0; //E_POP_B4_SPUTNIKV
                $total_epop_sputnikv = $total_epop_sputnikv_a1 + $total_epop_sputnikv_a2 + $total_epop_sputnikv_a3 + $total_epop_sputnikv_a4 +  $total_epop_sputnikv_a5 +
                                       $total_epop_sputnikv_b1 + $total_epop_sputnikv_b2 + $total_epop_sputnikv_b3 + $total_epop_sputnikv_b4; //TOTAL_E_POP_SPUTNIKV


                //MODERNA
                $total_epop_moderna_a1 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_a1 : 0; //E_POP_A1_MODERNA
                $total_epop_moderna_a2 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_a2 : 0; //E_POP_A2_MODERNA
                $total_epop_moderna_a3 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_a3 : 0; //E_POP_A3_MODERNA
                $total_epop_moderna_a4 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_a4 : 0; //E_POP_A4_MODERNA
                $total_epop_moderna_a5 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_a5 : 0; //E_POP_A5_MODERNA
                $total_epop_moderna_b1 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_b1 : 0; //E_POP_B1_MODERNA
                $total_epop_moderna_b2 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_b2 : 0; //E_POP_B2_MODERNA
                $total_epop_moderna_b3 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_b3 : 0; //E_POP_B3_MODERNA
                $total_epop_moderna_b4 = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $global_elipop_b4 : 0; //E_POP_B4_MODERNA
                $total_epop_moderna = $total_epop_moderna_a1 + $total_epop_moderna_a2 + $total_epop_moderna_a3 + $total_epop_moderna_a4 +  $total_epop_moderna_a5 +
                                      $total_epop_moderna_b1 + $total_epop_moderna_b2 + $total_epop_moderna_b3 + $total_epop_moderna_b4; //TOTAL_E_POP_MODERNA


                //JOHNSON
                $total_epop_johnson_a1 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_a1 : 0; //E_POP_A1_JOHNSON
                $total_epop_johnson_a2 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_a2 : 0; //E_POP_A2_JOHNSON
                $total_epop_johnson_a3 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_a3 : 0; //E_POP_A3_JOHNSON
                $total_epop_johnson_a4 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_a4 : 0; //E_POP_A4_JOHNSON
                $total_epop_johnson_a5 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_a5 : 0; //E_POP_A5_JOHNSON
                $total_epop_johnson_b1 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_b1 : 0; //E_POP_B1_JOHNSON
                $total_epop_johnson_b2 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_b2 : 0; //E_POP_B2_JOHNSON
                $total_epop_johnson_b3 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_b3 : 0; //E_POP_B3_JOHNSON
                $total_epop_johnson_b4 = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $global_elipop_b4 : 0; //E_POP_B4_JOHNSON
                $total_epop_johnson = $total_epop_johnson_a1 + $total_epop_johnson_a2 + $total_epop_johnson_a3 + $total_epop_johnson_a4 +  $total_epop_johnson_a5 +
                                      $total_epop_johnson_b1 + $total_epop_johnson_b2 + $total_epop_johnson_b3 + $total_epop_johnson_b4; //TOTAL_E_POP_JOHNSON


                //VACCINE_ALLOCATED
                $total_vallocated_svac_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->sinovac_allocated_first :0; //VACCINE ALLOCATED_SINOVAC (FD)
                $total_vallocated_svac_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->sinovac_allocated_second :0; //VACCINE ALLOCATED_SINOVAC (SD)
                $total_vallocated_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->astrazeneca_allocated_first :0; //VACCINE ALLOCATED_ASTRA (FD)
                $total_vallocated_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->astrazeneca_allocated_second :0; //VACCINE ALLOCATED_ASTRA (SD)
                $total_vallocated_pfizer_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $row->pfizer_allocated_first :0; //VACCINE ALLOCATED_PFIZER (FD)
                $total_vallocated_pfizer_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? $row->pfizer_allocated_second :0; //VACCINE ALLOCATED_PFIZER (SD)
                $total_vallocated_sputnikv_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $row->sputnikv_allocated_first :0; //VACCINE ALLOCATED_SPUTNIKV (FD)
                $total_vallocated_sputnikv_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? $row->sputnikv_allocated_second :0; //VACCINE ALLOCATED_SPUTNIKV (SD)
                $total_vallocated_moderna_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $row->moderna_allocated_first :0; //VACCINE ALLOCATED_MODERNA (FD)
                $total_vallocated_moderna_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? $row->moderna_allocated_second :0; //VACCINE ALLOCATED_MODERNA (SD)
                $total_vallocated_johnson_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $row->johnson_allocated_first :0; //VACCINE ALLOCATED_MODERNA (FD)
                $total_vallocated_johnson_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? $row->johnson_allocated_second :0; //VACCINE ALLOCATED_MODERNA (SD)


                //VACCINATED_SINOVAC_FIRST
                $total_svac_a1_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_first :0; //A1_SINOVAC
                $total_svac_a2_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_first :0; //A2_SINOVAC
                $total_svac_a3_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_first :0; //A3_SINOVAC
                $total_svac_a4_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_first :0; //A4_SINOVAC
                $total_svac_a5_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_first :0; //A5_SINOVAC
                $total_svac_b1_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_first :0; //B1_SINOVAC
                $total_svac_b2_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_first :0; //B2_SINOVAC
                $total_svac_b3_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_first :0; //B3_SINOVAC
                $total_svac_b4_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_first :0; //B4_SINOVAC

                //VACCINATED_SINOVAC_SECOND
                $total_svac_a1_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_second :0; //A1_SINOVAC 2
                $total_svac_a2_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_second :0; //A2_SINOVAC 2
                $total_svac_a3_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_second :0; //A3_SINOVAC 2
                $total_svac_a4_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_second :0; //A4_SINOVAC 2
                $total_svac_a5_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_second :0; //A5_SINOVAC 2
                $total_svac_b1_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_second :0; //B1_SINOVAC 2
                $total_svac_b2_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_second :0; //B2_SINOVAC 2
                $total_svac_b3_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_second :0; //B3_SINOVAC 2
                $total_svac_b4_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_second :0; //B4_SINOVAC 2

                //VACCINATED_ASTRAZENECA_FIRST
                $total_astra_a1_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_first :0; //A1_ASTRA
                $total_astra_a2_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_first :0; //A2_ASTRA
                $total_astra_a3_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_first :0; //A3_ASTRA
                $total_astra_a4_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_first :0; //A4_ASTRA
                $total_astra_a5_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_first :0; //A5_ASTRA
                $total_astra_b1_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_first :0; //B1_ASTRA
                $total_astra_b2_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_first :0; //B2_ASTRA
                $total_astra_b3_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_first :0; //B3_ASTRA
                $total_astra_b4_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_first :0; //B4_ASTRA

                //VACCINATED_ASTRAZENECA_SECOND
                $total_astra_a1_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_second :0; //A1_ASTRA 2
                $total_astra_a2_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_second :0; //A2_ASTRA 2
                $total_astra_a3_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_second :0; //A3_ASTRA 2
                $total_astra_a4_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_second :0; //A4_ASTRA 2
                $total_astra_a5_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_second :0; //A5_ASTRA 2
                $total_astra_b1_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_second :0; //B1_ASTRA 2
                $total_astra_b2_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_second :0; //B2_ASTRA 2
                $total_astra_b3_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_second :0; //B3_ASTRA 2
                $total_astra_b4_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_second :0; //B4_ASTRA 2

                //VACCINATED_PFIZER_FIRST
                $total_pfizer_a1_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_first :0; //A1_PFIZER
                $total_pfizer_a2_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_first :0; //A2_PFIZER
                $total_pfizer_a3_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_first :0; //A3_PFIZER
                $total_pfizer_a4_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_first :0; //A4_PFIZER
                $total_pfizer_a5_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_first :0; //A5_PFIZER
                $total_pfizer_b1_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_first :0; //B1_PFIZER
                $total_pfizer_b2_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_first :0; //B2_PFIZER
                $total_pfizer_b3_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_first :0; //B3_PFIZER
                $total_pfizer_b4_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_first :0; //B4_PFIZER

                //VACCINATED_PFIZER_SECOND
                $total_pfizer_a1_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_second :0; //A1_PFIZER 2
                $total_pfizer_a2_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_second :0; //A2_PFIZER 2
                $total_pfizer_a3_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_second :0; //A3_PFIZER 2
                $total_pfizer_a4_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_second :0; //A4_PFIZER 2
                $total_pfizer_a5_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_second :0; //A5_PFIZER 2
                $total_pfizer_b1_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_second :0; //B1_PFIZER 2
                $total_pfizer_b2_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_second :0; //B2_PFIZER 2
                $total_pfizer_b3_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_second :0; //B3_PFIZER 2
                $total_pfizer_b4_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_second :0; //B4_PFIZER 2

                //VACCINATED_SPUTNIKV_FIRST
                $total_sputnikv_a1_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_first :0; //A1_SPUTNIKV
                $total_sputnikv_a2_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_first :0; //A2_SPUTNIKV
                $total_sputnikv_a3_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_first :0; //A3_SPUTNIKV
                $total_sputnikv_a4_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_first :0; //A4_SPUTNIKV
                $total_sputnikv_a5_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_first :0; //A5_SPUTNIKV
                $total_sputnikv_b1_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_first :0; //B1_SPUTNIKV
                $total_sputnikv_b2_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_first :0; //B2_SPUTNIKV
                $total_sputnikv_b3_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_first :0; //B3_SPUTNIKV
                $total_sputnikv_b4_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_first :0; //B4_SPUTNIKV

                //VACCINATED_SPUTNIKV_SECOND
                $total_sputnikv_a1_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_second :0; //A1_SPUTNIKV 2
                $total_sputnikv_a2_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_second :0; //A2_SPUTNIKV 2
                $total_sputnikv_a3_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_second :0; //A3_SPUTNIKV 2
                $total_sputnikv_a4_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_second :0; //A4_SPUTNIKV 2
                $total_sputnikv_a5_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_second :0; //A5_SPUTNIKV 2
                $total_sputnikv_b1_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_second :0; //B1_SPUTNIKV 2
                $total_sputnikv_b2_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_second :0; //B2_SPUTNIKV 2
                $total_sputnikv_b3_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_second :0; //B3_SPUTNIKV 2
                $total_sputnikv_b4_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_second :0; //B4_SPUTNIKV 2


                //VACCINATED_MODERNA_FIRST
                $total_moderna_a1_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_first :0; //A1_MODERNA
                $total_moderna_a2_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_first :0; //A2_MODERNA
                $total_moderna_a3_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_first :0; //A3_MODERNA
                $total_moderna_a4_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_first :0; //A4_MODERNA
                $total_moderna_a5_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_first :0; //A5_MODERNA
                $total_moderna_b1_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_first :0; //B1_MODERNA
                $total_moderna_b2_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_first :0; //B2_MODERNA
                $total_moderna_b3_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_first :0; //B3_MODERNA
                $total_moderna_b4_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_first :0; //B4_MODERNA

                //VACCINATED_MODERNA_SECOND
                $total_moderna_a1_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_second :0; //A1_MODERNA 2
                $total_moderna_a2_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_second :0; //A2_MODERNA 2
                $total_moderna_a3_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_second :0; //A3_MODERNA 2
                $total_moderna_a4_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_second :0; //A4_MODERNA 2
                $total_moderna_a5_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_second :0; //A5_MODERNA 2
                $total_moderna_b1_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_second :0; //B1_MODERNA 2
                $total_moderna_b2_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_second :0; //B2_MODERNA 2
                $total_moderna_b3_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_second :0; //B3_MODERNA 2
                $total_moderna_b4_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_second :0; //B4_MODERNA 2

                //VACCINATED_JOHNSON_FIRST
                $total_johnson_a1_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_first :0; //A1_JOHNSON
                $total_johnson_a2_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_first :0; //A2_JOHNSON
                $total_johnson_a3_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_first :0; //A3_JOHNSON
                $total_johnson_a4_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_first :0; //A4_JOHNSON
                $total_johnson_a5_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_first :0; //A5_JOHNSON
                $total_johnson_b1_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_first :0; //B1_JOHNSON
                $total_johnson_b2_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_first :0; //B2_JOHNSON
                $total_johnson_b3_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_first :0; //B3_JOHNSON
                $total_johnson_b4_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_first,0)) as vaccinated_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_first :0; //B4_JOHNSON

                //VACCINATED_JOHNSON_SECOND
                $total_johnson_a1_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a1")->whereNull("facility_id")->first()->vaccinated_second :0; //A1_JOHNSON 2
                $total_johnson_a2_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a2")->whereNull("facility_id")->first()->vaccinated_second :0; //A2_JOHNSON 2
                $total_johnson_a3_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a3")->whereNull("facility_id")->first()->vaccinated_second :0; //A3_JOHNSON 2
                $total_johnson_a4_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a4")->whereNull("facility_id")->first()->vaccinated_second :0; //A4_JOHNSON 2
                $total_johnson_a5_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","a5")->whereNull("facility_id")->first()->vaccinated_second :0; //A5_JOHNSON 2
                $total_johnson_b1_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b1")->whereNull("facility_id")->first()->vaccinated_second :0; //B1_JOHNSON 2
                $total_johnson_b2_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b2")->whereNull("facility_id")->first()->vaccinated_second :0; //B2_JOHNSON 2
                $total_johnson_b3_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b3")->whereNull("facility_id")->first()->vaccinated_second :0; //B3_JOHNSON 2
                $total_johnson_b4_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(vaccinated_second,0)) as vaccinated_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->where("priority","b4")->whereNull("facility_id")->first()->vaccinated_second :0; //B4_JOHNSON 2

                $total_mild_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_first,0)) as mild_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->mild_first :0; //MILD_SINOVAC
                $total_mild_astra_frst =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_first,0)) as mild_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->mild_first :0; //MILD_ASTRA
                $total_mild_pfizer_frst =  $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_first,0)) as mild_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->mild_first :0; //MILD_PFIZER
                $total_mild_sputnikv_frst =  $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_first,0)) as mild_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->mild_first :0; //MILD_SPUTNIKV
                $total_mild_moderna_frst =  $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_first,0)) as mild_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->mild_first :0; //MILD_MODERNA
                $total_mild_johnson_frst =  $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_first,0)) as mild_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->mild_first :0; //MILD_JOHNSON

                $total_mild_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_second,0)) as mild_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->mild_second :0; //MILD_SINOVAC 2
                $total_mild_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_second,0)) as mild_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->mild_second :0; //MILD_ASTRA 2
                $total_mild_pfizer_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_second,0)) as mild_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->mild_second :0; //MILD_PFIZER 2
                $total_mild_sputnikv_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_second,0)) as mild_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->mild_second :0; //MILD_SPUTNIK 2
                $total_mild_moderna_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_second,0)) as mild_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->mild_second :0; //MILD_MODERNA 2
                $total_mild_johnson_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(mild_second,0)) as mild_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->mild_second :0; //MILD_JOHNSON 2

                $total_srs_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_first,0)) as serious_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->serious_first :0; //SERIOUS_SINOVAC
                $total_srs_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_first,0)) as serious_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->serious_first :0; //SERIOUS_ASTRA
                $total_srs_pfizer_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_first,0)) as serious_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->serious_first :0; //SERIOUS_PFIZER
                $total_srs_sputnikv_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_first,0)) as serious_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->serious_first :0; //SERIOUS_SPUTNIKV
                $total_srs_moderna_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_first,0)) as serious_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->serious_first :0; //SERIOUS_MODERNA
                $total_srs_johnson_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_first,0)) as serious_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->serious_first :0; //SERIOUS_JOHNSON

                $total_srs_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_second,0)) as serious_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->serious_second :0; //SERIOUS_SINOVAC 2
                $total_srs_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_second,0)) as serious_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->serious_second :0;  //SERIOUS_AZTRAZENECA 2
                $total_srs_pfizer_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_second,0)) as serious_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->serious_second :0; //SERIOUS_PFIZER2
                $total_srs_sputnikv_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_second,0)) as serious_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->serious_second :0; // SERIOUS SPUTNIK2
                $total_srs_moderna_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_second,0)) as serious_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->serious_second :0; // SERIOUS MODERNA2
                $total_srs_johnson_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(serious_second,0)) as serious_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->serious_second :0; // SERIOUS JOHNSON2

                $total_dfrd_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_first,0)) as deferred_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->deferred_first :0; //DEFERRED_SINOVAC
                $total_dfrd_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_first,0)) as deferred_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->deferred_first :0;  //DEFERRED_ASTRAZENECA
                $total_dfrd_pfizer_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ?\App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_first,0)) as deferred_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->deferred_first :0; //DEFERRED_PFIZER
                $total_dfrd_sputnikv_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_first,0)) as deferred_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->deferred_first :0;  //DEFERRED_SPUTNIKV
                $total_dfrd_moderna_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_first,0)) as deferred_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->deferred_first :0;  //DEFERRED_MODERNA
                $total_dfrd_johnson_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_first,0)) as deferred_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->deferred_first :0;  //DEFERRED_JOHNSON

                $total_dfrd_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_second,0)) as deferred_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->deferred_second :0; //DEFERRED_SINOVAC 2
                $total_dfrd_astra_scnd =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_second,0)) as deferred_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->deferred_second :0; //DEFERRED_ASTRA 2
                $total_dfrd_pfizer_scnd =  $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_second,0)) as deferred_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->deferred_second :0; //DEFERRED_PFIZER 2
                $total_dfrd_sputnikv_scnd =  $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_second,0)) as deferred_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->deferred_second :0; //DEFERRED_SPUTNIKV 2
                $total_dfrd_moderna_scnd =  $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_second,0)) as deferred_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->deferred_second :0; //DEFERRED_MODERNA 2
                $total_dfrd_johnson_scnd =  $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(deferred_second,0)) as deferred_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->deferred_second :0; //DEFERRED_JOHNSON 2

                $total_rfsd_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_first,0)) as refused_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->refused_first :0; //REFUSED_SINOVAC
                $total_rfsd_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_first,0)) as refused_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->refused_first :0; //REFUSED_ASTRA
                $total_rfsd_pfizer_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_first,0)) as refused_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->refused_first :0; //REFUSED_PFIZER
                $total_rfsd_sputnikv_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_first,0)) as refused_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->refused_first :0; //REFUSED_SPUTNIKV
                $total_rfsd_moderna_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_first,0)) as refused_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->refused_first :0; //REFUSED_JOHNSON
                $total_rfsd_johnson_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_first,0)) as refused_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->refused_first :0; //REFUSED_JOHNSON

                $total_rfsd_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_second,0)) as refused_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->refused_second :0; //REFUSED_SINOVAC 2
                $total_rfsd_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_second,0)) as refused_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->refused_second :0; //REFUSED_ASTRA 2
                $total_rfsd_pfizer_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_second,0)) as refused_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->refused_second :0; //REFUSED_PFIZER 2
                $total_rfsd_sputnikv_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_second,0)) as refused_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->refused_second :0; //REFUSED_SPUTNIKV 2
                $total_rfsd_moderna_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_second,0)) as refused_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->refused_second :0; //REFUSED_MODERNA 2
                $total_rfsd_johnson_scnd = $typeof_vaccine_filter == "Johnsonj" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(refused_second,0)) as refused_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->refused_second :0; //REFUSED_JOHNSON 2

                $total_wstge_svac_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_first,0)) as wastage_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->wastage_first :0; //WASTAGF_SINOVAC
                $total_wstge_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_first,0)) as wastage_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->wastage_first :0; //WASTAGE_ASTRA
                $total_wstge_pfizer_frst = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_first,0)) as wastage_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->wastage_first :0; //WASTAGE_PFIZER
                $total_wstge_sputnikv_frst = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_first,0)) as wastage_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->wastage_first :0; //WASTAGE_SPUTNIKV
                $total_wstge_moderna_frst = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_first,0)) as wastage_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->wastage_first :0; //WASTAGE_MODERNA
                $total_wstge_johnson_frst = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ?  \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_first,0)) as wastage_first"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->wastage_first :0; //WASTAGE_JOHNSON


                $total_wstge_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_second,0)) as wastage_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Sinovac")->whereNull("facility_id")->first()->wastage_second :0; //WASTAGE_SINOVAC 2
                $total_wstge_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_second,0)) as wastage_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Astrazeneca")->whereNull("facility_id")->first()->wastage_second :0; //WASTAGE_ASTRA2
                $total_wstge_pfizer_scnd = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_second,0)) as wastage_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Pfizer")->whereNull("facility_id")->first()->wastage_second :0; //WASTAGE_PFIZER2
                $total_wstge_sputnikv_scnd = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_second,0)) as wastage_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","SputnikV")->whereNull("facility_id")->first()->wastage_second :0; //WASTAGE_SPUTNIKV2
                $total_wstge_moderna_scnd = $typeof_vaccine_filter == "Moderna" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_second,0)) as wastage_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Moderna")->whereNull("facility_id")->first()->wastage_second :0; //WASTAGE_MODERNA 2
                $total_wstge_johnson_scnd = $typeof_vaccine_filter == "Johnson" || empty($typeof_vaccine_filter) ? \App\VaccineAccomplished::select(\DB::raw("SUM(COALESCE(wastage_second,0)) as wastage_second"))->where("province_id",$province_id)->where("muncity_id",$row->id)->where("typeof_vaccine","Johnson")->whereNull("facility_id")->first()->wastage_second :0; //WASTAGE_JOHNSON 2


                $total_vcted_svac_frst =  $total_svac_a1_frst + $total_svac_a2_frst + $total_svac_a3_frst + $total_svac_a4_frst + $total_svac_a5_frst +
                                          $total_svac_b1_frst +  $total_svac_b2_frst + $total_svac_b3_frst + $total_svac_b4_frst; //TOTAL VACCINATED_SINOVAC FIRST
                $total_vcted_astra_frst = $total_astra_a1_frst + $total_astra_a2_frst + $total_astra_a3_frst + $total_astra_a4_frst + $total_astra_a5_frst +
                                          $total_astra_b1_frst + $total_astra_b2_frst + $total_astra_b3_frst + $total_astra_b4_frst; //TOTAL VACCINATED_ASTRA FIRST
                $total_vcted_pfizer_frst = $total_pfizer_a1_frst + $total_pfizer_a2_frst + $total_pfizer_a3_frst + $total_pfizer_a4_frst + $total_pfizer_a5_frst +
                                         $total_pfizer_b1_frst + $total_pfizer_b2_frst + $total_pfizer_b3_frst + $total_pfizer_b4_frst ; // TOTAL VACCINATED_PFIZER FIRST
                $total_vcted_sputnikv_frst = $total_sputnikv_a1_frst + $total_sputnikv_a2_frst + $total_sputnikv_a3_frst + $total_sputnikv_a4_frst +  $total_sputnikv_a5_frst +
                                             $total_sputnikv_b1_frst + $total_sputnikv_b2_frst + $total_sputnikv_b3_frst + $total_sputnikv_b4_frst; //TOTAL VACCINATED_SPUTNIKV FIRST
                $total_vcted_moderna_frst = $total_moderna_a1_frst + $total_moderna_a2_frst + $total_moderna_a3_frst + $total_moderna_a4_frst +  $total_moderna_a5_frst +
                                            $total_moderna_b1_frst + $total_moderna_b2_frst + $total_moderna_b3_frst + $total_moderna_b4_frst; //TOTAL VACCINATED_MODERNA FIRST
                $total_vcted_johnson_frst = $total_johnson_a1_frst + $total_johnson_a2_frst + $total_johnson_a3_frst + $total_johnson_a4_frst +  $total_johnson_a5_frst +
                                             $total_johnson_b1_frst + $total_johnson_b2_frst + $total_johnson_b3_frst + $total_johnson_b4_frst; //TOTAL VACCINATED_JOHNSON FIRST

                $total_vcted_svac_scnd = $total_svac_a1_scnd + $total_svac_a2_scnd + $total_svac_a3_scnd + $total_svac_a4_scnd + $total_svac_a5_scnd +
                                         $total_svac_b1_scnd + $total_svac_b2_scnd + $total_svac_b3_scnd + $total_svac_b4_scnd; //TOTAL VACCINATED_SINOVAC SECOND
                $total_vcted_astra_scnd = $total_astra_a1_scnd + $total_astra_a2_scnd + $total_astra_a3_scnd + $total_astra_a4_scnd + $total_astra_a5_scnd +
                                          $total_astra_b1_scnd + $total_astra_b2_scnd + $total_astra_b3_scnd + $total_astra_b4_scnd ; //TOTAL VACCINATED_ASTRA SECOND
                $total_vcted_pfizer_scnd =  $total_pfizer_a1_scnd + $total_pfizer_a2_scnd + $total_pfizer_a3_scnd + $total_pfizer_a4_scnd + $total_pfizer_a4_scnd +
                                            $total_pfizer_b1_scnd + $total_pfizer_b2_scnd + $total_pfizer_b3_scnd + $total_pfizer_b4_scnd  ; // TOTAL VACCINATED_PFIZER SECOND
                $total_vcted_sputnikv_scnd = $total_sputnikv_a1_scnd + $total_sputnikv_a2_scnd + $total_sputnikv_a3_scnd + $total_sputnikv_a4_scnd + $total_sputnikv_a5_scnd +
                                             $total_sputnikv_b1_scnd + $total_sputnikv_b2_scnd + $total_sputnikv_b3_scnd + $total_sputnikv_b4_scnd; //TOTAL VACCINATED_SPUTNIKV SECOND
                $total_vcted_moderna_scnd = $total_moderna_a1_scnd + $total_moderna_a2_scnd + $total_moderna_a3_scnd + $total_moderna_a4_scnd + $total_moderna_a5_scnd +
                                             $total_moderna_b1_scnd + $total_moderna_b2_scnd + $total_moderna_b3_scnd + $total_moderna_b4_scnd; //TOTAL VACCINATED_MODERNA SECOND
                $total_vcted_johnson_scnd = $total_johnson_a1_scnd + $total_johnson_a2_scnd + $total_johnson_a3_scnd + $total_johnson_a4_scnd + $total_johnson_a5_scnd +
                                            $total_johnson_b1_scnd + $total_johnson_b2_scnd + $total_johnson_b3_scnd + $total_johnson_b4_scnd; //TOTAL VACCINATED_JOHNSON SECOND

                $total_vcted_frst = $total_vcted_svac_frst + $total_vcted_astra_frst + $total_vcted_pfizer_frst + $total_vcted_sputnikv_frst + $total_vcted_moderna_frst + $total_vcted_johnson_frst ; //TOTAL_VACCINATED
                $total_vcted_scnd = $total_vcted_svac_scnd + $total_vcted_astra_scnd + $total_vcted_pfizer_scnd + $total_vcted_sputnikv_scnd + $total_vcted_moderna_scnd + $total_vcted_johnson_scnd ; //TOTAL_VACCINATED - 2

                $total_vallocated_svac = $total_vallocated_svac_frst + $total_vallocated_svac_scnd; //TOTAL VACCINE ALLOCATED_SINOVAC
                $total_vallocated_astra = $total_vallocated_astra_frst + $total_vallocated_astra_scnd; //TOTAL VACCINE ALLOCATED_ASTRA
                $total_vallocated_pfizer = $total_vallocated_pfizer_frst + $total_vallocated_pfizer_scnd; //TOTAL VACCINE ALLOCATED_PFIZER
                $total_vallocated_sputnikv = $total_vallocated_sputnikv_frst + $total_vallocated_sputnikv_scnd; //TOTAL VACCINE ALLOCATED_SPUTNIKV
                $total_vallocated_moderna = $total_vallocated_moderna_frst + $total_vallocated_moderna_scnd; //TOTAL VACCINE ALLOCATED_MODERNA
                $total_vallocated_johnson = $total_vallocated_johnson_frst + $total_vallocated_johnson_scnd; //TOTAL VACCINE ALLOCATED_JOHNSON
                $total_vallocated  = $total_vallocated_svac + $total_vallocated_astra + $total_vallocated_sputnikv + $total_vallocated_pfizer + $total_vallocated_moderna + $total_vallocated_johnson ; //TOTAL_VACCINE_ALLOCATED

                $total_vallocated_frst =  $total_vallocated_svac_frst + $total_vallocated_astra_frst  + $total_vallocated_pfizer_frst + $total_vallocated_sputnikv_frst + $total_vallocated_moderna_frst + $total_vallocated_johnson_frst; //TOTAL_VACCINE_ALLOCATED_FIRST
                $total_vallocated_scnd = $total_vallocated_svac_scnd + $total_vallocated_astra_scnd  + $total_vallocated_pfizer_scnd  + $total_vallocated_sputnikv_scnd + $total_vallocated_moderna_scnd + $total_vallocated_johnson_scnd ; //TOTAL_VACCINE_ALLOCATED_SECOND

                $total_rfsd_frst = $total_rfsd_svac_frst + $total_rfsd_astra_frst + $total_rfsd_pfizer_frst  + $total_rfsd_sputnikv_frst + $total_rfsd_moderna_frst + $total_rfsd_johnson_frst; //TOTAL_REFUSED
                $total_rfsd_scnd = $total_rfsd_svac_scnd + $total_rfsd_astra_scnd  + $total_rfsd_pfizer_scnd + $total_rfsd_sputnikv_scnd + $total_rfsd_moderna_scnd + $total_rfsd_johnson_scnd ; //TOTAL_REFUSED - 2

                $p_cvrge_svac_frst = $total_vcted_svac_frst / $total_epop_svac * 100; //PERCENT_COVERAGE_SINOVAC
                $p_cvrge_astra_frst = $total_vcted_astra_frst / $total_epop_astra * 100; //PERCENT_COVERAGE_ASTRA
                $p_cvrge_pfizer_frst = $total_vcted_pfizer_scnd / $total_epop_pfizer * 100; //PERCENT_COVERAGE_PFIZER
                $p_cvrge_sputnikv_frst = $total_vcted_sputnikv_frst / $total_epop_sputnikv * 100; //PERCENT_COVERAGE_SPUTNIKV
                $p_cvrge_moderna_frst = $total_vcted_moderna_frst / $total_epop_moderna * 100; //PERCENT_COVERAGE_MODERNA
                $p_cvrge_johnson_frst = $total_vcted_johnson_frst / $total_epop_johnson * 100; //PERCENT_COVERAGE_JOHNSON

                $p_cvrge_svac_scnd = $total_vcted_svac_scnd / $total_epop_svac * 100; //PERCENT_COVERAGE_SINOVAC 2
                $p_cvrge_astra_scnd = $total_vcted_astra_scnd / $total_epop_astra * 100; //PERCENT_COVERAGE_ASTRA 2
                $p_cvrge_pfizer_scnd = $total_vcted_pfizer_scnd / $total_epop_pfizer * 100; //PERCENT_COVERAGE_PFIZER 2
                $p_cvrge_sputnikv_scnd = $total_vcted_sputnikv_scnd / $total_epop_sputnikv * 100; //PERCENT_COVERAGE_SPUTNIKV 2
                $p_cvrge_moderna_scnd = $total_vcted_moderna_scnd / $total_epop_moderna * 100; //PERCENT_COVERAGE_MODERNA 2
                $p_cvrge_johnson_scnd = $total_vcted_johnson_scnd / $total_epop_johnson * 100; //PERCENT_COVERAGE_JOHNSON 2

                $total_c_rate_svac_frst = $total_vcted_svac_frst / $total_vallocated_svac_frst * 100; //CONSUMPTION RATE_SINOVAC
                $total_c_rate_astra_frst = $total_vcted_astra_frst / $total_vallocated_astra_frst * 100; //CONSUMPTION RATE ASTRA
                $total_c_rate_pfizer_frst = $total_vcted_pfizer_frst / $total_vallocated_pfizer_frst * 100; //CONSUMPTION RATE PFIZER
                $total_c_rate_sputnikv_frst = $total_vcted_sputnikv_frst / $total_vallocated_sputnikv_frst * 100; //CONSUMPTION RATE SPUTNIKV
                $total_c_rate_moderna_frst = $total_vcted_moderna_frst / $total_vallocated_moderna_frst * 100; //CONSUMPTION RATE MODERNA
                $total_c_rate_johnson_frst = $total_vcted_johnson_frst / $total_vallocated_johnson_frst * 100; //CONSUMPTION RATE JONHSON

                $total_c_rate_svac_scnd = $total_vcted_svac_scnd / $total_vallocated_svac_scnd * 100; //CONSUMPTION RATE SINOVAC 2
                $total_c_rate_astra_scnd = $total_vcted_astra_scnd / $total_vallocated_astra_scnd * 100; //CONSUMPTION_RATE_ASTRA 2
                $total_c_rate_pfizer_scnd = $total_vcted_pfizer_scnd / $total_vallocated_pfizer_scnd * 100; //CONSUMPTION_RATE_PFIZER2
                $total_c_rate_sputnikv_scnd = $total_vcted_sputnikv_scnd / $total_vallocated_sputnikv_scnd * 100; //CONSUMPTION_RATE_SPUTNIKV 2
                $total_c_rate_moderna_scnd = $total_vcted_moderna_scnd / $total_vallocated_moderna_scnd * 100; //CONSUMPTION_RATE_MODERNA 2
                $total_c_rate_johnson_scnd = $total_vcted_johnson_scnd / $total_vallocated_johnson_scnd * 100; //CONSUMPTION_RATE_JOHNSON 2

                $total_r_unvcted_frst_svac = $total_epop_svac - $total_vcted_svac_frst - $total_rfsd_svac_frst; //REMAINING UNVACCINATED_SINOVAC
                $total_r_unvcted_frst_astra = $total_epop_astra - $total_vcted_astra_frst - $total_rfsd_astra_frst; //REMAINUNG UNVACCINATED_ASTRA
                $total_r_unvcted_frst_pfizer = $total_epop_pfizer - $total_vcted_pfizer_frst - $total_rfsd_pfizer_frst; //REMAINUNG_UNVACCINATED_PFIZER
                $total_r_unvcted_frst_sputnikv = $total_epop_sputnikv - $total_vcted_sputnikv_frst - $total_rfsd_sputnikv_frst; //REMAINUNG_UNVACCINATED_SPUTNIKV
                $total_r_unvcted_frst_moderna = $total_epop_moderna - $total_vcted_moderna_frst - $total_rfsd_moderna_frst; //REMAINUNG_UNVACCNATED_MODERNA
                $total_r_unvcted_frst_johnson = $total_epop_johnson - $total_vcted_johnson_frst - $total_rfsd_johnson_frst; //REMAINUNG_UNVACCNATED_JOHNSON

                $total_r_unvcted_scnd_svac = $total_epop_svac - $total_vcted_svac_scnd - $total_rfsd_svac_scnd; //REMAINING UNVACCINATED_SINOVAC 2
                $total_r_unvcted_scnd_astra = $total_epop_astra - $total_vcted_astra_scnd - $total_rfsd_astra_scnd; //REMAINUNG_UNVACCIANTED_ASTRA 2
                $total_r_unvcted_scnd_pfizer = $total_epop_pfizer - $total_vcted_pfizer_scnd - $total_rfsd_pfizer_scnd; //REMAINUNG_UNVACCIANTED_S2 PFIZER 2
                $total_r_unvcted_scnd_sputnikv = $total_epop_sputnikv - $total_vcted_sputnikv_scnd - $total_rfsd_sputnikv_scnd; //REMAINUNG_UNVACCIANTED_SPUTNIKV 2
                $total_r_unvcted_scnd_moderna = $total_epop_moderna - $total_vcted_moderna_scnd - $total_rfsd_moderna_scnd; //REMAINUNG_UNVACCIANTED_MODERNA 2
                $total_r_unvcted_scnd_johnson = $total_epop_johnson - $total_vcted_johnson_scnd - $total_rfsd_johnson_scnd; //REMAINUNG_UNVACCIANTED_JOHNSON 2

                $total_vcted_overall_a1_frst = $total_svac_a1_frst + $total_astra_a1_frst + $total_sputnikv_a1_frst + $total_pfizer_a1_frst + $total_moderna_a1_frst + $total_johnson_a1_frst; // TOTAL VACCINATED OVERALL A1
                $total_vcted_overall_a2_frst = $total_svac_a2_frst + $total_astra_a2_frst + $total_sputnikv_a2_frst + $total_pfizer_a2_frst + $total_moderna_a2_frst + $total_johnson_a2_frst; // TOTAL VACCINATED OVERALL A2
                $total_vcted_overall_a3_frst = $total_svac_a3_frst + $total_astra_a3_frst + $total_sputnikv_a3_frst + $total_pfizer_a3_frst + $total_moderna_a3_frst + $total_johnson_a3_frst; // TOTAL VACCINATED OVERALL A3
                $total_vcted_overall_a4_frst = $total_svac_a4_frst + $total_astra_a4_frst + $total_sputnikv_a4_frst + $total_pfizer_a4_frst + $total_moderna_a4_frst + $total_johnson_a4_frst; // TOTAL VACCINATED OVERALL A4
                $total_vcted_overall_a5_frst = $total_svac_a5_frst + $total_astra_a5_frst + $total_sputnikv_a5_frst + $total_pfizer_a5_frst + $total_moderna_a5_frst + $total_johnson_a5_frst; // TOTAL VACCINATED OVERALL A5
                $total_vcted_overall_b1_frst = $total_svac_b1_frst + $total_astra_b1_frst + $total_sputnikv_b1_frst + $total_pfizer_b1_frst + $total_moderna_b1_frst + $total_johnson_b1_frst; // TOTAL VACCINATED OVERALL B1
                $total_vcted_overall_b2_frst = $total_svac_b2_frst + $total_astra_b2_frst + $total_sputnikv_b2_frst + $total_pfizer_b2_frst + $total_moderna_b2_frst + $total_johnson_b2_frst; // TOTAL VACCINATED OVERALL B2
                $total_vcted_overall_b3_frst = $total_svac_b3_frst + $total_astra_b3_frst + $total_sputnikv_b3_frst + $total_pfizer_b3_frst + $total_moderna_b3_frst + $total_johnson_b3_frst; // TOTAL VACCINATED OVERALL B3
                $total_vcted_overall_b4_frst = $total_svac_b4_frst + $total_astra_b4_frst + $total_sputnikv_b4_frst + $total_pfizer_b4_frst + $total_moderna_b4_frst + $total_johnson_b4_frst; // TOTAL VACCINATED OVERALL B4

                $total_vcted_overall_a1_scnd = $total_svac_a1_scnd + $total_astra_a1_scnd + $total_sputnikv_a1_scnd + $total_pfizer_a1_scnd + $total_moderna_a1_scnd + $total_johnson_a1_scnd; // TOTAL VACCINATED OVERALL A1
                $total_vcted_overall_a2_scnd = $total_svac_a2_scnd + $total_astra_a2_scnd + $total_sputnikv_a2_scnd + $total_pfizer_a2_scnd + $total_moderna_a2_scnd + $total_johnson_a2_scnd; // TOTAL VACCINATED OVERALL A2
                $total_vcted_overall_a3_scnd = $total_svac_a3_scnd + $total_astra_a3_scnd + $total_sputnikv_a3_scnd + $total_pfizer_a3_scnd + $total_moderna_a3_scnd + $total_johnson_a3_scnd; // TOTAL VACCINATED OVERALL A3
                $total_vcted_overall_a4_scnd = $total_svac_a4_scnd + $total_astra_a4_scnd + $total_sputnikv_a4_scnd + $total_pfizer_a4_scnd + $total_moderna_a4_scnd + $total_johnson_a4_scnd; // TOTAL VACCINATED OVERALL A4
                $total_vcted_overall_a5_scnd = $total_svac_a5_scnd + $total_astra_a5_scnd + $total_sputnikv_a5_scnd + $total_pfizer_a5_scnd + $total_moderna_a5_scnd + $total_johnson_a5_scnd; // TOTAL VACCINATED OVERALL A5
                $total_vcted_overall_b1_scnd = $total_svac_b1_scnd + $total_astra_b1_scnd + $total_sputnikv_b1_scnd + $total_pfizer_b1_scnd + $total_moderna_b1_scnd + $total_johnson_b1_scnd; // TOTAL VACCINATED OVERALL B1
                $total_vcted_overall_b2_scnd = $total_svac_b2_scnd + $total_astra_b2_scnd + $total_sputnikv_b2_scnd + $total_pfizer_b2_scnd + $total_moderna_b2_scnd + $total_johnson_b2_scnd; // TOTAL VACCINATED OVERALL B2
                $total_vcted_overall_b3_scnd = $total_svac_b3_scnd + $total_astra_b3_scnd + $total_sputnikv_b3_scnd + $total_pfizer_b3_scnd + $total_moderna_b3_scnd + $total_johnson_b3_scnd; // TOTAL VACCINATED OVERALL B3
                $total_vcted_overall_b4_scnd = $total_svac_b4_scnd + $total_astra_b4_scnd + $total_sputnikv_b4_scnd + $total_pfizer_b4_scnd + $total_moderna_b4_scnd + $total_johnson_b4_scnd; // TOTAL VACCINATED OVERALL B4

                $total_vcted_overall_frst = $total_vcted_overall_a1_frst + $total_vcted_overall_a2_frst + $total_vcted_overall_a3_frst + $total_vcted_overall_a4_frst + $total_vcted_overall_a5_frst +
                                            $total_vcted_overall_b1_frst + $total_vcted_overall_b2_frst + $total_vcted_overall_b3_frst + $total_vcted_overall_b4_frst;   //TOTAL_VACCINATED_OVERAL
                $total_vcted_overall_scnd = $total_vcted_overall_a1_scnd + $total_vcted_overall_a2_scnd + $total_vcted_overall_a3_scnd + $total_vcted_overall_a4_scnd + $total_vcted_overall_a5_scnd +
                                                $total_vcted_overall_b1_scnd + $total_vcted_overall_b2_scnd + $total_vcted_overall_b3_scnd + $total_vcted_overall_b4_scnd; //TOTAL_VACCINATED_OVERALL_SECOND

                $total_p_cvrge_frst = $total_vcted_overall_frst / $total_epop_astra * 100; //TOTAL_PERCENT_COVERAGE
                $total_p_cvrge_scnd = $total_vcted_overall_scnd / $total_epop_astra * 100; //TOTAL_PERCENT_COVERAGE - 2

                $total_c_rate_frst = $total_vcted_overall_frst / $total_vallocated_frst * 100; //TOTAL CONSUMPTION_RATE
                $total_c_rate_scnd = $total_vcted_overall_scnd / $total_vallocated_scnd * 100; //TOTAL_CONSUMPTION_RATE - 2

                $total_mild_overall_frst = $total_mild_svac_frst + $total_mild_astra_frst + $total_mild_sputnikv_frst + $total_mild_pfizer_frst + $total_mild_moderna_frst + $total_mild_johnson_frst; // TOTAL OVERALL MILD FIRST
                $total_mild_overall_scnd = $total_mild_svac_scnd + $total_mild_astra_scnd + $total_mild_sputnikv_scnd + $total_mild_pfizer_scnd + $total_mild_moderna_scnd + $total_mild_johnson_scnd; // TOTAL OVERALL MILD SECOND

                $total_srs_overall_frst = $total_srs_svac_frst + $total_srs_astra_frst + $total_srs_sputnikv_frst + $total_srs_pfizer_frst + $total_srs_moderna_frst + $total_srs_johnson_frst; // TOTAL OVERALL SERIOUS FIRST
                $total_srs_overall_scnd = $total_srs_svac_scnd + $total_srs_astra_scnd + $total_srs_sputnikv_scnd + $total_srs_pfizer_scnd + $total_srs_moderna_scnd + $total_srs_johnson_scnd; // TOTAL OVERALL SERIOUS SECOND

                $total_dfrd_overall_frst = $total_dfrd_svac_frst + $total_dfrd_astra_frst + $total_dfrd_sputnikv_frst + $total_dfrd_pfizer_frst + $total_dfrd_moderna_frst + $total_dfrd_johnson_frst ; // TOTAL OVERALL DEFERRED FIRST
                $total_dfrd_overall_scnd = $total_dfrd_svac_scnd + $total_dfrd_astra_scnd + $total_dfrd_sputnikv_scnd + $total_dfrd_pfizer_scnd + $total_dfrd_moderna_scnd + $total_dfrd_moderna_scnd; // TOTAL OVERALL DEFERRED SECOND

                $total_wstge_overall_frst = $total_wstge_svac_frst + $total_wstge_astra_frst + $total_wstge_sputnikv_frst + $total_wstge_pfizer_frst + $total_wstge_moderna_frst + $total_wstge_johnson_frst; // TOTAL OVERALL WASTAGE FIRST
                $total_wstge_overall_scnd = $total_wstge_svac_scnd + $total_wstge_astra_scnd + $total_wstge_sputnikv_scnd + $total_wstge_pfizer_scnd + $total_wstge_moderna_scnd + $total_wstge_johnson_scnd; // TOTAL OVERALL WASTAGE SECOND

                $total_r_unvcted_frst = $total_epop_overall - $total_vcted_overall_frst - $total_rfsd_frst;
                $total_r_unvcted_scnd = $total_epop_overall - $total_vcted_overall_scnd - $total_rfsd_scnd;
            ?>
                <b>
                    <a  class="text-green" style= "font-size:14pt;cursor: pointer; " onclick="muncityVaccinated('<?php echo $row->province_id; ?>','<?php echo $row->id; ?>','<?php echo $date_start; ?>','<?php echo $date_end; ?>',$(this))">
                        {{ $row->description }}
                    </a>
                </b>
                <button class="btn btn-link collapsed" style="color: red;" type="button" data-toggle="collapse" data-target="#collapse_sinovac{{ $row->id }}" aria-expanded="false" aria-controls="collapse_sinovac{{ $row->id }}">
                    <b>Sinovac</b>
                </button>
                <button class="btn btn-link collapsed" style="color: darkgoldenrod;" type="button" data-toggle="collapse" data-target="#collapse_astra{{ $row->id }}" aria-expanded="false" aria-controls="collapse_astra{{ $row->id }}">
                    <b>Astrazeneca</b>
                </button>
                <button class="btn btn-link collapsed" style="color: #00c0ef;" type="button" data-toggle="collapse" data-target="#collapse_pfizer{{ $row->id }}" aria-expanded="false" aria-controls="collapse_pfizer{{ $row->id }}">
                    <b>Pfizer</b>
                </button>
                <button class="btn btn-link collapsed" style="color: #00a65a;" type="button" data-toggle="collapse" data-target="#collapse_sputnikv{{ $row->id }}" aria-expanded="false" aria-controls="collapse_sputnikv{{ $row->id }}">
                    <b>Sputnikv</b>
                </button>
                <button class="btn btn-link collapsed" style="color: #605ca8;" type="button" data-toggle="collapse" data-target="#collapse_moderna{{ $row->id }}" aria-expanded="false" aria-controls="collapse_moderna{{ $row->id }}">
                    <b>Moderna</b>
                </button>
                <button class="btn btn-link collapsed" style="color: #1d94ff;" type="button" data-toggle="collapse" data-target="#collapse_johnson{{ $row->id }}" aria-expanded="false" aria-controls="collapse_johnson{{ $row->id }}">
                    <b>Janssen</b>
                </button>
                <button class="btn" style="color: black;" type="button">
                    <h5>Total Allocated: <b style="font-size: 18px; color: #137fb1" class="individual_allocated{{ $row->id }}">{{ $total_vallocated }}</b></h5>
                </button>
                <button class="btn" style="color: black;" type="button">
                    <h5>Goal Completion: <b style="font-size: 18px; color:#00a65a;" class="goal_completion {{ $row->id }}"><i class="fa fa-thumbs-up" style="color:#00a65a;"> </i>
                            {{ number_format($total_vcted_overall_scnd / $total_vallocated * 100,2) }}%   </b>
                    </h5>
                </button>
                <br>
                <div class="table-responsive">
                    <table style="font-size: 8pt;" class="table" border="2">
                        <tbody>
                        <tr>
                            <th>Type of Vaccine</th> <!-- Type of Vaccine 1-1 -->
                            <th colspan="10">
                                <center><a
                                            href="#facility_modal"
                                            data-toggle="modal"
                                            onclick="MunicipalityBody('<?php echo $province_id; ?>','<?php echo $row->id; ?>')"
                                    >
                                        Eligible Population
                                    </a></center>
                            </th>
                            <th colspan="3">
                                <center><a
                                            href="#vaccine_modal_allocated"
                                            data-toggle="modal"
                                            onclick="vaccineAllocated('<?php echo $row->province_id; ?>','<?php echo $row->id; ?>')"
                                    >
                                        Vaccine Allocated
                                    </a></center>
                            </th>
                            <th colspan="10"><center>Total Vaccinated</center></th>
                            <th>Mild</th>
                            <th>Serious</th>
                            <th>Deferred</th>
                            <th>Refused</th>
                            <th>Wastage</th>
                            <th>Percent Coverage</th>
                            <th>Consumption Rate</th>
                            <th>Remaining Unvaccinated</th>
                        </tr>
                        <tr>
                            <td></td> <!-- 1-2 -->
                            <th>A1</th>
                            <th>A2</th>
                            <th>A3</th>
                            <th>A4</th>
                            <th>A5</th>
                            <th>B1</th>
                            <th>B2</th>
                            <th>B3</th>
                            <th>B4</th>
                            <th>Total</th>
                            <th>1st</th>
                            <th>2nd</th>
                            <th>Total</th>
                            <th>A1</th>
                            <th>A2</th>
                            <th>A3</th>
                            <th>A4</th>
                            <th>A5</th>
                            <th>B1</th>
                            <th>B2</th>
                            <th>B3</th>
                            <th>B4</th>
                            <th>Total</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                        <tbody id="collapse_sinovac{{ $row->id }}" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <tr style="background-color: #ffd8d6">
                            <td rowspan="2">

                            </td> <!-- 1-3 -->
                            <td rowspan="2" class="total_epop_svac_a1{{ $row->id }}">{{ $total_epop_svac_a1 }}</td> <!-- TOTAL_E_POP_FRONTLINE_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac_a2{{ $row->id }}">{{ $total_epop_svac_a2 }}</td> <!-- E_POP_SENIOR_SINOVAC -->
                            <td rowspan="2" class="total_epop_svac_a3{{ $row->id }}">{{ $total_epop_svac_a3 }}</td>  <!-- E_POP_A3_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac_a4{{ $row->id }}">{{$total_epop_svac_a4}}</td>  <!-- E_POP_A4_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac_a5{{ $row->id }}">{{$total_epop_svac_a5}}</td>  <!-- E_POP_A5_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac_b1{{ $row->id }}">{{$total_epop_svac_b1}}</td>  <!-- E_POP_B1_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac_b2{{ $row->id }}">{{$total_epop_svac_b2}}</td>  <!-- E_POP_B2_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac_b3{{ $row->id }}">{{$total_epop_svac_b3}}</td>  <!-- E_POP_B3_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac_b4{{ $row->id }}">{{$total_epop_svac_b4}}</td>  <!-- E_POP_B4_SINOVAC   -->
                            <td rowspan="2" class="total_epop_svac{{ $row->id }}">{{ $total_epop_svac }}</td> <!-- E_POP_SINOVAC FIRST  -->
                            <td rowspan="2" class="total_vallocated_svac_frst{{ $row->id }}">{{ $total_vallocated_svac_frst }}</td> <!-- VACCINE ALLOCATED_SINOVAC (FD)  -->
                            <td rowspan="2" class="total_vallocated_svac_scnd{{ $row->id }}">{{ $total_vallocated_svac_scnd }}</td> <!-- VACCINE ALLOCATED_SINOVAC (SD)  -->
                            <td rowspan="2" class="total_vallocated_svac{{ $row->id }}">{{ $total_vallocated_svac }}</td>  <!-- TOTAL VACCINE ALLOCATED_SINOVAC   -->
                            <td>
                                <span class="label label-success total_svac_a1_frst{{ $row->id }}">{{ $total_svac_a1_frst }}</span> <!-- A1_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_a2_frst{{ $row->id }}">{{ $total_svac_a2_frst }}</span> <!-- A2_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_a3_frst{{ $row->id }}">{{ $total_svac_a3_frst }}</span> <!-- A3_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_a4_frst{{ $row->id }}">{{ $total_svac_a4_frst }}</span> <!-- A4_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_a5_frst{{ $row->id }}">{{ $total_svac_a5_frst }}</span> <!-- A5_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_b1_frst{{ $row->id }}">{{ $total_svac_b1_frst }}</span> <!-- B1_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_b2_frst{{ $row->id }}">{{ $total_svac_b2_frst }}</span> <!-- B2_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_b3_frst{{ $row->id }}">{{ $total_svac_b3_frst }}</span> <!-- B3_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_svac_b4_frst{{ $row->id }}">{{ $total_svac_b4_frst }}</span> <!-- B4_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_vcted_svac_frst{{ $row->id }}">{{  $total_vcted_svac_frst }}</span><!-- TOTAL VACCINATED_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_mild_svac_frst{{ $row->id }}">{{ $total_mild_svac_frst }}</span> <!-- MILD_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_srs_svac_frst{{ $row->id }}">{{ $total_srs_svac_frst }}</span>  <!-- SERIOUS_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_dfrd_svac_frst{{ $row->id }}">{{ $total_dfrd_svac_frst }}</span>  <!-- DEFERRED_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_rfsd_svac_frst{{ $row->id }}">{{ $total_rfsd_svac_frst }}</span>  <!-- REFUSED_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success total_wstge_svac_frst{{ $row->id }}">{{ $total_wstge_svac_frst }}</span>  <!-- WASTAGF_SINOVAC -->
                            </td>
                            <td>
                                <span class="label label-success p_cvrge_svac_frst{{ $row->id }}">{{ number_format($p_cvrge_svac_frst,2) }}%</span>  <!-- PERCENT_COVERAGE_SINOVAC-->
                            </td>
                            <td>
                                <span class="label label-success total_c_rate_svac_frst{{ $row->id }}">{{ number_format($total_c_rate_svac_frst,2) }}%</span>  <!-- CONSUMPTION RATE_SINOVAC-->
                            </td>
                            <td>
                                <span class="label label-success total_r_unvcted_frst_svac{{ $row->id }}">{{ $total_r_unvcted_frst_svac }}</span>  <!-- REMAINING UNVACCINATED_SINOVAC -->
                            </td>
                        </tr>
                        <tr style="background-color: #ffd8d6">
                            <td>
                                <span class="label label-warning total_svac_a1_scnd{{ $row->id }}">{{ $total_svac_a1_scnd }}</span>   <!-- A1_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_a2_scnd{{ $row->id }}">{{ $total_svac_a2_scnd }}</span> <!-- A2_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_a3_scnd{{ $row->id }}">{{ $total_svac_a3_scnd }}</span> <!-- A3_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_a4_scnd{{ $row->id }}">{{ $total_svac_a4_scnd }}</span> <!-- A4_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_a5_scnd{{ $row->id }}">{{ $total_svac_a5_scnd }}</span> <!-- A5_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_b1_scnd{{ $row->id }}">{{ $total_svac_b1_scnd }}</span> <!-- B1_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_b2_scnd{{ $row->id }}">{{ $total_svac_b2_scnd }}</span> <!-- B2_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_b3_scnd{{ $row->id }}">{{ $total_svac_b3_scnd }}</span> <!-- B3_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_svac_b4_scnd{{ $row->id }}">{{ $total_svac_b4_scnd }}</span> <!-- B4_SINOVAC2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_vcted_svac_scnd{{ $row->id }}">{{ $total_vcted_svac_scnd }}</span> <!-- TOTAL_VACCINATED_SINOVAC 2-->
                            </td> <!-- 1-4 -->
                            <td>
                                <span class="label label-warning total_mild_svac_scnd{{ $row->id }}">{{ $total_mild_svac_scnd }}</span> <!-- MILD_SINOVAC 2-->
                            </td>
                            <td>
                                <span class="label label-warning total_srs_svac_scnd{{ $row->id }}">{{ $total_srs_svac_scnd }}</span> <!-- SERIOUS_SINOVAC 2-->
                            </td>
                            <td>
                                <span class="label label-warning total_dfrd_svac_scnd{{ $row->id }}">{{ $total_dfrd_svac_scnd }}</span> <!-- DEFERRED_SINOVAC 2-->
                            </td>
                            <td>
                                <span class="label label-warning total_rfsd_svac_scnd{{ $row->id }}">{{ $total_rfsd_svac_scnd }}</span> <!-- REFUSED_SINOVAC 2-->
                            </td>
                            <td>
                                <span class="label label-warning total_wstge_svac_scnd{{ $row->id }}">{{ $total_wstge_svac_scnd }}</span> <!--WASTAGE_SINOVAC 2-->
                            </td>
                            <td>
                                <span class="label label-warning p_cvrge_svac_scnd{{ $row->id }}">{{ number_format($p_cvrge_svac_scnd,2) }}%</span> <!-- PERCENT_COVERAGE_SINOVAC 2-->
                            </td>
                            <td>
                                <span class="label label-warning total_c_rate_svac_scnd{{ $row->id }}">{{ number_format($total_c_rate_svac_scnd,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC 2-->
                            </td>
                            <td>
                                <span class="label label-warning total_r_unvcted_scnd_svac{{ $row->id }}">{{ $total_r_unvcted_scnd_svac }} </span> <!-- REMAINING UNVACCINATED_SINOVAC 2 -->
                            </td>
                        </tr>
                        </tbody>
                        <tbody id="collapse_astra{{ $row->id }}" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <tr style="background-color: #f2fcac">
                            <td rowspan="2"></td> <!-- 1-5 -->
                            <td rowspan="2" style="color:black;" class="total_epop_astra_a1{{ $row->id }}">{{ $total_epop_astra_a1 }}</td> <!-- TOTAL_E_POP_A1_ASTRA -->
                            <td rowspan="2" style="color:black;" class="total_epop_astra_a2{{ $row->id }}">{{ $total_epop_astra_a2 }}</td>  <!-- TOTAL_E_POP_A2_ASTRA -->
                            <td rowspan="2" style="color:black" class="total_epop_astra_a3{{ $row->id }}">{{ $total_epop_astra_a3 }}</td>  <!-- TOTAL_E_POP_A3_ASTRA -->
                            <td rowspan="2" style="color:black" class="total_epop_astra_a4{{ $row->id }}">{{ $total_epop_astra_a4 }}</td>  <!-- TOTAL_E_POP_A4_ASTRA -->
                            <td rowspan="2" style="color:black" class="total_epop_astra_a5{{ $row->id }}">{{ $total_epop_astra_a5 }}</td>  <!-- TOTAL_E_POP_A5_ASTRA -->
                            <td rowspan="2" style="color:black" class="total_epop_astra_b1{{ $row->id }}">{{ $total_epop_astra_b1 }}</td>  <!-- TOTAL_E_POP_B1_ASTRA -->
                            <td rowspan="2" style="color:black" class="total_epop_astra_b2{{ $row->id }}">{{ $total_epop_astra_b2 }}</td>  <!-- TOTAL_E_POP_B2_ASTRA -->
                            <td rowspan="2" style="color:black" class="total_epop_astra_b3{{ $row->id }}">{{ $total_epop_astra_b3 }}</td>  <!-- TOTAL_E_POP_B3_ASTRA -->
                            <td rowspan="2" style="color:black" class="total_epop_astra_b4{{ $row->id }}">{{ $total_epop_astra_b4 }}</td>  <!-- TOTAL_E_POP_B4_ASTRA -->
                            <td rowspan="2" style="color:black;" class="total_epop_astra{{ $row->id }}">{{ $total_epop_astra }} </td>  <!-- TOTAL_E_POP_ASTRA -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_astra_frst{{ $row->id }}">{{ $total_vallocated_astra_frst }}</td>  <!-- VACCINE ALLOCATED_ASTRA (FD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_astra_scnd{{ $row->id }}">{{ $total_vallocated_astra_scnd }}</td>  <!-- VACCINE ALLOCATED_ASTRA (SD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_astra{{ $row->id }}">{{ $total_vallocated_astra }}</td>  <!-- TOTAL VACCINE ALLOCATED_ASTRA -->
                            <td style="color:black;">
                                <span class="label label-success total_astra_a1_frst{{ $row->id }}">{{ $total_astra_a1_frst }}</span>  <!-- A1_ASTRA  -->
                            </td>
                            <td style="color:black">
                                <span class="label label-success total_astra_a2_frst{{ $row->id }}">{{ $total_astra_a2_frst }}</span>  <!-- A2_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_astra_a3_frst{{ $row->id }}">{{ $total_astra_a3_frst }}</span>  <!-- A3_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_astra_a4_frst{{ $row->id }}">{{ $total_astra_a4_frst }}</span>  <!-- A4_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_astra_a5_frst{{ $row->id }}">{{ $total_astra_a5_frst }}</span>  <!-- A5_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_astra_b1_frst{{ $row->id }}">{{ $total_astra_b1_frst }}</span>  <!-- B1_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_astra_b2_frst{{ $row->id }}">{{ $total_astra_b2_frst }}</span>  <!-- B2_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_astra_b3_frst{{ $row->id }}">{{ $total_astra_b3_frst }}</span>  <!-- B3_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_astra_b4_frst{{ $row->id }}">{{ $total_astra_b4_frst }}</span>  <!-- B4_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_vcted_astra_frst{{ $row->id }}">{{ $total_vcted_astra_frst }}</span>  <!-- TOTAL VACCINATED_ASTRA-->
                            </td>
                            <td>
                                <span class="label label-success total_mild_astra_frst{{ $row->id }}">{{ $total_mild_astra_frst }}</span> <!-- MILD_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_srs_astra_frst{{ $row->id }}">{{ $total_srs_astra_frst }}</span>  <!-- SERIOUS_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_dfrd_astra_frst{{ $row->id }}">{{ $total_dfrd_astra_frst }}</span> <!-- DEFERRED_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_rfsd_astra_frst{{ $row->id }}">{{ $total_rfsd_astra_frst }}</span> <!-- REFUSED_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_wstge_astra_frst{{ $row->id }}">{{ $total_wstge_astra_frst }}</span> <!-- WASTAGE_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success p_cvrge_astra_frst{{ $row->id }}">{{ number_format($p_cvrge_astra_frst,2) }}%</span> <!-- PERCENT_COVERAGE_ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_c_rate_astra_frst{{ $row->id }}">{{ number_format($total_c_rate_astra_frst,2) }}%</span> <!-- CONSUMPTION RATE ASTRA  -->
                            </td>
                            <td>
                                <span class="label label-success total_r_unvcted_frst_astra{{ $row->id }}">{{ $total_r_unvcted_frst_astra }}</span> <!-- REMAINUNG UNVACCINATED_ASTRA  -->
                            </td>
                        </tr>
                        <tr style="background-color: #f2fcac">
                            <td style="color: black;">
                                <span class="label label-warning total_astra_a1_scnd{{ $row->id }}">{{ $total_astra_a1_scnd }}</span>  <!-- A1_ASTRA2  -->
                            </td>
                            <td style="color:black;">
                                <span class="label label-warning total_astra_a2_scnd{{ $row->id }}">{{ $total_astra_a2_scnd }}</span>  <!-- A2_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_astra_a3_scnd{{ $row->id }}">{{ $total_astra_a3_scnd }}</span>  <!-- A3_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_astra_a4_scnd{{ $row->id }}">{{ $total_astra_a4_scnd }}</span>  <!-- A4_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_astra_a5_scnd{{ $row->id }}">{{ $total_astra_a5_scnd }}</span>  <!-- A5_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_astra_b1_scnd{{ $row->id }}">{{ $total_astra_b1_scnd }}</span>  <!-- B1_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_astra_b2_scnd{{ $row->id }}">{{ $total_astra_b2_scnd }}</span>  <!-- B2_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_astra_b3_scnd{{ $row->id }}">{{ $total_astra_b3_scnd }}</span>  <!-- B3_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_astra_b4_scnd{{ $row->id }}">{{ $total_astra_b4_scnd }}</span>  <!-- B4_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_vcted_astra_scnd{{ $row->id }}">{{ $total_vcted_astra_scnd }}</span> <!-- TOTAL VACCINATED_ASTRA 2-->
                            </td> <!-- 1-6 -->
                            <td>
                                <span class="label label-warning total_mild_astra_scnd{{ $row->id }}">{{ $total_mild_astra_scnd }}</span> <!-- MILD_ASTRA 2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_srs_astra_scnd{{ $row->id }}">{{ $total_srs_astra_scnd }}</span> <!-- SERIOUS_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_dfrd_astra_scnd{{ $row->id }}">{{ $total_dfrd_astra_scnd }}</span> <!-- DEFERRED_ASTRA 2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_rfsd_astra_scnd{{ $row->id }}">{{ $total_rfsd_astra_scnd }}</span> <!-- REFUSED_ASTRA 2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_wstge_astra_scnd{{ $row->id }}">{{ $total_wstge_astra_scnd }}</span> <!-- WASTAGE_ASTRA2  -->
                            </td>
                            <td>
                                <span class="label label-warning p_cvrge_astra_scnd{{ $row->id }}">{{ number_format($p_cvrge_astra_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_ASTRA 2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_c_rate_astra_scnd{{ $row->id }}">{{ number_format($total_c_rate_astra_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_ASTRA 2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_r_unvcted_scnd_astra{{ $row->id }}">{{ $total_r_unvcted_scnd_astra }}</span> <!-- REMAINUNG_UNVACCIANTED_ASTRA 2  -->
                            </td>
                        </tr>
                        </tbody>
                        <!-- PFIZER -->
                        <tbody id="collapse_pfizer{{ $row->id }}" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <tr style="background-color: #8fe7fd">
                            <td rowspan="2">

                            </td> <!-- 1-3 -->
                            <td rowspan="2" style="color:black;" class="total_epop_pfizer_a1{{ $row->id }}">{{ $total_epop_pfizer_a1 }}</td> <!-- TOTAL_E_POP_A1_PFIZER -->
                            <td rowspan="2" style="color:black;" class="total_epop_pfizer_a2{{ $row->id }}">{{ $total_epop_pfizer_a2 }}</td>  <!-- TOTAL_E_POP_A2_PFIZER -->
                            <td rowspan="2" style="color:black" class="total_epop_pfizer_a3{{ $row->id }}">{{ $total_epop_pfizer_a3 }}</td>  <!-- TOTAL_E_POP_A3_PFIZER -->
                            <td rowspan="2" style="color:black" class="total_epop_pfizer_a4{{ $row->id }}">{{ $total_epop_pfizer_a4 }}</td>  <!-- TOTAL_E_POP_A4_PFIZER -->
                            <td rowspan="2" style="color:black" class="total_epop_pfizer_a5{{ $row->id }}">{{ $total_epop_pfizer_a5 }}</td>  <!-- TOTAL_E_POP_A5_PFIZER -->
                            <td rowspan="2" style="color:black" class="total_epop_pfizer_b1{{ $row->id }}">{{ $total_epop_pfizer_b1 }}</td>  <!-- TOTAL_E_POP_B1_PFIZER -->
                            <td rowspan="2" style="color:black" class="total_epop_pfizer_b2{{ $row->id }}">{{ $total_epop_pfizer_b2 }}</td>  <!-- TOTAL_E_POP_B2_PFIZER -->
                            <td rowspan="2" style="color:black" class="total_epop_pfizer_b3{{ $row->id }}">{{ $total_epop_pfizer_b3 }}</td>  <!-- TOTAL_E_POP_B3_PFIZER -->
                            <td rowspan="2" style="color:black" class="total_epop_pfizer_b4{{ $row->id }}">{{ $total_epop_pfizer_b4 }}</td>  <!-- TOTAL_E_POP_B4_PFIZER -->
                            <td rowspan="2" style="color:black;" class="total_epop_pfizer{{ $row->id }}">{{ $total_epop_pfizer }} </td>  <!-- TOTAL_E_POP_PFIZER -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_pfizer_frst{{ $row->id }}">{{ $total_vallocated_pfizer_frst }}</td>  <!-- VACCINE ALLOCATED_PFIZER (FD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_pfizer_scnd{{ $row->id }}">{{ $total_vallocated_pfizer_scnd }}</td>  <!-- VACCINE ALLOCATED_PFIZER (SD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_pfizer{{ $row->id }}">{{ $total_vallocated_pfizer }}</td>  <!-- TOTAL VACCINE ALLOCATED_PFIZER -->
                            <td style="color:black;">
                                <span class="label label-success total_pfizer_a1_frst{{ $row->id }}">{{ $total_pfizer_a1_frst }}</span>  <!-- A1_PFIZER  -->
                            </td>
                            <td style="color:black">
                                <span class="label label-success total_pfizer_a2_frst{{ $row->id }}">{{ $total_pfizer_a2_frst }}</span>  <!-- A2_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_pfizer_a3_frst{{ $row->id }}">{{ $total_pfizer_a3_frst }}</span>  <!-- A3_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_pfizer_a4_frst{{ $row->id }}">{{ $total_pfizer_a4_frst }}</span>  <!-- A4_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_pfizer_a5_frst{{ $row->id }}">{{ $total_pfizer_a5_frst }}</span>  <!-- A5_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_pfizer_b1_frst{{ $row->id }}">{{ $total_pfizer_b1_frst }}</span>  <!-- B1_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_pfizer_b2_frst{{ $row->id }}">{{ $total_pfizer_b2_frst }}</span>  <!-- B2_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_pfizer_b3_frst{{ $row->id }}">{{ $total_pfizer_b3_frst }}</span>  <!-- B3_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_pfizer_b4_frst{{ $row->id }}">{{ $total_pfizer_b4_frst }}</span>  <!-- B4_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_vcted_pfizer_frst{{ $row->id }}">{{ $total_vcted_pfizer_frst }}</span> <!-- TOTAL VACCINATED_PFIZER-->
                            </td>
                            <td>
                                <span class="label label-success total_mild_pfizer_frst{{ $row->id }}">{{ $total_mild_pfizer_frst }}</span> <!-- MILD_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-success total_srs_pfizer_frst{{ $row->id }}">{{ $total_srs_pfizer_frst }}</span> <!-- SERIOUS_PFIZER  -->
                            </td>
                            <td>
                                <span class="label label-success total_dfrd_pfizer_frst{{ $row->id }}">{{ $total_dfrd_pfizer_frst }}</span> <!-- DEFERRED_PFIZER  -->
                            </td>
                            <td>
                                <span class="label label-success total_rfsd_pfizer_frst{{ $row->id }}">{{ $total_rfsd_pfizer_frst }}</span> <!-- REFUSED_PFIZER  -->
                            </td>
                            <td>
                                <span class="label label-success total_wstge_pfizer_frst{{ $row->id }}">{{ $total_wstge_pfizer_frst }}</span> <!-- WASTAGE_PFIZER  -->
                            </td>
                            <td>
                                <span class="label label-success p_cvrge_pfizer_frst{{ $row->id }}">{{ number_format($p_cvrge_pfizer_frst,2)}}%</span> <!-- PERCENT_COVERAGE_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_c_rate_pfizer_frst{{ $row->id }}">{{ number_format($total_c_rate_pfizer_frst,2) }}%</span> <!-- CONSUMPTION_RATE_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-success total_r_unvcted_frst_pfizer{{ $row->id }}">{{ $total_r_unvcted_frst_pfizer }}</span> <!-- REMAINUNG_UNVACCIANTED_PFIZER -->
                            </td>
                        </tr>
                        <tr style="background-color: #8fe7fd">
                            <td style="color: black;">
                                <span class="label label-warning total_pfizer_a1_scnd{{ $row->id }}">{{ $total_pfizer_a1_scnd }}</span>  <!-- A1_PFIZER  -->
                            </td>
                            <td style="color:black;">
                                <span class="label label-warning total_pfizer_a2_scnd{{ $row->id }}">{{ $total_pfizer_a2_scnd }}</span>  <!-- A2_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_pfizer_a3_scnd{{ $row->id }}">{{ $total_pfizer_a3_scnd }}</span>  <!-- A3_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_pfizer_a4_scnd{{ $row->id }}">{{ $total_pfizer_a4_scnd }}</span>  <!-- A4_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_pfizer_a5_scnd{{ $row->id }}">{{ $total_pfizer_a5_scnd }}</span>  <!-- A5_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_pfizer_b1_scnd{{ $row->id }}">{{ $total_pfizer_b1_scnd }}</span>  <!-- B1_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_pfizer_b2_scnd{{ $row->id }}">{{ $total_pfizer_b2_scnd }}</span>  <!-- B2_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_pfizer_b3_scnd{{ $row->id }}">{{ $total_pfizer_b3_scnd }}</span>  <!-- B3_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_pfizer_b4_scnd{{ $row->id }}">{{ $total_pfizer_b4_scnd }}</span>  <!-- B4_PFIZER   -->
                            </td>
                            <td>
                                <span class="label label-warning total_vcted_pfizer_scnd{{ $row->id }}">{{ $total_vcted_pfizer_scnd }}</span> <!-- TOTAL VACCINATED_PFIZER-->
                            </td> <!-- 1-6 -->
                            <td>
                                <span class="label label-warning total_mild_pfizer_scnd{{ $row->id }}">{{ $total_mild_pfizer_scnd }}</span> <!-- MILD_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-warning total_srs_pfizer_scnd{{ $row->id }}">{{ $total_srs_pfizer_scnd }}</span> <!-- SERIOUS_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-warning total_dfrd_pfizer_scnd{{ $row->id }}">{{ $total_dfrd_pfizer_scnd }}</span> <!-- DEFERRED_PFIZER  -->
                            </td>
                            <td>
                                <span class="label label-warning total_rfsd_pfizer_scnd{{ $row->id }}">{{ $total_rfsd_pfizer_scnd }}</span> <!-- REFUSED_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-warning total_wstge_pfizer_scnd{{ $row->id }}">{{ $total_wstge_pfizer_scnd }}</span> <!-- WASTAGE_PFIZER  -->
                            </td>
                            <td>
                                <span class="label label-warning p_cvrge_pfizer_scnd{{ $row->id }}">{{ number_format($p_cvrge_pfizer_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-warning total_c_rate_pfizer_scnd{{ $row->id }}">{{ number_format($total_c_rate_pfizer_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_PFIZER -->
                            </td>
                            <td>
                                <span class="label label-warning total_r_unvcted_scnd_pfizer{{ $row->id }}">{{ $total_r_unvcted_scnd_pfizer }}</span> <!-- REMAINUNG_UNVACCIANTED_S2 PFIZER -->
                            </td>
                        </tr>
                        </tbody>
                        <!-- SPUTNIK V-->
                        <tbody id="collapse_sputnikv{{ $row->id }}" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <tr style="background-color: #b1ffdb">
                            <td rowspan="2">
                            </td> <!-- 1-3 -->
                            <td rowspan="2" style="color:black;" class="total_epop_sputnikv_a1{{ $row->id }}">{{ $total_epop_sputnikv_a1 }}</td>  <!-- TOTAL_E_POP_A1_SPUTNIKV -->
                            <td rowspan="2" style="color:black;" class="total_epop_sputnikv_a2{{ $row->id }}">{{ $total_epop_sputnikv_a2 }}</td>  <!-- TOTAL_E_POP_A2_SPUTNIKV -->
                            <td rowspan="2" style="color:black" class="total_epop_sputnikv_a3{{ $row->id }}">{{ $total_epop_sputnikv_a3 }}</td>  <!-- TOTAL_E_POP_A3_SPUTNIKV -->
                            <td rowspan="2" style="color:black" class="total_epop_sputnikv_a4{{ $row->id }}">{{ $total_epop_sputnikv_a4 }}</td>  <!-- TOTAL_E_POP_A4_SPUTNIKV -->
                            <td rowspan="2" style="color:black" class="total_epop_sputnikv_a5{{ $row->id }}">{{ $total_epop_sputnikv_a5 }}</td>  <!-- TOTAL_E_POP_A5_SPUTNIKV -->
                            <td rowspan="2" style="color:black" class="total_epop_sputnikv_b1{{ $row->id }}">{{ $total_epop_sputnikv_b1 }}</td>  <!-- TOTAL_E_POP_B1_SPUTNIKV -->
                            <td rowspan="2" style="color:black" class="total_epop_sputnikv_b2{{ $row->id }}">{{ $total_epop_sputnikv_b2 }}</td>  <!-- TOTAL_E_POP_B2_SPUTNIKV -->
                            <td rowspan="2" style="color:black" class="total_epop_sputnikv_b3{{ $row->id }}">{{ $total_epop_sputnikv_b3 }}</td>  <!-- TOTAL_E_POP_B3_SPUTNIKV -->
                            <td rowspan="2" style="color:black" class="total_epop_sputnikv_b4{{ $row->id }}">{{ $total_epop_sputnikv_b4 }}</td>  <!-- TOTAL_E_POP_B4_SPUTNIKV -->
                            <td rowspan="2" style="color:black;" class="total_epop_sputnikv{{ $row->id }}">{{ $total_epop_sputnikv }} </td>  <!-- TOTAL_E_POP_SPUTNIKV -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_sputnikv_frst{{ $row->id }}">{{ $total_vallocated_sputnikv_frst }}</td>  <!-- VACCINE ALLOCATED_SPUTNIKV (FD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_sputnikv_scnd{{ $row->id }}">{{ $total_vallocated_sputnikv_scnd }}</td>  <!-- VACCINE ALLOCATED_SPUTNIKV (SD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_sputnikv{{ $row->id }}">{{ $total_vallocated_sputnikv }}</td>  <!-- TOTAL VACCINE ALLOCATED_SPUTNIKV -->
                            <td style="color:black;">
                                <span class="label label-success total_sputnikv_a1_frst{{ $row->id }}">{{ $total_sputnikv_a1_frst }}</span>  <!-- A1_SPUTNIKV  -->
                            </td>
                            <td style="color:black">
                                <span class="label label-success total_sputnikv_a2_frst{{ $row->id }}">{{ $total_sputnikv_a2_frst }}</span>  <!-- A2_SPUTNIKV  -->
                            </td>
                            <td>
                                <span class="label label-success total_sputnikv_a3_frst{{ $row->id }}">{{ $total_sputnikv_a3_frst }}</span>  <!-- A3_SPUTNIKV  -->
                            </td>
                            <td>
                                <span class="label label-success total_sputnikv_a4_frst{{ $row->id }}">{{ $total_sputnikv_a4_frst }}</span>  <!-- A4_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_sputnikv_a5_frst{{ $row->id }}">{{ $total_sputnikv_a5_frst }}</span>  <!-- A5_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_sputnikv_b1_frst{{ $row->id }}">{{ $total_sputnikv_b1_frst }}</span>  <!-- B1_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_sputnikv_b2_frst{{ $row->id }}">{{ $total_sputnikv_b2_frst }}</span>  <!-- B2_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_sputnikv_b3_frst{{ $row->id }}">{{ $total_sputnikv_b3_frst }}</span>  <!-- B3_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_sputnikv_b4_frst{{ $row->id }}">{{ $total_sputnikv_b4_frst }}</span>  <!-- B4_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_vcted_sputnikv_frst{{ $row->id }}">{{ $total_vcted_sputnikv_frst }}</span> <!-- TOTAL VACCINATED_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_mild_sputnikv_frst{{ $row->id }}">{{ $total_mild_sputnikv_frst }}</span> <!-- MILD_SPUTNIKV   -->
                            </td>
                            <td>
                                <span class="label label-success total_srs_sputnikv_frst{{ $row->id }}">{{ $total_srs_sputnikv_frst }}</span> <!-- SERIOUS_SPUTNIKV  -->
                            </td>
                            <td>
                                <span class="label label-success total_dfrd_sputnikv_frst{{ $row->id }}">{{ $total_dfrd_sputnikv_frst }}</span> <!-- DEFERRED_SPUTNIKV   -->
                            </td>
                            <td>
                                <span class="label label-success total_rfsd_sputnikv_frst{{ $row->id }}">{{ $total_rfsd_sputnikv_frst }}</span> <!-- REFUSED_SPUTNIKV   -->
                            </td>
                            <td>
                                <span class="label label-success total_wstge_sputnikv_frst{{ $row->id }}">{{ $total_wstge_sputnikv_frst }}</span> <!-- WASTAGE_SPUTNIKV  -->
                            </td>
                            <td>
                                <span class="label label-success p_cvrge_sputnikv_frst{{ $row->id }}">{{ number_format($p_cvrge_sputnikv_frst,2)}}%</span> <!-- PERCENT_COVERAGE_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_c_rate_sputnikv_frst{{ $row->id }}">{{ number_format($total_c_rate_sputnikv_frst,2) }}%</span> <!-- CONSUMPTION_RATE_SPUTNIKV -->
                            </td>
                            <td>
                                <span class="label label-success total_r_unvcted_frst_sputnikv{{ $row->id }}">{{ $total_r_unvcted_frst_sputnikv }}</span> <!-- REMAINUNG_UNVACCIANTED_SPUTNIKV -->
                            </td>
                        </tr>
                        <tr style="background-color: #b1ffdb">
                            <td style="color: black;">
                                <span class="label label-warning total_sputnikv_a1_scnd{{ $row->id }}">{{ $total_sputnikv_a1_scnd }}</span>  <!-- A1_SPUTNIK2  -->
                            </td>
                            <td style="color:black;">
                                <span class="label label-warning total_sputnikv_a2_scnd{{ $row->id }}">{{ $total_sputnikv_a2_scnd }}</span>  <!-- A2_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_sputnikv_a3_scnd{{ $row->id }}">{{ $total_sputnikv_a3_scnd }}</span>  <!-- A3_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_sputnikv_a4_scnd{{ $row->id }}">{{ $total_sputnikv_a4_scnd }}</span>  <!-- A4_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_sputnikv_a5_scnd{{ $row->id }}">{{ $total_sputnikv_a5_scnd }}</span>  <!-- A5_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_sputnikv_b1_scnd{{ $row->id }}">{{ $total_sputnikv_b1_scnd }}</span>  <!-- B1_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_sputnikv_b2_scnd{{ $row->id }}">{{ $total_sputnikv_b2_scnd }}</span>  <!-- B2_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_sputnikv_b3_scnd{{ $row->id }}">{{ $total_sputnikv_b3_scnd }}</span>  <!-- B3_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_sputnikv_b4_scnd{{ $row->id }}">{{ $total_sputnikv_b4_scnd }}</span>  <!-- B4_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_vcted_sputnikv_scnd{{ $row->id }}">{{ $total_vcted_sputnikv_scnd }}</span> <!-- TOTAL VACCINATED_SPUTNIKV2-->
                            </td>
                            <td>
                                <span class="label label-warning total_mild_sputnikv_scnd{{ $row->id }}">{{ $total_mild_sputnikv_scnd }}</span> <!-- MILD_SPUTNIKV2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_srs_sputnikv_scnd{{ $row->id }}">{{ $total_srs_sputnikv_scnd }}</span> <!-- SERIOUS_SPUTNIKV2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_dfrd_sputnikv_scnd{{ $row->id }}">{{ $total_dfrd_sputnikv_scnd }}</span> <!-- DEFERRED_SPUTNIKV2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_rfsd_sputnikv_scnd{{ $row->id }}">{{ $total_rfsd_sputnikv_scnd }}</span> <!-- REFUSED_SPUTNIKV2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_wstge_sputnikv_scnd{{ $row->id }}">{{ $total_wstge_sputnikv_scnd }}</span> <!-- WASTAGE_SPUTNIKV2  -->
                            </td>
                            <td>
                                <span class="label label-warning p_cvrge_sputnikv_scnd{{ $row->id }}">{{ number_format($p_cvrge_sputnikv_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_SPUTNIKV2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_c_rate_sputnikv_scnd{{ $row->id }}">{{ number_format($total_c_rate_sputnikv_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_SPUTNIKV2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_r_unvcted_scnd_sputnikv{{ $row->id }}">{{ $total_r_unvcted_scnd_sputnikv }}</span> <!-- REMAINUNG_UNVACCIANTED_S2  SPUTNIKV2-->
                            </td>
                        </tr>
                        </tbody>
                        <!-- MODERNA-->
                        <tbody id="collapse_moderna{{ $row->id }}" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <tr style="background-color: #dad8ff">
                            <td rowspan="2">
                            </td> <!-- 1-3 -->
                            <td rowspan="2" style="color:black;" class="total_epop_moderna_a1{{ $row->id }}">{{ $total_epop_moderna_a1 }}</td>  <!-- TOTAL_E_POP_A1_MODERNA -->
                            <td rowspan="2" style="color:black;" class="total_epop_moderna_a2{{ $row->id }}">{{ $total_epop_moderna_a2 }}</td>  <!-- TOTAL_E_POP_A2_MODERNA -->
                            <td rowspan="2" style="color:black" class="total_epop_moderna_a3{{ $row->id }}">{{ $total_epop_moderna_a3 }}</td>  <!-- TOTAL_E_POP_A3_MODERNA -->
                            <td rowspan="2" style="color:black" class="total_epop_moderna_a4{{ $row->id }}">{{ $total_epop_moderna_a4 }}</td>  <!-- TOTAL_E_POP_A4_MODERNA -->
                            <td rowspan="2" style="color:black" class="total_epop_moderna_a5{{ $row->id }}">{{ $total_epop_moderna_a5 }}</td>  <!-- TOTAL_E_POP_A5_MODERNA -->
                            <td rowspan="2" style="color:black" class="total_epop_moderna_b1{{ $row->id }}">{{ $total_epop_moderna_b1 }}</td>  <!-- TOTAL_E_POP_B1_MODERNA -->
                            <td rowspan="2" style="color:black" class="total_epop_moderna_b2{{ $row->id }}">{{ $total_epop_moderna_b2 }}</td>  <!-- TOTAL_E_POP_B2_MODERNA -->
                            <td rowspan="2" style="color:black" class="total_epop_moderna_b3{{ $row->id }}">{{ $total_epop_moderna_b3 }}</td>  <!-- TOTAL_E_POP_B3_MODERNA -->
                            <td rowspan="2" style="color:black" class="total_epop_moderna_b4{{ $row->id }}">{{ $total_epop_moderna_b4 }}</td>  <!-- TOTAL_E_POP_B4_MODERNA -->
                            <td rowspan="2" style="color:black;" class="total_epop_moderna{{ $row->id }}">{{ $total_epop_moderna }} </td>  <!-- TOTAL_E_POP_MODERNA -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_moderna_frst{{ $row->id }}">{{ $total_vallocated_moderna_frst }}</td>  <!-- VACCINE ALLOCATED_MODERNA (FD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_moderna_scnd{{ $row->id }}">{{ $total_vallocated_moderna_scnd }}</td>  <!-- VACCINE ALLOCATED_MODERNA (SD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_moderna{{ $row->id }}">{{ $total_vallocated_moderna }}</td>  <!-- TOTAL VACCINE ALLOCATED_MODERNA -->
                            <td style="color:black;">
                                <span class="label label-success total_moderna_a1_frst{{ $row->id }}">{{ $total_moderna_a1_frst }}</span>  <!-- A1_MODERNA  -->
                            </td>
                            <td style="color:black">
                                <span class="label label-success total_moderna_a2_frst{{ $row->id }}">{{ $total_moderna_a2_frst }}</span>  <!-- A2_MODERNA  -->
                            </td>
                            <td>
                                <span class="label label-success total_moderna_a3_frst{{ $row->id }}">{{ $total_moderna_a3_frst }}</span>  <!-- A3_MODERNA  -->
                            </td>
                            <td>
                                <span class="label label-success total_moderna_a4_frst{{ $row->id }}">{{ $total_moderna_a4_frst }}</span>  <!-- A4_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_moderna_a5_frst{{ $row->id }}">{{ $total_moderna_a5_frst }}</span>  <!-- A5_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_moderna_b1_frst{{ $row->id }}">{{ $total_moderna_b1_frst }}</span>  <!-- B1_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_moderna_b2_frst{{ $row->id }}">{{ $total_moderna_b2_frst }}</span>  <!-- B2_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_moderna_b3_frst{{ $row->id }}">{{ $total_moderna_b3_frst }}</span>  <!-- B3_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_moderna_b4_frst{{ $row->id }}">{{ $total_moderna_b4_frst }}</span>  <!-- B4_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_vcted_moderna_frst{{ $row->id }}">{{ $total_vcted_moderna_frst }}</span> <!-- TOTAL VACCINATED_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_mild_moderna_frst{{ $row->id }}">{{ $total_mild_moderna_frst }}</span> <!-- MILD_MODERNA   -->
                            </td>
                            <td>
                                <span class="label label-success total_srs_moderna_frst{{ $row->id }}">{{ $total_srs_moderna_frst }}</span> <!-- SERIOUS_MODERNA  -->
                            </td>
                            <td>
                                <span class="label label-success total_dfrd_moderna_frst{{ $row->id }}">{{ $total_dfrd_moderna_frst }}</span> <!-- DEFERRED_MODERNA   -->
                            </td>
                            <td>
                                <span class="label label-success total_rfsd_moderna_frst{{ $row->id }}">{{ $total_rfsd_moderna_frst }}</span> <!-- REFUSED_MODERNA   -->
                            </td>
                            <td>
                                <span class="label label-success total_wstge_moderna_frst{{ $row->id }}">{{ $total_wstge_moderna_frst }}</span> <!-- WASTAGE_MODERNA  -->
                            </td>
                            <td>
                                <span class="label label-success p_cvrge_moderna_frst{{ $row->id }}">{{ number_format($p_cvrge_moderna_frst,2)}}%</span> <!-- PERCENT_COVERAGE_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_c_rate_moderna_frst{{ $row->id }}">{{ number_format($total_c_rate_moderna_frst,2) }}%</span> <!-- CONSUMPTION_RATE_MODERNA -->
                            </td>
                            <td>
                                <span class="label label-success total_r_unvcted_frst_moderna{{ $row->id }}">{{ $total_r_unvcted_frst_moderna }}</span> <!-- REMAINUNG_UNVACCIANTED_MODERNA -->
                            </td>
                        </tr>
                        <tr style="background-color: #dad8ff">
                            <td style="color: black;">
                                <span class="label label-warning total_moderna_a1_scnd{{ $row->id }}">{{ $total_moderna_a1_scnd }}</span>  <!-- A1_SPUTNIK2  -->
                            </td>
                            <td style="color:black;">
                                <span class="label label-warning total_moderna_a2_scnd{{ $row->id }}">{{ $total_moderna_a2_scnd }}</span>  <!-- A2_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_moderna_a3_scnd{{ $row->id }}">{{ $total_moderna_a3_scnd }}</span>  <!-- A3_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_moderna_a4_scnd{{ $row->id }}">{{ $total_moderna_a4_scnd }}</span>  <!-- A4_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_moderna_a5_scnd{{ $row->id }}">{{ $total_moderna_a5_scnd }}</span>  <!-- A5_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_moderna_b1_scnd{{ $row->id }}">{{ $total_moderna_b1_scnd }}</span>  <!-- B1_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_moderna_b2_scnd{{ $row->id }}">{{ $total_moderna_b2_scnd }}</span>  <!-- B2_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_moderna_b3_scnd{{ $row->id }}">{{ $total_moderna_b3_scnd }}</span>  <!-- B3_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_moderna_b4_scnd{{ $row->id }}">{{ $total_moderna_b4_scnd }}</span>  <!-- B4_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_vcted_moderna_scnd{{ $row->id }}">{{ $total_vcted_moderna_scnd }}</span> <!-- TOTAL VACCINATED_MODERNA2-->
                            </td>
                            <td>
                                <span class="label label-warning total_mild_moderna_scnd{{ $row->id }}">{{ $total_mild_moderna_scnd }}</span> <!-- MILD_MODERNA2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_srs_moderna_scnd{{ $row->id }}">{{ $total_srs_moderna_scnd }}</span> <!-- SERIOUS_MODERNA2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_dfrd_moderna_scnd{{ $row->id }}">{{ $total_dfrd_moderna_scnd }}</span> <!-- DEFERRED_MODERNA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_rfsd_moderna_scnd{{ $row->id }}">{{ $total_rfsd_moderna_scnd }}</span> <!-- REFUSED_MODERNA2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_wstge_moderna_scnd{{ $row->id }}">{{ $total_wstge_moderna_scnd }}</span> <!-- WASTAGE_MODERNA2  -->
                            </td>
                            <td>
                                <span class="label label-warning p_cvrge_moderna_scnd{{ $row->id }}">{{ number_format($p_cvrge_moderna_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_MODERNA2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_c_rate_moderna_scnd{{ $row->id }}">{{ number_format($total_c_rate_moderna_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_MODERNA2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_r_unvcted_scnd_moderna{{ $row->id }}">{{ $total_r_unvcted_scnd_moderna }}</span> <!-- REMAINUNG_UNVACCIANTED_S2  MODERNA2-->
                            </td>
                        </tr>
                        </tbody>
                        <!-- JOHNSON-->
                        <tbody id="collapse_johnson{{ $row->id }}" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <tr style="background-color: #9af5ee">
                            <td rowspan="2">
                            </td> <!-- 1-3 -->
                            <td rowspan="2" style="color:black;" class="total_epop_johnson_a1{{ $row->id }}">{{ $total_epop_johnson_a1 }}</td>  <!-- TOTAL_E_POP_A1_JOHNSON -->
                            <td rowspan="2" style="color:black;" class="total_epop_johnson_a2{{ $row->id }}">{{ $total_epop_johnson_a2 }}</td>  <!-- TOTAL_E_POP_A2_JOHNSON -->
                            <td rowspan="2" style="color:black" class="total_epop_johnson_a3{{ $row->id }}">{{ $total_epop_johnson_a3 }}</td>  <!-- TOTAL_E_POP_A3_JOHNSON -->
                            <td rowspan="2" style="color:black" class="total_epop_johnson_a4{{ $row->id }}">{{ $total_epop_johnson_a4 }}</td>  <!-- TOTAL_E_POP_A4_JOHNSON -->
                            <td rowspan="2" style="color:black" class="total_epop_johnson_a5{{ $row->id }}">{{ $total_epop_johnson_a5 }}</td>  <!-- TOTAL_E_POP_A5_JOHNSON -->
                            <td rowspan="2" style="color:black" class="total_epop_johnson_b1{{ $row->id }}">{{ $total_epop_johnson_b1 }}</td>  <!-- TOTAL_E_POP_B1_JOHNSON -->
                            <td rowspan="2" style="color:black" class="total_epop_johnson_b2{{ $row->id }}">{{ $total_epop_johnson_b2 }}</td>  <!-- TOTAL_E_POP_B2_JOHNSON -->
                            <td rowspan="2" style="color:black" class="total_epop_johnson_b3{{ $row->id }}">{{ $total_epop_johnson_b3 }}</td>  <!-- TOTAL_E_POP_B3_JOHNSON -->
                            <td rowspan="2" style="color:black" class="total_epop_johnson_b4{{ $row->id }}">{{ $total_epop_johnson_b4 }}</td>  <!-- TOTAL_E_POP_B4_JOHNSON -->
                            <td rowspan="2" style="color:black;" class="total_epop_johnson{{ $row->id }}">{{ $total_epop_johnson }} </td>  <!-- TOTAL_E_POP_JOHNSON -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_johnson_frst{{ $row->id }}">{{ $total_vallocated_johnson_frst }}</td>  <!-- VACCINE ALLOCATED_JOHNSON (FD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_johnson_scnd{{ $row->id }}">{{ $total_vallocated_johnson_scnd }}</td>  <!-- VACCINE ALLOCATED_JOHNSON (SD) -->
                            <td rowspan="2" style="color:black;" class="total_vallocated_johnson{{ $row->id }}">{{ $total_vallocated_johnson }}</td>  <!-- TOTAL VACCINE ALLOCATED_JOHNSON -->
                            <td style="color:black;">
                                <span class="label label-success total_johnson_a1_frst{{ $row->id }}">{{ $total_johnson_a1_frst }}</span>  <!-- A1_JOHNSON  -->
                            </td>
                            <td style="color:black">
                                <span class="label label-success total_johnson_a2_frst{{ $row->id }}">{{ $total_johnson_a2_frst }}</span>  <!-- A2_JOHNSON  -->
                            </td>
                            <td>
                                <span class="label label-success total_johnson_a3_frst{{ $row->id }}">{{ $total_johnson_a3_frst }}</span>  <!-- A3_JOHNSON  -->
                            </td>
                            <td>
                                <span class="label label-success total_johnson_a4_frst{{ $row->id }}">{{ $total_johnson_a4_frst }}</span>  <!-- A4_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_johnson_a5_frst{{ $row->id }}">{{ $total_johnson_a5_frst }}</span>  <!-- A5_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_johnson_b1_frst{{ $row->id }}">{{ $total_johnson_b1_frst }}</span>  <!-- B1_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_johnson_b2_frst{{ $row->id }}">{{ $total_johnson_b2_frst }}</span>  <!-- B2_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_johnson_b3_frst{{ $row->id }}">{{ $total_johnson_b3_frst }}</span>  <!-- B3_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_johnson_b4_frst{{ $row->id }}">{{ $total_johnson_b4_frst }}</span>  <!-- B4_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_vcted_johnson_frst{{ $row->id }}">{{ $total_vcted_johnson_frst }}</span> <!-- TOTAL VACCINATED_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_mild_johnson_frst{{ $row->id }}">{{ $total_mild_johnson_frst }}</span> <!-- MILD_JOHNSON   -->
                            </td>
                            <td>
                                <span class="label label-success total_srs_johnson_frst{{ $row->id }}">{{ $total_srs_johnson_frst }}</span> <!-- SERIOUS_JOHNSON  -->
                            </td>
                            <td>
                                <span class="label label-success total_dfrd_johnson_frst{{ $row->id }}">{{ $total_dfrd_johnson_frst }}</span> <!-- DEFERRED_JOHNSON   -->
                            </td>
                            <td>
                                <span class="label label-success total_rfsd_johnson_frst{{ $row->id }}">{{ $total_rfsd_johnson_frst }}</span> <!-- REFUSED_JOHNSON   -->
                            </td>
                            <td>
                                <span class="label label-success total_wstge_johnson_frst{{ $row->id }}">{{ $total_wstge_johnson_frst }}</span> <!-- WASTAGE_JOHNSON  -->
                            </td>
                            <td>
                                <span class="label label-success p_cvrge_johnson_frst{{ $row->id }}">{{ number_format($p_cvrge_johnson_frst,2)}}%</span> <!-- PERCENT_COVERAGE_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_c_rate_johnson_frst{{ $row->id }}">{{ number_format($total_c_rate_johnson_frst,2) }}%</span> <!-- CONSUMPTION_RATE_JOHNSON -->
                            </td>
                            <td>
                                <span class="label label-success total_r_unvcted_frst_johnson{{ $row->id }}">{{ $total_r_unvcted_frst_johnson }}</span> <!-- REMAINUNG_UNVACCIANTED_JOHNSON -->
                            </td>
                        </tr>
                        <tr style="background-color: #9af5ee">
                            <td style="color: black;">
                                <span class="label label-warning total_johnson_a1_scnd{{ $row->id }}">{{ $total_johnson_a1_scnd }}</span>  <!-- A1_SPUTNIK2  -->
                            </td>
                            <td style="color:black;">
                                <span class="label label-warning total_johnson_a2_scnd{{ $row->id }}">{{ $total_johnson_a2_scnd }}</span>  <!-- A2_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_johnson_a3_scnd{{ $row->id }}">{{ $total_johnson_a3_scnd }}</span>  <!-- A3_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_johnson_a4_scnd{{ $row->id }}">{{ $total_johnson_a4_scnd }}</span>  <!-- A4_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_johnson_a5_scnd{{ $row->id }}">{{ $total_johnson_a5_scnd }}</span>  <!-- A5_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_johnson_b1_scnd{{ $row->id }}">{{ $total_johnson_b1_scnd }}</span>  <!-- B1_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_johnson_b2_scnd{{ $row->id }}">{{ $total_johnson_b2_scnd }}</span>  <!-- B2_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_johnson_b3_scnd{{ $row->id }}">{{ $total_johnson_b3_scnd }}</span>  <!-- B3_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_johnson_b4_scnd{{ $row->id }}">{{ $total_johnson_b4_scnd }}</span>  <!-- B4_SPUTNIK2   -->
                            </td>
                            <td>
                                <span class="label label-warning total_vcted_johnson_scnd{{ $row->id }}">{{ $total_vcted_johnson_scnd }}</span> <!-- TOTAL VACCINATED_JOHNSON2-->
                            </td>
                            <td>
                                <span class="label label-warning total_mild_johnson_scnd{{ $row->id }}">{{ $total_mild_johnson_scnd }}</span> <!-- MILD_JOHNSON2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_srs_johnson_scnd{{ $row->id }}">{{ $total_srs_johnson_scnd }}</span> <!-- SERIOUS_JOHNSON2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_dfrd_johnson_scnd{{ $row->id }}">{{ $total_dfrd_johnson_scnd }}</span> <!-- DEFERRED_JOHNSON2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_rfsd_johnson_scnd{{ $row->id }}">{{ $total_rfsd_johnson_scnd }}</span> <!-- REFUSED_JOHNSON2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_wstge_johnson_scnd{{ $row->id }}">{{ $total_wstge_johnson_scnd }}</span> <!-- WASTAGE_JOHNSON2  -->
                            </td>
                            <td>
                                <span class="label label-warning p_cvrge_johnson_scnd{{ $row->id }}">{{ number_format($p_cvrge_johnson_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_JOHNSON2 -->
                            </td>
                            <td>
                                <span class="label label-warning total_c_rate_johnson_scnd{{ $row->id }}">{{ number_format($total_c_rate_johnson_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_JOHNSON2  -->
                            </td>
                            <td>
                                <span class="label label-warning total_r_unvcted_scnd_johnson{{ $row->id }}">{{ $total_r_unvcted_scnd_johnson }}</span> <!-- REMAINUNG_UNVACCIANTED_S2  JOHNSON2-->
                            </td>
                        </tr>
                        </tbody>
                        <tbody>
                        <tr>
                            <td>Total</td> <!-- 1-7 -->
                            <td>{{ $total_epop_pfizer_a1 }}</td> <!-- TOTAL_ELIGIBLE_POP_A1 -->
                            <td>{{ $total_epop_pfizer_a2 }}</td> <!-- TOTAL_ELIGIBLE_POP_A2  -->
                            <td>{{ $total_epop_pfizer_a3 }}</td> <!-- TOTAL_ELIGIBLE_POP_A3  -->
                            <td>{{ $total_epop_pfizer_a4 }}</td> <!-- TOTAL_ELIGIBLE_POP_A4  -->
                            <td>{{ $total_epop_pfizer_a5 }}</td> <!-- TOTAL_ELIGIBLE_POP_A5  -->
                            <td>{{ $total_epop_pfizer_b1 }}</td> <!-- TOTAL_ELIGIBLE_POP_B1  -->
                            <td>{{ $total_epop_pfizer_b2 }}</td> <!-- TOTAL_ELIGIBLE_POP_B2  -->
                            <td>{{ $total_epop_pfizer_b3 }}</td> <!-- TOTAL_ELIGIBLE_POP_B3  -->
                            <td>{{ $total_epop_pfizer_b4 }}</td> <!-- TOTAL_ELIGIBLE_POP_B4  -->
                            <td>{{ $total_epop_pfizer }}</td> <!-- TOTAL_ELIGBLE_POP_OVERALL -->
                            <td>
                                <b class="total_vallocated_frst{{ $row->id }}">{{ $total_vallocated_frst }}</b> <!-- TOTAL_VACCINE_ALLOCATED_FIRST  -->
                            </td>
                            <td>
                                <b class="total_vallocated_scnd{{ $row->id }}">{{ $total_vallocated_scnd }} </b> <!-- TOTAL_VACCINE_ALLOCATED_SECOND  -->
                            </td>
                            <td>
                                <b class="total_vallocated{{ $row->id }}">{{$total_vallocated }}</b> <!-- TOTAL_VACCINE_ALLOCATED  -->
                            </td>
                            <td>
                                <b class="label label-success total_a1{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a1_frst }}</b> <!-- TOTAL_A1  -->
                            </td>
                            <td>
                                <b class="label label-success total_a2{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a2_frst }}</b> <!-- TOTAL_A2  -->
                            </td>
                            <td>
                                <b class="label label-success total_a3{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a3_frst }}</b> <!-- TOTAL_A3  -->
                            </td>
                            <td>
                                <b class="label label-success total_a4{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a4_frst }}</b> <!-- TOTAL_A4  -->
                            </td>
                            <td>
                                <b class="label label-success total_a5{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a5_frst }}</b> <!-- TOTAL_A5  -->
                            </td>
                            <td>
                                <b class="label label-success total_b1{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b1_frst }}</b> <!-- TOTAL_B1  -->
                            </td>
                            <td>
                                <b class="label label-success total_b2{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b2_frst }}</b> <!-- TOTAL_B2  -->
                            </td>
                            <td>
                                <b class="label label-success total_b3{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b3_frst }}</b> <!-- TOTAL_B3  -->
                            </td>
                            <td>
                                <b class="label label-success total_b4{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b4_frst }}</b> <!-- TOTAL_B4  -->
                            </td>
                            <td>
                                <b class="label label-success total_vcted_frst{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_frst }}</b> <!-- TOTAL_VACCINATED  -->
                            </td>
                            <td>
                                <b class="label label-success total_mild{{ $row->id }}" style="margin-right: 5%">{{ $total_mild_overall_frst }}</b> <!-- TOTAL_MILD -->
                            </td>
                            <td>
                                <b class="label label-success total_serious{{ $row->id }}" style="margin-right: 5%">{{ $total_srs_overall_frst }}</b>  <!-- TOTAL_SERIOUS -->
                            </td>
                            <td>
                                <b class="label label-success total_deferred{{ $row->id }}" style="margin-right: 5%">{{ $total_dfrd_overall_frst }}</b>  <!-- TOTAL_DEFERRED -->
                            </td>
                            <td>
                                <b class="label label-success total_refused{{ $row->id }}" style="margin-right: 5%">{{ $total_rfsd_frst }}</b>  <!-- TOTAL_REFUSED -->
                            </td>
                            <td>
                                <b class="label label-success total_wastage{{ $row->id }}" style="margin-right: 5%">{{ $total_wstge_overall_frst }}</b>  <!-- TOTAL_WASTAGE -->
                            </td>
                            <td>
                                <b class="label label-success total_p_cvrge_frst{{ $row->id }}" style="margin-right: 5%">{{ number_format($total_p_cvrge_frst,2) }}%</b>  <!-- TOTAL_PERCENT_COVERAGE -->
                            </td>
                            <td>
                                <b class="label label-success total_c_rate_frst{{ $row->id }}" style="margin-right: 5%">{{ number_format($total_c_rate_frst,2) }}%</b>  <!-- TOTAL CONSUMPTION_RATE -->
                            </td>
                            <td>
                                <b class="label label-success total_r_unvcted_frst{{ $row->id }}" style="margin-right: 5%">{{ $total_r_unvcted_frst }}</b>  <!-- REMAINUNG_UNVACCINATED -->
                            </td>
                        </tr>
                        <tr>
                            <td></td> <!-- 1-7 -->
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <b class="label label-warning total_overall_a1{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a1_scnd }}</b>  <!-- TOTAL_A1 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_a2{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a2_scnd }}</b>  <!-- TOTAL_A2 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_a3{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a3_scnd }}</b>  <!-- TOTAL_A3 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_a4{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a4_scnd }}</b>  <!-- TOTAL_A4 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_a5{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_a5_scnd }}</b>  <!-- TOTAL_A4 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_b1{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b1_scnd }}</b>  <!-- TOTAL_A4 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_b2{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b2_scnd }}</b>  <!-- TOTAL_A4 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_b3{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b3_scnd }}</b>  <!-- TOTAL_A4 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_b4{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_b4_scnd }}</b>  <!-- TOTAL_A4 - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_vcted_scnd{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_overall_scnd }}</b>  <!-- TOTAL_VACCINATED - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_mild{{ $row->id }}" style="margin-right: 5%">{{ $total_mild_overall_scnd }}</b>  <!-- TOTAL_MILD OVERALL SECOND -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_serious{{ $row->id }}" style="margin-right: 5%">{{ $total_srs_overall_scnd }}</b> <!-- TOTAL_SERIOUS - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_deferred{{ $row->id }}" style="margin-right: 5%">{{ $total_dfrd_overall_scnd }}</b> <!-- TOTAL_DEFERRED - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_refused{{ $row->id }}" style="margin-right: 5%">{{ $total_rfsd_scnd }}</b> <!-- TOTAL_REFUSED - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_wastage{{ $row->id }}" style="margin-right: 5%">{{ $total_wstge_overall_scnd }}</b> <!-- TOTAL_WASTAGE - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_p_coverage{{ $row->id }}" style="margin-right: 5%">{{number_format($total_p_cvrge_scnd,2)}}%</b> <!-- TOTAL_PERCENT_COVERAGE - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_c_rate{{ $row->id }}" style="margin-right: 5%">{{ number_format($total_c_rate_scnd,2)}}%</b> <!-- TOTAL_CONSUMPTION_RATE - 2 -->
                            </td>
                            <td>
                                <b class="label label-warning total_overall_r_unvcted{{ $row->id }}" style="margin-right: 5%">{{ $total_r_unvcted_scnd }}</b> <!-- REMAINING_UNVACCINATED - 2 -->
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                @endforeach
                {{ $data->links() }}
                <div class="table-responsive">
                    @include('vaccine.vaccine_grand_total')
                </div>
            </div>
            <div class="col-md-3">
                <div id="chartContainer1" style="height: 370px; width: 100%;"></div><br><br><br>
                <div id="chartContainer2" style="height: 370px; width: 100%;"></div><br><br><br>
                <div id="chartPercentCoverage" style="height: 370px; width: 100%;"></div><br><br><br>
                <div id="chartConsumptionRate" style="height: 370px; width: 100%;"></div>
            </div>
            @else
                <div class="alert alert-warning">
                    <span class="text-warning">
                        <i class="fa fa-warning"></i> No Municipality found!
                    </span>
                </div>
            @endif
    </div>
</div>
<div class="modal fade"  role="dialog" data-backdrop="static" data-keyboard="false" id="vaccine_modal_municipality" style="min-width: 100%">
    <div class="modal-dialog modal-lg modal_w" role="document">
        <div class="modal-content">
            <div class="modal-body vaccinated_content_municipality">

            </div><!-- /.modal-content -->
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" role="dialog" id="vaccine_modal_allocated">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body vaccine_allocated_modal">
                <center>
                    <img src="{{ asset('resources/img/loading.gif') }}" alt="">
                </center>
            </div><!-- /.modal-content -->
        </div>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@include('admin.modal.facility_modal')

@endsection

@section('js')
    @include('script.chart')
    <script>

        $('#date_range').daterangepicker();
        $(".sinovac_dashboard").text({{ Session::get("sinovac_dashboard") }});
        $(".astra_dashboard").text({{ Session::get("astra_dashboard") }});
        $(".moderna_dashboard").text({{ Session::get("moderna_dashboard") }});
        $(".pfizer_dashboard").text({{ Session::get("pfizer_dashboard") }});
        $(".johnson_dashboard").text({{ Session::get("johnson_dashboard") }});

        <?php $user = Session::get('auth'); ?>

        console.log("<?php echo $typeof_vaccine_filter; ?>");

        function muncityVaccinated(province_id,muncity_id,date_start,date_end,data){
            var json = {
                "_token" : "<?php echo csrf_token()?>",
                "province_id" : province_id,
                "muncity_id" : muncity_id,
                "date_start" : date_start,
                "date_end" : date_end,
                "muncity_filter": "<?php echo $muncity_filter; ?>",
                "date_range" : "<?php echo $date_range; ?>",
                "typeof_vaccine_filter" : "<?php echo $typeof_vaccine_filter; ?>",
                "priority_filter" : "<?php echo $priority_filter; ?>",
                "pagination_table": "false",
                //sinovac
                "total_epop_svac_a1" : $(".total_epop_svac_a1"+muncity_id).text(),
                "total_epop_svac_a2" : $(".total_epop_svac_a2"+muncity_id).text(),
                "total_epop_svac_a3" : $(".total_epop_svac_a3"+muncity_id).text(),
                "total_epop_svac_a4" : $(".total_epop_svac_a4"+muncity_id).text(),
                "total_epop_svac_a5" : $(".total_epop_svac_a5"+muncity_id).text(),
                "total_epop_svac_b1" : $(".total_epop_svac_b1"+muncity_id).text(),
                "total_epop_svac_b2" : $(".total_epop_svac_b2"+muncity_id).text(),
                "total_epop_svac_b3" : $(".total_epop_svac_b3"+muncity_id).text(),
                "total_epop_svac_b4" : $(".total_epop_svac_b4"+muncity_id).text(),
                "total_epop_svac" : $(".total_epop_svac"+muncity_id).text(),
                "total_vallocated_svac_frst" : $(".total_vallocated_svac_frst"+muncity_id).text(),
                "total_vallocated_svac_scnd" : $(".total_vallocated_svac_scnd"+muncity_id).text(),
                "total_vallocated_svac" : $(".total_vallocated_svac"+muncity_id).text(),
                "total_svac_a1_frst" : $(".total_svac_a1_frst"+muncity_id).text(),
                "total_svac_a2_frst" : $(".total_svac_a2_frst"+muncity_id).text(),
                "total_svac_a3_frst" : $(".total_svac_a3_frst"+muncity_id).text(),
                "total_svac_a4_frst" : $(".total_svac_a4_frst"+muncity_id).text(),
                "total_svac_a5_frst" : $(".total_svac_a5_frst"+muncity_id).text(),
                "total_svac_b1_frst" : $(".total_svac_b1_frst"+muncity_id).text(),
                "total_svac_b2_frst" : $(".total_svac_b2_frst"+muncity_id).text(),
                "total_svac_b3_frst" : $(".total_svac_b3_frst"+muncity_id).text(),
                "total_svac_b4_frst" : $(".total_svac_b4_frst"+muncity_id).text(),
                "total_vcted_svac_frst" : $(".total_vcted_svac_frst"+muncity_id).text(),
                "total_mild_svac_frst" : $(".total_mild_svac_frst"+muncity_id).text(),
                "total_srs_svac_frst" : $(".total_srs_svac_frst"+muncity_id).text(),
                "total_dfrd_svac_frst" : $(".total_dfrd_svac_frst"+muncity_id).text(),
                "total_rfsd_svac_frst" : $(".total_rfsd_svac_frst"+muncity_id).text(),
                "total_wstge_svac_frst" : $(".total_wstge_svac_frst"+muncity_id).text(),
                "p_cvrge_svac_frst" : $(".p_cvrge_svac_frst"+muncity_id).text(),
                "total_c_rate_svac_frst" : $(".total_c_rate_svac_frst"+muncity_id).text(),
                "total_r_unvcted_frst_svac" : $(".total_r_unvcted_frst_svac"+muncity_id).text(),

                "total_svac_a1_scnd" : $(".total_svac_a1_scnd"+muncity_id).text(),
                "total_svac_a2_scnd" : $(".total_svac_a2_scnd"+muncity_id).text(),
                "total_svac_a3_scnd" : $(".total_svac_a3_scnd"+muncity_id).text(),
                "total_svac_a4_scnd" : $(".total_svac_a4_scnd"+muncity_id).text(),
                "total_svac_a5_scnd" : $(".total_svac_a5_scnd"+muncity_id).text(),
                "total_svac_b1_scnd" : $(".total_svac_b1_scnd"+muncity_id).text(),
                "total_svac_b2_scnd" : $(".total_svac_b2_scnd"+muncity_id).text(),
                "total_svac_b3_scnd" : $(".total_svac_b3_scnd"+muncity_id).text(),
                "total_svac_b4_scnd" : $(".total_svac_b4_scnd"+muncity_id).text(),
                "total_vcted_svac_scnd" : $(".total_vcted_svac_scnd"+muncity_id).text(),
                "total_mild_svac_scnd" : $(".total_mild_svac_scnd"+muncity_id).text(),
                "total_srs_svac_scnd" : $(".total_srs_svac_scnd"+muncity_id).text(),
                "total_dfrd_svac_scnd" : $(".total_dfrd_svac_scnd"+muncity_id).text(),
                "total_rfsd_svac_scnd" : $(".total_rfsd_svac_scnd"+muncity_id).text(),
                "total_wstge_svac_scnd" : $(".total_wstge_svac_scnd"+muncity_id).text(),
                "p_cvrge_svac_scnd" : $(".p_cvrge_svac_scnd"+muncity_id).text(),
                "total_c_rate_svac_scnd" : $(".total_c_rate_svac_scnd"+muncity_id).text(),
                "total_r_unvcted_scnd_svac" : $(".total_r_unvcted_scnd_svac"+muncity_id).text(),

                //ASTRAZENECA
                "total_epop_astra_a1" : $(".total_epop_astra_a1"+muncity_id).text(),
                "total_epop_astra_a2" : $(".total_epop_astra_a2"+muncity_id).text(),
                "total_epop_astra_a3" : $(".total_epop_astra_a3"+muncity_id).text(),
                "total_epop_astra_a4" : $(".total_epop_astra_a4"+muncity_id).text(),
                "total_epop_astra_a5" : $(".total_epop_astra_a5"+muncity_id).text(),
                "total_epop_astra_b1" : $(".total_epop_astra_b1"+muncity_id).text(),
                "total_epop_astra_b2" : $(".total_epop_astra_b2"+muncity_id).text(),
                "total_epop_astra_b3" : $(".total_epop_astra_b3"+muncity_id).text(),
                "total_epop_astra_b4" : $(".total_epop_astra_b4"+muncity_id).text(),
                "total_epop_astra" : $(".total_epop_astra"+muncity_id).text(),
                "total_vallocated_astra_frst" : $(".total_vallocated_astra_frst"+muncity_id).text(),
                "total_vallocated_astra_scnd" : $(".total_vallocated_astra_scnd"+muncity_id).text(),
                "total_vallocated_astra" : $(".total_vallocated_astra"+muncity_id).text(),
                "total_astra_a1_frst" : $(".total_astra_a1_frst"+muncity_id).text(),
                "total_astra_a2_frst" : $(".total_astra_a2_frst"+muncity_id).text(),
                "total_astra_a3_frst" : $(".total_astra_a3_frst"+muncity_id).text(),
                "total_astra_a4_frst" : $(".total_astra_a4_frst"+muncity_id).text(),
                "total_astra_a5_frst" : $(".total_astra_a5_frst"+muncity_id).text(),
                "total_astra_b1_frst" : $(".total_astra_b1_frst"+muncity_id).text(),
                "total_astra_b2_frst" : $(".total_astra_b2_frst"+muncity_id).text(),
                "total_astra_b3_frst" : $(".total_astra_b3_frst"+muncity_id).text(),
                "total_astra_b4_frst" : $(".total_astra_b4_frst"+muncity_id).text(),
                "total_vcted_astra_frst" : $(".total_vcted_astra_frst"+muncity_id).text(),
                "total_mild_astra_frst" : $(".total_mild_astra_frst"+muncity_id).text(),
                "total_srs_astra_frst" : $(".total_srs_astra_frst"+muncity_id).text(),
                "total_dfrd_astra_frst" : $(".total_dfrd_astra_frst"+muncity_id).text(),
                "total_rfsd_astra_frst" : $(".total_rfsd_astra_frst"+muncity_id).text(),
                "total_wstge_astra_frst" : $(".total_wstge_astra_frst"+muncity_id).text(),
                "p_cvrge_astra_frst" : $(".p_cvrge_astra_frst"+muncity_id).text(),
                "total_c_rate_astra_frst" : $(".total_c_rate_astra_frst"+muncity_id).text(),
                "total_r_unvcted_frst_astra" : $(".total_r_unvcted_frst_astra"+muncity_id).text(),

                "total_astra_a1_scnd" : $(".total_astra_a1_scnd"+muncity_id).text(),
                "total_astra_a2_scnd" : $(".total_astra_a2_scnd"+muncity_id).text(),
                "total_astra_a3_scnd" : $(".total_astra_a3_scnd"+muncity_id).text(),
                "total_astra_a4_scnd" : $(".total_astra_a4_scnd"+muncity_id).text(),
                "total_astra_a5_scnd" : $(".total_astra_a5_scnd"+muncity_id).text(),
                "total_astra_b1_scnd" : $(".total_astra_b1_scnd"+muncity_id).text(),
                "total_astra_b2_scnd" : $(".total_astra_b2_scnd"+muncity_id).text(),
                "total_astra_b3_scnd" : $(".total_astra_b3_scnd"+muncity_id).text(),
                "total_astra_b4_scnd" : $(".total_astra_b4_scnd"+muncity_id).text(),
                "total_vcted_astra_scnd" : $(".total_vcted_astra_scnd"+muncity_id).text(),
                "total_mild_astra_scnd" : $(".total_mild_astra_scnd"+muncity_id).text(),
                "total_srs_astra_scnd" : $(".total_srs_astra_scnd"+muncity_id).text(),
                "total_dfrd_astra_scnd" : $(".total_dfrd_astra_scnd"+muncity_id).text(),
                "total_rfsd_astra_scnd" : $(".total_rfsd_astra_scnd"+muncity_id).text(),
                "total_wstge_astra_scnd" : $(".total_wstge_astra_scnd"+muncity_id).text(),
                "p_cvrge_astra_scnd" : $(".p_cvrge_astra_scnd"+muncity_id).text(),
                "total_c_rate_astra_scnd" : $(".total_c_rate_astra_scnd"+muncity_id).text(),
                "total_r_unvcted_scnd_astra" : $(".total_r_unvcted_scnd_astra"+muncity_id).text(),

                //PFIZER
                "total_epop_pfizer_a1" : $(".total_epop_pfizer_a1"+muncity_id).text(),
                "total_epop_pfizer_a2" : $(".total_epop_pfizer_a2"+muncity_id).text(),
                "total_epop_pfizer_a3" : $(".total_epop_pfizer_a3"+muncity_id).text(),
                "total_epop_pfizer_a4" : $(".total_epop_pfizer_a4"+muncity_id).text(),
                "total_epop_pfizer_a5" : $(".total_epop_pfizer_a5"+muncity_id).text(),
                "total_epop_pfizer_b1" : $(".total_epop_pfizer_b1"+muncity_id).text(),
                "total_epop_pfizer_b2" : $(".total_epop_pfizer_b2"+muncity_id).text(),
                "total_epop_pfizer_b3" : $(".total_epop_pfizer_b3"+muncity_id).text(),
                "total_epop_pfizer_b4" : $(".total_epop_pfizer_b4"+muncity_id).text(),
                "total_epop_pfizer" : $(".total_epop_pfizer"+muncity_id).text(),
                "total_vallocated_pfizer_frst" : $(".total_vallocated_pfizer_frst"+muncity_id).text(),
                "total_vallocated_pfizer_scnd" : $(".total_vallocated_pfizer_scnd"+muncity_id).text(),
                "total_vallocated_pfizer" : $(".total_vallocated_pfizer"+muncity_id).text(),
                "total_pfizer_a1_frst" : $(".total_pfizer_a1_frst"+muncity_id).text(),
                "total_pfizer_a2_frst" : $(".total_pfizer_a2_frst"+muncity_id).text(),
                "total_pfizer_a3_frst" : $(".total_pfizer_a3_frst"+muncity_id).text(),
                "total_pfizer_a4_frst" : $(".total_pfizer_a4_frst"+muncity_id).text(),
                "total_pfizer_a5_frst" : $(".total_pfizer_a5_frst"+muncity_id).text(),
                "total_pfizer_b1_frst" : $(".total_pfizer_b1_frst"+muncity_id).text(),
                "total_pfizer_b2_frst" : $(".total_pfizer_b2_frst"+muncity_id).text(),
                "total_pfizer_b3_frst" : $(".total_pfizer_b3_frst"+muncity_id).text(),
                "total_pfizer_b4_frst" : $(".total_pfizer_b4_frst"+muncity_id).text(),
                "total_vcted_pfizer_frst" : $(".total_vcted_pfizer_frst"+muncity_id).text(),
                "total_mild_pfizer_frst" : $(".total_mild_pfizer_frst"+muncity_id).text(),
                "total_srs_pfizer_frst" : $(".total_srs_pfizer_frst"+muncity_id).text(),
                "total_dfrd_pfizer_frst" : $(".total_dfrd_pfizer_frst"+muncity_id).text(),
                "total_rfsd_pfizer_frst" : $(".total_rfsd_pfizer_frst"+muncity_id).text(),
                "total_wstge_pfizer_frst" : $(".total_wstge_pfizer_frst"+muncity_id).text(),
                "p_cvrge_pfizer_frst" : $(".p_cvrge_pfizer_frst"+muncity_id).text(),
                "total_c_rate_pfizer_frst" : $(".total_c_rate_pfizer_frst"+muncity_id).text(),
                "total_r_unvcted_frst_pfizer" : $(".total_r_unvcted_frst_pfizer"+muncity_id).text(),

                "total_pfizer_a1_scnd" : $(".total_pfizer_a1_scnd"+muncity_id).text(),
                "total_pfizer_a2_scnd" : $(".total_pfizer_a2_scnd"+muncity_id).text(),
                "total_pfizer_a3_scnd" : $(".total_pfizer_a3_scnd"+muncity_id).text(),
                "total_pfizer_a4_scnd" : $(".total_pfizer_a4_scnd"+muncity_id).text(),
                "total_pfizer_a5_scnd" : $(".total_pfizer_a5_scnd"+muncity_id).text(),
                "total_pfizer_b1_scnd" : $(".total_pfizer_b1_scnd"+muncity_id).text(),
                "total_pfizer_b2_scnd" : $(".total_pfizer_b2_scnd"+muncity_id).text(),
                "total_pfizer_b3_scnd" : $(".total_pfizer_b3_scnd"+muncity_id).text(),
                "total_pfizer_b4_scnd" : $(".total_pfizer_b4_scnd"+muncity_id).text(),
                "total_vcted_pfizer_scnd" : $(".total_vcted_pfizer_scnd"+muncity_id).text(),
                "total_mild_pfizer_scnd" : $(".total_mild_pfizer_scnd"+muncity_id).text(),
                "total_srs_pfizer_scnd" : $(".total_srs_pfizer_scnd"+muncity_id).text(),
                "total_dfrd_pfizer_scnd" : $(".total_dfrd_pfizer_scnd"+muncity_id).text(),
                "total_rfsd_pfizer_scnd" : $(".total_rfsd_pfizer_scnd"+muncity_id).text(),
                "total_wstge_pfizer_scnd" : $(".total_wstge_pfizer_scnd"+muncity_id).text(),
                "p_cvrge_pfizer_scnd" : $(".p_cvrge_pfizer_scnd"+muncity_id).text(),
                "total_c_rate_pfizer_scnd" : $(".total_c_rate_pfizer_scnd"+muncity_id).text(),
                "total_r_unvcted_scnd_pfizer" : $(".total_r_unvcted_scnd_pfizer"+muncity_id).text(),

                //SPUTNIKV
                "total_epop_sputnikv_a1" : $(".total_epop_sputnikv_a1"+muncity_id).text(),
                "total_epop_sputnikv_a2" : $(".total_epop_sputnikv_a2"+muncity_id).text(),
                "total_epop_sputnikv_a3" : $(".total_epop_sputnikv_a3"+muncity_id).text(),
                "total_epop_sputnikv_a4" : $(".total_epop_sputnikv_a4"+muncity_id).text(),
                "total_epop_sputnikv_a5" : $(".total_epop_sputnikv_a5"+muncity_id).text(),
                "total_epop_sputnikv_b1" : $(".total_epop_sputnikv_b1"+muncity_id).text(),
                "total_epop_sputnikv_b2" : $(".total_epop_sputnikv_b2"+muncity_id).text(),
                "total_epop_sputnikv_b3" : $(".total_epop_sputnikv_b3"+muncity_id).text(),
                "total_epop_sputnikv_b4" : $(".total_epop_sputnikv_b4"+muncity_id).text(),
                "total_epop_sputnikv" : $(".total_epop_sputnikv"+muncity_id).text(),
                "total_vallocated_sputnikv_frst" : $(".total_vallocated_sputnikv_frst"+muncity_id).text(),
                "total_vallocated_sputnikv_scnd" : $(".total_vallocated_sputnikv_scnd"+muncity_id).text(),
                "total_vallocated_sputnikv" : $(".total_vallocated_sputnikv"+muncity_id).text(),
                "total_sputnikv_a1_frst" : $(".total_sputnikv_a1_frst"+muncity_id).text(),
                "total_sputnikv_a2_frst" : $(".total_sputnikv_a2_frst"+muncity_id).text(),
                "total_sputnikv_a3_frst" : $(".total_sputnikv_a3_frst"+muncity_id).text(),
                "total_sputnikv_a4_frst" : $(".total_sputnikv_a4_frst"+muncity_id).text(),
                "total_sputnikv_a5_frst" : $(".total_sputnikv_a5_frst"+muncity_id).text(),
                "total_sputnikv_b1_frst" : $(".total_sputnikv_b1_frst"+muncity_id).text(),
                "total_sputnikv_b2_frst" : $(".total_sputnikv_b2_frst"+muncity_id).text(),
                "total_sputnikv_b3_frst" : $(".total_sputnikv_b3_frst"+muncity_id).text(),
                "total_sputnikv_b4_frst" : $(".total_sputnikv_b4_frst"+muncity_id).text(),
                "total_vcted_sputnikv_frst" : $(".total_vcted_sputnikv_frst"+muncity_id).text(),
                "total_mild_sputnikv_frst" : $(".total_mild_sputnikv_frst"+muncity_id).text(),
                "total_srs_sputnikv_frst" : $(".total_srs_sputnikv_frst"+muncity_id).text(),
                "total_dfrd_sputnikv_frst" : $(".total_dfrd_sputnikv_frst"+muncity_id).text(),
                "total_rfsd_sputnikv_frst" : $(".total_rfsd_sputnikv_frst"+muncity_id).text(),
                "total_wstge_sputnikv_frst" : $(".total_wstge_sputnikv_frst"+muncity_id).text(),
                "p_cvrge_sputnikv_frst" : $(".p_cvrge_sputnikv_frst"+muncity_id).text(),
                "total_c_rate_sputnikv_frst" : $(".total_c_rate_sputnikv_frst"+muncity_id).text(),
                "total_r_unvcted_frst_sputnikv" : $(".total_r_unvcted_frst_sputnikv"+muncity_id).text(),

                "total_sputnikv_a1_scnd" : $(".total_sputnikv_a1_scnd"+muncity_id).text(),
                "total_sputnikv_a2_scnd" : $(".total_sputnikv_a2_scnd"+muncity_id).text(),
                "total_sputnikv_a3_scnd" : $(".total_sputnikv_a3_scnd"+muncity_id).text(),
                "total_sputnikv_a4_scnd" : $(".total_sputnikv_a4_scnd"+muncity_id).text(),
                "total_sputnikv_a5_scnd" : $(".total_sputnikv_a5_scnd"+muncity_id).text(),
                "total_sputnikv_b1_scnd" : $(".total_sputnikv_b1_scnd"+muncity_id).text(),
                "total_sputnikv_b2_scnd" : $(".total_sputnikv_b2_scnd"+muncity_id).text(),
                "total_sputnikv_b3_scnd" : $(".total_sputnikv_b3_scnd"+muncity_id).text(),
                "total_sputnikv_b4_scnd" : $(".total_sputnikv_b4_scnd"+muncity_id).text(),
                "total_vcted_sputnikv_scnd" : $(".total_vcted_sputnikv_scnd"+muncity_id).text(),
                "total_mild_sputnikv_scnd" : $(".total_mild_sputnikv_scnd"+muncity_id).text(),
                "total_srs_sputnikv_scnd" : $(".total_srs_sputnikv_scnd"+muncity_id).text(),
                "total_dfrd_sputnikv_scnd" : $(".total_dfrd_sputnikv_scnd"+muncity_id).text(),
                "total_rfsd_sputnikv_scnd" : $(".total_rfsd_sputnikv_scnd"+muncity_id).text(),
                "total_wstge_sputnikv_scnd" : $(".total_wstge_sputnikv_scnd"+muncity_id).text(),
                "p_cvrge_sputnikv_scnd" : $(".p_cvrge_sputnikv_scnd"+muncity_id).text(),
                "total_c_rate_sputnikv_scnd" : $(".total_c_rate_sputnikv_scnd"+muncity_id).text(),
                "total_r_unvcted_scnd_sputnikv" : $(".total_r_unvcted_scnd_sputnikv"+muncity_id).text(),

                //MODERNA
                "total_epop_moderna_a1" : $(".total_epop_moderna_a1"+muncity_id).text(),
                "total_epop_moderna_a2" : $(".total_epop_moderna_a2"+muncity_id).text(),
                "total_epop_moderna_a3" : $(".total_epop_moderna_a3"+muncity_id).text(),
                "total_epop_moderna_a4" : $(".total_epop_moderna_a4"+muncity_id).text(),
                "total_epop_moderna_a5" : $(".total_epop_moderna_a5"+muncity_id).text(),
                "total_epop_moderna_b1" : $(".total_epop_moderna_b1"+muncity_id).text(),
                "total_epop_moderna_b2" : $(".total_epop_moderna_b2"+muncity_id).text(),
                "total_epop_moderna_b3" : $(".total_epop_moderna_b3"+muncity_id).text(),
                "total_epop_moderna_b4" : $(".total_epop_moderna_b4"+muncity_id).text(),
                "total_epop_moderna" : $(".total_epop_moderna"+muncity_id).text(),
                "total_vallocated_moderna_frst" : $(".total_vallocated_moderna_frst"+muncity_id).text(),
                "total_vallocated_moderna_scnd" : $(".total_vallocated_moderna_scnd"+muncity_id).text(),
                "total_vallocated_moderna" : $(".total_vallocated_moderna"+muncity_id).text(),
                "total_moderna_a1_frst" : $(".total_moderna_a1_frst"+muncity_id).text(),
                "total_moderna_a2_frst" : $(".total_moderna_a2_frst"+muncity_id).text(),
                "total_moderna_a3_frst" : $(".total_moderna_a3_frst"+muncity_id).text(),
                "total_moderna_a4_frst" : $(".total_moderna_a4_frst"+muncity_id).text(),
                "total_moderna_a5_frst" : $(".total_moderna_a5_frst"+muncity_id).text(),
                "total_moderna_b1_frst" : $(".total_moderna_b1_frst"+muncity_id).text(),
                "total_moderna_b2_frst" : $(".total_moderna_b2_frst"+muncity_id).text(),
                "total_moderna_b3_frst" : $(".total_moderna_b3_frst"+muncity_id).text(),
                "total_moderna_b4_frst" : $(".total_moderna_b4_frst"+muncity_id).text(),
                "total_vcted_moderna_frst" : $(".total_vcted_moderna_frst"+muncity_id).text(),
                "total_mild_moderna_frst" : $(".total_mild_moderna_frst"+muncity_id).text(),
                "total_srs_moderna_frst" : $(".total_srs_moderna_frst"+muncity_id).text(),
                "total_dfrd_moderna_frst" : $(".total_dfrd_moderna_frst"+muncity_id).text(),
                "total_rfsd_moderna_frst" : $(".total_rfsd_moderna_frst"+muncity_id).text(),
                "total_wstge_moderna_frst" : $(".total_wstge_moderna_frst"+muncity_id).text(),
                "p_cvrge_moderna_frst" : $(".p_cvrge_moderna_frst"+muncity_id).text(),
                "total_c_rate_moderna_frst" : $(".total_c_rate_moderna_frst"+muncity_id).text(),
                "total_r_unvcted_frst_moderna" : $(".total_r_unvcted_frst_moderna"+muncity_id).text(),

                "total_moderna_a1_scnd" : $(".total_moderna_a1_scnd"+muncity_id).text(),
                "total_moderna_a2_scnd" : $(".total_moderna_a2_scnd"+muncity_id).text(),
                "total_moderna_a3_scnd" : $(".total_moderna_a3_scnd"+muncity_id).text(),
                "total_moderna_a4_scnd" : $(".total_moderna_a4_scnd"+muncity_id).text(),
                "total_moderna_a5_scnd" : $(".total_moderna_a5_scnd"+muncity_id).text(),
                "total_moderna_b1_scnd" : $(".total_moderna_b1_scnd"+muncity_id).text(),
                "total_moderna_b2_scnd" : $(".total_moderna_b2_scnd"+muncity_id).text(),
                "total_moderna_b3_scnd" : $(".total_moderna_b3_scnd"+muncity_id).text(),
                "total_moderna_b4_scnd" : $(".total_moderna_b4_scnd"+muncity_id).text(),
                "total_vcted_moderna_scnd" : $(".total_vcted_moderna_scnd"+muncity_id).text(),
                "total_mild_moderna_scnd" : $(".total_mild_moderna_scnd"+muncity_id).text(),
                "total_srs_moderna_scnd" : $(".total_srs_moderna_scnd"+muncity_id).text(),
                "total_dfrd_moderna_scnd" : $(".total_dfrd_moderna_scnd"+muncity_id).text(),
                "total_rfsd_moderna_scnd" : $(".total_rfsd_moderna_scnd"+muncity_id).text(),
                "total_wstge_moderna_scnd" : $(".total_wstge_moderna_scnd"+muncity_id).text(),
                "p_cvrge_moderna_scnd" : $(".p_cvrge_moderna_scnd"+muncity_id).text(),
                "total_c_rate_moderna_scnd" : $(".total_c_rate_moderna_scnd"+muncity_id).text(),
                "total_r_unvcted_scnd_moderna" : $(".total_r_unvcted_scnd_moderna"+muncity_id).text(),

                //JOHNSON
                "total_epop_johnson_a1" : $(".total_epop_johnson_a1"+muncity_id).text(),
                "total_epop_johnson_a2" : $(".total_epop_johnson_a2"+muncity_id).text(),
                "total_epop_johnson_a3" : $(".total_epop_johnson_a3"+muncity_id).text(),
                "total_epop_johnson_a4" : $(".total_epop_johnson_a4"+muncity_id).text(),
                "total_epop_johnson_a5" : $(".total_epop_johnson_a5"+muncity_id).text(),
                "total_epop_johnson_b1" : $(".total_epop_johnson_b1"+muncity_id).text(),
                "total_epop_johnson_b2" : $(".total_epop_johnson_b2"+muncity_id).text(),
                "total_epop_johnson_b3" : $(".total_epop_johnson_b3"+muncity_id).text(),
                "total_epop_johnson_b4" : $(".total_epop_johnson_b4"+muncity_id).text(),
                "total_epop_johnson" : $(".total_epop_johnson"+muncity_id).text(),
                "total_vallocated_johnson_frst" : $(".total_vallocated_johnson_frst"+muncity_id).text(),
                "total_vallocated_johnson_scnd" : $(".total_vallocated_johnson_scnd"+muncity_id).text(),
                "total_vallocated_johnson" : $(".total_vallocated_johnson"+muncity_id).text(),
                "total_johnson_a1_frst" : $(".total_johnson_a1_frst"+muncity_id).text(),
                "total_johnson_a2_frst" : $(".total_johnson_a2_frst"+muncity_id).text(),
                "total_johnson_a3_frst" : $(".total_johnson_a3_frst"+muncity_id).text(),
                "total_johnson_a4_frst" : $(".total_johnson_a4_frst"+muncity_id).text(),
                "total_johnson_a5_frst" : $(".total_johnson_a5_frst"+muncity_id).text(),
                "total_johnson_b1_frst" : $(".total_johnson_b1_frst"+muncity_id).text(),
                "total_johnson_b2_frst" : $(".total_johnson_b2_frst"+muncity_id).text(),
                "total_johnson_b3_frst" : $(".total_johnson_b3_frst"+muncity_id).text(),
                "total_johnson_b4_frst" : $(".total_johnson_b4_frst"+muncity_id).text(),
                "total_vcted_johnson_frst" : $(".total_vcted_johnson_frst"+muncity_id).text(),
                "total_mild_johnson_frst" : $(".total_mild_johnson_frst"+muncity_id).text(),
                "total_srs_johnson_frst" : $(".total_srs_johnson_frst"+muncity_id).text(),
                "total_dfrd_johnson_frst" : $(".total_dfrd_johnson_frst"+muncity_id).text(),
                "total_rfsd_johnson_frst" : $(".total_rfsd_johnson_frst"+muncity_id).text(),
                "total_wstge_johnson_frst" : $(".total_wstge_johnson_frst"+muncity_id).text(),
                "p_cvrge_johnson_frst" : $(".p_cvrge_johnson_frst"+muncity_id).text(),
                "total_c_rate_johnson_frst" : $(".total_c_rate_johnson_frst"+muncity_id).text(),
                "total_r_unvcted_frst_johnson" : $(".total_r_unvcted_frst_johnson"+muncity_id).text(),

                "total_johnson_a1_scnd" : $(".total_johnson_a1_scnd"+muncity_id).text(),
                "total_johnson_a2_scnd" : $(".total_johnson_a2_scnd"+muncity_id).text(),
                "total_johnson_a3_scnd" : $(".total_johnson_a3_scnd"+muncity_id).text(),
                "total_johnson_a4_scnd" : $(".total_johnson_a4_scnd"+muncity_id).text(),
                "total_johnson_a5_scnd" : $(".total_johnson_a5_scnd"+muncity_id).text(),
                "total_johnson_b1_scnd" : $(".total_johnson_b1_scnd"+muncity_id).text(),
                "total_johnson_b2_scnd" : $(".total_johnson_b2_scnd"+muncity_id).text(),
                "total_johnson_b3_scnd" : $(".total_johnson_b3_scnd"+muncity_id).text(),
                "total_johnson_b4_scnd" : $(".total_johnson_b4_scnd"+muncity_id).text(),
                "total_vcted_johnson_scnd" : $(".total_vcted_johnson_scnd"+muncity_id).text(),
                "total_mild_johnson_scnd" : $(".total_mild_johnson_scnd"+muncity_id).text(),
                "total_srs_johnson_scnd" : $(".total_srs_johnson_scnd"+muncity_id).text(),
                "total_dfrd_johnson_scnd" : $(".total_dfrd_johnson_scnd"+muncity_id).text(),
                "total_rfsd_johnson_scnd" : $(".total_rfsd_johnson_scnd"+muncity_id).text(),
                "total_wstge_johnson_scnd" : $(".total_wstge_johnson_scnd"+muncity_id).text(),
                "p_cvrge_johnson_scnd" : $(".p_cvrge_johnson_scnd"+muncity_id).text(),
                "total_c_rate_johnson_scnd" : $(".total_c_rate_johnson_scnd"+muncity_id).text(),
                "total_r_unvcted_scnd_johnson" : $(".total_r_unvcted_scnd_johnson"+muncity_id).text(),

                //TOTAL
                "total_vallocated_frst" : $(".total_vallocated_frst"+muncity_id).text(),
                "total_vallocated_scnd" : $(".total_vallocated_scnd"+muncity_id).text(),
                "total_vallocated" : $(".total_vallocated"+muncity_id).text(),
                "total_a1" : $(".total_a1"+muncity_id).text(),
                "total_a2" : $(".total_a2"+muncity_id).text(),
                "total_a3" : $(".total_a3"+muncity_id).text(),
                "total_a4" : $(".total_a4"+muncity_id).text(),
                "total_a5" : $(".total_a5"+muncity_id).text(),
                "total_b1" : $(".total_b1"+muncity_id).text(),
                "total_b2" : $(".total_b2"+muncity_id).text(),
                "total_b3" : $(".total_b3"+muncity_id).text(),
                "total_b4" : $(".total_b4"+muncity_id).text(),
                "total_vcted_frst" : $(".total_vcted_frst"+muncity_id).text(),
                "total_mild" : $(".total_mild"+muncity_id).text(),
                "total_serious" : $(".total_serious"+muncity_id).text(),
                "total_deferred" : $(".total_deferred"+muncity_id).text(),
                "total_refused" : $(".total_refused"+muncity_id).text(),
                "total_wastage" : $(".total_wastage"+muncity_id).text(),
                "total_p_cvrge_frst" : $(".total_p_cvrge_frst"+muncity_id).text(),
                "total_c_rate_frst" : $(".total_c_rate_frst"+muncity_id).text(),
                "total_r_unvcted_frst" : $(".total_r_unvcted_frst"+muncity_id).text(),

                "total_overall_a1" : $(".total_overall_a1"+muncity_id).text(),
                "total_overall_a2" : $(".total_overall_a2"+muncity_id).text(),
                "total_overall_a3" : $(".total_overall_a3"+muncity_id).text(),
                "total_overall_a4" : $(".total_overall_a4"+muncity_id).text(),
                "total_overall_a5" : $(".total_overall_a5"+muncity_id).text(),
                "total_overall_b1" : $(".total_overall_b1"+muncity_id).text(),
                "total_overall_b2" : $(".total_overall_b2"+muncity_id).text(),
                "total_overall_b3" : $(".total_overall_b3"+muncity_id).text(),
                "total_overall_b4" : $(".total_overall_b4"+muncity_id).text(),
                "total_vcted_scnd" : $(".total_vcted_scnd"+muncity_id).text(),
                "total_overall_mild" : $(".total_overall_mild"+muncity_id).text(),
                "total_overall_serious" : $(".total_overall_serious"+muncity_id).text(),
                "total_overall_deferred" : $(".total_overall_deferred"+muncity_id).text(),
                "total_overall_refused" : $(".total_overall_refused"+muncity_id).text(),
                "total_overall_wastage" : $(".total_overall_wastage"+muncity_id).text(),
                "total_overall_p_coverage" : $(".total_overall_p_coverage"+muncity_id).text(),
                "total_overall_c_rate" : $(".total_overall_c_rate"+muncity_id).text(),
                "total_overall_r_unvcted" : $(".total_overall_r_unvcted"+muncity_id).text()


            };
            console.log(json);
            $("#vaccine_modal_municipality").modal('show');
            $(".vaccinated_content_municipality").html(loading);
            $("a").css("background-color","");
            data.css("background-color","yellow");
            var url = "<?php echo asset('vaccine/vaccinated/municipality/content'); ?>";
            $.post(url,json,function(data){
                setTimeout(function(){
                    $(".vaccinated_content_municipality").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }

        function MunicipalityBody(province_id,muncity_id){
            var json;
            if(muncity_id == 'empty'){
                json = {
                    "province_id" : province_id,
                    "_token" : "<?php echo csrf_token()?>"
                };
            } else {
                json = {
                    "province_id" : province_id,
                    "muncity_id" : muncity_id ,
                    "_token" : "<?php echo csrf_token()?>"
                };
            }
            var url = "<?php echo asset('vaccine/eligible_pop') ?>";
            $.post(url,json,function(result){
                $(".facility_body").html(result);
            })
        }

        function vaccineAllocated(province_id,muncity_id){
            var url = "<?php echo asset('vaccine/vaccine_allocated_modal'); ?>";
            json = {
                "province_id" : province_id,
                "muncity_id" : muncity_id ,
                "_token" : "<?php echo csrf_token()?>"
            };
            $.post(url,json,function(result){
                $(".vaccine_allocated_modal").html(result);
            })
        }

        function MunicipalityDelete(muncity_id){
            $(".muncity_id").val(muncity_id);
        }

        @if(Session::get('municipality'))
        Lobibox.notify('success', {
            title: "",
            msg: "<?php echo Session::get("municipality_message"); ?>",
            size: 'mini',
            rounded: true
        });
        <?php
        Session::put("municipality",false);
        Session::put("municipality_message",false)
        ?>
        @endif

        @if(Session::get("vaccine_saved"))
        <?php
        Session::put('vaccine_saved',false);
        ?>
        Lobibox.notify('success', {
            size: 'mini',
            rounded: true,
            msg: 'Your vaccination record is successfully saved!'
        });
        @endif

            window.onload = function() {

            var sinovac_dashboard = <?php if(Session::get('sinovac_dashboard')) echo Session::get('sinovac_dashboard'); else echo 0; ?>;
            var astra_dashboard = <?php if(Session::get('astra_dashboard')) echo Session::get('astra_dashboard'); else echo 0; ?>;
            var pfizer_dashboard = <?php if(Session::get('pfizer_dashboard')) echo Session::get('pfizer_dashboard'); else echo 0; ?>;
            var sputnikv_dashboard = <?php if(Session::get('sputnikv_dashboard')) echo Session::get('sputnikv_dashboard'); else echo 0; ?>;
            var moderna_dashboard = <?php if(Session::get('moderna_dashboard')) echo Session::get('moderna_dashboard'); else echo 0; ?>;
            var johnson_dashboard = <?php if(Session::get('johnson_dashboard')) echo Session::get('johnson_dashboard'); else echo 0; ?>;

            var options1 = {
                title: {
                    text: "Total Vaccinated"
                },
                animationEnabled: true,
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: [
                            { label: "Sinovac",  y: sinovac_dashboard, color: "#dd4b39" },
                            { label: "AstraZeneca", y: astra_dashboard, color: "#f39c12"},
                            { label: "Pfizer",  y: pfizer_dashboard, color:"#00c0ef" },
                            { label: "Sputnik V", y: sputnikv_dashboard, color:"#00a65a" },
                            { label: "Moderna", y: moderna_dashboard, color:"#605ca8" },
                            { label: "Johnson", y: johnson_dashboard, color:"#1d94ff" }
                        ]

                    }
                ]

            };

            $("#chartContainer1").CanvasJSChart(options1);

            var a1_dashboard = <?php if(Session::get('a1_dashboard')) echo Session::get('a1_dashboard'); else echo 0; ?>;
            var a2_dashboard = <?php if(Session::get('a2_dashboard')) echo Session::get('a2_dashboard'); else echo 0; ?>;
            var a3_dashboard = <?php if(Session::get('a3_dashboard')) echo Session::get('a3_dashboard'); else echo 0; ?>;
            var a4_dashboard = <?php if(Session::get('a4_dashboard')) echo Session::get('a4_dashboard'); else echo 0; ?>;
            var a5_dashboard = <?php if(Session::get('a5_dashboard')) echo Session::get('a5_dashboard'); else echo 0; ?>;
            var b1_dashboard = <?php if(Session::get('b1_dashboard')) echo Session::get('b1_dashboard'); else echo 0; ?>;
            var b2_dashboard = <?php if(Session::get('b2_dashboard')) echo Session::get('b2_dashboard'); else echo 0; ?>;
            var b3_dashboard = <?php if(Session::get('b3_dashboard')) echo Session::get('b3_dashboard'); else echo 0; ?>;
            var b4_dashboard = <?php if(Session::get('b4_dashboard')) echo Session::get('b4_dashboard'); else echo 0; ?>;
            var options2 = {
                title: {
                    text: "Priority"
                },
                animationEnabled: true,
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: [
                            { label: "(A1)",  y: a1_dashboard },
                            { label: "(A2)",  y: a2_dashboard },
                            { label: "(A3)",  y: a3_dashboard },
                            { label: "(A4)",  y: a4_dashboard },
                            { label: "(A5)",  y: a5_dashboard },
                            { label: "(B1)",  y: b1_dashboard },
                            { label: "(B2)",  y: b2_dashboard },
                            { label: "(B3)",  y: b3_dashboard },
                            { label: "(B4)",  y: b4_dashboard }
                        ]
                    }
                ]
            };
            $("#chartContainer2").CanvasJSChart(options2);

            var percent_coverage_firstdose = <?php if(Session::get('percent_coverage_firstdose')) echo str_replace(',','',Session::get('percent_coverage_firstdose')); else echo 0; ?>;
            var percent_coverage_seconddose = <?php if(Session::get('percent_coverage_seconddose')) echo str_replace(',','',Session::get('percent_coverage_seconddose')); else echo 0; ?>;
            var options3 = {
                title: {
                    text: "Percentage Coverage",
                    fontSize: 23,
                },
                animationEnabled: true,
                data: [{
                    type: "doughnut",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y}%)",
                    yValueFormatString:"##0.00\"\"",
                    dataPoints: [
                        { label: "First Dose", y: percent_coverage_firstdose, color:"#00a65a" },
                        { label: "Second Dose", y: percent_coverage_seconddose, color:"#f39c12" },

                    ]
                }]
            };
            $("#chartPercentCoverage").CanvasJSChart(options3);

            var consumption_rate_firstdose = <?php if(Session::get('consumption_rate_firstdose')) echo str_replace(',','',Session::get('consumption_rate_firstdose')); else echo 0; ?>;
            var consumption_rate_secondddose = <?php if(Session::get('consumption_rate_secondddose')) echo str_replace(',','',Session::get('consumption_rate_seconddose')); else echo 0; ?>;
            var options4 = {
                title: {
                    text: "Consumption Rate",
                    fontSize: 23,
                },
                animationEnabled: true,
                data: [{
                    type: "doughnut",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y}%)",
                    yValueFormatString:"##0.00\"\"",
                    dataPoints: [
                        { label: "First Dose", y: consumption_rate_firstdose, color:"#00a65a" },
                        { label: "Second Dose", y: consumption_rate_secondddose, color:"#f39c12" },

                    ]
                }]
            };
            $("#chartConsumptionRate").CanvasJSChart(options4);



        };

    </script>

@endsection



