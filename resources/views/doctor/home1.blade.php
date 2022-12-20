<?php
    $error = \Illuminate\Support\Facades\Input::get('error');
    $user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <script src="{{ asset('public/js/app_doctor.js?version=').date('YmdHis') }}" defer></script>

    @include('script.chart')

    <div id="app_doctor">
        <input type="hidden" value="{{ asset('resources/img/loading.gif') }}" id="loadingGif">
        <doctor-app :date_start="{{ json_encode($date_start) }}" :date_end="{{ json_encode($date_end) }}" :user="{{ $user }}" :error="{{ json_encode($error) }}"></doctor-app>
    </div>

@endsection

@section('js')

@endsection

