@extends('layouts.app')
@section('content')
    <!-- VUE Scripts -->
    <script src="{{ asset('public/js/app_reco.js?version=').date('YmdHis') }}" defer></script>
    <input type="hidden" id="doh_logo" value="{{ asset('resources/img/doh.png') }}">
    <div id="app_reco">
        <reco-app :user="{{ Session::get('auth') }}"></reco-app>
    </div>
@endsection

@section('js')

@endsection