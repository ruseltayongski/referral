<?php
$error = \Illuminate\Support\Facades\Input::get('error');
$user = Session::get('auth');
$multi_faci = Session::get('multiple_login');
?>
@extends('layouts.app')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bed Availability Tracker Dashboard</title>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body {
            /* background-color: #f5f7fa; */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* padding: 20px 0; */
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            margin-bottom: 15px;
            font-size: 18px;
            color: #555;
        }

        .dashboard-header-2 {
            background: #ffffff;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge {
            background: #2ecc71;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }

        .panel-danger {
            border-left: 4px solid #e74c3c;
        }

        .panel-warning {
            border-left: 4px solid #f39c12;
        }

        .sidebar-panel .panel-heading {
            background-color: #667eea;
            color: white;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            padding: 12px 15px;
        }

        .waitlist-card {
            margin-bottom: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .waitlist-card .panel-heading {
            font-size: 12px;
            padding: 8px 10px;
        }

        .waitlist-card .panel-body {
            padding: 15px 10px;
        }

        .waitlist-card h3 {
            font-size: 28px;
            font-weight: 700;
            margin: 5px 0;
        }

        .waitlist-card p {
            font-size: 11px;
            margin: 0;
        }

        .live-updates {
            max-height: 300px;
            overflow-y: auto;
        }

        .live-updates p {
            padding: 8px 12px;
            margin: 5px 0;
            background-color: #fff3cd;
            border-left: 3px solid #f39c12;
            border-radius: 4px;
            font-size: 13px;
        }

        .form-control,
        .btn {
            border-radius: 4px;
        }

        .statistics-section {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-top: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-separator {
            border: 0;
            height: 2px;
            background: linear-gradient(to right, #667eea, #764ba2);
            margin: 30px 0;
        }

        .statistics-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .statistics-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .time-filter {
            display: flex;
            gap: 5px;
        }

        .time-filter button {
            padding: 5px 12px;
            font-size: 12px;
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .chart-container {
                height: 250px;
            }
        }

        /* Scrollbar styling for live updates */
        .live-updates::-webkit-scrollbar {
            width: 6px;
        }

        .live-updates::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .live-updates::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .live-updates::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* commented for individual */
        /* Option 1: Split display style */
        .bed-stats-split {
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 15px;
        }

        /* Option 2: Card style */
        /* Bed Stats Cards */
        .bed-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .bed-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .bed-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
        }

        .bed-card.regular::before {
            --card-color: var(--primary);
            --card-color-light: #60a5fa;
        }

        .bed-card.emergency::before {
            --card-color: var(--success);
            --card-color-light: #34d399;
        }

        .bed-card.covid::before {
            --card-color: var(--danger);
            --card-color-light: #f87171;
        }

        .bed-card.icu::before {
            --card-color: var(--info);
            --card-color-light: #22d3ee;
        }

        .bed-card.isolation::before {
            --card-color: var(--warning);
            --card-color-light: #fbbf24;
        }

        .bed-card.ventilator::before {
            --card-color: #6b7280;
            --card-color-light: #9ca3af;
        }

        .bed-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .bed-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }

        .regular .bed-icon {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
        }

        .emergency .bed-icon {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .covid .bed-icon {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .icu .bed-icon {
            background: rgba(6, 182, 212, 0.1);
            color: var(--info);
        }

        .isolation .bed-icon {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .ventilator .bed-icon {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
        }

        .bed-title {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
        }

        /* individual  */
        .bed-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .stat-box1 {
            flex: 1 1 100px;
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            background: var(--light);
        }

        .stat-box1.safe {
            background-color: #e8f5e9;
        }
        .stat-box1.critical {
            background-color: #fce4ec; 
        }
        .stat-box1.warning {
            background-color: #fff8e1; 
        }

        .stat-box.available {
            background: rgba(16, 185, 129, 0.05);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .stat-box.occupied {
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }

        .stat-box.available .stat-number {
            color: var(--success);
        }

        .stat-box.occupied .stat-number {
            color: var(--danger);
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        /* end individual */

        .total-beds {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            color: #6b7280;
            font-size: 13px;
        }

        .online-container {
            display: flex;
            flex-flow: row wrap;
            justify-content: flex-start;
            /* Align items to the left */
            align-items: flex-start;
            /* Align items to the top */
        }

        .online-container1 {
            /* display: flex; */
            flex-flow: row wrap;
            justify-content: flex-start;
            /* Align items to the left */
            align-items: flex-start;
            /* Align items to the top */
        }

        .card-wrap {
            display: flex;
            flex-direction: column;
            /* Ensures content stacks properly */
            padding: 10px;
            width: 200px;
            /* Fixed width for all cards */
            word-wrap: break-word;
            /* Ensures long words wrap */
            overflow-wrap: break-word;
            /* Alternative for compatibility */
            white-space: normal;
            /* Allows text to wrap */
            text-align: center;
            /* Optional: keeps text aligned */
        }

        .facility-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: 0.3s ease;
            border-top: 5px solid transparent;
        }

        /* Status Borders */
        .facility-card.safe {
            border-top-color: #82af86;
            /* background-color: #e8f5e9; */
        }

        .facility-card.warning {
            border-top-color: #ffc107;
            /* background-color: #fff8e1; */
        }

        .facility-card.critical {
            border-top-color: #dc3545;
            /* background-color: #fce4ec; */
        }

        /* Header */
        .card-header-custom {
            font-size: 15px;
            margin-bottom: 15px;
        }

        /* Badge */
        .occupancy-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            color: #fff;
        }

        .occupancy-badge.safe {
            background: #a1c7aa;
        }

        .occupancy-badge.warning {
            background: #ffc107;
            color: #333;
        }

        .occupancy-badge.critical {
            background: #dc3545;
        }

        /* Stats */
        .stats-wrapper {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .stat-box {
            text-align: center;
            flex: 1 ;
            padding-top: 11px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }
        /* individual */
        .available .stat-number {
            color: #28a745;
        }

        .occupied .stat-number {
            color: #dc3545;
        }
        /* end individual */

        .stat-label {
            font-size: 13px;
            color: #6c757d;
        }

        .divider {
            width: 1px;
            height: 45px;
            background: #e5e5e5;
        }

        /* Total */
        .total-beds {
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }

            /* ── Progress bar ── */
        .progress {
            height: 5px !important;
            background: #e9ecef;
            border-radius: 10px !important;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width .5s ease;
        }
        .progress-bar.safe     { background: #4caf50; }
        .progress-bar.warning  { background: #ffc107; }
        .progress-bar.critical { background: #e53935; }

        .k-depts {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .kd {
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
            border: 1.5px solid;
        }

        .kd.er  { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
        .kd.icu { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
        .kd.iso { background: #faf5ff; border-color: #e9d5ff; color: #9333ea; }

    </style>
</head>
@section('content')
<div class="container-fluid">
    <div class="row">
        <div id="main" class="col-xs-12 col-sm-9">
            <div class="section">
                <div class="dashboard-header-2">
                    <div>
                        <h2 style="margin:0;">Bed Availability Tracker</h2>
                        <div style="margin-top:5px; font-size:14px;">
                            Bed Availability Status as of
                            <span class="status-badge" id="time"></span>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <button class="btn btn-default btn-sm" style="margin-right: 3px;" onclick="window.location.href='{{ asset('/doctor') }}' ">
                           <i class='fa fa-reply' style='font-size:18px'></i>  
                        </button>
                        <button id="toggleSidebar" class="btn btn-default btn-sm" style="margin-right: 1px;">
                            ☰ 
                        </button>
                        &nbsp;
                        <button class="btn btn-default btn-sm pull-right" onclick="window.location.href='{{ asset('/dashboard') }}'">
                           <i class='fa fa-eye' style='font-size:15px'></i>  Show All
                        </button>
                        <div style="font-size:12px; color:#555; padding-top:10px;">Facility</div>
                        <h3 style="margin:5px 0 0;" id="facility_name"> All Facilities</h3>
                    </div>
                </div>
            </div>
            <div class="row online-container1" id="specific-facility-cards" style="display: none;">
                <!-- First Row -->
                <!-- <div class="row"> -->
                    <!-- Regular Beds -->
                    <div class="col-xs-12 col-sm-6 col-md-4 card-wrap">
                        <div class="bed-card regular">
                            <div class="bed-card-header">
                                <div class="bed-icon">
                                    <i class="fa fa-bed"></i>
                                </div>
                                <h5 class="bed-title">Regular Beds</h5>
                            </div>
                            <div class="bed-stats">
                                <div class="stat-box available">           
                                    <p class="stat-number" id="beds_vacant">0</p>
                                    <p class="stat-label">Available</p>
                                </div>
                                <div class="stat-box occupied">
                                    <p class="stat-number" id="beds_occupied">0</p>
                                    <p class="stat-label">Occupied</p>
                                </div>
                            </div>
                            <div class="total-beds">
                                <strong id="beds_total">Total:</strong> beds
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Room -->
                    <div class="col-xs-12 col-sm-6 col-md-4 card-wrap">
                        <div class="bed-card regular">
                            <div class="bed-card-header">
                                <div class="bed-icon">
                                    <i class="fa fa-bed"></i>
                                </div>
                                <h5 class="bed-title">Emergency Room</h5>
                            </div>
                            <div class="bed-stats">
                                <div class="stat-box available">
                                    <p class="stat-number" id="er_vacant">0</p>
                                    <p class="stat-label">AVAILABLE</p>
                                </div>
                                <div class="stat-box occupied">
                                    <p class="stat-number" id="er_occupied">0</p>
                                    <p class="stat-label">OCCUPIED</p>
                                </div>
                            </div>
                            <div class="total-beds">
                                <strong id="er_total">Total:</strong> beds
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ICU -->
                    <div class="col-xs-12 col-sm-6 col-md-4 card-wrap">
                        <div class="bed-card regular">
                            <div class="bed-card-header">
                                <div class="bed-icon">
                                    <i class="fa fa-bed"></i>
                                </div>
                                <h5 class="bed-title">ICU</h5>
                            </div>
                            <div class="bed-stats">
                                <div class="stat-box available">
                                    <p class="stat-number" id="icu_vacant">0</p>
                                    <p class="stat-label">Available</p>
                                </div>
                                <div class="stat-box occupied">
                                    <p class="stat-number" id="icu_occupied">0</p>
                                    <p class="stat-label">Occupied</p>
                                </div>
                            </div>
                            <div class="total-beds">
                                <strong id="icu_total">Total:</strong> beds
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                <!-- </div> -->

                <!-- Second Row -->
                <!-- <div class="row"> -->
                    <!-- Isolation Beds -->
                    <div class="col-xs-12 col-sm-6 col-md-4 card-wrap">
                        <div class="bed-card regular">
                            <div class="bed-card-header">
                                <div class="bed-icon">
                                    <i class="fa fa-bed"></i>
                                </div>
                                <h5 class="bed-title">Isolation Beds</h5>
                            </div>
                            <div class="bed-stats">
                                <div class="stat-box available">
                                    <p class="stat-number" id="isolation_vacant">0</p>
                                    <p class="stat-label">Available</p>
                                </div>
                                <div class="stat-box occupied">
                                    <p class="stat-number" id="isolation_occupied">0</p>
                                    <p class="stat-label">Occupied</p>
                                </div>
                            </div>
                            <div class="total-beds">
                                <strong id="isolation_total">Total:</strong> beds
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- COVID Beds -->
                    <div class="col-xs-12 col-sm-6 col-md-4 card-wrap">
                        <div class="bed-card regular">
                            <div class="bed-card-header">
                                <div class="bed-icon">
                                    <i class="fa fa-bed"></i>
                                </div>
                                <h5 class="bed-title">Covid Beds</h5>
                            </div>
                            <div class="bed-stats">
                                <div class="stat-box available">
                                    <p class="stat-number" id="covid_available">0</p>
                                    <p class="stat-label">Available</p>
                                </div>
                                <div class="stat-box occupied">
                                    <p class="stat-number" id="covid_occupied">0</p>
                                    <p class="stat-label">Occupied</p>
                                </div>
                            </div>
                            <div class="total-beds">
                                <strong id="covid_total">Total:</strong> beds
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Mechanical Ventilators -->
                    <div class="col-xs-12 col-sm-6 col-md-4 card-wrap">
                        <div class="bed-card regular">
                            <div class="bed-card-header">
                                <div class="bed-icon">
                                    <i class="fa fa-bed"></i>
                                </div>
                                <h5 class="bed-title">Mechanical Ventilators</h5>
                            </div>
                            <div class="bed-stats">
                                <div class="stat-box available">
                                    <p class="stat-number" id="mecha_vacant">0</p>
                                    <p class="stat-label">Available</p>
                                </div>
                                <div class="stat-box occupied">
                                    <p class="stat-number" id="mecha_used">0</p>
                                    <p class="stat-label">Occupied</p>
                                </div>
                            </div>
                            <div class="total-beds">
                                <strong id="mecha_total">Total:</strong> beds
                            </div>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                <!-- </div> -->
            </div>
            <!-- <hr class="section-separator"> -->
            <!-- STATISTICS SECTION -->
            <div class="statistics-section" id="statistics" style="display: none;">
                <div class="statistics-header">
                    <h5>📊 Bed Occupancy Statistics</h5>
                    <div class="time-filter">
                        <button class="btn btn-default btn-sm active" onclick="updateChart('weekly')">Weekly</button>
                        <button class="btn btn-default btn-sm" onclick="updateChart('monthly')">Monthly</button>
                        <button class="btn btn-default btn-sm" onclick="updateChart('yearly')">Yearly</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="bedStatisticsChart"></canvas>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="online-container">
                        @foreach ($facilities as $facility)
                        @php
                            $percentage = $facility->occupancy_percentage ?? 0;
                            if ($percentage > 85) {
                                $statusClass = 'critical';
                            } elseif ($percentage > 60) {
                                $statusClass = 'warning';
                            } else {
                                $statusClass = 'safe';
                            }
                        @endphp
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-4 card-wrap">
                            <div class="facility-card {{ $statusClass }}">
                                <div class="bed-card1 regular">
                                    <div class="bed-card-header">
                                        <div class="bed-icon"><i class="fa fa-building"></i></div>
                                        <h5 class="bed-title">{{ $facility->name }}</h5>
                                    </div>
                                    <div class="stats-wrapper">
                                        <div class="stat-box1 {{$statusClass}}">
                                            <div class="stat-number">{{ $facility->total_vacant }}</div>
                                            <div class="stat-label">Available</div>
                                        </div>
                                        <div class="divider"></div>
                                        <div class="stat-box1 {{$statusClass}}" style="position: relative;">
                                            <span class="occupancy-badge {{ $statusClass }}" style="
                                                position: absolute;
                                                top: -10px;
                                                right: -10px;
                                                font-size: 11px;
                                                font-weight: bold;
                                                padding: 2px 7px;
                                                border-radius: 20px;
                                                color: white;
                                            ">{{ $percentage }}%</span>
                                            <div class="stat-number">{{ $facility->total_occupied }}</div>
                                            <div class="stat-label">Occupied</div>
                                        </div>
                                    </div>
                                    <div class="total-beds">Total Beds: <strong>{{ $facility->total_beds }}</strong></div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar {{ $statusClass }}" role="progressbar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="k-depts">
                                        <span class="kd er">ER - {{ $facility->er_percentage }}%</span>
                                        <span class="kd icu">ICU - {{ $facility->icu_percentage }}%</span>
                                        <!-- <span class="kd iso">ISO 25%</span>
                                        <span class="kd er">MECHA 25%</span>
                                        <span class="kd iso">REG BEDS 25%</span>
                                        <span class="kd er">COVID 25%</span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="sidebar" class="col-xs-12 col-sm-3">
            <!-- Filter -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Filter</strong>
                </div>
                <div class="panel-body">
                    <form class="form" method="GET" id="filterForm">
                        <!-- Province -->
                        <div class="form-group">
                            <label>Province</label>
                            <select name="province" class="form-control" onchange="onChangeProvince($(this).val())">
                                <option value="">Select All Province</option>
                                @foreach($province as $pro)
                                <option value="{{ $pro->id }}" <?php if (isset($province_select)) {
                                                                    if ($pro->id == $province_select) echo 'selected';
                                                                } ?>>{{ $pro->description }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Facility -->
                        <div class="form-group">
                            <label>Facility</label>
                            <select name="facility" id="facility" class="select2" onchange="">
                                <option value="{{ $facility_select }}">Select All Facility</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Waitlist -->
            <!-- <div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Waitlist</strong>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="panel panel-danger waitlist-card">
                                    <div class="panel-heading text-center">
                                        <strong>Emergency Room (ER)</strong>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3 id="emergency_wait"></h3>
                                        <p>Waiting for Bed</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="panel panel-warning waitlist-card">
                                    <div class="panel-heading text-center">
                                        <strong>ICU</strong>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3 id="icu_wait"> </h3>
                                        <p>Waiting for Bed</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Live Updates -->
            <div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>Live Updates</strong>
                    </div>
                    <div class="panel-body">
                        <p>⚠️ 5 beds nearing full capacity</p>
                        <p>⚠️ 2 ICU beds under maintenance</p>
                        <p>⚠️ 1 emergency slot reserved</p>
                        <p>⚠️ 5 beds nearing full capacity</p>
                        <p>⚠️ 2 ICU beds under maintenance</p>
                        <p>⚠️ 1 emergency slot reserved</p>
                        <p>⚠️ 5 beds nearing full capacity</p>
                        <p>⚠️ 2 ICU beds under maintenance</p>
                        <p>⚠️ 1 emergency slot reserved</p>
                        <p>⚠️ 5 beds nearing full capacity</p>
                        <p>⚠️ 2 ICU beds under maintenance</p>
                        <p>⚠️ 1 emergency slot reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var timeDisplay = document.getElementById("time");

    function refreshTime() {
        var dateString = new Date().toLocaleString("en-US");
        var formattedString = dateString.replace(", ", " - ");
        timeDisplay.innerHTML = formattedString;
    }
    setInterval(refreshTime, 1000);

    // Chart configuration
    const ctx = document.getElementById('bedStatisticsChart').getContext('2d');

    // Sample data for different time periods
    const chartData = {
        weekly: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            available: [48, 45, 42, 47, 45, 50, 52],
            occupied: [72, 75, 78, 73, 75, 70, 68]
        },
        monthly: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            available: [50, 47, 45, 43],
            occupied: [70, 73, 75, 77]
        },
        yearly: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            available: [55, 52, 48, 45, 42, 40, 43, 45, 47, 46, 44, 45],
            occupied: [65, 68, 72, 75, 78, 80, 77, 75, 73, 74, 76, 75]
        }
    };

    let currentChart = null;

    function createChart(period) {
        const data = chartData[period];

        if (currentChart) {
            currentChart.destroy();
        }

        currentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                        label: 'Available Beds',
                        data: data.available,
                        backgroundColor: 'rgba(46, 204, 113, 0.8)',
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    },
                    {
                        label: 'Occupied Beds',
                        data: data.occupied,
                        backgroundColor: 'rgba(231, 76, 60, 0.8)',
                        borderColor: 'rgba(231, 76, 60, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' beds';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 120,
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function updateChart(period) {
        // Update button states
        const buttons = document.querySelectorAll('.time-filter button');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');

        // Create new chart
        createChart(period);
    }

    // Initialize with weekly view
    createChart('weekly');

    @if($province_select)
    onChangeProvince("<?php echo $province_select; ?>");
    @endif

    function onChangeProvince($province_id) {
        $('.loading').show();
        if ($province_id) {
            var url = "{{ url('bed_tracker/select/facility') }}";
            $.ajax({
                url: url + '/' + $province_id,
                type: 'GET',
                success: function(data) {
                    $("#facility").select2("val", "");
                    $('#facility').empty()
                        .append($('<option>', {
                            value: '',
                            text: 'Select All Facility'
                        }));
                    var facility_select = "<?php echo $facility_select; ?>";
                    jQuery.each(data, function(i, val) {
                        $('#facility').append($('<option>', {
                            value: val.id,
                            text: val.name
                        }));
                    });
                    $('#facility option[value="' + facility_select + '"]').attr("selected", "selected");
                    $('.loading').hide();
                },
                error: function() {
                    $('#serverModal').modal();
                }
            });
        } else {
            $('.loading').hide();
            $("#facility").select2("val", "");
            $('#facility').empty()
                .append($('<option>', {
                    value: '',
                    text: 'Select All Facility'
                }));
        }
    }

    $('#filterForm').on('submit', function(e){
        e.preventDefault();

        const province = $('#filterForm select[name="province"]').val();
        const facility = $('#facility').val();

        if(!facility) return; // nothing selected

        $.ajax({
            url: "{{ url('bed') }}/" + facility,
            type: 'GET',
            success: function(response){
                // Show the cards and hide multi-facility view
                $('#specific-facility-cards').show();
                $('#statistics').show();
                $('.online-container').hide();

                // Update facility name
                $('#facility_name').text(response.name);

                // ===== NON-COVID BEDS =====
                    let beds_occupied = Number(response.beds_non_occupied ?? 0);
                    let beds_vacant = Number(response.beds_non_vacant ?? 0);
                    let beds_total = beds_occupied + beds_vacant;

                    $('#beds_total').text(beds_total);
                    $('#beds_occupied').text(beds_occupied);
                    $('#beds_vacant').text(beds_vacant);

                // ===== EMERGENCY BEDS =====
                    let er_occupied = Number(response.emergency_room_non_occupied ?? 0) + Number(response.emergency_room_covid_occupied ?? 0);
                    let er_vacant = Number(response.emergency_room_non_vacant ?? 0) + Number(response.emergency_room_covid_vacant ?? 0);
                    let er_total = er_occupied + er_vacant;

                    $('#er_total').text(er_total);
                    $('#er_occupied').text(er_occupied);
                    $('#er_vacant').text(er_vacant);

                // ===== ICU =====
                    let icu_occupied = Number(response.icu_non_occupied ?? 0) + Number(response.icu_covid_occupied ?? 0);
                    let icu_vacant = Number(response.icu_non_vacant ?? 0) + Number(response.icu_covid_vacant ?? 0);
                    let icu_total = icu_occupied + icu_vacant;

                    $('#icu_total').text(icu_total);
                    $('#icu_occupied').text(icu_occupied);
                    $('#icu_vacant').text(icu_vacant);

                // ===== ISOLATION =====
                    let isolation_occupied = Number(response.isolation_non_occupied ?? 0) + Number(response.isolation_covid_occupied ?? 0);
                    let isolation_vacant = Number(response.isolation_non_vacant ?? 0) + Number(response.isolation_covid_vacant ?? 0);
                    let isolation_total = isolation_occupied + isolation_vacant;

                    $('#isolation_total').text(isolation_total);
                    $('#isolation_occupied').text(isolation_occupied);
                    $('#isolation_vacant').text(isolation_vacant);

                 // ===== COVID BEDS =====
                    let covid_occupied = Number(response.beds_covid_occupied ?? 0);
                    let covid_vacant = Number(response.beds_covid_vacant ?? 0);
                    let covid_total = covid_occupied + covid_vacant;

                    $('#covid_available').text(covid_vacant);
                    $('#covid_occupied').text(covid_occupied);
                    $('#covid_total').text(covid_total);

                // ===== MECHANICAL VENTILATORS =====
                    let mecha_used = Number(response.mechanical_used_covid ?? 0) + Number(response.mechanical_used_non ?? 0);
                    let mecha_vacant = Number(response.mechanical_vacant_non ?? 0) + Number(response.mechanical_vacant_covid ?? 0);
                    let mecha_total = mecha_used + mecha_vacant;

                    $('#mecha_total').text(mecha_total);
                    $('#mecha_used').text(mecha_used);
                    $('#mecha_vacant').text(mecha_vacant);

                    let emergency_wait = Number(response.emergency_room_covid_wait ?? 0) + Number(response.emergency_room_non_wait ?? 0);
                    let icu_wait = Number(response.icu_covid_wait ?? 0) + Number(response.icu_non_wait ?? 0);

                    $('#emergency_wait').text(emergency_wait);
                    $('#icu_wait').text(icu_wait);

                // ===================
                // Update URL without reloading
                // ===================
                const encodedProvince = encodeURIComponent(province);
                const encodedFacility = encodeURIComponent(facility);
                history.pushState(null, null, `?province=${encodedProvince}&facility=${encodedFacility}`);
            },
            error: function(){
                alert('Error loading data');
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {

    const params = new URLSearchParams(window.location.search);

    const province = params.get("province");
    const facility = params.get("facility");

    if (facility) {
        // Hide all facilities view immediately
        $('.online-container').hide();
        $('#specific-facility-cards').show();
        $('#statistics').show();
        // Set dropdown values
        $('#filterForm select[name="province"]').val(province);
        $('#facility').val(facility);

        // Trigger form submit automatically
        $('#filterForm').trigger('submit');
    }

});
    // -------------------------------
    // 3️⃣ Trigger submit when facility changes
    // -------------------------------
    $('#facility').on('change', function(){
        $('#filterForm').submit();
    });

    $('#toggleSidebar').click(function () {
        if ($('#sidebar').hasClass('col-sm-3')) {
            $('#sidebar')
                .removeClass('col-sm-3')
                .css('display', 'none');
            $('#main')
                .removeClass('col-sm-9')
                .addClass('col-sm-12');
        } else {
            $('#sidebar')
                .addClass('col-sm-3')
                .css('display', 'block');
            $('#main')
                .removeClass('col-sm-12')
                .addClass('col-sm-9');
        }
    });
</script>
@endsection