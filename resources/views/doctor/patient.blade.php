<?php
$user = Session::get('auth');
$counter = 0;
?>
@extends('layouts.app')

@section('content')
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

<!-- Privacy Notice Modal -->
<div class="modal fade modalnotice" id="privacyNoticeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog1" role="document">
        <div class="modal-content modal-content1">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Privacy Notice</h4>
            </div>
            <div class="modal-body" style="max-height: 800px; overflow-y: auto;">
                <p>This Privacy Policy will enable you to better understand how DOHCVCHD collects, processes, retains and uses your data. We hope you read through the policy.</p>

                <p class="notice_category">Statement of Policy</p>

                <p>The DOHCVCHD is the regional arm in Central Visayas of the Department of Health. It is the principal health agency in the country and is responsible for the enforcement of laws on health, ensuring access to basic public health services and quality health care, and regulation of health facilities, goods and services.</p>

                <p>Guided by the Data Privacy Principles, we collect, process, retain, use and share your data when you visit our office premises, avail of our services and systems, file for applications/renewals, submit requests and inquiries, lodge complaints, or when it is necessary in the performance of our statutory and regulatory mandates, including the operation of health information services, and implementation of disease surveillance and response initiatives, among others, subject to your consent or when expressly allowed by law.</p>
                
                <p>The DOHCVCHD faithfully adheres to the requirements of the Data Privacy Act, its implementing rules, and the regulations promulgated by the National Privacy Commission. We highly value the security of your data and your rights as data subjects.</p>

                <p class="notice_category">Collection and Use of Data</p>
                
                <p>Data is collected when the DOHCVCHD performs its governmental functions such as, but not limited to, provision of technical assistance to government and private partners, disease surveillance and health events response per Republic Act No. 11332 and related statutes, management of public health information systems, enforcement of regulatory authority (e.g. receipt of applications of health facilities), handling of complaints, and operations of health and laboratory services. Data is also collected when you avail of our programs and services such as the E-health Referral System and the DOHCVCHD Telemedicine, provided you have granted your consent.</p>

                <p>The manner by which these data is collected may be through the access of online portals, filling out of forms and information sheets, e-mail or in person through our receiving officers, and recording in the closed circuit television (CCTV) systems for office transactions. The online systems may require you of your name, address, contact information and birthday.</p>

                <p>The data shall be used for regulation, surveillance, analysis, policy formulation and guidance, health emergency and response efforts, provision of appropriate technical assistance, clarification of questions, conduct of investigation, identification and communication, safety and security, and continual service improvement.</p>
                
                <p>At all times, the data we collect shall be equal to the requirements needed to fulfill an intended purpose. The collection and use of data shall always be guided by the principles of transparency, legitimate purpose and proportionality.</p>

                <p class="notice_category">Data Sharing</p>

                <p>Data collected by DOHCVCHD is shared with the DOH Central Office, local government units, and other operating partners when required by laws and government regulation, particularly on management of public health information systems, and surveillance of notifiable diseases abd health events, among others. In all other instances, the DOHCVCHD executes a Data Sharing Agreement following the requirements of the NPC to ensure that your data is protected from unlawful uses and disclosures.</p>

                <p class="notice_category">Data Retention, Protection and Disposition</p>

                <p>For the services and systems available to the public, the DOHCVCHD may necessarily store and retain your data as part of its inherent and operational functions, without prejudice to the enforcement of the relevant rights of data subjects.</p>

                <p>Data collected are retained depending on the nature of the data being handled. Physical data are retained by the respective end-users or program managers through proper record filing and keeping. Electronic data which passes through online systems are saved in our local and cloud servers using encryption, firewall, or similar security features. It may also require entering a One-Time Password (OTP) as an added layer of protection. Access to these data is granted only to select personnel, all of whom are required to execute a Non-Disclosure Agreement.</p>

                <p>The DOHCVCHD does not warrant a foolproof or 100% breach-free data system. However, it commits to continually update its security features, review existing data protection policies, coordinate with the NPC for any data incidents, and keep you informed in all stages.</p>

                <p>The data subject may request for the deletion of his/her data, subject to the provisions of the data privacy act. As such, upon the data subject’s request or when necessitated by the circumstances, the DOHCVCHD shall fully dispose of the data retained in the most prompt manner. The length of time in the retention and subsequent disposition of data, as the case may be, shall be in accordance with the records retention and disposition schedule of the National Archives of the Philippines and pertinent internal office protocols, taking into account the legitimate purpose(s) of the collection. When applicable, data shall be returned to the data owners. At all times, the data subject shall be informed that the data has been deleted and disposed of by issuing a certification to such effect.</p>

                <p class="notice_category">Data Subject's Rights</p>

                <p>Pursuant to the DPA, the data subject is entitled to the following rights:</p>
                
                <strong>Right to be informed:</strong>
                <br>The data subject has a right to be informed whether personal data pertaining to him or her shall be, are being, or have been processed, including the existence of automated decision-making and profiling.
                <br>The data subject shall be notified and furnished with information indicated hereunder before the entry of his or her personal data into the processing system of the personal information controller, or at the next practical opportunity:
                <br>Description of the personal data to be entered into the system;
                <br>Purposes for which they are being or will be processed, including processing for direct marketing, profiling or historical, statistical or scientific purpose;
                <br>Basis of processing, when processing is not based on the consent of the data subject;
                <br>Scope and method of the personal data processing;
                <br>Methods utilized for automated access, if the same is allowed by the data subject, and the extent to which such access is authorized, including meaningful information about the logic involved, as well as the significance and the envisaged consequences of such processing for the data subject;
                <br>The identity and contact details of the personal data controller or its representative;
                <br>The period for which the information will be stored; and
                <br>The existence of their rights as data subjects, including the right to access, correction, and object to the processing, as well as the right to lodge a complaint before the Commission.
                Right to object. The data subject shall have the right to object to the processing of his or her personal data, including processing for direct marketing, automated processing or profiling. The data subject shall also be notified and given an opportunity to withhold consent to the processing in case of changes or any amendment to the information supplied or declared to the data subject in the preceding paragraph.</p>

                <p>When the data subject objects or withholds consent, the personal information controller shall no longer process the personal data, unless:</p>
                
                <p>The personal data is needed pursuant to a subpoena;
                <br>The collection and processing are for obvious purposes, including, when it is necessary for the performance of or in relation to a contract or service to which the data subject is a party, or when necessary or desirable in the context of an employer-employee relationship between the collector and the data subject; or
                <br>The information is being collected and processed as a result of a legal obligation.</p>
                
                <p>Right to access. The data subject has the right to reasonable access, upon demand, the following:</p>
                
                <p>Contents of his or her personal data that were processed;
                <br>Sources from which personal data were obtained;
                <br>Names and addresses of recipients of the personal data;
                <br>Manner by which such data were processed;
                <br>Reasons for the disclosure of the personal data to recipients, if any;
                <br>Information on automated processes where the data will, or is likely to, be made as the sole basis for any decision that significantly affects or will affect the data subject;
                <br>Date when his or her personal data concerning the data subject were last accessed and modified; and
                The designation, name or identity, and address of the personal information controller.</p>
                
                <p>Right to rectification. The data subject has the right to dispute the inacuracy or error in the personal data and have the personal information controller correct it immediately and accordingly, unless the request is vexatious or otherwise unreasonable. If the personal data has been corrected, the personal information controller shall ensure the accessibility of both the new and retracted information and the simultaneous receipt of the new and retracted information by the intended recipients thereof: Provided,
                That recipients or third parties who have previously received such processed personal shall be informed of its inaccuracy and its rectification, upon reasonable request of the data subject.
                Right to erasure or blocking. The data subject shall have the right to suspend, withdraw or order the blocking, removal or destruction of his or her personal data from the personal information controller’s filing system.
                <br>This right may be exercised upon discovery and substantial proof of any of the following:
                <br>The personal data is incomplete, outdated, false, or unlawfully obtained;
                <br>The personal data is being used for purpose not authorized by the data subject;
                <br>The personal data is no longer necessary for the purposes for which they were collected;
                <br>The data subject withdraws consent or objects to the processing, and there is no other legal ground or overriding legitimate interest for the processing;
                <br>The personal data concerns private information that is prejudicial to data subject, unless justified by freedom of speech, of expression, or of the press or otherwise authorized;
                <br>The processing is unlawful;</p>
                <br>The personal information controller or personal information processor violated the rights of the data subject.
                <br>The personal information controller may notify third parties who have previously received such processed personal information
                <br>Right to damages. The data subject shall be indemnified for any damages sustained due to such inaccurate, incomplete, outdated, false, unlawfully obtained or unauthorized use of personal data, taking into account any violation of his or her rights and freedoms as data subject.</p>
                
                <p class="notice_category">Responsibility of Data Subjects</p>
                
                <p>As we commit to ensuring the best service to our clients, data subjects are concomitantly urged to be circumspect and vigilant that the online systems it is accessing is legitimate and valid. If unsure, you may call or coordinate with our office through the client feedback information provided herein.</p>

                <h4>Client Feedback</h4>

                <p>For requests, questions, complaints, or reports of any data breach or incidents, you may contact our Data Protection Officer through the following contact information:</p>
                
                <strong>Name :</strong> Data Protection Officer <br>
                <strong>Title/Office :</strong> Legal Section/Data Protection Office <br>
                <strong>Contact No. :</strong> (032) 260-9740 loc. 104 <br>
                <strong>Email :</strong> legal@ro7.doh.gov.ph
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="privacyCheckbox">
                    <label class="form-check-label" for="privacyCheckbox">
                        I have read and accept the Privacy Statement
                    </label>
                </div>
            </div>
            <div class="modal-footer footer-privacy-noctice">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="acceptPrivacyBtn" disabled>I Accept</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        @include('sidebar.filter_profile')
    </div>
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">{{ $title }}</h3>
            @if(count($data))
            <div class="table-responsive">
                <table class="table table-striped" style="white-space:nowrap;">
                    <tbody>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Age/DOB</th>
                            <th>Region/Province</th>
                            <th>Municipality/Barangay</th>
                            <th style="width:18%; text-align: center">Action</th>
                        </tr>
                        @foreach($data as $row)
                        <?php
                        $modal = ($row->type == 'normal') ? '#normalFormModal' : '#pregnantFormModal';
                        ?>
                        <tr>
                            <td>
                                <b>
                                    <a href="#patient_modal" data-toggle="modal" data-id="{{ $row->id }}" onclick="PatientBody('<?php echo $row->id ?>')" class="update_info">
                                        {{ $row->fname }} {{ isset($row->mname[0]) ? strtoupper($row->mname[0]) : '' }}. {{ $row->lname }}
                                    </a>
                                </b><br>
                                <small class="text-success">{{ $row->contact }}</small>
                            </td>
                            <td>
                                {{ $row->sex }}<br>
                                <small class="text-success">{{ $row->civil_status }}</small>
                            </td>
                            <td>
                                <?php $age = \App\Http\Controllers\ParamCtrl::getAge($row->dob);
                                $month = \App\Http\Controllers\ParamCtrl::getMonths($row->dob) ?>
                                @if( $age == 1)
                                {{ $age }} year old
                                @elseif( $age > 0)
                                {{ $age }} years old
                                @else
                                @if($month['month'] == 1)
                                {{ $month['month'] }} mo,
                                @else
                                {{ $month['month'] }} mos,
                                @endif
                                @if($month['days'] == 1)
                                {{ $month['days'] }} day
                                @else
                                {{ $month['days'] }} days
                                @endif
                                @endif
                                <br />
                                <small class="text-muted">{{ date('M d, Y',strtotime($row->dob)) }}</small>
                            </td>
                            <td>
                                {{ $row->region == 'Negros Island Region' ? $row->region . ' (NIR)' : $row->region }} <br />
                                <?php
                                if (!$province_display = \App\Province::find($row->province)->description)
                                    $province_display = $row->province_others;
                                ?>
                                <small class="text-success">{{ $province_display }}</small>
                            </td>
                            <td>
                                <?php
                                $brgy_id = ($source == 'tsekap') ? $row->barangay_id : $row->brgy;
                                $city_id = ($source == 'tsekap') ? $row->muncity_id : $row->muncity;
                                $phic_id = ($source == 'tsekap') ? $row->phicID : $row->phic_id;
                                $phic_id_stat = 0;
                                if ($phic_id) {
                                    $phic_id_stat = 1;
                                }
                                ?>

                                <?php
                                if (!$brgy = \App\Barangay::find($brgy_id)->description)
                                    $brgy = $row->brgy_others;
                                if (!$city = \App\Muncity::find($city_id)->description)
                                    $city = $row->muncity_others;
                                ?>
                                {{ $city }}<br />
                                <small class="text-success">{{ $brgy }}</small>
                            </td>
                            <td>
                                @if($row->sex=='Female' && ($age >= 10 && $age <= 49)) 
                                <a href="#pregnantModal" data-patient_id="{{ $row->id }}" data-toggle="modal" data-type="pregnant" class="btn btn-primary btn-xs profile_info hide patient-emergency hidden" onclick="handleRefer()" style="width:100%;margin-bottom:5px;">
                                    <i class="fa fa-ambulance"></i>
                                    Refer
                                    </a>
                                    <a href="#" data-patient_id="{{ $row->id }}" data-toggle="modal" data-type="pregnant" data-telemedicine="1" onclick="showPrivacyNotice('#pregnantModalTelemed')" class="btn btn-success btn-xs profile_info hide patient-consultation hidden" style="width:100%;margin-bottom:5px;">
                                        <i class="fa fa-stethoscope"></i>
                                        Consultation
                                    </a><br>
                                    <a href="#" id="walkinPregnant{{ $counter }}" data-patient_id="{{ $row->id }}" data-toggle="modal" data-type="pregnant" onclick="promptWalkinPregnant(<?php echo $counter++ ?>)" style="width:100%;" class="btn btn-warning btn-xs profile_info hide patient-emergency hidden">
                                        <i class="fa fa-stethoscope"></i>
                                        Walk-In
                                    </a>
                                        @elseif ($row->sex=='Female' && $age >= 9)
                                        <a href="#pregnantModal"
                                               data-patient_id="{{ $row->id }}"
                                               data-backdrop="static"
                                               data-toggle="modal"
                                               data-type="normal"
                                               style="width:100%;margin-bottom:5px;"
                                               onclick="handleRefer()"
                                               class="btn btn-primary btn-xs profile_info patient-emergency hidden">
                                                <i class="fa fa-ambulance"></i>
                                                Refer
                                            </a>
                                            <a href="#"
                                                data-patient_id="{{ $row->id }}"
                                                data-backdrop="static"
                                                data-toggle="modal"
                                                data-type="normal"
                                                onclick="showPrivacyNotice('#normalFormModal')"
                                                style="width:100%;margin-bottom:5px;"
                                                class="btn btn-success btn-xs profile_info patient-consultation hidden">
                                                <i class="fa fa-stethoscope"></i>
                                                Consultation
                                            </a><br>
                                            <a href="#"
                                               id="walkinNormal{{ $counter }}"
                                               data-patient_id="{{ $row->id }}"
                                               data-backdrop="static"
                                               data-toggle="modal"
                                               data-type="normal"
                                               onclick="promptWalkinNormal(<?php echo $counter++ ?>)"
                                               style="width:100%;"
                                               class="btn btn-warning btn-xs profile_info patient-emergency hidden">
                                                <i class="fa fa-stethoscope"></i>
                                                Walk-In
                                            </a>
                                        
                                    @elseif ($row->sex=='Male')
                                        <a href="#nonPregnantChooseVersionModal" 
                                            data-patient_id="{{ $row->id }}" 
                                            data-backdrop="static" 
                                            data-toggle="modal" 
                                            data-type="normal" 
                                            style="width:100%;margin-bottom:5px;" 
                                            onclick="handleRefer()" 
                                            class="btn btn-primary btn-xs profile_info patient-emergency hidden">
                                            <i class="fa fa-ambulance"></i>
                                            Refer
                                        </a>
                                        <a href="#" data-patient_id="{{ $row->id }}" data-toggle="modal" data-type="pregnant" data-telemedicine="1" onclick="showPrivacyNotice('#normalFormModal')" class="btn btn-success btn-xs profile_info hide patient-consultation hidden" style="width:100%;margin-bottom:5px;">
                                            <i class="fa fa-stethoscope"></i>
                                            Consultation
                                        </a><br>
                                        <a href="#" id="walkinNormal{{ $counter }}" data-patient_id="{{ $row->id }}" data-backdrop="static" data-toggle="modal" data-type="normal" onclick="promptWalkinNormal(<?php echo $counter++ ?>)" style="width:100%;" class="btn btn-warning btn-xs profile_info patient-emergency hidden">
                                            <i class="fa fa-stethoscope"></i>
                                            Walk-In
                                        </a>
                                    @elseif ($row->sex=='Female' && $age <= 9)
                                    <a href="#nonPregnantChooseVersionModal" 
                                            data-patient_id="{{ $row->id }}" 
                                            data-backdrop="static" 
                                            data-toggle="modal" 
                                            data-type="normal" 
                                            style="width:100%;margin-bottom:5px;" 
                                            onclick="handleRefer()" 
                                            class="btn btn-primary btn-xs profile_info patient-emergency hidden">
                                            <i class="fa fa-ambulance"></i>
                                            Refer
                                        </a>
                                        <a href="#" data-patient_id="{{ $row->id }}" data-backdrop="static" data-toggle="modal" data-type="normal"onclick="showPrivacyNotice('#normalFormModal')" style="width:100%;margin-bottom:5px;" class="btn btn-success btn-xs profile_info patient-consultation hidden">
                                            <i class="fa fa-stethoscope"></i>
                                            Consultation
                                        </a><br>
                                        <a href="#" id="walkinNormal{{ $counter }}" data-patient_id="{{ $row->id }}" data-backdrop="static" data-toggle="modal" data-type="normal" onclick="promptWalkinNormal(<?php echo $counter++ ?>)" style="width:100%;" class="btn btn-warning btn-xs profile_info patient-emergency hidden">
                                            <i class="fa fa-stethoscope"></i>
                                            Walk-In
                                        </a>
                                    @else
                                    <a href="#normalFormModal" 
                                        data-patient_id="{{ $row->id }}" 
                                        data-backdrop="static" 
                                        data-toggle="modal" 
                                        data-type="normal" 
                                        style="width:100%;margin-bottom:5px;" 
                                        onclick="handleRefer()" 
                                        class="btn btn-primary btn-xs profile_info patient-emergency hidden">
                                        <i class="fa fa-ambulance"></i>
                                        Refer
                                    </a>
                                    <a href="#" data-patient_id="{{ $row->id }}" data-backdrop="static" data-toggle="modal" data-type="normal" onclick="showPrivacyNotice('#normalFormModal')" style="width:100%;margin-bottom:5px;" class="btn btn-success btn-xs profile_info patient-consultation hidden">
                                        <i class="fa fa-stethoscope"></i>
                                        Consultation
                                    </a><br>
                                    <a href="#" id="walkinNormal{{ $counter }}" data-patient_id="{{ $row->id }}" data-backdrop="static" data-toggle="modal" data-type="normal" onclick="promptWalkinNormal(<?php echo $counter++ ?>)" style="width:100%;" class="btn btn-warning btn-xs profile_info patient-emergency hidden">
                                        <i class="fa fa-stethoscope"></i>
                                        Walk-In
                                    </a>
                                    @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <ul class="pagination pagination-sm no-margin pull-right">
                {{ $data->links() }}
            </ul>

            @else
            <div class="alert alert-warning">
                <span class="text-warning">
                    <i class="fa fa-warning"></i> Patient not found!
                </span>
            </div>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-labelledby="filePreviewModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header"> -->
                <!-- <h4 class="modal-title" id="filePreviewModalLabel">File Preview</h4> -->
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div> -->
            <div class="modal-body">
                <div class="file-preview-container">
                    <button class="nav-button prev-button" onclick="navigateCarousel('prev')">
                        <span class="prev-icon-file glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </button>
                    <div id="filePreviewCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
                        <div class="carousel-inner" id="carousel-inner">
                            <!-- File previews will be dynamically inserted here -->
                        </div>
                    </div>
                    <button class="nav-button next-button" onclick="navigateCarousel('next')">
                        <span class="next-icon-file glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
{{--@include('script.firebase')--}}
<script src="https://www.gstatic.com/firebasejs/8.2.1/firebase.js"></script>
@include('script.datetime')
<script>

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

        function openNewForms(type){
                 // Get facility_id and pregnancy status from server-side
                var referred_facility = "{{ $user->facility_id }}";
                let telemed = $(".telemedicine").val();
    
                $(".telemedicine").val(telemed);

                // if (referred_facility == 63) {
                    if (type == 'pregnant') {
                        $('#pregnantModal').modal('hide');
                        // $('#pregnantchooseVersionModal').modal('show');
                        selectFormTitle("BEmONC/ CEmONC ");
                    } else if (type == 'normal') {
                        $('#pregnantModal').modal('hide');
                        $('#nonPregnantChooseVersionModal').modal('show');
                        selectFormTitle("Clinical");
                        $('#baby_show').hide();
                    }
                    
                // } else {
                //     if(type == "pregnant") {
                //         selectFormTitle("BEmONC/ CEmONC ");
                //         $('#pregnantFormModal').modal('show');
                //     } else if(type == "normal") {
                //         selectFormTitle("Clinical ");
                //         $('#normalFormModal').modal('show');
                //     }
                //     $('#menarche_show_normal').hide();
                //     $('#pedia_show_normal').hide();
                // }
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

    $('.profile_info').removeClass('hide');
    $('.profile_info').on('click', function() {
        patient_id = $(this).data('patient_id');
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
                    // console.log("show obstetric");
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
    });

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
     
        // const formData = new FormData(this);

        // const activFiles =  fileInfoArray.filter(file => file && !file.removed);

        // formData.delete('file_upload[]');

        // activFiles.forEach(fileInfo => {
        //     if(fileInfo.file){
        //         formData.append('file_upload[]', fileInfo.file);
        //     }
        // });
        // console.log("remaining file to upload", formData);
        form_type = '#normalFormModal';
        department_id = $('.select_department_normal').val();
        department_name = $('.select_department_normal option:selected').html();
        telemed = $('.telemedicine').val();

        $(this).ajaxSubmit({
            url: "{{ url('doctor/patient/refer/normal') }}",
            type: 'POST',
            success: function(data) {
                console.log(data);
                
                if (data.trim() === 'consultation_rejected') {
                    $('.loading').hide();
                    $('#pregnantModal').modal('hide');
                    $('#normalFormModal').modal('hide');
                    Lobibox.alert("error", {
                        msg: "This appointment schedule is not available because it is fully booked. Please select another schedule from the calendar."
                    });
                    return;
                }
                //if((data.referred_to == 790 || data.referred_to == 23) && data.userid == 1687) {
                if (data.referred_to == 790 || data.referred_to == 23) {
                    var push_diagnosis = push_notification_diagnosis_ccmc ? push_notification_diagnosis_ccmc : $("#other_diag").val();
                    data.age = parseInt(data.age);
                    sendNotifierData(data.age, data.chiefComplaint, data.department, push_diagnosis, data.patient, data.sex, data.referring_hospital, data.date_referred, data.patient_code);
                    $('.loading').hide();
                    $('#pregnantModal').modal('hide');
                    $('#normalFormModal').modal('hide');
                    $('.btn-submit').attr('disabled', false);
                    Lobibox.alert("success", {
                        msg: "Successfully referred the patient!"
                    });
                } //push notification for CCMD
                else {
                    $(location).attr('href', `{{ asset('doctor/referred') }}?filterRef=${encodeURIComponent(telemed)}`);
                }
            }
            /*,
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(XMLHttpRequest);
                console.log(textStatus);
                console.log(errorThrown);
                $('.loading').hide();
                $('#pregnantModal').modal('hide');
                $('#normalFormModal').modal('hide');
                $('.btn-submit').attr('disabled',false);
                Lobibox.notify('error', {
                    title: "Error",
                    msg: "Status: " + textStatus+" Error: " + errorThrown
                });
            }*/
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
            telemed = $('.telemedicine').val();
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
            telemed = $('.telemedicine').val();
            console.log("revised Telemed", telemed);
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
                    $('.btn-submit').attr('disabled', false);
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