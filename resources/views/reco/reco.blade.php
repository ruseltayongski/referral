@extends('layouts.app')

@section('content')
    <!-- VUE Scripts -->
    <script src="{{ asset('public/js/app_reco.js?version=').date('YmdHis') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.0.3/tinymce.min.js"></script>
    <input type="hidden" id="doh_logo" value="{{ asset('resources/img/doh.png') }}">
    <div id="app_reco">
        <reco-app :user="{{ Session::get('auth') }}"></reco-app>
    </div>
@endsection

@section('js')
    <script>
        tinymce.init({
            selector: "#mytextarea",
            plugins: "emoticons autoresize",
            toolbar: "emoticons",
            toolbar_location: "bottom",
            menubar: false,
            statusbar: false
        });
    </script>
@endsection