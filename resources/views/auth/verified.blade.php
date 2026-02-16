@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Email Verified</div>
                <div class="card-body text-center">

                    <h4>✅ Your email has been verified!</h4>
                    <p>Your account is now active. You can now access the telemedicine portal.</p>

                    <a href="{{ url('/telemedicine/dashboard') }}" class="btn btn-success">
                        Go to Dashboard
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection