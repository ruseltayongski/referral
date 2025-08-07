<?php $user = Session::get('auth'); ?>
<style>
    .mobile-view {
        display: none;
        visibility: hidden;
    }

    @media only screen and (max-width: 720px) {
        .file-upload {
            background-color: #ffffff;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
        }

        .web-view {
            display: none;
            visibility: hidden;
        }

        .mobile-view {
            display: block;
            visibility: visible;
        }
    }

    #telemedicine {
        border-color:#00a65a;
        border: none;
        padding: 7px;
    }
    #telemedicine:hover {
        background-color: lightgreen;
    }

    .patient-info-header {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .patient-info-header h3 {
        margin-bottom: 15px;
        color: #495057;
    }

    .patient-info-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .patient-info-item {
        flex: 1;
        min-width: 200px;
        margin-right: 20px;
    }

    .patient-info-label {
        font-weight: bold;
        color: #6c757d;
    }

    .patient-info-value {
        color: #495057;
        margin-left: 5px;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    .history-table th,
    .history-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .history-table th {
        background-color: #4F9B83;
        color: white;
        font-weight: bold;
    }

    .history-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .history-table tr:hover {
        background-color: #f5f5f5;
    }

    .btn-view-pmr {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 12px;
    }

    .btn-view-pmr:hover {
        color: white;
        text-decoration: none;
    }

    /* Modal Styles */
    .pmr-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .pmr-modal-content {
        background-color: #fefefe;
        margin: 2% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 90%;
        max-width: 1200px;
        border-radius: 5px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .pmr-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }

    .pmr-close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .pmr-close:hover {
        color: black;
    }

    .form-label {
        font-weight: bold;
        color: #495057;
    }

    .referral-table {
        margin-bottom: 20px;
    }
</style>

<?php
use App\Facility;
use App\Icd;
use App\ReasonForReferral;
use App\Models\PregnantForm;

// Get the first patient record for header display
$firstPatient = null;

if (!empty($emr_forms_data)) {
    $firstRecord = $emr_forms_data[0];
    if ($firstRecord['normal']) {
        $firstPatient = $firstRecord['normal']['form'];
    } elseif ($firstRecord['newNormal']) {
        $firstPatient = $firstRecord['newNormal']['form'];
    } elseif ($firstRecord['pregnant']) {
        $firstPatient = $firstRecord['pregnant']['form']['pregnant'];
    } elseif ($firstRecord['newPregnant']) {
        $firstPatient = $firstRecord['newPregnant']['form']['pregnant'];
       //dd($firstPatient);
    }
}
?>
@if(empty($emr_forms_data))
<div class="alert alert-info" style="color: #0d6efd !important;">
    <i class="bi bi-info-circle"></i> No referral history found for this patient.
</div>
<?php return; ?>
@endif

@include('include.header_form', ['optionHeader' => 'referred'])<br>

@if($firstPatient)
<div class="patient-info-header">
    <h3>Patient Information</h3>
    <div class="patient-info-row">
        <div class="patient-info-item">
            <span class="patient-info-label">Patient Name:</span>
            <span class="patient-info-value">{{ $firstPatient->patient_name ?? $firstPatient->woman_name }}</span>
        </div>
    </div>
    <div class="patient-info-row">
       <div class="patient-info-item">
            <span class="patient-info-label">Patient Address:</span>
            <span class="patient-info-value">{{ $firstPatient->patient_address }}</span>
        </div>
    </div>
    <div class="patient-info-row">
        <div class="patient-info-item">
            <span class="patient-info-label">Status:</span>
            <span class="patient-info-value">{{ $firstPatient->patient_status ?? $firstPatient->civil_status ?? 'N/A' }}</span>
        </div>
         <div class="patient-info-item">
            <span class="patient-info-label">Sex:</span>
            <span class="patient-info-value">{{ $firstPatient->patient_sex ?? $firstPatient->sex }}</span>
        </div>
         <div class="patient-info-item">
            <span class="patient-info-label">Age:</span>
            <span class="patient-info-value">
                @if($firstPatient->woman_age)

                    {{$firstPatient->woman_age}}

                @else
                    <?php
                        $patient_age = \App\Http\Controllers\ParamCtrl::getAge($firstPatient->dob);
                        $age_type = 'y';
                        if ($patient_age == 0) {
                            $patient_age = \App\Http\Controllers\ParamCtrl::getMonths($firstPatient->dob);
                            $age_type = 'm';
                        }
                    ?>
                    @if($age_type == "y")
                        {{ $patient_age }} {{ $patient_age == 1 ? 'year' : 'years' }} old
                    @else
                        {{ $patient_age['month'] }} {{ $patient_age['month'] == 1 ? 'mo' : 'mos' }}, {{ $patient_age['days'] }} {{ $patient_age['days'] == 1 ? 'day' : 'days' }} old
                    @endif
                @endif    
            </span>
        </div>
    </div>
</div>
@endif
<!-- Referral History Table -->
<div class="history-section">
    <h4>Referral History</h4>
    <table class="table history-table">
        <thead>
            <tr>
                <th>Date Referred</th>
                <th>Referring Facility</th>
                <th>Referred Facility</th>
                <th>Category</th>
                <th>Type</th>
                <th>Date Discharged</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

            @foreach($emr_forms_data as $index => $emr_record)
                <?php
                    $currentData = null;
                    $formData = null;
                    // Determine which data structure to use
                    if ($emr_record['normal']) {
                        $currentData = $emr_record['normal'];
                        $formData = $currentData['form'];
                    } elseif ($emr_record['newNormal']) {
                        $currentData = $emr_record['newNormal'];
                        $formData = $currentData['form'];
                    } elseif ($emr_record['pregnant']) {
                        $currentData = $emr_record['pregnant'];
                        $formData = $currentData['form']['pregnant'] ?? null;
                    } elseif ($emr_record['newPregnant']) {
                        $currentData = $emr_record['newPregnant'];
                        $formData = $currentData['form']['pregnant'] ?? null;
                    }
                ?>
             
                @if($currentData && $formData)
                <tr>
                    <!-- Date Referred -->
                    <td>
                        @if($emr_record['type'] == "normal")
                            @if(isset($formData->patient_referred_date))
                                {{ \Carbon\Carbon::parse($formData->patient_referred_date)->format('M d, Y') }}
                                <br><small><i>({{ \Carbon\Carbon::parse($formData->patient_referred_date)->format('h:i A') }})</i></small>
                            @elseif(isset($formData->date_referred))
                                {{ \Carbon\Carbon::parse($formData->date_referred)->format('M d, Y') }}
                                <br><small><i>({{ \Carbon\Carbon::parse($formData->date_referred)->format('h:i A') }})</i></small>
                            @else
                                <span class="text-muted">Not Available</span>
                            @endif
                        @else
                            @if(isset($formData->referred_date))
                                  {{ \Carbon\Carbon::parse($formData->referred_date)->format('M d, Y') }}
                                <br><small><i>({{ \Carbon\Carbon::parse($formData->referred_date)->format('h:i A') }})</i></small>
                            @elseif(isset($formData->date_referred))
                                {{ \Carbon\Carbon::parse($formData->date_referred)->format('M d, Y') }}
                                <br><small><i>({{ \Carbon\Carbon::parse($formData->date_referred)->format('h:i A') }})</i></small>
                            @else
                                <span class="text-muted">Not Available</span>
                            @endif
                        @endif
                    </td>
                    
                    <!-- Referring Facility -->
                    <td>
                        @if($emr_record['type'] == "normal")
                            @if(isset($formData->patient_refer_from))
                                {{ Facility::find($formData->patient_refer_from)->name ?? 'Unknown' }}
                            @elseif(isset($currentData['referring_fac_id']))
                                {{ Facility::find($currentData['referring_fac_id'])->name ?? 'Unknown' }}
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        @else
                            @if(isset($formData->pregnant_refer))
                                {{ Facility::find($formData->pregnant_refer)->name ?? 'Unknown' }}
                            @elseif(isset($currentData['referring_fac_id']))
                                {{ Facility::find($currentData['referring_fac_id'])->name ?? 'Unknown' }}
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        @endif
                    </td>
                    
                    <!-- Referred To Facility -->
                    <td>
                        @if($emr_record['type'] == "normal")
                            @if(isset($formData->referred_fac_id))
                                {{ Facility::find($formData->referred_fac_id)->name ?? 'Unknown' }}
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        @else
                            @if(isset($formData->referred_facility_id))
                                {{ Facility::find($formData->referred_facility_id)->name ?? 'Unknown' }}
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        @endif
                    </td>
                    
                    <!-- Category -->
                    <td>
                        @if(isset($formData->telemedicine) && $formData->telemedicine == 1)
                            <!-- <span class="label label-info" style="padding: 5px;">Telemedicine</span> -->
                              <span class="text-success" style="padding: 5px;">Telemedicine</span>
                        @else
                            <span class="text-success" style="padding: 5px;">Referral</span>
                        @endif
                    </td>
                    
                    <!-- Type -->
                    <td>
                        <span class="text-warning" style="padding: 5px;">{{ ucfirst($emr_record['type']) }}</span>
                        <!-- <br><small class="text-muted">{{ $emr_record['formtype'] }}</small> -->
                    </td>
                     
                    <!-- Date Discharged -->
                    <td>
                     @if(!empty($emr_record['date_discharged']))
                        {{ \Carbon\Carbon::parse($emr_record['date_discharged'])->format('M d, Y') }}
                        <br><small><i>({{ \Carbon\Carbon::parse($emr_record['date_discharged'])->format('h:i A') }})</i></small>
                    @else
                        <span class="text-muted">Not Available</span>
                    @endif
                    </td>
                    
                    <!-- Action -->
                    <td>
                        <div style="display:flex;gap:5px;">
                            <form method="GET" action="{{ url('doctor/referred') }}">
                                @csrf
                                <input type="hidden" name="referredCode" value="{{ $formData->code }}">
                                <button type="submit" class="btn btn-success btn-sm" formtarget="_blank">
                                    <i class="bi bi-search"></i> Track
                                </button>
                            </form>
                            <button type="button" class="btn btn-warning btn-sm btn-view-pmr"
                                data-type="{{ $emr_record['type'] ?? '' }}"
                                data-formtype="{{ $emr_record['formtype'] ?? '' }}"
                                data-formid="{{ $emr_record['formId'] ?? '' }}"
                                onclick="openPMRModal(this)"
                                style="padding: 5px;">
                                <i class="bi bi-eye"></i> PMR
                            </button>
                        </div>
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>


<button class="btn-sm btn-default btn-flat" data-dismiss="modal" id="closeReferralForm"><i class="fa fa-times"></i> Close</button>


