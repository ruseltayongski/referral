@extends('layouts.app')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<div class="box box-success">
    <h1 class="text-center">Telemedicine Consolidated Report</h1>
    <hr>

        <!-- Date Filter Form -->

    <form method="GET" action="{{ url('/count/Consultation') }}" class="form-inline text-left" style="margin-bottom: 20px; align-items: center; gap: 10px; flex-wrap: wrap;">
        <label for="from_date">From:</label>
        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-control filter-input" required>
        <label for="to_date" style="margin-left:10px;">To:</label>
        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-control filter-input" required>
        <select id="consultationType" class="form-control filter-input" style="margin-left:10px; min-width: 140px;">
            <option value="outgoing">Outgoing</option>
            <option value="incoming">Incoming</option>
        </select>
    </form>
   
    <!-- Overall Statistics -->
    <div class="panel panel-default" style="box-shadow: 0 4px 18px rgba(44,62,80,0.10); border-radius: 18px; border: none; margin-bottom: 32px;">
        <div class="panel-heading" style="background: linear-gradient(90deg, #1cc88a 0%, #36b9cc 100%); color: #fff; border-radius: 18px 18px 0 0; padding: 24px 32px; box-shadow: 0 2px 8px rgba(78,115,223,0.10);">
            <h3 class="panel-title" style="font-size: 1.5em; font-weight: bold; letter-spacing: 1px; margin: 0; display: flex; align-items: center;">
                <i class="fa fa-pie-chart" style="margin-right: 12px; font-size: 1.2em;"></i>
                Overall Telemedicine Statistics
            </h3>
        </div>
        <div class="panel-body" style="background: #f8fafc; border-radius: 0 0 18px 18px; padding: 32px 24px;">
            <div class="row text-center" id="overallStatsOutgoing">
                <div class="col-md-3">
                    <div style="background: #4e73df; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(78,115,223,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-comments"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{$totalConsult}}</div>
                        <div style="font-size: 1.1em;">Consultations</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #36b9cc; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(54,185,204,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-building"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{ $countDepartment }}</div>
                        <div style="font-size: 1.1em;">Departments</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #1cc88a; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(28,200,138,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-user"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{ $numberPatient }}</div>
                        <div style="font-size: 1.1em;">Patients</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #f6c23e; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(246,194,62,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-clock-o"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{$averageConsultationDuration}}</div>
                        <div style="font-size: 1.1em;">Avg. Consultation Duration</div>
                    </div>
                </div>
            </div>
            <div class="row text-center" id="overallStatsIncoming" style="display:none;">
                <div class="col-md-3">
                    <div style="background: #4e73df; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(78,115,223,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-comments"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{$totalConsultIncoming}}</div>
                        <div style="font-size: 1.1em;">Consultations</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #36b9cc; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(54,185,204,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-building"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{ $countDepartmentIncoming }}</div>
                        <div style="font-size: 1.1em;">Departments</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #1cc88a; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(28,200,138,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-user"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{ $numberPatientIncoming }}</div>
                        <div style="font-size: 1.1em;">Patients</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div style="background: #f6c23e; color: #fff; border-radius: 12px; padding: 24px 10px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(246,194,62,0.10);">
                        <div style="font-size: 2.2em; font-weight: bold; margin-bottom: 6px;"><i class="fa fa-clock-o"></i></div>
                        <div style="font-size: 1.5em; font-weight: bold;">{{$formattedAvgDurationIncoming}}</div>
                        <div style="font-size: 1.1em;">Avg. Consultation Duration</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" style="background: #60ac94; color: #fff; border-radius: 6px 6px 0 0; box-shadow: 0 2px 8px rgba(78,115,223,0.08); padding: 18px 24px;">
            <h3 class="panel-title" style="font-size: 1.5em; font-weight: bold; letter-spacing: 1px; margin: 0; display: flex; align-items: center;">
                <i class="fa fa-bar-chart" style="margin-right: 12px;"></i>
                Consultations Monitoring &amp; By Department
            </h3>
        </div>
        <!-- Outgoing/Incoming label removed -->
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <canvas id="consultationTrackingData" width="400" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #4e73df; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-hospital-o" style="margin-right: 8px; color: #36b9cc;"></i>Consultations by Department
                        </h4>
                        <canvas id="doctorChart" width="420" height="240"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Outgoing/Incoming label removed -->
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <canvas id="consultationTrackingDataIncoming" width="400" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #4e73df; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-hospital-o" style="margin-right: 8px; color: #36b9cc;"></i>Consultations by Department
                        </h4>
                        <canvas id="doctorChartIncoming" width="420" height="240"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Patient Demographics & Diagnosis Statistics -->
    <div class="panel panel-default">
        <div class="panel-heading" style="background: linear-gradient(90deg, #1cc88a 0%, #36b9cc 100%); color: #fff; border-radius: 6px 6px 0 0; box-shadow: 0 2px 8px rgba(28,200,138,0.08); padding: 18px 24px;">
            <h3 class="panel-title" style="font-size: 1.5em; font-weight: bold; letter-spacing: 1px; margin: 0; display: flex; align-items: center;">
                <i class="fa fa-users" style="margin-right: 12px;"></i>
                Patient Demographics &amp; Diagnosis Statistics
            </h3>
        </div>
        <div class="panel-body">
            <!-- Outgoing/Incoming label removed -->
            <div class="row">
                <div class="col-md-6">
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px; margin-bottom: 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #1cc88a; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-birthday-cake" style="margin-right: 8px; color: #36b9cc;"></i>Age Distribution
                        </h4>
                        <canvas id="ageDistributionChart" width="420" height="220"></canvas>
                    </div>
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #4e73df; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-venus-mars" style="margin-right: 8px; color: #f6c23e;"></i>Gender Distribution
                        </h4>
                        <canvas id="genderDistributionChart" width="420" height="220"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #e74a3b; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-stethoscope" style="margin-right: 8px; color: #36b9cc;"></i>Diagnosis Statistics
                        </h4>
                        <canvas id="diagnosisChart" width="420" height="320" style="margin: 0 auto;"></canvas>
                    </div>
                </div>
            </div>
            <!-- Outgoing/Incoming label removed -->
             <div class="row">
                <div class="col-md-6">
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px; margin-bottom: 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #1cc88a; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-birthday-cake" style="margin-right: 8px; color: #36b9cc;"></i>Age Distribution
                        </h4>
                        <canvas id="ageDistributionChartIncoming" width="420" height="220"></canvas>
                    </div>
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #4e73df; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-venus-mars" style="margin-right: 8px; color: #f6c23e;"></i>Gender Distribution
                        </h4>
                        <canvas id="genderDistributionChartIncoming" width="420" height="220"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="background: #f8fafc; border-radius: 18px; box-shadow: 0 4px 18px rgba(44,62,80,0.08); padding: 32px 18px 24px 18px;">
                        <h4 style="font-size: 1.3em; font-weight: bold; color: #e74a3b; text-align: center; margin-bottom: 18px; letter-spacing: 1px;">
                            <i class="fa fa-stethoscope" style="margin-right: 8px; color: #36b9cc;"></i>Diagnosis Statistics
                        </h4>
                        <canvas id="diagnosisChartIncoming" width="420" height="320" style="margin: 0 auto;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('export.consultation.report', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" 
        class="btn btn-primary" 
        style="padding: 12px 32px; font-size: 1.2em; border-radius: 8px; box-shadow: 0 2px 8px rgba(44,62,80,0.10);">
        Download Report
    </a>
</div>

@endsection

@section('js')

<style>
    .filter-input {
        font-size: 1rem;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid #4e73df;
        background: #f8f9fc;
        color: #222;
        transition: border-color 0.2s;
        outline: none;
    }
    .filter-input:focus {
        border-color: #3357FF;
    }
</style>
<script>
    // Auto-submit form on date input change
    document.addEventListener('DOMContentLoaded', function() {
        var fromInput = document.getElementById('from_date');
        var toInput = document.getElementById('to_date');
        var form = fromInput && toInput ? fromInput.form : null;
        if (form && fromInput && toInput) {
            function autoSubmit() {
                if (fromInput.value && toInput.value) {
                    form.submit();
                }
            }
            fromInput.addEventListener('change', autoSubmit);
            toInput.addEventListener('change', autoSubmit);
        }
    });
        // Example data for charts
        let consultPerDepartment = @json($totalperDepartment);
        let consultPerDepartmentIncoming = @json($totalperDepartmentIncoming);
        let totalPatientDemographicPerAge = @json($totalPatientDemographicPerAge);
        let totalPatientDemographicPerAgeIncoming = @json($totalPatientDemographicPerAgeIncoming);
        let totalPatientPerGender = @json($totalPatientPerGender);
        let totalPatientPerGenderIncoming = @json($totalPatientPerGenderIncoming);
        let totalDiagnosticStat = @json($totalDiagnosticStat);
        let totalDiagnosticStatIncoming = @json($totalDiagnosticStatIncoming);
        
        
        let PerDepartment = consultPerDepartment.map(item => item.description);
        let PerDepartmentIncoming = consultPerDepartmentIncoming.map(item => item.description);
        let PerConsultation = consultPerDepartment.map(item => item.total_consultations);
        let PerConsultationIncoming = consultPerDepartmentIncoming.map(item => item.total_consultations);
        let below_18 = totalPatientDemographicPerAge.below_18;
        let below_18_incoming = totalPatientDemographicPerAgeIncoming.below_18;
        let age_18_30 = totalPatientDemographicPerAge.age_18_30;
        let age_18_30_incoming = totalPatientDemographicPerAgeIncoming.age_18_30;
        let age_31_45 = totalPatientDemographicPerAge.age_31_45;
        let age_31_45_incoming = totalPatientDemographicPerAgeIncoming.age_31_45;
        let age_46_60 = totalPatientDemographicPerAge.age_46_60;
        let age_46_60_incoming = totalPatientDemographicPerAgeIncoming.age_46_60;
        let above_60 = totalPatientDemographicPerAge.above_60;
        let above_60_incoming = totalPatientDemographicPerAgeIncoming.above_60;
        let total_male = totalPatientPerGender.male_count;
        let total_male_incoming = totalPatientPerGenderIncoming.male_count;
        let total_female = totalPatientPerGender.female_count;
        let total_female_incoming = totalPatientPerGenderIncoming.female_count;
        let hypertension_count = totalDiagnosticStat.hypertension_count;
        let hypertension_count_incoming = totalDiagnosticStatIncoming.hypertension_count;
        let diabetes_count = totalDiagnosticStat.diabetes_count;
        let diabetes_count_incoming = totalDiagnosticStatIncoming.diabetes_count;
        let respiratory_count = totalDiagnosticStat.respiratory_count;
        let respiratory_count_incoming = totalDiagnosticStatIncoming.respiratory_count;
        let cancer_count = totalDiagnosticStat.cancer_count;
        let cancer_count_incoming = totalDiagnosticStatIncoming.cancer_count;

        let others_count = totalDiagnosticStat.others_count;
        let others_count_incoming = totalDiagnosticStatIncoming.others_count;
        
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

        var doctorDataIncoming = {
            labels: PerDepartmentIncoming,
            datasets: [{
                label: 'Consultations per Doctor',
                data: PerConsultationIncoming,
                backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FF9333'],
                borderColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FF9333'],
                borderWidth: 1
            }]
        };

        // Create the consultation tracking data
        var ageDistributionData = {
            labels: ['Below 18', '18-30', '31-45', '46-60', 'Above 60'],
            datasets: [{
                label: 'Age Distribution',
                data: [below_18, age_18_30, age_31_45, age_46_60, above_60],
                backgroundColor: [
                    'rgba(255, 153, 51, 0.85)',   // Orange
                    'rgba(51, 255, 153, 0.85)',   // Green
                    'rgba(51, 153, 255, 0.85)',   // Blue
                    'rgba(255, 51, 153, 0.85)',   // Pink
                    'rgba(255, 255, 51, 0.85)'    // Yellow
                ],
                borderColor: [
                    '#FF9933', '#33FF99', '#3399FF', '#FF3399', '#FFFF33'
                ],
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: [
                    'rgba(255, 153, 51, 1)',
                    'rgba(51, 255, 153, 1)',
                    'rgba(51, 153, 255, 1)',
                    'rgba(255, 51, 153, 1)',
                    'rgba(255, 255, 51, 1)'
                ]
            }]
        };

        // Create the consultation tracking data incoming
        var ageDistributionDataIncoming = {
            labels: ['Below 18', '18-30', '31-45', '46-60', 'Above 60'],
            datasets: [{
                label: 'Age Distribution',
                data: [below_18_incoming, age_18_30_incoming, age_31_45_incoming, age_46_60_incoming, above_60_incoming],
                backgroundColor: [
                    'rgba(255, 153, 51, 0.85)',   // Orange
                    'rgba(51, 255, 153, 0.85)',   // Green
                    'rgba(51, 153, 255, 0.85)',   // Blue
                    'rgba(255, 51, 153, 0.85)',   // Pink
                    'rgba(255, 255, 51, 0.85)'    // Yellow
                ],
                borderColor: [
                    '#FF9933', '#33FF99', '#3399FF', '#FF3399', '#FFFF33'
                ],
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: [
                    'rgba(255, 153, 51, 1)',
                    'rgba(51, 255, 153, 1)',
                    'rgba(51, 153, 255, 1)',
                    'rgba(255, 51, 153, 1)',
                    'rgba(255, 255, 51, 1)'
                ]
            }]
        };

        // gender distribution chart
        var genderDistributionData = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender Distribution',
                data: [total_male, total_female],
                backgroundColor: [
                    'rgba(102, 179, 255, 0.85)', // Blue
                    'rgba(255, 102, 179, 0.85)'  // Pink
                ],
                borderColor: ['#66B3FF', '#FF66B3'],
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: [
                    'rgba(102, 179, 255, 1)',
                    'rgba(255, 102, 179, 1)'
                ]
            }]
        };

        // gender distribution chart incoming
        var genderDistributionDataIncoming = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender Distribution',
                data: [total_male_incoming, total_female_incoming],
                backgroundColor: [
                    'rgba(102, 179, 255, 0.85)', // Blue
                    'rgba(255, 102, 179, 0.85)'  // Pink
                ],
                borderColor: ['#66B3FF', '#FF66B3'],
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: [
                    'rgba(102, 179, 255, 1)',
                    'rgba(255, 102, 179, 1)'
                ]
            }]
        };

        // Diagnosis statistics chart
        var diagnosisData = {
            labels: ['Hypertension', 'Diabetes', 'Respiratory', 'Cancer', 'Other'],
            datasets: [{
                label: 'Diagnosis Statistics',
                data: [hypertension_count, diabetes_count, respiratory_count, cancer_count, others_count],
                backgroundColor: ['#FF6666', '#66FF66', '#6699FF', '#FF66FF', '#FFCC33'],
                borderColor: ['#FF6666', '#66FF66', '#6699FF', '#FF66FF', '#FFCC33'],
                borderWidth: 1
            }]
        };

        // Diagnosis statistics chart incoming
        var diagnosisDataIncoming = {
            labels: ['Hypertension', 'Diabetes', 'Respiratory', 'Cancer', 'Other'],
            datasets: [{
                label: 'Diagnosis Statistics',
                data: [hypertension_count_incoming, diabetes_count_incoming, respiratory_count_incoming, cancer_count_incoming, others_count_incoming],
                backgroundColor: ['#FF6666', '#66FF66', '#6699FF', '#FF66FF', '#FFCC33'],
                borderColor: ['#FF6666', '#66FF66', '#6699FF', '#FF66FF', '#FFCC33'],
                borderWidth: 1
            }]
        };

        let numberPatient = {{$numberPatient}};
        let totalConsult = {{$totalConsult}};
        let avgConsultPerPatient = numberPatient > 0 ? (totalConsult / numberPatient).toFixed(2) : 0;
        
        var consultationTrackerData = {
            labels: ['Referred', 'Accepted', 'Follow Up'],
            datasets: [{
                label: 'Consultation Tracking Data',
                data: [{{$totalReferred}}, {{$totalAccepted}}, {{$totalFollowUp}}],
                backgroundColor: [
                    '#FF5733', // same as Consultations by Department
                    '#3357FF',
                    '#FF33A1'
                ],
                borderColor: [
                    '#FF5733',
                    '#3357FF',
                    '#FF33A1'
                ],
                borderWidth: 1,
                borderRadius: 8,
                hoverBackgroundColor: [
                    'rgba(255, 87, 51, 0.85)',
                    'rgba(51, 87, 255, 0.85)',
                    'rgba(255, 51, 161, 0.85)'
                ]
            }]
        };

        var consultationTrackerDataIncoming = {
            labels: ['Referred', 'Accepted', 'Follow Up'],
            datasets: [{
                label: 'Consultation Tracking Data',
                data: [{{$totalReferredIncoming}}, {{$totalAcceptedIncoming}}, {{$totalFollowUpIncoming}}],
                backgroundColor: [
                    '#FF5733', // same as Consultations by Department
                    '#3357FF',
                    '#FF33A1'
                ],
                borderColor: [
                    '#FF5733',
                    '#3357FF',
                    '#FF33A1'
                ],
                borderWidth: 1,
                borderRadius: 8,
                hoverBackgroundColor: [
                    'rgba(255, 87, 51, 0.85)',
                    'rgba(51, 87, 255, 0.85)',
                    'rgba(255, 51, 161, 0.85)'
                ]
            }]
        };

        // Helper to show/hide chart containers and overall stats
        function toggleConsultationCharts(type) {
            // Outgoing = default, Incoming = alternate
            const outgoingIds = [
                'doctorChart', 'ageDistributionChart', 'genderDistributionChart', 'diagnosisChart', 'consultationTrackingData', 'overallStatsOutgoing'
            ];
            const incomingIds = [
                'doctorChartIncoming', 'ageDistributionChartIncoming', 'genderDistributionChartIncoming', 'diagnosisChartIncoming', 'consultationTrackingDataIncoming', 'overallStatsIncoming'
            ];
            outgoingIds.forEach(id => {
                let el = document.getElementById(id);
                if (el) {
                    if (id === 'overallStatsOutgoing') {
                        el.style.display = (type === 'outgoing') ? '' : 'none';
                    } else {
                        el.closest('.col-md-6') ? el.closest('.col-md-6').style.display = (type === 'outgoing') ? '' : 'none' : el.parentElement.style.display = (type === 'outgoing') ? '' : 'none';
                    }
                }
            });
            incomingIds.forEach(id => {
                let el = document.getElementById(id);
                if (el) {
                    if (id === 'overallStatsIncoming') {
                        el.style.display = (type === 'incoming') ? '' : 'none';
                    } else {
                        el.closest('.col-md-6') ? el.closest('.col-md-6').style.display = (type === 'incoming') ? '' : 'none' : el.parentElement.style.display = (type === 'incoming') ? '' : 'none';
                    }
                }
            });
        }

        // Initial toggle
        document.addEventListener('DOMContentLoaded', function() {
            toggleConsultationCharts('outgoing');
            document.getElementById('consultationType').addEventListener('change', function() {
                toggleConsultationCharts(this.value);
            });
        });

        // Create the charts
        new Chart(document.getElementById('doctorChart'), {
            type: 'bar',
            data: doctorData,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.parsed.y || context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#4e73df',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 12, weight: 'bold' },
                        titleFont: { size: 12, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 12 },
                        padding: 8,
                        anchor: 'end',
                        align: 'start',
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let data = context.dataset.data;
                            let total = data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + ' (' + percent + ')';
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#eee', borderColor: '#ccc', borderWidth: 2 },
                        ticks: {
                            font: { size: 16, weight: 'bold' },
                            color: '#333',
                            padding: 8
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f5f5f5', borderColor: '#ccc', borderWidth: 2 },
                        ticks: {
                            font: { size: 16, weight: 'bold' },
                            color: '#333',
                            stepSize: 1,
                            padding: 8
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

          // Create the charts for Incoming consultations
        new Chart(document.getElementById('doctorChartIncoming'), {
            type: 'bar',
            data: doctorDataIncoming,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.parsed.y || context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#4e73df',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 12, weight: 'bold' },
                        titleFont: { size: 12, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 12 },
                        padding: 8,
                        anchor: 'end',
                        align: 'start',
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let data = context.dataset.data;
                            let total = data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + ' (' + percent + ')';
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#eee', borderColor: '#ccc', borderWidth: 2 },
                        ticks: {
                            font: { size: 16, weight: 'bold' },
                            color: '#333',
                            padding: 8
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f5f5f5', borderColor: '#ccc', borderWidth: 2 },
                        ticks: {
                            font: { size: 16, weight: 'bold' },
                            color: '#333',
                            stepSize: 1,
                            padding: 8
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Create the consultation tracking charts
        new Chart(document.getElementById('ageDistributionChart'), {
            type: 'doughnut',
            data: ageDistributionData,
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 18, weight: 'bold' },
                            color: '#333',
                            padding: 24,
                            boxWidth: 28,
                            boxHeight: 18,
                            borderRadius: 8
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#36b9cc',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 18, weight: 'bold' },
                        titleFont: { size: 18, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 18 },
                        padding: 10,
                        anchor: 'center',
                        align: 'center',
                        // backgroundColor: function(context) {
                        //     return context.dataset.backgroundColor[context.dataIndex];
                        // },
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + '\n' + percent;
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
        // Create the consultation tracking charts for Incoming consultations
        new Chart(document.getElementById('ageDistributionChartIncoming'), {
            type: 'doughnut',
            data: ageDistributionDataIncoming,
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 18, weight: 'bold' },
                            color: '#333',
                            padding: 24,
                            boxWidth: 28,
                            boxHeight: 18,
                            borderRadius: 8
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#36b9cc',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 18, weight: 'bold' },
                        titleFont: { size: 18, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 18 },
                        padding: 10,
                        anchor: 'center',
                        align: 'center',
                        // backgroundColor: function(context) {
                        //     return context.dataset.backgroundColor[context.dataIndex];
                        // },
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + '\n' + percent;
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Gender Distribution Chart
        new Chart(document.getElementById('genderDistributionChart'), {
            type: 'doughnut',
            data: genderDistributionData,
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 18, weight: 'bold' },
                            color: '#333',
                            padding: 24,
                            boxWidth: 28,
                            boxHeight: 18,
                            borderRadius: 8
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#4e73df',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 18, weight: 'bold' },
                        titleFont: { size: 18, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 18 },
                        padding: 10,
                        anchor: 'center',
                        align: 'center',
                        // backgroundColor: function(context) {
                        //     return context.dataset.backgroundColor[context.dataIndex];
                        // },
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + '\n' + percent;
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Gender Distribution Chart Incoming
        new Chart(document.getElementById('genderDistributionChartIncoming'), {
            type: 'doughnut',
            data: genderDistributionDataIncoming,
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 18, weight: 'bold' },
                            color: '#333',
                            padding: 24,
                            boxWidth: 28,
                            boxHeight: 18,
                            borderRadius: 8
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#4e73df',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 18, weight: 'bold' },
                        titleFont: { size: 18, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 18 },
                        padding: 10,
                        anchor: 'center',
                        align: 'center',
                        // backgroundColor: function(context) {
                        //     return context.dataset.backgroundColor[context.dataIndex];
                        // },
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + '\n' + percent;
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Diagnosis Statistics Chart
        new Chart(document.getElementById('diagnosisChart'), {
            type: 'doughnut',
            data: diagnosisData,
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 18, weight: 'bold' },
                            color: '#333',
                            padding: 24,
                            boxWidth: 28,
                            boxHeight: 18,
                            borderRadius: 8
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#e74a3b',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 18, weight: 'bold' },
                        titleFont: { size: 18, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 18 },
                        padding: 10,
                        anchor: 'center',
                        align: 'center',
                        // backgroundColor: function(context) {
                        //     return context.dataset.backgroundColor[context.dataIndex];
                        // },
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + '\n' + percent;
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

           // Diagnosis Statistics Chart Incoming
        new Chart(document.getElementById('diagnosisChartIncoming'), {
            type: 'doughnut',
            data: diagnosisDataIncoming,
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: { size: 18, weight: 'bold' },
                            color: '#333',
                            padding: 24,
                            boxWidth: 28,
                            boxHeight: 18,
                            borderRadius: 8
                        }
                    },
                    title: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                return label + ': ' + value + percent;
                            }
                        },
                        backgroundColor: '#fff',
                        titleColor: '#222',
                        bodyColor: '#222',
                        borderColor: '#e74a3b',
                        borderWidth: 2,
                        padding: 14,
                        cornerRadius: 12,
                        bodyFont: { size: 18, weight: 'bold' },
                        titleFont: { size: 18, weight: 'bold' }
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 8,
                        font: { weight: 'bold', size: 18 },
                        padding: 10,
                        anchor: 'center',
                        align: 'center',
                        // backgroundColor: function(context) {
                        //     return context.dataset.backgroundColor[context.dataIndex];
                        // },
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + '\n' + percent;
                        },
                        display: function(context) {
                            var v = context.dataset.data[context.dataIndex];
                            return v !== 0 && v !== '0' && !!v;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Consultation Tracking Data Chart
        const ctxConsult = document.getElementById('consultationTrackingData');
        if (ctxConsult) {
            new Chart(ctxConsult, {
                type: 'pie',
                data: consultationTrackerData,
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                font: { size: 18, weight: 'bold' },
                                color: '#333',
                                padding: 24,
                                boxWidth: 28,
                                boxHeight: 18,
                                borderRadius: 8
                            }
                        },
                        title: {
                            display: true,
                            text: 'Consultations Overview',
                            font: { size: 22, weight: 'bold' },
                            color: '#333',
                            padding: { top: 10, bottom: 30 }
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                    let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                    return label + ': ' + value + percent;
                                }
                            },
                            backgroundColor: '#fff',
                            titleColor: '#222',
                            bodyColor: '#222',
                            borderColor: '#4e73df',
                            borderWidth: 2,
                            padding: 14,
                            cornerRadius: 12,
                            bodyFont: { size: 18, weight: 'bold' },
                            titleFont: { size: 18, weight: 'bold' }
                        },
                        datalabels: {
                            color: '#fff',
                            borderRadius: 8,
                            font: { weight: 'bold', size: 18 },
                            padding: 10,
                            anchor: 'center',
                            align: 'center',
                            // backgroundColor: function(context) {
                            //     return context.dataset.backgroundColor[context.dataIndex];
                            // },
                            formatter: function(value, context) {
                                if (!value || value === 0 || value === '0') return '';
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                                return value + '\n' + percent;
                            },
                            display: function(context) {
                                var v = context.dataset.data[context.dataIndex];
                                return v !== 0 && v !== '0' && !!v;
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        } else {
            console.error('Element #consultationTrackingData not found!');
        }

         // Consultation Tracking Data Chart Incoming
        const ctxConsultIncoming = document.getElementById('consultationTrackingDataIncoming');
        if (ctxConsultIncoming) {
            new Chart(ctxConsultIncoming, {
                type: 'pie',
                data: consultationTrackerDataIncoming,
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                font: { size: 18, weight: 'bold' },
                                color: '#333',
                                padding: 24,
                                boxWidth: 28,
                                boxHeight: 18,
                                borderRadius: 8
                            }
                        },
                        title: {
                            display: true,
                            text: 'Consultations Overview',
                            font: { size: 22, weight: 'bold' },
                            color: '#333',
                            padding: { top: 10, bottom: 30 }
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                    let percent = total > 0 ? ' (' + ((value / total) * 100).toFixed(1) + '%)' : '';
                                    return label + ': ' + value + percent;
                                }
                            },
                            backgroundColor: '#fff',
                            titleColor: '#222',
                            bodyColor: '#222',
                            borderColor: '#4e73df',
                            borderWidth: 2,
                            padding: 14,
                            cornerRadius: 12,
                            bodyFont: { size: 18, weight: 'bold' },
                            titleFont: { size: 18, weight: 'bold' }
                        },
                        datalabels: {
                            color: '#fff',
                            borderRadius: 8,
                            font: { weight: 'bold', size: 18 },
                            padding: 10,
                            anchor: 'center',
                            align: 'center',
                            // backgroundColor: function(context) {
                            //     return context.dataset.backgroundColor[context.dataIndex];
                            // },
                            formatter: function(value, context) {
                                if (!value || value === 0 || value === '0') return '';
                                let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                                let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                                return value + '\n' + percent;
                            },
                            display: function(context) {
                                var v = context.dataset.data[context.dataIndex];
                                return v !== 0 && v !== '0' && !!v;
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        } else {
            console.error('Element #consultationTrackingData not found!');
        }
        

        // new Chart(document.getElementById('adherenceChart'), {
        //     type: 'pie',
        //     data: adherenceData,
        //     options: {
        //         responsive: true
        //     }
        // });
    </script>
@endsection