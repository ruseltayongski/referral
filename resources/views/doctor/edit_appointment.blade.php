@extends('layouts.app')

@section('content')
    <!-- Edit appointment form -->
   {{-- <form action="{{ route('update-appointment', ['id' => $appointment->id]) }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <!-- Include form fields for editing appointment details -->
        <button type="submit">Update</button>
    </form>--}}
    <form action="{{ route('update-appointment', ['id' => $appointment->id]) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <fieldset>
            <legend><i class="fa fa-edit"></i> Edit Appointment</legend>
        </fieldset>
        <div class="form-group">
            <label for="appointed_date">Appointed Date:</label>
            <input type="date" class="form-control" name="appointed_date" required>

            <label for="appointed_time">Appointed Time:</label>
            <input type="time" class="form-control" name="appointed_time" required>

            <label for="created_by">Created By:</label>
            <input type="number" class="form-control" name="created_by" required>

            <label for="facility_id">Facility:</label>
            <input type="number" class="form-control" name="facility_id" required>

            <label for="department_id">Department:</label>
            <input type="number" class="form-control" name="department_id" required>

            <label for="appointed_by">Appointed By:</label>
            <input type="number" class="form-control" name="appointed_by" required>

            <label for="code">Code:</label>
            <input type="text" class="form-control" name="code" required>

            <label for="status">Status:</label>
            <input type="text" class="form-control" name="status" required>

            <label for="slot">Slot:</label>
            <input type="number" class="form-control" name="slot" required>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" onclick="resetSignatureField()"><i class="fa fa-times"></i> Cancel</button>
            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Update</button>
        </div>
    </form>
@endsection
