<?php
$error = \Illuminate\Support\Facades\Input::get('error');
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')

    <div class="col-md-12">
        <div class="jim-content">   

            @if($error)
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fa fa-times-circle me-2 fs-5"></i>
                    <span><strong>Error:</strong> Switching account failed. Please try again.</span>
                </div>
            @endif

            <h3 class="mb-4 border-bottom pb-2" >Chronic Kidney Disease (CKD) Patients</h3>
            <!-- Search Input Only -->
            <div class="d-flex justify-content-start align-items-center mb-3" style="margin-bottom: 15px; width: 250px">
                <input type="text" id="searchInput" class="form-control w-50" placeholder="Search by name or contact...">
            </div>

            <div class="table-responsive">
                <table id="ckd-table" class="table modern-table shadow-sm">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Contact No.</th>
                            <th>Birth Date</th>
                            <th>Age</th>
                            <th>Sex</th>
                            <th>Civil Status</th>
                            <th>Indigenous</th>
                            <th>Employment</th>
                            <th>Assessment Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ckdTableBody">
                        @forelse ($data['patient'] as $index => $patient)
                            <tr>
                                <td>{{ $patient['first_name'] ?? 'N/A' }}</td>
                                <td>{{ $patient['middle_name'] ?? 'N/A' }}</td>
                                <td>{{ $patient['last_name'] ?? 'N/A' }}</td>
                                <td>{{ $patient['contact_no'] ?? 'N/A' }}</td>
                                <td>{{ $patient['birth_date'] ?? 'N/A' }}</td>
                                <td>{{ $patient['age'] ?? 'N/A' }}</td>
                                <td>
                                    @if($patient['sex'] == 'Male')
                                        ♂️ Male
                                    @elseif($patient['sex'] == 'Female')
                                        ♀️ Female
                                    @else
                                        {{ $patient['sex'] ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusIcons = [
                                            'Single' => '🧍',
                                            'Married' => '💍',
                                            'Widowed' => '🖤',
                                            'Divorced' => '💔',
                                        ];
                                        $civilStatus = $patient['civil_status'] ?? 'N/A';
                                    @endphp
                                    {{ $statusIcons[$civilStatus] ?? '' }} {{ $civilStatus }}
                                </td>
                                <td>{{ $patient['indigenous_status'] ?? 'N/A' }}</td>
                                <td>{{ $patient['employment_status'] ?? 'N/A' }}</td>
                                <td>{{ $patient['date_of_assessment'] ?? 'N/A' }}</td>
                                <td>
                                    <a href="#"
                                        class="btn btn-primary btn-xs ckd_info"
                                        data-ckd_id="{{ $patient['id'] }}"
                                        data-first="{{ $patient['first_name'] }}"
                                        data-middle="{{ $patient['middle_name'] }}"
                                        data-last="{{ $patient['last_name'] }}"
                                        data-birth="{{ $patient['birth_date'] }}"
                                        data-contact="{{ $patient['contact_no'] }}"
                                        data-sex="{{ $patient['sex'] }}"
                                        data-civil_status= "{{$patient['civil_status']}}"
                                        data-barangay_id="{{$patient['barangay']['barangay_id']}}">
                                        <i class="fa fa-stethoscope"></i> Refer
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-muted">No patient records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <!-- Pagination -->
                        @if ($data['patient']->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {!! $data['patient']->appends(request()->except('page'))->links() !!}
                            </div>
                        @endif
            </div>
        </div>
    </div> 



<style>
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        font-family: 'Segoe UI', sans-serif;
    }

    .modern-table td, .modern-table th {
    white-space: nowrap; /* Prevent text from wrapping */
    }
    
    .modern-table thead th {
        /* position: sticky; */
        top: 0;
        z-index: 1;
        background: #59ab91;
        color: #fff;
        font-weight: 600;
        padding: 0.75rem;
        font-size: 14px;
        text-transform: uppercase;
    }

    .modern-table td {
        background: #f9fafb;
        color: #1f2937;
        padding: 0.75rem;
        font-size: 14px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modern-table tbody tr:hover td {
    background-color:rgba(206, 241, 231, 0.33) !important;
    transition: background-color 0.3s ease, color 0.3s ease;
    }

    input#searchInput {
        border-radius: 6px;
        border: 1px solid #ccc;
        padding: 8px 12px;
    }

    @media print {
        .btn, input, .alert, h3, .mb-3 {
            display: none !important;
        }
    }

    .mobile-view {
            display: none;
            visibility: hidden;
        }

    #ckd-table th,
    #ckd-table td {
        white-space: nowrap; /* Prevent cell content from wrapping */
    }



    /* Sticky Action (last column, 12th) */
    #ckd-table th:nth-child(12)
    {
        position: sticky;
        right: 0;
        z-index: 2;
        background: #59ab91;
        color: #fff;
    }
    #ckd-table td:nth-child(12) {
        position: sticky;
        right: 0;
        z-index: 2;
    }
</style>

<style>
    .ui-autocomplete {
        background-color: white;
        width: 20%;
        z-index: 1100;
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
    }

    .ui-menu-item {
        cursor: pointer;
    }

    .file-upload {
        background-color: #ffffff;
        /*width: 100%!*200px*!;*/
        margin: 0 auto;
        /* padding: 20px; */
        padding: 10px;
        border: 1px dashed dimgrey;
    }
    .file-upload_ {
        background-color: #ffffff;
        /*width: 100%!*200px*!;*/
        margin: 0 auto;
        padding: 20px;
        border: 1px dashed dimgrey;
    }

    .file-upload-btn {
        width: 100%;
        margin: 0;
        color: #fff;
        background: #1FB264;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #15824B;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 700;
    }

    .file-upload-btn:hover {
        background: #1AA059;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .file-upload-btn:active {
        border: 0;
        transition: all .2s ease;
    }

    .file-upload-content {
        display: none;
        text-align: center;
    }
    
    .file-upload-input {
        position: absolute;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        outline: none;
        opacity: 0;
        cursor: pointer;
    }

    .image-upload-wrap {
        margin-top: 20px;
        border: 4px dashed #1FB264;
        position: relative;
    }
  

    .image-dropping,
    .image-upload-wrap:hover {
        background-color:
            /*#6ab155*/
            #1FB264;
        border: 4px dashed #ffffff;
    }

    .image-title-wrap {
        padding: 0 15px 15px 15px;
        color: #222;
    }
 

    .drag-text {
        text-align: center;
    }

    .drag-text h3 {
        font-weight: 100;
        text-transform: uppercase;
        color: #15824B;
        padding: 60px 0;
    }

    .file-upload-image {
        max-height: 75%;
        max-width: 75%;
        margin: auto;
        padding: 20px;
    }


    .remove-image {
        width: 100%;
        margin: 0;
        color: #fff;
        background: #cd4535;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #b02818;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 600;
    }

    .remove-image_ {
        width: 100%;
        margin: 0;
        color: #fff;
        background: #cd4535;
        border: none;
        padding: 10px;
        border-radius: 4px;
        border-bottom: 4px solid #b02818;
        transition: all .2s ease;
        outline: none;
        text-transform: uppercase;
        font-weight: 600;
    }

    .remove-image:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }
    .remove-image_:hover {
        background: #c13b2a;
        color: #ffffff;
        transition: all .2s ease;
        cursor: pointer;
    }

    .remove-image:active {
        border: 0;
        transition: all .2s ease;
    }
    .remove-image_:active {
        border: 0;
        transition: all .2s ease;
    }

    .mobile-view {
        display: none;
        visibility: hidden;
    }

    .container-referral {
        border: 1px solid lightgrey;
        width: 100%;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
        padding-right: 5px;
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

    .remove-icon-btn{
        position: absolute;
        top: -2px; /* Adjust as needed */
        right: 12px; /* Adjust as needed */
        background: transparent;
        border: none;
        font-weight: bold;
        font-size: 24px;
        color: #ff0000; /* Optional: Trash icon color */
        font-size: 18px; /* Optional: Adjust icon size */
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .remove-icon-btn_{
        position: absolute;
        top: -2px; /* Adjust as needed */
        right: 12px; /* Adjust as needed */
        background: transparent;
        border: none;
        font-weight: bold;
        font-size: 24px;
        color: #ff0000; /* Optional: Trash icon color */
        font-size: 18px; /* Optional: Adjust icon size */
        cursor: pointer;
        transition: transform 0.2s;
    }
    .remove-icon-btn:hover {
        transform: scale(1.1); /* Slightly increase size on hover */
    }
    .remove-icon-btn_:hover {
        transform: scale(1.1); /* Slightly increase size on hover */
    }
    .remove-icon-btn i {
        pointer-events: none; /* To ensure the button handles the click, not the icon */
    }

      /********************* for file upload NORMAL FORM *********************/
    .fileuploadBackground{
        background-color: rgba(0, 0, 0, 0.7); 
    }
   .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.7); 
    }

    /* Center the modal vertically */
    .modal-dialog-centered {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }

    @media (min-width: 576px) {
        .modal-dialog-centered {
            min-height: calc(100% - 3.5rem);
        }
    }

    /* Full width modal content */
    .modal-content {
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
    }

    /* Uniform file preview container */
    .file-preview-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
    }

    /* Carousel styling */
    #filePreviewCarousel {
        flex-grow: 1;
        margin: 0 15px;
        max-width: 800px;
    }

    /* Navigation buttons */
    .nav-button {
        background: none;
        border: none;
        font-size: 2rem;
        color: #333;
        opacity: 0.7;
        transition: opacity 0.3s;
    }

    .nav-button:hover {
        opacity: 1;
    }

    .prev-icon-file,
    .next-icon-file {
        background-color: #007bff;
        color: white;
        border-radius: 50%;
        padding: 10px;
    }

    /* Ensure uniform preview display */
    .carousel-item {
        text-align: center;
    }

    .carousel-item img, 
    .carousel-item embed {
        max-width: 100%;
        max-height: 70vh;
        margin: auto;
        display: block;
        width: auto;
        height: auto;
    }

    /* Set default size for different file types */
    .pdf-preview,
    .image-preview {
        width: 100%;
        height: 600px; /* Standard height for all previews */
    }

    /* Word/Excel file unsupported message */
    .file-not-supported {
        text-align: center;
        margin-top: 20px;
        color: #dc3545;
    }

    div#filePreviewModal {
        background-color: rgba(0,0,0,0.99);
    }

    .notice_category{
        font-size: 20px;
        font-weight: bold;
    }

    .modal-dialog1{
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

@include('modal.pregnantModal')
@include('modal.choose_version')
@include('modal.revised_pregnant_form')
@include('modal.pregnant_form_editable')
@include('modal.pregnant_form_editable_walkin')
@include('modal.normal_form_editable')
@include('modal.normal_form_editable_walkin')
@include('modal.revised_normal_form')
@endsection

@section('js')
@include('script.filterMuncity')

<script>
    
  // Keep the existing event listener for the search functionality
document.getElementById('searchInput').addEventListener('keyup', function () {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#ckdTableBody tr");
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
    });
});

// Store patient data globally to be accessed by the form after successful crossmatch
let currentPatientData = null;
let processedPatientData = {
            ckd_id:null,
            first_name: null,
            middle_name: null,
            last_name: null,
            birth_date: null,
            contact_no: null,
            sex: null,
            civil_status: null,
            barangay_id: null,
    };
let patientCode = null;

function fetchDataFromDb(patient_id){
    fetch("/referral/get-patient-code/" + patient_id)
    .then(response => response.json())
    .then(data => {
        console.log('Raw fetch response:', data);
        if (data.patient_code) {
            patientCode = data.patient_code;
            console.log('Patient Saved:', patientCode);
        } else {
            console.error('Error fetching data:', data.message);
        }
    })
}


// Updated event listener for the ckd_info buttons
document.querySelectorAll('.ckd_info').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        // Extract patient data from the button's data attributes
        currentPatientData = {
            ckd_id: this.dataset.ckd_id,
            first_name: this.dataset.first,
            middle_name: this.dataset.middle,
            last_name: this.dataset.last,
            birth_date: this.dataset.birth,
            contact_no: this.dataset.contact,
            sex: this.dataset.sex,
            civil_status: this.dataset.civil_status,
            barangay_id: this.dataset.barangay_id
        };

        // let processedPatientData = {
        //     ckd_id:null,
        //     first_name: null,
        //     middle_name: null,
        //     last_name: null,
        //     birth_date: null,
        //     contact_no: null,
        //     sex: null,
        //     civil_status: null,
        //     barangay_id: null,
        // };

        const payload = new URLSearchParams({
            ckd_id: currentPatientData.ckd_id,
            first_name: currentPatientData.first_name,
            middle_name: currentPatientData.middle_name,
            last_name: currentPatientData.last_name,
            birth_date: currentPatientData.birth_date,
            contact_no: currentPatientData.contact_no,
            sex: currentPatientData.sex,
            civil_status: currentPatientData.civil_status,
            barangay_id: currentPatientData.barangay_id,
            _token: document.querySelector('meta[name="csrf-token"]').content // More reliable CSRF token source
        }).toString();
        
        fetch("{{ url('opcen/ckd/crossmatch') }}?" + payload, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(fetch => {
            if (fetch.match) {
                // Lobibox.notify('success', {
                //     title: 'Match found. Data updated!',
                // });
            } else {
                // Lobibox.notify('success', {
                //     title: 'New patient created!',
                // });
            }
            processedPatientData = {
                patient_id: fetch.data.id,
                first_name: fetch.data.fname,
                middle_name: fetch.data.mname,
                last_name: fetch.data.lname,
                birth_date: fetch.data.dob,
                contact_no: fetch.data.contact,
                sex: fetch.data.sex,
                civil_status: fetch.data.civil_status,
                barangay_id: fetch.data.brgy,
            }
            // Set the form fields before opening the form
            populateFormWithPatientData(processedPatientData);
            processedPatientData_func(fetch.data.id);
            console.log('Patient ID:', fetch.data.id);
            fetchDataFromDb(fetch.data.id);
            // Open the appropriate form
            if (fetch.data.sex == 'Male'){
                
                openNewForms('normal');
            }else if (fetch.data.sex == 'Female'){
                // fetchDataFromDb(fetch.data.id);
                openNewForms('pregnant');
            }
            // console.log(fetch);
           
        })
        .catch(err => {
            console.error(err);
            Lobibox.notify('error', {
                title: 'Error',
                msg: 'Something went wrong processing the request.'
            });
        });
    });
});

// Function to populate the form with patient data
function populateFormWithPatientData(patientData) {
    setTimeout(() => {
        // Select all relevant form modals
        const formElements = document.querySelectorAll('#normalFormModal, #revisednormalFormModal, #normal_form_editable');
        PatientBody(patientData.patient_id);
        formElements.forEach(form => {
            if (!form) return;

            // Set patient name
            const nameFields = form.querySelectorAll('.patient_name');
            nameFields.forEach(field => {
                field.textContent = `${patientData.first_name} ${patientData.middle_name} ${patientData.last_name}`;
            });

            // Set patient ID if there's a hidden input for it
            const patientIdFields = form.querySelectorAll('input[name="patient_id"]');
            patientIdFields.forEach(field => {
                if (field) field.value = patientData.patient_id || '';
            });

            // Set CKD ID if present
            const ckdIdFields = form.querySelectorAll('input[name="ckd_id"]');
            ckdIdFields.forEach(field => {
                if (field) field.value = currentPatientData && currentPatientData.ckd_id ? currentPatientData.ckd_id : '';
            });

            // Set sex
            const sexFields = form.querySelectorAll('.patient_sex, select[name="sex"]');
            sexFields.forEach(field => {
                if (field.tagName === 'SELECT') {
                    const option = Array.from(field.options).find(opt => opt.value === patientData.sex);
                    if (option) field.value = patientData.sex;
                } else {
                    field.value = patientData.sex;
                }
            });

            // Set civil status
            const civilStatusFields = form.querySelectorAll('.civil_status, select[name="civil_status"]');
            civilStatusFields.forEach(field => {
                if (field.tagName === 'SELECT') {
                    const option = Array.from(field.options).find(opt => opt.value === patientData.civil_status);
                    if (option) field.value = patientData.civil_status;
                } else {
                    field.value = patientData.civil_status;
                }
            });

            // Calculate and set age
            let age = calculateAge(patientData.birth_date);
            const ageFields = form.querySelectorAll('.patient_age');
            ageFields.forEach(field => {
                field.textContent = `${age} ${age === 1 ? 'year old' : 'years old'}`;
            });

            // Show/hide age-specific elements
            if (age > 18) {
                const pedFields = form.querySelectorAll('#pedia_show_normal');
                pedFields.forEach(field => { field.style.display = 'none'; });
            } else {
                const pedFields = form.querySelectorAll('#pedia_show_normal');
                pedFields.forEach(field => { field.style.display = 'block'; });
            }

            if (age > 9 && patientData.sex === 'Female') {
                const menarche = form.querySelectorAll('#menarche_show, #menarche_show_normal, #menarche_show_pregnant');
                menarche.forEach(field => { field.style.display = 'block'; });

                const menarcheField = form.querySelector('#menarche');
                if (menarcheField) menarcheField.setAttribute('min', '9');
            } else {
                const menarche = form.querySelectorAll('#menarche_show, #menarche_show_normal, #menarche_show_pregnant');
                menarche.forEach(field => { field.style.display = 'none'; });
            }
        });
    }, 1); // Small delay to ensure the DOM elements are ready
}

// Helper function to calculate age from birth date
function calculateAge(birthDateStr) {
    const birthDate = new Date(birthDateStr);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// Updated openNewForms function
function openNewForms(type) {
    // Get facility_id from server-side
    var referred_facility = "{{ $user->facility_id }}";
    
    console.log("Facility ID: ", type);
    
    if (type == 'pregnant') {
        $('#pregnantModal').modal('show');
        // $('#pregnantchooseVersionModal').modal('hide');
        selectFormTitle("BEmONC/ CEmONC ");
    } else if (type == 'normal') {
        // $('#pregnantModal').modal('hide');
        $('#nonPregnantChooseVersionModal').modal('show');
        selectFormTitle("Clinical");
        $('#baby_show').hide();
    }
    
    handleRefer();
}

// Ensure the existing form functionality works
function handleTelemedicine() {
    document.querySelectorAll(".telemedicine").forEach(el => {
        el.value = 1;
    });
    selectFormTitle("Clinical ");
}

function selectFormTitle(initialTitle) {
    let telemedicine = 0;
    const telemedicineEls = document.querySelectorAll(".telemedicine");
    if (telemedicineEls.length > 0) {
        telemedicine = parseInt(telemedicineEls[0].value);
    }
    
    document.querySelectorAll(".clinical-form-title").forEach(el => {
        if (telemedicine) {
            el.innerHTML = `${initialTitle} Telemedicine Consultation`;
        } else {
            el.innerHTML = `${initialTitle} Referral Form`;
        }
    });
}

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($trigger_ckd_info == true)
            console.log('Triggering CKD Info link...');
            
            const ckdInfoLink = document.querySelector('.ckd_info'); // gets the first element
            if (ckdInfoLink) {
                ckdInfoLink.click();
            } else {
                console.warn('No .ckd_info element found.');
            }
        @endif
    });

    function showPrivacyNotice(modalSelector) {
    
        const cleanModalSelector = modalSelector.replace(/^#/, '');
        const clickedButton = $(event.target);
        const patientId = clickedButton.data('patient_id');
        const modalType = clickedButton.data('type');
        
        // Store data for use after privacy notice acceptance
        window.privacyModalData = {
            modalSelector: '#' + cleanModalSelector,
            patientId: patientId,
            modalType: modalType
        };
        
        // Show the privacy notice modal
        $('#privacyNoticeModal').modal('show');
    }

    $(document).ready(function() {
      
        $('#privacyNoticeModal').on('shown.bs.modal', function () {
            $('#privacyCheckbox').prop('checked', false); 
            $('#acceptPrivacyBtn').prop('disabled', true); 
        });

        $('#privacyCheckbox').change(function () {
            $('#acceptPrivacyBtn').prop('disabled', !this.checked);
        });

        $('#acceptPrivacyBtn').click(function () {
            
            $('#privacyNoticeModal').modal('hide');
            
            const modalData = window.privacyModalData;
            
            setTimeout(function() {
                if (modalData) {
                    const $targetModal = $(modalData.modalSelector);
                    
                    const $patientIdInput = $targetModal.find('input[name="patient_id"]');
                    if ($patientIdInput.length) {
                        $patientIdInput.val(modalData.patientId);
                    }
                    handleTelemedicine();
                    $targetModal.modal('show');
                    window.privacyModalData = null;
                }
            }, 500);
        });

        $(".patient-emergency").removeClass('hidden');
        $(".patient-consultation").removeClass('hidden');

        const telemedicineAppoinmentSlot = decodeURIComponent(new URL(window.location.href).searchParams.get('appointment'));
        var appointment = @json($telemedicine);
        
        //let url = new URL(window.location.href);
        if (telemedicineAppoinmentSlot && telemedicineAppoinmentSlot !== null) {
            $(".Appointment").val(telemedicineAppoinmentSlot);
        }

        if (JSON.parse(telemedicineAppoinmentSlot)) {
            $(".patient-emergency").remove();
        
            setCookie('telemedicineAppointment', telemedicineAppoinmentSlot, 1);
            
            console.log("telemedicineAppoinmentSlot", telemedicineAppoinmentSlot);
            
        }else if(appointment){

            $(".patient-emergency").remove();
            $(".Appointment").val(appointment);

            // $.ajax({
            //     url: "{{ url('pass/appointment') }}",
            //     method: 'POST',
            //     data: {
            //         telemed: appointment,
            //         _token: '{{ csrf_token() }}'  // Include the CSRF token for security
            //     },
            //     success: function(response) {
            //         console.log("Appointment successfully passed to the backend.", appointment);
            //     },
            // });
            console.log("appointment-search", appointment)

        } else {
            $(".patient-consultation").remove();
            $(".Appointment").val('');
            setCookie('telemedicineAppointment', false, 1);
        }
    });

    function handleRefer() {
        $(".telemedicine").val(0);
        selectFormTitle("Clinical ");
    }

    function handleTelemedicine() {
        $(".telemedicine").val(1);
        selectFormTitle("Clinical ");
    }

        function setClinicalFormTile(type) {
            if (type == "pregnant") {
                selectFormTitle("BEmONC/ CEmONC ");
            } else {
                selectFormTitle("Clinical ");
            }
        }




    function selectFormTitle(initialTitle) {
        const telemedicine = parseInt($(".telemedicine").val());
        if (telemedicine) {
            $(".clinical-form-title").html(`${initialTitle} Telemedicine Consultation`);
        } else {
            $(".clinical-form-title").html(`${initialTitle} Referral Form`);
        }
    }

    function promptWalkinPregnant(counter) {
        Lobibox.confirm({
            msg: "Do you want to proceed to walkin?",
            callback: function($this, type, ev) {
                if (type == 'yes') {
                    $('#walkinPregnant' + counter).attr('onclick', "");
                    $('#walkinPregnant' + counter).attr('href', '#pregnantModalWalkIn');
                    $('#walkinPregnant' + counter).click();
                } else {
                    $('#walkinPregnant' + counter).attr('onClick', 'promptWalkinPregnant(' + counter + ')');
                    $('#walkinPregnant' + counter).attr('href', '#');
                }
            }
        });
    }

    $('.cancelWalkin').on('click', function() {
        var counter = "<?php echo $counter; ?>";
        for (var i = 0; i < counter; i++) {
            $('#walkinPregnant' + i).attr('href', '#');
            $('#walkinPregnant' + i).attr('onClick', 'promptWalkinPregnant(' + i + ')');
            $('#walkinNormal' + i).attr('href', '#');
            $('#walkinNormal' + i).attr('onClick', 'promptWalkinNormal(' + i + ')');
        }
    });

    function promptWalkinNormal(counter) {
        selectFormTitle("Clinical ");
        Lobibox.confirm({
            msg: "Do you want to proceed to walkin?",
            callback: function($this, type, ev) {
                if (type == 'yes') {
                    $('#walkinNormal' + counter).attr('onclick', "");
                    $('#walkinNormal' + counter).attr('href', '#normalFormModalWalkIn');
                    $('#walkinNormal' + counter).click();
                } else {
                    $('#walkinNormal' + counter).attr('onClick', 'promptWalkinNormal(' + counter + ')');
                    $('#walkinNormal' + counter).attr('href', '#');
                }
            }
        });
    }

    function PatientBody(patient_id) {
        console.log(patient_id);
        var url = "<?php echo asset('doctor/patient/update'); ?>";
        var json = {
            "patient_id": patient_id,
            "_token": "<?php echo csrf_token(); ?>"
        };
        $.post(url, json, function(result) {
            $(".patient_body").html(result);
        });
    }

    // $(".select2").select2({
    //     width: '100%',
    // });
    
        var referring_facility = "{{ $user->facility_id }}";
        var referred_facility = '';
        var referring_facility_name = $(".referring_name").val();
        var referring_md = "{{ $user->fname }} {{ $user->mname }} {{ $user->lname }}";
        var name,
        age,
        sex,
        address,
        form_type,
        reason,
        patient_id,
        civil_status,
        phic_status,
        phic_id,
        department_id,
        department_name;

    $('.select_facility_walkin').on('change', function() {
        var id = $(this).val();
        referred_facility = "{{ $user->facility_id }}";
        var url = "{{ url('location/facility/') }}";
        referring_facility_name = $(this).find(':selected').data('name');

        $.ajax({
            url: url + '/' + id,
            type: 'GET',
            success: function(data) {
                console.log(data);
                $('.facility_address').html(data.address);
            },
            error: function() {
                $('#serverModal').modal();
            }
        });
    });

    $('.select_department').on('change', function() {
        var id = $(this).val();
        var list = "{{ url('list/doctor') }}";
        if (id) {
            if (referred_facility == 0) {
                referred_facility = "{{ $user->facility_id }}";
            }
            $.ajax({
                url: list + '/' + referred_facility + '/' + id,
                type: 'GET',
                success: function(data) {
                    $('.referred_md').empty()
                        .append($('<option>', {
                            value: '',
                            text: 'Any...'
                        }));
                    jQuery.each(data, function(i, val) {
                        $('.referred_md').append($('<option>', {
                            value: val.id,
                            text: 'Dr. ' + val.fname + ' ' + val.mname + ' ' + val.lname + ' - ' + val.contact
                        }));

                    });
                },
                error: function() {
                    $('#serverModal').modal();
                }
            });
        }
    });
    function processedPatientData_func(patient_id){
        console.log("patient_id params:", patient_id);
        $('.ckd_info').removeClass('hide');
            $.ajax({
                url: "{{ url('doctor/patient/info/') }}/" + patient_id,
                type: "GET",
                success: function(data) {
                    patient_id = data.id;
                    name = data.patient_name;
                    sex = data.sex;
                    age = data.age;
                    civil_status = data.civil_status;
                    phic_status = data.phic_status;
                    phic_id = data.phic_id;
                    address = data.address;

                    $('.patient_name').html(name);
                    $('.patient_address').html(address);
                    $('input[name="phic_status"][value="' + phic_status + '"]').attr('checked', true);
                    $('.phic_id').val(phic_id);
                    $('.patient_sex').val(sex);
                    if (age > 18) {
                        $('#pedia_show_normal').css('display', 'none');
                    } else {
                        $('#pedia_show_normal').css('display', 'block');
                    }
                    if (age > 9 && sex === 'Female'){
                        $('#menarche_show').css('display', 'block');
                        $('#menarche_show_pregnant').css('display', 'block');
                        $('#menarche').attr('min', '9');
                        console.log("show obstetric");
                    } else {
                        $('#menarche_show').css('display', 'none');
                        $('#menarche_show_pregnant').css('display', 'none');
                        console.log("hidden obstetric");
                    }
                    if (data.ageType === 'y') {
                        if (age === 1)
                            $('.patient_age').html(age + " year old");             
                        else
                            $('.patient_age').html(age + " years old"); 

                    } else if (data.ageType === 'm') {
                        var age_str = "";
                        if (age.month === 1)
                            age_str = age.month + " month, ";
                        else
                            age_str = age.month + " months, ";

                        if (age.days === 1)
                            age_str += age.days + " day old";
                        else
                            age_str += age.days + " days old";

                        $('.patient_age').html(age_str);
                    }
                    $('.civil_status').val(civil_status);
                    $('.patient_id').val(patient_id);
                },
                error: function() {
                    $('#serverModal').modal();
                }
            });
    }
   
    function sendNotifierData(age, chiefComplaint, department, diagnosis, patient, sex, referring_hospital, date_referred, patient_code) {
        // Check if Firebase app with name '[DEFAULT]' already exists
        if (!firebase.apps.length) {
            // Your web app's Firebase configuration
            var firebaseConfig = {
                apiKey: "AIzaSyB_vRWWDwfiJVCA7RWOyP4lxyWn5QLYKmA",
                authDomain: "notifier-5e4e8.firebaseapp.com",
                databaseURL: "https://notifier-5e4e8-default-rtdb.firebaseio.com",
                projectId: "notifier-5e4e8",
                storageBucket: "notifier-5e4e8.appspot.com",
                messagingSenderId: "359294836752",
                appId: "1:359294836752:web:87c854779366d0f11d2a95",
                measurementId: "G-HEYDWWHLKV"
            };
            // Initialize Firebase
            firebase.initializeApp(firebaseConfig);
        }

        //initialize firebase
        var dbRef = firebase.database();
        //create table
        var requestRef = dbRef.ref('23');

        const newRef = requestRef.push({
            age: age,
            chiefComplaint: chiefComplaint,
            department: department,
            diagnosis: diagnosis,
            patient: patient,
            sex: sex,
            referring_hospital: referring_hospital,
            date_referred: moment(date_referred).format("YYYY-MM-DD HH:mm:ss"),
            patient_code: patient_code
        });

        const firebase_key = newRef.key;
        console.log(firebase_key)


        var form = new FormData();
        form.append("age", age);
        form.append("chiefComplaint", chiefComplaint);
        form.append("department", department);
        form.append("diagnosis", diagnosis);
        form.append("patient", patient);
        form.append("sex", sex);
        form.append("referring_hospital", referring_hospital);
        form.append("date_referred", moment(date_referred).format("YYYY-MM-DD HH:mm:ss"));
        form.append("patient_code", patient_code);
        form.append("firebase_key", firebase_key);

        var settings = {
            "url": "https://dohcsmc.com/notifier/api/insert_referral_5pm",
            "method": "POST",
            "timeout": 0,
            "processData": false,
            "mimeType": "multipart/form-data",
            "contentType": false,
            "data": form
        };

        $.ajax(settings).done(function(response) {
            console.log(response);
        });
    }

 $('.normal_form').on('submit', function(e) {
    e.preventDefault();
    $('.loading').show();
    $('.btn-submit').attr('disabled', true);

    form_type = '#normalFormModal';
    department_id = $('.select_department_normal').val();
    department_name = $('.select_department_normal option:selected').html();
    telemed = $('.telemedicine').val(0);

    // Get ckd_id from hidden input as fallback
    let ckd_id = $('input[name="ckd_id"]').val();
    // Debug: Show both currentPatientData and ckd_id from form
    console.log('currentPatientData:', currentPatientData);
    console.log('ckd_id from currentPatientData:', currentPatientData ? currentPatientData.ckd_id : undefined);
    console.log('ckd_id from hidden input:', ckd_id);

    // Prefer currentPatientData.ckd_id if available, else use hidden input
    let patchCkdId = (currentPatientData && currentPatientData.ckd_id) ? currentPatientData.ckd_id : ckd_id;

    $(this).ajaxSubmit({
            url: "{{ url('doctor/patient/refer/normal') }}",
            type: 'POST',
            success: function(data) {
                if (typeof data === 'string' && data.trim() === 'consultation_rejected') {
                    $('.loading').hide();
                    $('#pregnantModal').modal('hide');
                    $('#normalFormModal').modal('hide');
                    Lobibox.alert("error", {
                        msg: "This appointment schedule is not available because it is fully booked. Please select another schedule from the calendar."
                    });
                    return;
                }

                let url = "https://ckd.cvchd7.com/api/tracker";
                // PATCH request, then handle redirect/modal after PATCH completes
                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        "id": patchCkdId,
                        "referred": patientCode,
                        // "patientCode": patientCode || '',
                        "CKD_REFERRAL": '9mG6W5MlHE6JmkVVHTzrQL3ximxpSWbWJx0AhpdO7MJvVKHEuJY1Uc68wjcIUQDa',
                    })
                })
                .then(response => response.json())
                .then(result => {
                    console.log('PATCH result:', result);
                    if (result && result.message === "Success!") {
                        Lobibox.alert("success", {
                            msg: "CKD tracker updated successfully!"
                        });
                    } else {
                        Lobibox.alert("error", {
                            msg: "CKD tracker update failed. Please check the details or try again."
                        });
                    }

                    // Only redirect or close modals after PATCH completes
                    if (data.referred_to == 790 || data.referred_to == 23) {
                        var push_diagnosis = typeof push_notification_diagnosis_ccmc !== 'undefined' && push_notification_diagnosis_ccmc ? push_notification_diagnosis_ccmc : $("#other_diag").val();
                        data.age = parseInt(data.age);
                        sendNotifierData(data.age, data.chiefComplaint, data.department, push_diagnosis, data.patient, data.sex, data.referring_hospital, data.date_referred, data.patient_code);
                        $('.loading').hide();
                        $('#pregnantModal').modal('hide');
                        $('#normalFormModal').modal('hide');
                        $('.btn-submit').attr('disabled', false);
                        Lobibox.alert("success", {
                            msg: "Successfully referred the patient!"
                        });
                    } else {
                        $(location).attr('href', `{{ asset('doctor/referred') }}?filterRef=${encodeURIComponent(telemed)}`);
                    }
                })
                .catch(error => {
                    console.error('PATCH error:', error);
                    Lobibox.alert("error", {
                        msg: "An error occurred while updating CKD tracker. Please try again."
                    });
                    // Still handle redirect/modal even if PATCH fails
                    if (data.referred_to == 790 || data.referred_to == 23) {
                        var push_diagnosis = typeof push_notification_diagnosis_ccmc !== 'undefined' && push_notification_diagnosis_ccmc ? push_notification_diagnosis_ccmc : $("#other_diag").val();
                        data.age = parseInt(data.age);
                        sendNotifierData(data.age, data.chiefComplaint, data.department, push_diagnosis, data.patient, data.sex, data.referring_hospital, data.date_referred, data.patient_code);
                        $('.loading').hide();
                        $('#pregnantModal').modal('hide');
                        $('#normalFormModal').modal('hide');
                        $('.btn-submit').attr('disabled', false);
                        Lobibox.alert("success", {
                            msg: "Successfully referred the patient!"
                        });
                    } else {
                        $(location).attr('href', `{{ asset('doctor/referred') }}?filterRef=${encodeURIComponent(telemed)}`);
                    }
                });
            }
            // You can add error handling here if needed
        });
    });

    $('.normal_form_walkin').on('submit',  function(e)  {
        e.preventDefault();
        $('.loading').show();
        reason = $('.reason_referral').val();
        form_type = '#normalFormModal';
        department_id = $('.select_department_normal').val();
        department_name = $('.select_department_normal option:selected').html();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/patient/refer/walkin/normal') }}",
            type: 'POST',
            success: function(data)  {
                console.log(data);
                setTimeout(function()  {
                    window.location.reload(false);
                },  500);
                },
                error: function() {
                    $('#serverModal').modal();
                }
            });
        });

        $('.revised_normal_form').on('submit',function(e){
            e.preventDefault();
            $('.loading').show();
            $('.btn-submit').attr('disabled',true);
            form_type = '#revisednormalFormModal';
            department_id = $('.select_department_normal').val();
            department_name = $('.select_department_normal option:selected').html();
            $(this).ajaxSubmit({
                url: "{{ url('submit-referral/normal') }}",
                type: 'POST',
                success: function(data) {
                    console.log(data);
                    if(data == 'consultation_rejected') {
                        $('.loading').hide();
                        $('#revisedpregnantModal').modal('hide');
                        $('#revisednormalFormModal').modal('hide');
                        Lobibox.alert("error",
                        {
                            msg: "This appoinment schedule is not available, please select other schedule in the calendar."
                        });
                        return;
                    }
                    //if((data.referred_to == 790 || data.referred_to == 23) && data.userid == 1687) {
                    if(data.referred_to == 790 || data.referred_to == 23) {
                        var push_diagnosis = push_notification_diagnosis_ccmc ? push_notification_diagnosis_ccmc : $("#other_diag").val();
                        data.age = parseInt(data.age);
                        sendNotifierData(data.age, data.chiefComplaint, data.department, push_diagnosis, data.patient, data.sex, data.referring_hospital, data.date_referred, data.patient_code);
                        $('.loading').hide();
                        $('#revisedpregnantModal').modal('hide');
                        $('#revisednormalFormModal').modal('hide');
                        $('.btn-submit').attr('disabled',false);
                        Lobibox.alert("success",
                            {
                                msg: "Successfully referred the patient!"
                            });
                    } //push notification for CCMD
                    else {
                        $(location).attr('href', "{{ asset('doctor/referred') }}");
                    }
                }
                });
            
        });

        $('.revised_pregnant_form').on('submit', function(e){
            e.preventDefault();
            $('.loading').show();
            form_type = '#revisedpregnantFormModal';
            sex = 'Female';
            reason = $('.woman_information_given').val();
            department_id = $('.select_department_pregnant').val();
            department_name = $('.select_department_pregnant :selected').text();
            $(this).ajaxSubmit({
                    url: "{{ url('submit-referral/pregnant') }}",
                    type: 'POST',
                    success: function(data){
                        console.log("patient", data);
                        if(data.referred_to == 790 || data.referred_to == 23) {
                        data.age = parseInt(data.age);
                        var push_diagnosis = push_notification_diagnosis_ccmc_pregnant ? push_notification_diagnosis_ccmc_pregnant : $("#other_diag_preg").val();
                        sendNotifierData(data.age, data.chiefComplaint, data.department, push_diagnosis, data.patient, data.sex, data.referring_hospital, data.date_referred, data.patient_code);
                        $('.loading').hide();
                        $('#pregnantModal').modal('hide');
                        $('#revisedpregnantFormModal').modal('hide');
                        $('.btn-submit').attr('disabled',false);
                        Lobibox.alert("success",
                            {
                                msg: "Successfully referred the patient!"
                            });
                    } else {
                        $('.loading').hide(); // Hide loading animation on success
                        setTimeout(function(){
                            $(location).attr('href', "{{ asset('doctor/referred') }}");
                        }, 500);
                    }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: ", error);
                        console.error("Response: ", xhr.responseText);
                        $('#serverModal').modal();
                        $('.loading').hide(); // Hide loading animation on error
                    }
                });
        });

        $('.choose_version').on('submit', function(e){
            e.preventDefault();
            // $('.loading').show();
            form_type = '#pregnantchooseVersionModal';
            $(this).ajaxSubmit({
                url:"{{ route('show-choose-version') }}",
                type: 'GET',
                success: function(res){
                    $('#pregnantModal').modal('hide');
            },
            error: function() {
                $('#serverModal').modal();
            }
        })
        });

        $('.choose_version').on('submit', function(e){
            e.preventDefault();
            // $('.loading').show();
            form_type = '#nonPregnantChooseVersionModal';
            $(this).ajaxSubmit({
                url:"{{ route('show-choose-version') }}",
                type: 'GET',
                success: function(res){
                    $('#pregnantModal').modal('hide');      
                },
                error: function(){
                    $('#serverModal').modal();
                }
            })
    });

    $('.pregnant_form').on('submit', function(e) {
        e.preventDefault();
        $('.loading').show();
        form_type = '#pregnantFormModal';
        sex = 'Female';
        reason = $('.woman_information_given').val();
        department_id = $('.select_department_pregnant').val();
        department_name = $('.select_department_pregnant :selected').text();
        telemed = $('.telemedicine').val();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/patient/refer/pregnant') }}",
            type: 'POST',
            success: function(data) {
                console.log("patient", data);
                //if((data.referred_to == 790 || data.referred_to == 23) && data.userid == 1687) {
                if (data.referred_to == 790 || data.referred_to == 23) {
                    data.age = parseInt(data.age);
                    var push_diagnosis = push_notification_diagnosis_ccmc_pregnant ? push_notification_diagnosis_ccmc_pregnant : $("#other_diag_preg").val();
                    sendNotifierData(data.age, data.chiefComplaint, data.department, push_diagnosis, data.patient, data.sex, data.referring_hospital, data.date_referred, data.patient_code);
                    $('.loading').hide();
                    $('#pregnantModal').modal('hide');
                    $('#pregnantFormModal').modal('hide');
                    // $('.btn-submit').attr('disabled', false);
                    Lobibox.alert("success", {
                        msg: "Successfully referred the patient!"
                    });
                } else {
                    $(location).attr('href', `{{ asset('doctor/referred') }}?filterRef=${encodeURIComponent(telemed)}`);
                }
            }
            /*,
                        error: function(XMLHttpRequest, textStatus, errorThrown){
                            $('.loading').hide();
                            $('#pregnantModal').modal('hide');
                            $('#pregnantFormModal').modal('hide');
                            $('.btn-submit').attr('disabled',false);
                            Lobibox.notify('error', {
                                title: "Error",
                                msg: "Status: " + textStatus+" Error: " + errorThrown
                            });
                        }*/
        });

    });

    $('.pregnant_form_walkin').on('submit', function(e) {
        e.preventDefault();
        $('.loading').show();
        form_type = '#pregnantFormModal';
        sex = 'Female';
        reason = $('.woman_information_given').val();
        department_id = $('.select_department_pregnant').val();
        department_name = $('.select_department_pregnant :selected').text();
        $(this).ajaxSubmit({
            url: "{{ url('doctor/patient/refer/walkin/pregnant') }}",
            type: 'POST',
            success: function(data) {
                console.log(data);
                setTimeout(function() {
                    window.location.reload(false);
                }, 500);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest);
                console.log(textStatus);
                console.log(errorThrown);
                console.log("Status: " + textStatus);
                console.log("Error: " + errorThrown);
                $('#serverModal').modal();
            }
        });

    });

    function sendNormalData(data) {
        console.log("ni sud!");
        if (data.id != 0) {
            var form_data = {
                referring_name: referring_facility_name,
                patient_code: data.patient_code,
                patient_name: name,
                age: age,
                sex: sex,
                date: data.referred_date,
                form_type: form_type,
                tracking_id: data.id,
                referring_md: referring_md,
                referred_from: referring_facility,
                department_id: department_id,
                department_name: department_name
            };

            var dbRef = firebase.database();
            var connRef = dbRef.ref('Referral');
            connRef.child(referred_facility).push(form_data);

            var data = {
                "to": "/topics/ReferralSystem" + referred_facility,
                "data": {
                    "subject": "New Referral",
                    "date": data.referred_date,
                    "body": name + " was referred to your facility from " + referring_facility_name + "!"
                }
            };

            connRef.on('child_added', function(data) {
                setTimeout(function() {
                    connRef.child(data.key).remove();
                    window.location.reload(false);
                }, 500);
            });

            $.ajax({
                url: 'https://fcm.googleapis.com/fcm/send',
                type: 'post',
                data: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'key=AAAAJjRh3xQ:APA91bFJ3YMPNZZkuGMZq8MU8IKCMwF2PpuwmQHnUi84y9bKiozphvLFiWXa5I8T-lP4aHVup0Ch83PIxx8XwdkUZnyY-LutEUGvzk2mu_YWPar8PmPXYlftZnsJCazvpma3y5BI7QHP'
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    setTimeout(function() {
                        console.log("Force refresh!");
                        window.location.reload(false);
                    }, 15000);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log("Status: " + textStatus);
                    console.log("Error: " + errorThrown);
                }
            });

        } else {
            console.log("error else");
            setTimeout(function() {
                window.location.reload(false);
            }, 500);
        }
        console.log("ni lahus sa last!");
    }

    @if(Session::get('patient_update_save'))
    Lobibox.notify('success', {
        title: "",
        msg: "<?php echo Session::get("patient_message"); ?>",
        size: 'mini',
        rounded: true
    });
    <?php
    Session::put("patient_update_save", false);
    Session::put("patient_message", false)
    ?>
    @endif
</script>

@endsection