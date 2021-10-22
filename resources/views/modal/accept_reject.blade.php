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
</style>

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