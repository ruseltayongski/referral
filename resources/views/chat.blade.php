@extends('layouts.app')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    <div class="row">
        <div class="box box-success">
            <div class="box-body" id="app">
                <chat-app :user="{{ Session::get('auth') }}"></chat-app>
            </div>
        </div>
    </div>


@endsection

@section('js')
    <!-- Scripts -->
    <script src="{{ asset('public/js/app.js') }}" defer></script>
@endsection

