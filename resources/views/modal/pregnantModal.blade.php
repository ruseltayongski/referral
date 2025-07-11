<style>
    @media only screen and (max-width: 720px) {
        #pregnant-content {
            width: 100%;
        }
    }
</style>
@php
    $appointmentJson = request()->query('appointment');
    $appointmentData = json_decode($appointmentJson, true);
    $appointmentId = $appointmentData[0]['appointmentId'] ?? 'N/A'; // Handle missing data
@endphp

<div class="modal fade" role="dialog" id="pregnantModal" style="text-align: center">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="pregnant-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
                <i class="fa fa-user-secret"></i> SELECT OPTION 
            </div>
            <div class="modal-body">
                <!-- <button  data-target="#nonPregnantChooseVersionModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-warning col-sm-6"> -->
                <button data-backdrop="static" data-toggle="modal" type="button" class="btn btn-warning col-sm-6" onclick="openNewForms('normal')">    
                    <img src="{{ url('resources/img/female.png') }}" width="100" />
                    <br />
                    Non-Pregnant
                </button>
                <!-- <button data-target="#pregnantchooseVersionModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-info col-sm-6"> -->
               
                <button id="pregnantButton" data-target="#pregnantchooseVersionModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-info col-sm-6" onclick="openNewForms('pregnant')">    
                    <img src="{{ url('resources/img/pregnant.png') }}" width="100" />
                    <br />
                   Pregnant
                </button>
            </div>
            <div class="modal-footer"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="pregnantModalTelemed" style="text-align: center">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="pregnant-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
                <i class="fa fa-user-secret"></i> SELECT OPTION 
            </div>
            <div class="modal-body">
                <!-- <button  data-target="#nonPregnantChooseVersionModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-warning col-sm-6"> -->
                <button data-backdrop="static" data-target="#normalFormModal" data-toggle="modal" type="button" class="btn btn-warning col-sm-6" onclick="selectFormTitle('Clinical')">    
                    <img src="{{ url('resources/img/female.png') }}" width="100" />
                    <br />
                    Non-Pregnant
                </button>
                <!-- <button data-target="#pregnantchooseVersionModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-info col-sm-6"> -->
               
                <button id="pregnantButton" data-target="#pregnantFormModal" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-info col-sm-6" onclick="selectFormTitle('BEmONC/ CEmONC ')">    
                    <img src="{{ url('resources/img/pregnant.png') }}" width="100" />
                    <br />
                   Pregnant
                </button>
            </div>
            <div class="modal-footer"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="pregnantModalWalkIn" style="text-align: center">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="pregnant-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span></button>
                <i class="fa fa-user-secret"></i> SELECT OPTION
            </div>
            <div class="modal-body">
                <button  data-target="#normalFormModalWalkIn" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-warning col-sm-6" onclick="setClinicalFormTile('normal')">
                    <img src="{{ url('resources/img/female.png') }}" width="100" />
                    <br />
                    Non-Pregnant
                </button>
                <button data-target="#pregnantFormModalWalkIn" data-backdrop="static" data-toggle="modal" type="button" class="btn btn-info col-sm-6" onclick="setClinicalFormTile('pregnant')">
                    <img src="{{ url('resources/img/pregnant.png') }}" width="100" />
                    <br />
                    Pregnant
                </button>
            </div>
            <div class="modal-footer"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
    $(document).ready(function() {

        // let appointmentJson = @json($appointmentId);     
        // console.log("appointmentID::", appointmentJson);
        // if (!appointmentJson || appointmentJson === "" || appointmentJson === "N/A") {
        //     $("#pregnantButton").prop("disabled", false); // Enable if empty, null, or N/A
        // } else {
        //     $("#pregnantButton").prop("disabled", true); // Disable if appointmentId exists
        // }

    });
</script>