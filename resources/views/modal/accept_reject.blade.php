<?php
    $user = Session::get('auth');
    $myfacility = \App\Facility::find($user->facility_id);
    $department = \App\Http\Controllers\LocationCtrl::facilityAddress($myfacility->id);
    $facility_address = $department['address'];
?>
<style>
    #normalFormModal span {
        color: #e08e0b;
    }

    #pregnantFormModal span {
        color: #1e8a2a;
    }

    .modal-extraLarge {
        width: 90%;
        max-width: 1200px;
    }
</style>

<div class="modal fade" id="EmrForm">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="jim-content referral_body">

            <button class="btn-sm btn-default btn-flat" data-dismiss="modal" id="closeReferralForm{{$form->code}}"><i class="fa fa-times"></i> Close </button>
                <div class="clearfix"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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

<div class="modal fade" role="dialog" id="referralForm">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="jim-content referral_body">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade" role="dialog" id="editReferralForm" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close exit_edit_btn" data-dismiss="modal">&times;</button>
            </div>
            <div class="edit_referral_body">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="icd-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title" style="font-size: 17pt;">Search ICD-10 by keyword</h4>
            </div>
            <div class="modal-body">
                <div class="input-group input-group-lg">
                    <input type="text" id="icd10_keyword" class="form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat" onclick="searchICD10()">Find</button>
                    </span>
                </div><br>
                <div class="icd_body"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="othersDiagnosis()"> Other Diagnosis</button>
                <button type="button" class="btn btn-success" onclick="getAllCheckBox()"><i class="fa fa-save"></i> Save selected check</button>
            </div>
        </div>
    </div>
</div>

