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
</style>

<?php

use App\Facility;
use App\Icd;
use App\ReasonForReferral;
use App\Models\PregnantForm;

?>
  @include('include.header_form', ['optionHeader' => 'referred'])<br>

    @foreach($emr_data as $emr)
    
        @if($emr->sex == "Male")  
            <table class="table table-striped form-label referral-table">
                <tr>
                    <td colspan="6" class="form-label">Name of Referring Facility: <span class="referring_name form-details">   {{ Facility::find($emr->patient_refer_from)->name ?? 'Unknown' }}</span></td>
                    <td> <form method="GET" action="{{ url('doctor/referred') }}"> @csrf <input type="hidden" name="referredCode" value="{{ $emr->patientCode }}"> <button type="submit" class="btn btn-outline-primary btn-sm"> <i class="bi bi-search"></i> Track Patient </button> </form> </td>
                </tr>
                <tr>
                    <td colspan="6">Facility Contact #: <span class="referring_contact form-details"> {{ Facility::find($emr->patient_refer_from)->contact ?? 'Unknown' }} </span></td>
                </tr>
                <tr>
                    <td colspan="6">Address: <span class="referring_address form-details">{{ Facility::find($emr->patient_refer_from)->address ?? 'Unknown' }}</span></td>
                </tr>
                <tr>
                    <td colspan="3">Referred to: <span class="referred_name form-details">{{ Facility::find($emr->patient_refer_to)->name ?? 'Unknown' }} </span></td>
                    <td colspan="3">Department: <span class="department_name form-details">{{$emr->department}}</span></td>
                </tr>
                <tr>
                    <td colspan="6">Address: <span class="referred_address form-details">{{ Facility::find($emr->patient_refer_to)->address ?? 'Unknown' }}</span></td>
                </tr>
                <tr>
                    <td colspan="3">Date/Time Referred (ReCo): <span class="date_referred form-details">{{ \Carbon\Carbon::parse($emr->patient_referred_date)->format('F d, Y h:i:s A')}} </span></td>
                    <td colspan="3">Date/Time Transferred: <span class="date_transferred form-details"></span></td>
                </tr>
                <tr>
                    <td colspan="3">Name of Patient: <span class="patient_name form-details">{{$emr->fname}} {{$emr->mname}} {{$emr->lname}} </span></td>
                    <td>Age: <span class="patient_age form-details">

                    <?php

                        $patient_age = \App\Http\Controllers\ParamCtrl::getAge($emr->dob);
                        $age_type = 'y';

                        if ($patient_age == 0) {
                            $patient_age = \App\Http\Controllers\ParamCtrl::getMonths($emr->dob);
                            $age_type = 'm';
                        }

                        ?>
                   @if($age_type == "y")
                        @if($patient_age == 1)
                            {{ $patient_age }} year old
                        @else
                            {{ $patient_age }} years old
                        @endif
                    @elseif($age_type == "m")
                        @if($patient_age['month'])
                            {{ $patient_age['month'] }} mo,
                        @else
                            {{ $patient_age['month'] }} mos,
                        @endif
                        @if($patient_age['days'] == 1)
                            {{ $patient_age['days'] }} day old
                        @else
                            {{ $patient_age['days'] }} days old
                        @endif
                    @endif
                        </span><br>
                        <small><i>(at time of referral)</i></small>
                    </td>
                    <td>Sex: <span class="patient_sex form-details">{{$emr->sex}}</span></td>
                    <td>Status: <span class="patient_status form-details">{{$emr->civil_status}}</span></td>
                </tr>
                <tr>
                    <td colspan="6">Address: <span class="patient_address form-details">{{$emr->patient_address}}</span></td>
                </tr>
                <tr>
                    <td colspan="3">PhilHealth status: <span class="phic_status form-details">{{ $emr->phic_status }}</span></td>
                    <td colspan="3">PhilHealth #: <span class="phic_id form-details">{{$emr->phic_id}}</span></td>
                </tr>
                <tr>
                    <td colspan="6">Covid Number: <span class="covid_number form-details">{{$emr->covid_number}}</span></td>
                </tr>
                <tr>
                    <td colspan="6">Clinical Status: <span class="clinical_status form-details" style="text-transform: capitalize;">{{ $emr->patient_clinical_status }}</span></td>
                </tr>
                <tr>
                    <td colspan="6">Surveillance Category: <span class="surveillance_category form-details" style="text-transform: capitalize;">{{ $emr->patient_sur_category }}</span></td>
                </tr>
                <tr>
                    <td colspan="6">
                        Case Summary (pertinent Hx/PE, including meds, labs, course etc.):
                        <br />
                        <span class="case_summary form-details">{{$emr->patient_case_summary}}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist):
                        <br />
                        <span class="reco_summary form-details">{{$emr->patient_reco_summary}}</span>
                    </td>
                </tr>
                    <?php 

                        $icd = Icd::select('icd10.code', 'icd10.description')
                        ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
                        ->where('icd.code',$emr->patientCode)->get();

                    ?>
                    @if(isset($icd[0]))
                    <tr>
                        <td colspan="6">
                            ICD-10 Code and Description:
                            <br />
                            @foreach($icd as $i)
                                <span class="reason form-details">{{ $i->code }} - {{ $i->description }}</span><br>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @if(isset($emr->patient_diagnosis))
                    <tr>
                        <td colspan="6">
                            Diagnosis/Impression:
                            <br />
                            <span class="diagnosis form-details">{!! nl2br($emr->patient_diagnosis) !!}</span>
                        </td>
                    </tr>
                    @endif
                    @if(isset($emr->other_patient_diagnosis))
                    <tr>
                        <td colspan="6">
                            Other Diagnoses:
                            <br />
                            <span class="reason form-details">{{$emr->other_patient_diagnosis}}</span>
                        </td>
                    </tr>
                    @endif
                    <?php
                        $reason = ReasonForReferral::select("reason_referral.reason","reason_referral.id")
                        ->join('patient_form', 'patient_form.reason_referral', 'reason_referral.id')
                        ->where('patient_form.code', $emr->patientCode)->first();
                    ?>
                    @if(isset($reason))
                    <tr>
                        <td colspan="6">
                            Reason for referral:
                            <br />
                            <span class="reason form-details">{{$reason->reason}}</span>
                        </td>
                    </tr>
                    @endif   

                      @if(isset($file_path) && count($file_path))
                            @foreach($file_path as $file)
                                @php
                                    $paths = $file['paths'] ?? [];
                                    $filenames = $file['filenames'] ?? [];
                                    $code = $file['code'] ?? null;
                                @endphp

                                @if($code == $emr->patientCode)
                                    @if(count($paths))
                                        <tr>
                                            <td colspan="6">
                                                {{ count($paths) > 1 ? 'File Attachments:' : 'File Attachment:' }} <br>
                                                @for($i = 0; $i < count($paths); $i++)
                                                    @php
                                                        $cleanPath = explode('|', $paths[$i])[0];
                                                        $cleanFilename = explode('|', $filenames[$i])[0];
                                                    @endphp
                                                    <a href="{{ $cleanPath }}" id="file_download" class="reason" target="_blank" style="font-size: 12pt;" download>
                                                        {{ $cleanFilename }}
                                                    </a>
                                                    @if($i + 1 != count($paths))
                                                        ,&nbsp;
                                                    @endif
                                                @endfor
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                <tr>
                    <td colspan="6">
                        Name of referring MD/HCW: <span class="referring_md form-details">{{$emr->md_referring}}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        Contact # of referring MD/HCW: <span class="referring_md_contact form-details"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">Name of referred MD/HCW- Mobile Contact # (ReCo): <span class="referred_md form-details"></span></td>
                </tr>
            </table>
            <hr  style="height: 2px; background-color: #D5ECCC; border: none;"/>
            
        @else                       

            <table class="table table-striped form-label">
                <tr>
                    <th colspan="4">REFERRAL RECORD</th>
                </tr>
                <tr>
                    <td>Who is Referring</td>
                    <td>Record Number: <span class="record_no form-details">{{ $emr->pregnant_record_no }}</span></td>
                    <td colspan="2">Referred Date: <span class="referred_date form-details">{{ $emr->pregnant_referred_date }}</span></td>
                </tr>
                <tr>
                    <td colspan="2">Referring Name: <span class="md_referring form-details">{{ $emr->pregnant_md_referring }}</span></td>
                    <td colspan="2">Arrival Date: </td>
                </tr>
                <tr>
                    <td colspan="4">Contact # of referring MD/HCW: <span class="referring_md_contact form-details">{{$emr->pgmd_referring_contact}}</span></td>
                </tr>
                <tr>
                    <td colspan="4">Referring Facility: <span class="referring_facility form-details">{{ Facility::find($emr->pregnant_refer)->name ?? 'Unknown' }}</span></td>
                </tr>
                <tr>
                    <td colspan="4">Facility Contact #: <span class="referring_contact form-details">{{ Facility::find($emr->pregnant_refer)->contact ?? 'Unknown' }}</span></td>
                </tr>
                <tr>
                    <td colspan="4">Accompanied by the Health Worker: <span class="health_worker form-details">{{ $form['pregnant']->health_worker }}</span></td>
                </tr>
                <tr>
                    <td colspan="2">Referred To: <span class="referred_name form-details">{{ Facility::find($emr->pregnant_referred_to)->name ?? 'Unknown' }}</span></td>
                    <td colspan="2">Department: <span class="department_name form-details">{{ $emr->pregDepartment }}</span></td>
                </tr>
                <tr>
                    <td colspan="4">Covid Number: <span class="covid_number form-details">{{ $emr->preg_covid }}</span></td>
                </tr>
                <tr>
                    <td colspan="4">Clinical Status: <span class="clinical_status form-details" style="text-transform: capitalize;">{{ $emr->pregnant_refer_clinical_status }}</span></td>
                </tr>
                <tr>
                    <td colspan="4">Surveillance Category: <span class="surveillance_category form-details" style="text-transform: capitalize;">{{ $emr->pregnant_refer_sur_category }}</span></td>
                </tr>
            </table>

            <div class="row">
                <div class="col-sm-6">
                    <table class="table bg-warning">
                        <tr class="bg-gray">
                            <th colspan="4">WOMAN</th>
                        </tr>
                        <tr>
                            <td colspan="3">Name: <span class="woman_name form-details">{{$emr->fname}} {{$emr->mname}} {{$emr->lname}}</span></td>
                            <td>Age: <span class="patient_age form-details"> 
                                <?php

                                    $patient_age = \App\Http\Controllers\ParamCtrl::getAge($emr->dob);

                                ?>

                                {{ $patient_age }}</span><br><small><i>(at time of referral)</i></small>
                            
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">Address: <span class="woman_address form-details">{{ $emr->patient_address }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                Main Reason for Referral: <span class="woman_reason form-details">{!! nl2br($emr->woman_reason) !!}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">Major Findings (Clinica and BP,Temp,Lab)
                                <br />
                                <span class="woman_major_findings form-details">{!! nl2br($emr->woman_major_findings) !!}</span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                        <tr class="bg-gray">
                            <td colspan="4">Treatments Give Time</td>
                        </tr>
                        <tr>
                            <td colspan="4">Before Referral: <span class="woman_before_treatment form-details">{{ $emr->woman_before_treatment }}</span> - <span class="woman_before_given_time form-details">{{ $form['pregnant']->woman_before_given_time }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4">During Transport: <span class="woman_during_transport form-details">{{ $emr->woman_during_transport }}</span> - <span class="woman_transport_given_time form-details">{{ $form['pregnant']->woman_transport_given_time }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4">Information Given to the Woman and Companion About the Reason for Referral
                                <br />
                                <span class="woman_information_given form-details">{!! nl2br($emr->woman_information_given) !!}</span>
                            </td>
                        </tr>
                        <?php 

                            $icd = Icd::select('icd10.code', 'icd10.description')
                            ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
                            ->where('icd.code',$emr->pregnantCode)->get();

                        ?>
                        @if(isset($icd[0]))
                            <tr>
                                <td colspan="4">
                                    ICD-10 Code and Description:
                                    <br />
                                    @foreach($icd as $i)
                                        <span class="reason form-details">{{ $i->code }} - {{ $i->description }}</span><br>
                                    @endforeach
                                </td>
                            </tr>
                        @endif

                        @if(isset($emr->notes_diagnoses))
                            <tr>
                                <td colspan="4">
                                    Diagnosis/Impression:
                                    <br />
                                    <span class="diagnosis form-details">{!! nl2br($emr->notes_diagnoses) !!}</span>
                                </td>
                            </tr>
                        @endif
                        @if(isset($emr->other_diagnoses))
                            <tr>
                                <td colspan="4">
                                    Other Diagnoses:
                                    <br />
                                    <span class="reason form-details">{{ $emr->other_diagnoses }}</span>
                                </td>
                            </tr>
                        @endif

                         <?php
                        $reason = ReasonForReferral::select("reason_referral.reason","reason_referral.id")
                            ->join('pregnant_form', 'pregnant_form.reason_referral', 'reason_referral.id')
                            ->where('pregnant_form.code', $emr->pregnantCode)->first();
                        ?>
                  
                        @if(isset($reason))
                            <tr>
                                <td colspan="4">
                                    Reason for referral:
                                    <br />
                                    <span class="reason form-details">{{ $reason->reason }}</span>
                                </td>
                            </tr>
                        @endif
                        @if(isset($emr->other_reason_referral))
                            <tr>
                                <td colspan="4">
                                    Reason for referral:
                                    <br />
                                    <span class="reason form-details">{{ $emr->other_reason_referral }}</span>
                                </td>
                            </tr>
                        @endif

                      @if(isset($file_path) && count($file_path))
                            @foreach($file_path as $file)
                                @php
                                    $paths = $file['paths'] ?? [];
                                    $filenames = $file['filenames'] ?? [];
                                    $code = $file['code'] ?? null;
                                @endphp

                                @if($code == $emr->pregnantCode)
                                    @if(count($paths))
                                        <tr>
                                            <td colspan="6">
                                                {{ count($paths) > 1 ? 'File Attachments:' : 'File Attachment:' }} <br>
                                                @for($i = 0; $i < count($paths); $i++)
                                                    @php
                                                        $cleanPath = explode('|', $paths[$i])[0];
                                                        $cleanFilename = explode('|', $filenames[$i])[0];
                                                    @endphp
                                                    <a href="{{ $cleanPath }}" id="file_download" class="reason" target="_blank" style="font-size: 12pt;" download>
                                                        {{ $cleanFilename }}
                                                    </a>
                                                    @if($i + 1 != count($paths))
                                                        ,&nbsp;
                                                    @endif
                                                @endfor
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                        {{--@if(isset($file_path))--}}
                            {{--<tr>--}}
                                {{--<td colspan="4">--}}
                                    {{--File Attachment:--}}
                                    {{--<a href="{{ asset($file_path) }}" target="_blank" class="reason" style="font-size: 12pt;" download>{{ $file_name }}</a>--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                        {{--@endif--}}
                    </table>
                </div>

                <div class="col-sm-6">
                    <table class="table bg-warning">
                        <tr class="bg-gray">
                            <th colspan="4">BABY</th>
                        </tr>
                        <tr>
                            <td colspan="2">Name: <span class="baby_name form-details">{{ $form['baby']->baby_name }}</span></td>
                            <td>Date of Birth: <span class="baby_dob form-details">{{ $form['baby']->baby_dob }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="2">Birth Weight: <span class="weight form-details">{{ $form['baby']->weight }}</span></td>
                            <td>Gestational Age: <span class="gestational_age form-details">{{ $form['baby']->gestational_age }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                Main Reason for Referral: <span class="baby_reason form-details">{{ $form['baby']->baby_reason }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">Major Findings (Clinical and BP,Temp,Lab)
                                <br />
                                <span class="baby_major_findings form-details">{!! nl2br($form['baby']->baby_major_findings) !!}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">Last (Breast) Feed (Time): <span class="baby_last_feed form-details">{{ $form['baby']->baby_last_feed }}</span></td>
                        </tr>
                        <tr class="bg-gray">
                            <td colspan="4">Treatments Give Time</td>
                        </tr>
                        <tr>
                            <td colspan="4">Before Referral: <span class="baby_before_treatment form-details">{{ $form['baby']->baby_before_treatment }}</span> - <span class="baby_before_given_time form-details">{{ $form['baby']->baby_before_given_time }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4">During Transport: <span class="baby_during_transport form-details">{{ $form['baby']->baby_during_transport }}</span> - <span class="baby_transport_given_time form-details">{{ $form['baby']->baby_transport_given_time }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4">Information Given to the Woman and Companion About the Reason for Referral
                                <br />
                                <span class="baby_information_given form-details">{!! $form['baby']->baby_information_given !!}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <table class="table table-striped col-sm-6"></table>
            <div class="clearfix"></div>
            <hr />
        @endif
    @endforeach    
    <button class="btn-sm btn-default btn-flat" data-dismiss="modal" id="closeReferralForm{{$form->code}}"><i class="fa fa-times"></i> Close</button>

    {{-- <div class="d-flex justify-content-center">
    <div id="pagination-container">
        {{ $emr_data->appends(request()->query())->links() }}
    </div>
</div> --}}

<script>

// $(document).ready(function() {
//     // Handle pagination clicks
//     $(document).on('click', '#pagination-container .pagination a', function(e) {
//         e.preventDefault();
//         var url = $(this).attr('href');
        
//         // Show loading indicator
//         $('#pagination-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
        
//         // Make AJAX request
//         $.get(url, function(data) {
//             // Replace the modal content with new data
//             $('.modal-body').html(data);
//         }).fail(function() {
//             alert('Error loading data. Please try again.');
//             // Restore pagination on error
//             location.reload();
//         });
//     });
// });

    // function getParameterByName(name) {
    //     url = window.location.href;
    //     name = name.replace(/[\[\]]/g, '\\$&');
    //     var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
    //         results = regex.exec(url);
    //     if (!results) return null;
    //     if (!results[2]) return '';
    //     return decodeURIComponent(results[2].replace(/\+/g, ' '));
    // }
    // if(getParameterByName('referredCode')) {
    //     $("#telemedicine").addClass('hide');
    //     $(".edit_form_btn").addClass('hide');
    // }

    // function openTelemedicine(tracking_id, code, action_md, referring_md) {
    //     console.log("mao");
    //     var url = "<?php echo asset('api/video/call'); ?>";
    //     var json = {
    //         "_token" : "<?php echo csrf_token(); ?>",
    //         "tracking_id" : tracking_id,
    //         "code" : code,
    //         "action_md" : action_md,
    //         "referring_md" : referring_md,
    //         "trigger_by" : "{{ $user->id }}",
    //         "form_type" : "normal"
    //     };
    //     $.post(url,json,function(){

    //     });
    //     var windowName = 'NewWindow'; // Name of the new window
    //     var windowFeatures = 'width=600,height=400'; // Features for the new window (size, position, etc.)
    //     var newWindow = window.open("{{ asset('doctor/telemedicine?id=') }}"+tracking_id+"&code="+code+"&form_type=normal&referring_md=yes", windowName, windowFeatures);
    //     if (newWindow && newWindow.outerWidth) {
    //         // If the window was successfully opened, attempt to maximize it
    //         newWindow.moveTo(0, 0);
    //         newWindow.resizeTo(screen.availWidth, screen.availHeight);
    //     }
    // }
</script>