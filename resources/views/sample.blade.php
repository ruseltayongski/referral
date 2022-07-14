<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h1>Hello World!</h1>
        </div>
        <div class="box-body">
            <h3>This is body content!</h3>
        </div>
    </div>
@endsection

@section('js')

@endsection

