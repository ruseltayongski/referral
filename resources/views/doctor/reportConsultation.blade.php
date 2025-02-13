@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="box box-success">
    <h1 class="text-center">Telemedicine Consolidated Report</h1>
    <hr>

    <!-- Overall Statistics -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Overall Telemedicine Statistics</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <h4>Total Consultations</h4>
                    <p><strong>{{$totalConsult}} consultations</strong></p>
                </div>
                <div class="col-md-3">
                    <h4>Total Departments</h4>
                    <p><strong>{{ $countDepartment }}</strong></p>
                </div>
                <div class="col-md-3">
                    <h4>Total Patients</h4>
                    <p><strong>{{ $numberPatient }} patients</strong></p>
                </div>
                <div class="col-md-3">
                    <h4>Average Consultation Duration</h4>
                    <p><strong>{{$totalConsultationMinutes}}</strong></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Consultation by Doctor -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Consultations by Department</h3>
        </div>
        <div class="panel-body">
            <canvas id="doctorChart" width="400" height="200"></canvas>
        </div>
    </div>
    <!-- Patient Demographics -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Patient Demographics</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Age Distribution</h4>
                    <canvas id="ageDistributionChart" width="400" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <h4>Gender Distribution</h4>
                    <canvas id="genderDistributionChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnosis Statistics -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Diagnosis Statistics</h3>
        </div>
        <div class="panel-body">
            <canvas id="diagnosisChart" style="width: 529px; height: 529px; margin: 0 auto;"></canvas>
        </div>
    </div>

    <!-- Treatment Adherence -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Treatment Adherence</h3>
        </div>
        <div class="panel-body">
            <canvas id="adherenceChart" style="width: 529px; height: 529px; margin: 0 auto;"></canvas>
        </div>
    </div>

    <div class="text-center">
        <button class="btn btn-primary">Download Report</button>
    </div>
</div>
@endsection

@section('js')

<script>
        // Example data for charts
        let consultPerDepartment = @json($totalperDepartment);
        
        let PerDepartment = consultPerDepartment.map(item => item.description);
        let PerConsultation = consultPerDepartment.map(item => item.total_consultations);

        var doctorData = {
            labels: PerDepartment,
            datasets: [{
                label: 'Consultations per Doctor',
                data: PerConsultation,
                backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FF9333'],
                borderColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FF9333'],
                borderWidth: 1
            }]
        };

        var ageDistributionData = {
            labels: ['<18', '18-30', '31-45', '46-60', '>60'],
            datasets: [{
                label: 'Age Distribution',
                data: [50, 200, 350, 300, 150],
                backgroundColor: ['#FF9933', '#33FF99', '#3399FF', '#FF3399', '#FFFF33'],
                borderColor: ['#FF9933', '#33FF99', '#3399FF', '#FF3399', '#FFFF33'],
                borderWidth: 1
            }]
        };

        var genderDistributionData = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender Distribution',
                data: [600, 500],
                backgroundColor: ['#66B3FF', '#FF66B3'],
                borderColor: ['#66B3FF', '#FF66B3'],
                borderWidth: 1
            }]
        };

        var diagnosisData = {
            labels: ['Hypertension', 'Diabetes', 'Respiratory', 'Cancer', 'Other'],
            datasets: [{
                label: 'Diagnosis Statistics',
                data: [300, 450, 200, 100, 200],
                backgroundColor: ['#FF6666', '#66FF66', '#6699FF', '#FF66FF', '#FFCC33'],
                borderColor: ['#FF6666', '#66FF66', '#6699FF', '#FF66FF', '#FFCC33'],
                borderWidth: 1
            }]
        };

        var adherenceData = {
            labels: ['Followed Treatment', 'Did Not Follow Treatment'],
            datasets: [{
                label: 'Treatment Adherence',
                data: [850, 150],
                backgroundColor: ['#66FF66', '#FF6666'],
                borderColor: ['#66FF66', '#FF6666'],
                borderWidth: 1
            }]
        };

        // Create the charts
        new Chart(document.getElementById('doctorChart'), {
            type: 'bar',
            data: doctorData,
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('ageDistributionChart'), {
            type: 'pie',
            data: ageDistributionData,
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('genderDistributionChart'), {
            type: 'pie',
            data: genderDistributionData,
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('diagnosisChart'), {
            type: 'pie',
            data: diagnosisData,
            options: {
                responsive: true
            }
        });

        new Chart(document.getElementById('adherenceChart'), {
            type: 'pie',
            data: adherenceData,
            options: {
                responsive: true
            }
        });
    </script>
@endsection