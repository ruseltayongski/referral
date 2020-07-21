@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-body">
                Call Classification
                <ul>
                    <li>New Call</li>
                    <li>Repeat Call</li>
                </ul>
                Client Code
                Name:
                Age:
                Gender:
                Current Address:
                Active contact number:
                Relationship to patient (Patient, Family & Others)
                Reason for calling:
                <ul>
                    <li>Inquiry</li>
                    <ul>
                        <li>Notes</li>
                    </ul>
                    <li>Referral</li>
                    <ul>
                        <li>Patient Data(Name,Age,Gender)</li>
                        <li>Chief Complains</li>
                    </ul>
                    <li>Others</li>
                    <ul>
                        <li>Notes</li>
                    </ul>
                </ul>
                Action taken
                Transaction
                <ul>
                    <li>Complete</li>
                    <li>Incomplete</li>
                </ul>
                End of Transaction
            </div>
        </div>
    </div>

@endsection

@section('js')

@endsection

