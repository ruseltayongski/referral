<?php
    $user = Session::get('auth');
?>

@extends('layouts.app')

@section('content')
    <div class="col-md-3">
        @include('sidebar.'.$sidebar)
    </div>
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">Patient List</h3>
            @if(count($data))
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Barangay</th>
                            <th style="width:18%;">Action</th>
                        </tr>
                        @foreach($data as $row)
                        <tr>
                            <td>
                                {{ $row->lname }}, {{ $row->fname }} {{ $row->mname }}<br />
                                <small class="text-info">
                                    @if($source=='tsekap')
                                        Family ID: {{ $row->familyID }}
                                    @else

                                    @endif
                                </small>
                            </td>
                            <td>{{ $row->sex }}</td>
                            <td>
                                <?php $age = \App\Http\Controllers\ParamCtrl::getAge($row->dob);?>
                                {{ $age }}
                                <br />
                                <small class="text-muted">{{ date('M d, Y',strtotime($row->dob)) }}</small>
                            </td>
                            <td>
                                <?php
                                    $brgy_id = ($source=='tsekap') ? $row->barangay_id: $row->brgy;
                                    $city_id = ($source=='tsekap') ? $row->muncity_id: $row->muncity;
                                    $phic_id = ($source=='tsekap') ? $row->phicID: $row->phic_id;
                                    $phic_id_stat = 0;
                                    if($phic_id){
                                        $phic_id_stat = 1;
                                    }
                                ?>
                                @if($brgy_id!=0)
                                {{ $brgy = \App\Barangay::find($brgy_id)->description }}<br />
                                <small class="text-success">{{ $city = \App\Muncity::find($city_id)->description }}</small>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($row->sex=='Female' && ($age > 14 && $age < 50))
                                    <a href="#pregnantModal"
                                       data-patient_id = "{{ $row->id }}"
                                       data-name = "{{ $row->lname }}, {{ $row->fname }} {{ $row->mname }}"
                                       data-age = "{{ $age }}"
                                       data-sex = "{{ $row->sex }}"
                                       data-address = "{{ $brgy }}, {{ $city }}"
                                       data-phic = "{{ $phic_id }}"
                                       data-phic_stat = "{{ $phic_id_stat }}"
                                       data-toggle="modal"
                                       class="btn btn-primary btn-xs profile_info">
                                        <i class="fa fa-stethoscope"></i>
                                        Refer
                                    </a>
                                @else
                                    <a href="#normalFormModal"
                                       data-patient_id = "{{ $row->id }}"
                                       data-name = "{{ $row->lname }}, {{ $row->fname }} {{ $row->mname }}"
                                       data-age = "{{ $age }}"
                                       data-sex = "{{ $row->sex }}"
                                       data-address = "{{ $brgy }}, {{ $city }}"
                                       data-phic = "{{ $phic_id }}"
                                       data-phic_stat = "{{ $phic_id_stat }}"
                                       data-backdrop="static"
                                       data-toggle="modal"
                                       class="btn btn-primary btn-xs profile_info">
                                        <i class="fa fa-stethoscope"></i>
                                        Refer
                                    </a>
                                @endif
                                <a href="#" class="btn btn-warning btn-xs"><i class="fa fa-line-chart"></i> History </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <ul class="pagination pagination-sm no-margin pull-right">
                        {{ $data->links() }}
                </ul>

            @else
                <div class="alert alert-warning">
                <span class="text-warning">
                    <i class="fa fa-warning"></i> Please search for patients!
                </span>
                </div>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
    @include('modal.pregnantModal')
    @include('modal.normal_form_editable')
    @include('modal.pregnant_form_editable')
@endsection

@section('js')
@include('script.filterMuncity')
@include('script.firebase')
@include('script.datetime')
<script>
    var referred_facility = 0;
    var referring_facility = "{{ $user->facility_id }}";
    var referred_name = '';
    var referring_name = $(".referring_name").val();
    var patient_form_id = 0;
    var name = '';
    var age, sex, address,form_type,reason;
    $('.form-submit').on('submit',function(){
        $('.loading').show();
        $('.btn-submit').attr('disabled',true);
    });

    $('.select_facility').on('change',function(){
        var id = $(this).val();
        referred_name = $(this).find(':selected').data('name');
        referred_facility = id;
        var url = "{{ url('location/facility/') }}";

        $.ajax({
            url: url+'/'+id,
            type: 'GET',
            success: function(data){
                $('.facility_address').html(data);
            },
            error: function(){
                $('#serverModal').modal();
            }
        });

        var list = "{{ url('list/doctor') }}";
        $.ajax({
            url: list+'/'+id,
            type: 'GET',
            success: function(data){
                $('.referred_md').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Any...'
                    }));
                jQuery.each(data, function(i,val){
                    $('.referred_md').append($('<option>', {
                        value: val.id,
                        text : 'Dr. '+val.fname+' '+val.mname+' '+val.lname+' - '+val.contact
                    }));

                });
            },
            error:function(){
                $('#serverModal').modal();
            }
        });
    });

    $('.profile_info').on('click',function(){
        name = $(this).data('name');
        age = $(this).data('age');
        sex = $(this).data('sex');
        address = $(this).data('address');
        var phic_id = $(this).data('phic');
        var patient_id = $(this).data('patient_id');
        $('.patient_name').html(name);
        $('.patient_age').html(age);
        $('.patient_sex').val(sex);
        $('.patient_address').html(address);
        $('.phic_id').val(phic_id);
        $('.patient_id').val(patient_id);
    });
</script>
<script>
    $('.normal_form').on('submit',function(e){
        e.preventDefault();
        reason = $('.reason_referral').val();
        form_type = '#normalFormModal';
        $(this).ajaxSubmit({
            url: "{{ url('doctor/patient/refer/normal') }}",
            type: 'POST',
            success: function(data){
                sendNormalData(data);
            },
            error: function(){
                $('#serverModal').modal();
            }
        });

    });

    $('.pregnant_form').on('submit',function(e){
        e.preventDefault();
        form_type = '#pregnantModal';
        sex = 'Female';
        reason = $('.woman_information_given').val();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/patient/refer/pregnant') }}",
            type: 'POST',
            success: function(data){
                //console.log(data);
                sendNormalData(data);
            },
            error: function(){
                $('#serverModal').modal();
            }
        });

    });

    function sendNormalData(data)
    {
        var dbRef = firebase.database();
        var connRef = dbRef.ref('Referral');
        connRef.child(referred_facility).push({
            referring_name: referring_name,
            reason: reason,
            ref_no: data.ref_no,
            name: name,
            age: age,
            sex: sex,
            date: "{{ date('M d, Y h:i:s') }}",
            form_type: form_type,
            form_id: data.id
        });

        connRef.on('child_added',function(data){
            setTimeout(function(){
                //connRef.child(data.key).remove();
                window.location.reload(false);
            },500);
        });
    }
</script>
@endsection

