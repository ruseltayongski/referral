<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <style>
        .facility {
            color: #ff8456;
        }
    </style>
    <div class="col-md-12">
        <div class="jim-content">
            <h3 class="page-header">{{ $title }}
            </h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- The time line -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="bg-gray">
                            <tr>
                                <th>Facility From</th>
                                <th>Patient Code</th>
                                <th>Patient Name</th>
                                <th>Date Accepted</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><span class="facility">Badian District Hospital</span></td>
                                <td>180401-001-160446</td>
                                <td>Anna Baclayon</td>
                                <td>Monday March 19, 2018 09:40 AM</td>
                                <td>
                                    <button class="btn btn-xs btn-info">Return</button>
                                    <button class="btn btn-xs btn-warning">Close</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
@endsection
@include('script.firebase')
@section('js')
    <script>
        var dbRef = firebase.database();
        var connRef = dbRef.ref('Referral');
        var myfacility = "{{ $user->facility_id }}";
    </script>
@endsection

