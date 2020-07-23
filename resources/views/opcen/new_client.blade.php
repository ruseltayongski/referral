@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-body">
                <table class="table table-hover table-bordered">
                    <tr>
                        <th>Call Classification
                            <button class="btn-xs btn-primary" onclick="newCall()">New Call</button>
                            <button class="btn-xs btn-warning" onclick="repeatCall()">Repeat Call</button>
                        </th>
                        <th class="pull-right">Time Started: <input type="text" id="time_started" readonly></th>
                    </tr>
                </table>
                <div class="call_classification">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        var d = new Date();
        console.log(d.toUTCString());

        var path_gif = "<?php echo asset('resources/img/loading.gif'); ?>";
        var loading = '<center><img src="'+path_gif+'" alt=""></center>';
        function newCall(){
            $(".call_classification").html(loading);
            var url = "<?php echo asset('opcen/new_call'); ?>";
            $.get(url,function(data){
                setTimeout(function(){
                    $(".call_classification").html(data);
                    var d = new Date();
                    $("#time_started").val(d.toLocaleString());
                },700);
            });
        }

        function repeatCall(){
            $(".call_classification").html(loading);
            var url = "<?php echo asset('opcen/repeat_call'); ?>";
            $.get(url,function(data){
                setTimeout(function(){
                    $(".call_classification").html(data);
                    var d = new Date();
                    $("#time_started").val(d.toLocaleString());
                },700);
            });
        }

        function reasonCalling($reason){
            $(".reason_calling").html(loading);
            var url = "<?php echo asset('opcen/reason_calling').'/'; ?>"+$reason;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".reason_calling").html(data);
                },700);
            });
        }

        function actionComplete(){
            Lobibox.notify('success', {
                title: 'Action Complete',
                msg: "Sending SMS!"
            });
        }

        function actionInComplete(){
            Lobibox.notify('error', {
                title: 'Action In-Complete',
                msg: "Sending SMS!"
            });
        }

        function endTransaction(){
            var d = new Date();
            $("#time_ended").val(d.toLocaleString());
        }
    </script>
@endsection

