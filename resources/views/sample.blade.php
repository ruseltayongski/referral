<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal" data-target="#feedbackModal" data-code="220629-163-1000271631687" onclick="viewReco($(this))">
        <i class="fa fa-comments"></i> ReCo
        <span class="badge bg-blue" id="reco_count220629-163-1000271631687">0</span>
    </button>
    <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal" data-target="#feedbackModal" data-code="220629-163-1000271631687" onclick="viewReco($(this))">
        <i class="fa fa-comments"></i> ReCo
        <span class="badge bg-blue" id="reco_count220629-163-1000271631687">0</span>
    </button>
@endsection

@section('js')
    <script>
        tinymce.init({
            selector: ".mytextarea1",
            plugins: "emoticons autoresize",
            toolbar: "emoticons",
            toolbar_location: "bottom",
            menubar: false,
            statusbar: false
        });
    </script>
@endsection

