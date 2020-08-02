@extends('layouts.app')

@section('content')
    <style>
        .color1 {
            color: #ff8456;
            font-size: 15pt;
        }
    </style>
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <form action="" method="GET">
                    <div class="input-group input-group-md" style="width: 50%">
                        <input type="text" class="form-control" placeholder="Reference Number or Name" name="search" value="{{ $search }}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                            <button type="button" class="btn btn-warning" onclick="refreshPage()"><i class="fa fa-eye"></i> View All</button>
                            <button type="button" class="btn btn-primary" onclick="newCall('new_call')"><i class="fa fa-phone-square"></i> New Call</button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <div class="call_classification table-responsive">
                    @if(count($client)>0)
                        <table class="table table-striped">
                            <thead class="bg-gray">
                            <tr>
                                <th>Reference Number</th>
                                <th>Name</th>
                                <th>Call Classification</th>
                                <th>Time Started</th>
                                <th>Time Ended</th>
                                <th>Time Duration</th>
                                <th>Option</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($client as $row)
                                    <tr>
                                        <td><span class="color1" >{{ $row->reference_number }}</span></td>
                                        <td><span class="text-green">{{ $row->name }}</span></td>
                                        <td>@if($row->call_classification == 'new_call')<span class="text-blue">New Call</span>@else<span class="text-red">Repeat Call</span>@endif</td>
                                        <td>{{ $row->time_started }}</td>
                                        <td>{{ $row->time_ended }}</td>
                                        <td>{{ $row->time_duration }}</td>
                                        <td><button type="button" class="btn-xs btn-info" onclick="repeatCall('<?php echo $row->id; ?>','repeat_call')"><i class="fa fa-phone-square"></i> Repeat Call</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination">
                            {{ $client->links() }}
                        </div>
                    @else
                        <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No data found!
                        </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="loading_modal">
        <center style="margin-top: 10%;">
            <img src="{{ asset('resources/img/loading.gif') }}" alt="">
        </center>
    </div><!-- /.modal -->

@endsection

@section('js')
    <script>

        @if(Session::get('opcen'))
            Lobibox.notify('success', {
                title: "",
                msg: "New client saved!",
                size: 'mini',
                rounded: true
            });
            <?php Session::put("opcen",false); ?>
        @endif

        function newCall($call_classification){
            $(".call_classification").html(loading);
            var url = "<?php echo asset('opcen/new_call'); ?>";
            $.get(url,function(data){
                setTimeout(function(){
                    $(".call_classification").html(data);

                    $(".select2").select2({ width: '100%' });

                    $("#call_classification_text").html("New Call");
                    $("#call_classification").val($call_classification);

                    var d = new Date();
                    $("#time_started").val(d.toLocaleString());
                    $("#time_started_text").html(d.toLocaleString());
                },700);
            });
        }

        function repeatCall($client_id,$call_classification){
            $(".call_classification").html(loading);
            var url = "<?php echo asset('opcen/repeat_call').'/'; ?>"+$client_id;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".call_classification").html(data);
                    $(".select2").select2({ width: '100%' });

                    $("#call_classification_text").html("Repeat Call");
                    $("#call_classification").val($call_classification);

                    var d = new Date();
                    $("#time_started").val(d.toLocaleString());
                    $("#time_started_text").html(d.toLocaleString());
                },700);
            });
        }

        function reasonCalling($reason){
            $("#reason_calling").val($reason);
            $(".reason_calling").html(loading);
            var url = "<?php echo asset('opcen/reason_calling').'/'; ?>"+$reason;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".reason_calling").html(data);
                },700);
            });
        }

        function transactionComplete(){
            $(".transaction_status").html(loading);
            var url = "<?php echo asset('opcen/transaction/complete'); ?>";
            $.get(url,function(data){
                setTimeout(function(){
                    $(".transaction_status").html(data);
                },700);
            });
        }

        function transactionInComplete(){
            $(".transaction_status").html(loading);
            var url = "<?php echo asset('opcen/transaction/incomplete'); ?>";
            $.get(url,function(data){
                setTimeout(function(){
                    $(".transaction_status").html(data);
                },700);
            });
        }

        function actionInComplete(){
            Lobibox.notify('error', {
                title: 'Action In-Complete',
                msg: "Sending SMS!"
            });
        }

        function endTransaction($element){
            var d = new Date();
            $("#time_ended").val(d.toLocaleString());

            $($element).prop('disabled', true);
            $('.loading').show();
            $("#form_submit").submit();
        }

        function onChangeProvince($province_id){
            if($province_id){
                var url = "{{ url('opcen/onchange/province') }}";
                $.ajax({
                    url: url+'/'+$province_id,
                    type: 'GET',
                    success: function(data){
                        $("#municipality").select2("val", "");
                        $('#municipality').empty()
                            .append($('<option>', {
                                value: '',
                                text : 'Select Option'
                            }));
                        jQuery.each(data, function(i,val){
                            $('#municipality').append($('<option>', {
                                value: val.id,
                                text : val.description
                            }));
                        });
                    },
                    error: function(){
                        $('#serverModal').modal();
                    }
                });
            } else {
                $("#municipality").select2("val", "");
                $('#municipality').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Select Option'
                    }));
            }
        }

        function onChangeMunicipality($municipality_id){
            if($municipality_id){
                var url = "{{ url('opcen/onchange/municipality') }}";
                $.ajax({
                    url: url+'/'+$municipality_id,
                    type: 'GET',
                    success: function(data){
                        $("#barangay").select2("val", "");
                        $('#barangay').empty()
                            .append($('<option>', {
                                value: '',
                                text : 'Select Option'
                            }));
                        jQuery.each(data, function(i,val){
                            $('#barangay').append($('<option>', {
                                value: val.id,
                                text : val.description
                            }));
                        });
                    },
                    error: function(){
                        $('#serverModal').modal();
                    }
                });
            } else {
                $("#barangay").select2("val", "");
                $('#barangay').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Select Option'
                    }));
            }
        }

    </script>
@endsection

