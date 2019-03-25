<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('css')
    <style type="text/css">
        .bs-wizard {margin-top: 40px;}

        /*Form Wizard*/
        .bs-wizard {border-bottom: solid 1px #4caf50; padding: 0 0 10px 0;}
        .bs-wizard > .bs-wizard-step {padding: 0; position: relative;}
        .bs-wizard > .bs-wizard-step + .bs-wizard-step {}
        .bs-wizard > .bs-wizard-step .bs-wizard-stepnum {color: #595959; font-size: 16px; margin-bottom: 5px;}
        .bs-wizard > .bs-wizard-step .bs-wizard-info {color: #999; font-size: 14px;}
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot {position: absolute; width: 30px; height: 30px; display: block; background: #4caf50; top: 45px; left: 50%; margin-top: -15px; margin-left: -15px; border-radius: 50%;}
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {content: ' '; width: 14px; height: 14px; background: #4caf50; border-radius: 50px; position: absolute; top: 8px; left: 8px; }
        .bs-wizard > .bs-wizard-step > .progress {position: relative; border-radius: 0px; height: 8px; box-shadow: none; margin: 20px 0;}
        .bs-wizard > .bs-wizard-step > .progress > .progress-bar {width:0px; box-shadow: none; background: #4caf50;}
        .bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {width:100%;}
        .bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {width:50%;}
        .bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {width:0%;}
        .bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {width: 100%;}
        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {background-color: #f5f5f5;}
        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {opacity: 0;}
        .bs-wizard > .bs-wizard-step:first-child  > .progress {left: 50%; width: 50%;}
        .bs-wizard > .bs-wizard-step:last-child  > .progress {width: 50%;}
        .bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot{ pointer-events: none; }
        /*END Form Wizard*/
        @media only screen and (max-width: 440px) {
            .bs-wizard-stepnum { visibility: hidden; }
        }
    </style>
@endsection
@section('content')

    <div class="col-md-9">
        <div class="row bs-wizard" style="border-bottom:0;">

            <div class="col-xs-2 bs-wizard-step complete">
                <div class="text-center bs-wizard-stepnum">Referred</div>
                <div class="progress"><div class="progress-bar"></div></div>
                <a href="javascript();" class="bs-wizard-dot" title="Referred"></a>
            </div>

            <div class="col-xs-2 bs-wizard-step complete"><!-- complete -->
                <div class="text-center bs-wizard-stepnum">Seen</div>
                <div class="progress"><div class="progress-bar"></div></div>
                <a href="#" class="bs-wizard-dot"></a>
            </div>

            <div class="col-xs-2 bs-wizard-step active"><!-- complete -->
                <div class="text-center bs-wizard-stepnum">Accepted</div>
                <div class="progress"><div class="progress-bar"></div></div>
                <a href="#" class="bs-wizard-dot"></a>
            </div>

            <div class="col-xs-2 bs-wizard-step disabled"><!-- active -->
                <div class="text-center bs-wizard-stepnum">Arrived</div>
                <div class="progress"><div class="progress-bar"></div></div>
                <a href="#" class="bs-wizard-dot"></a>
            </div>
            <div class="col-xs-2 bs-wizard-step disabled"><!-- active -->
                <div class="text-center bs-wizard-stepnum">Admitted</div>
                <div class="progress"><div class="progress-bar"></div></div>
                <a href="#" class="bs-wizard-dot"></a>
            </div>
            <div class="col-xs-2 bs-wizard-step disabled"><!-- active -->
                <div class="text-center bs-wizard-stepnum">Discharged</div>
                <div class="progress"><div class="progress-bar"></div></div>
                <a href="#" class="bs-wizard-dot"></a>
            </div>
        </div>

    </div>
    <div class="col-md-3">
        @include('sidebar.quick')
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function(){
        $('.toggle').toggle();

        $("a[href='#toggle']").on('click',function () {
            var id = $(this).data('id');
            $('.toggle'+id).toggle();
            var txt = ($(this).html() =='View More') ? 'View Less': 'View More';
            $(this).html(txt);
        });
    });
</script>
@endsection

