@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<div class="box box-success">
    <h1 class="text-center">Telemedicine Consolidated Report</h1>
    <hr>

        <!-- Date Filter Form -->
    <form method="GET" action="{{ url('/count/Consultation') }}" class="form-inline text-left" style="margin-bottom: 20px;">
        <label for="from_date">From:</label>
        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-control" required>
        <label for="to_date" style="margin-left:10px;">To:</label>
        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-control" required>
        <button type="submit" class="btn btn-success" style="margin-left:10px;">Filter</button>
        @if(request('from_date') && request('to_date'))
            <a href="{{ url('/count/Consultation') }}" class="btn btn-default" style="margin-left:10px;">Clear</a>
        @endif
    </form>


    <!-- Overall Statistics -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Overall Telemedicine Statistics</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <h4>Total Consultations</h4>
                    <p class="">{{$totalConsult}} consultations</p>
                </div>
                <div class="col-md-3">
                    <h4>Total Departments</h4>
                    <p>{{ $countDepartment }}</p>
                </div>
                <div class="col-md-3">
                    <h4>Total Patients</h4>
                    <p>{{ $numberPatient }} patients</p>
                </div>
                <div class="col-md-3">
                    <h4>Average Consultation Duration</h4>
                    <p>{{$averageConsultationDuration}}</p>
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

    <div class="text-center">
        <button class="btn btn-primary">Download Report</button>
    </div>
</div>
@endsection

@section('js')

<script>
        // Example data for charts
        let consultPerDepartment = @json($totalperDepartment);
        let totalPatientDemographicPerAge = @json($totalPatientDemographicPerAge);
        let totalPatientPerGender = @json($totalPatientPerGender);
        let totalDiagnosticStat = @json($totalDiagnosticStat);
        
        let PerDepartment = consultPerDepartment.map(item => item.description);
        let PerConsultation = consultPerDepartment.map(item => item.total_consultations);
        let below_18 = totalPatientDemographicPerAge.below_18;
        let age_18_30 = totalPatientDemographicPerAge.age_18_30;
        let age_31_45 = totalPatientDemographicPerAge.age_31_45;
        let age_46_60 = totalPatientDemographicPerAge.age_46_60;
        let above_60 = totalPatientDemographicPerAge.above_60;
        let total_male = totalPatientPerGender.male_count;
        let total_female = totalPatientPerGender.female_count;
        let hypertension_count = totalDiagnosticStat.hypertension_count;
        let diabetes_count = totalDiagnosticStat.diabetes_count;
        let respiratory_count = totalDiagnosticStat.respiratory_count;
        let cancer_count = totalDiagnosticStat.cancer_count;
        let others_count = totalDiagnosticStat.others_count;
        
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
            labels: ['Below 18', '18-30', '31-45', '46-60', 'Above 60'],
            datasets: [{
                label: 'Age Distribution',
                data: [below_18, age_18_30, age_31_45, age_46_60, above_60],
                backgroundColor: ['#FF9933', '#33FF99', '#3399FF', '#FF3399', '#FFFF33'],
                borderColor: ['#FF9933', '#33FF99', '#3399FF', '#FF3399', '#FFFF33'],
                borderWidth: 1
            }]
        };

        var genderDistributionData = {
            labels: ['Male', 'Female'],
            datasets: [{
                label: 'Gender Distribution',
                data: [total_male, total_female],
                backgroundColor: ['#66B3FF', '#FF66B3'],
                borderColor: ['#66B3FF', '#FF66B3'],
                borderWidth: 1
            }]
        };

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

        // var adherenceData = {
        //     labels: ['Followed Treatment', 'Did Not Follow Treatment'],
        //     datasets: [{
        //         label: 'Treatment Adherence',
        //         data: [850, 150],
        //         backgroundColor: ['#66FF66', '#FF6666'],
        //         borderColor: ['#66FF66', '#FF6666'],
        //         borderWidth: 1
        //     }]
        // };

        // Create the charts
        new Chart(document.getElementById('doctorChart'), {
            type: 'bar',
            data: doctorData,
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
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
                        borderColor: '#888',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    },
                    datalabels: {
                        color: '#fff',
                        // backgroundColor: function(context) {
                        //     let data = context.dataset.data;
                        //     let max = Math.max(...data.map(v => parseFloat(v) || 0));
                        //     return (parseFloat(data[context.dataIndex]) === max) ? '#FF5733' : '#888';
                        // },
                        borderRadius: 6,
                        font: { weight: 'bold', size: 16 },
                        padding: 2,
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
                        ticks: {
                            font: { size: 14, weight: 'bold' },
                            color: '#333'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 14, weight: 'bold' },
                            color: '#333',
                            stepSize: 1
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        new Chart(document.getElementById('ageDistributionChart'), {
            type: 'pie',
            data: ageDistributionData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            font: { size: 16, weight: 'bold' },
                            color: '#333',
                            padding: 20
                        }
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
                        borderColor: '#888',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 6,
                        font: { weight: 'bold', size: 16 },
                        padding: 8,
                        anchor: 'end',
                        align: 'start',
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + ' (' + percent + ')';
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

        new Chart(document.getElementById('genderDistributionChart'), {
            type: 'pie',
            data: genderDistributionData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            font: { size: 16, weight: 'bold' },
                            color: '#333',
                            padding: 20
                        }
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
                        borderColor: '#888',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 6,
                        font: { weight: 'bold', size: 16 },
                        padding: 8,
                        anchor: 'end',
                        align: 'start',
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + ' (' + percent + ')';
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

        new Chart(document.getElementById('diagnosisChart'), {
            type: 'pie',
            data: diagnosisData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        labels: {
                            font: { size: 16, weight: 'bold' },
                            color: '#333',
                            padding: 20
                        }
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
                        borderColor: '#888',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    },
                    datalabels: {
                        color: '#fff',
                        borderRadius: 6,
                        font: { weight: 'bold', size: 16 },
                        padding: 8,
                        anchor: 'end',
                        align: 'start',
                        formatter: function(value, context) {
                            if (!value || value === 0 || value === '0') return '';
                            let total = context.dataset.data.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                            let percent = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '';
                            return value + ' (' + percent + ')';
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

        // new Chart(document.getElementById('adherenceChart'), {
        //     type: 'pie',
        //     data: adherenceData,
        //     options: {
        //         responsive: true
        //     }
        // });
    </script>
@endsection