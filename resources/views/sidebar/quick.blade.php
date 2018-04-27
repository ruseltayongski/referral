<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Links</h3>
    </div>
    <?php
    $count = \App\Http\Controllers\doctor\ReferralCtrl::countReferral();
    ?>
    <div class="panel-body">
        <div class="list-group">
            <a href="{{ asset('doctor/referral') }}" class="list-group-item clearfix">
                Referral
                <span class="pull-right">
                        <div class="badge">
                            <i class="fa fa-wheelchair-alt"></i> <span class="count_referral">{{ $count }}</span> New
                        </div>

                    </span>
            </a>
            <a href="{{ asset('doctor/patient') }}" class="list-group-item clearfix">
                Patient List
                <span class="pull-right">
                        <i class="fa fa-users"></i>
                    </span>
            </a>
            <a href="{{ asset('doctor/report') }}" class="list-group-item clearfix">
                Generate Report
                <span class="pull-right">
                        <i class="fa fa-print"></i>
                    </span>
            </a>
        </div>

    </div>
</div>