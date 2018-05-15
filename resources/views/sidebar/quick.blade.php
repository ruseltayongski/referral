<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Main Menu</h3>
    </div>
    <?php
    $count = \App\Http\Controllers\doctor\ReferralCtrl::countReferral();
    ?>
    <div class="panel-body">
        <div class="list-group">
            <a href="{{ asset('doctor/referral') }}" class="list-group-item clearfix">
                Incoming
                <span class="pull-right">
                        <div class="badge">
                            <i class="fa fa-wheelchair-alt"></i> <span class="count_referral">{{ $count }}</span> New
                        </div>

                    </span>
            </a>
            <a href="{{ asset('doctor/referred') }}" class="list-group-item clearfix">
                Referred
                <span class="pull-right">
                        <i class="fa fa-ambulance"></i>
                    </span>
            </a>
            <a href="{{ asset('doctor/patient') }}" class="list-group-item clearfix">
                Accepted
                <span class="pull-right">
                        <i class="fa fa-users"></i>
                    </span>
            </a>
            <a href="{{ asset('doctor/list') }}" class="list-group-item clearfix">
                Online Doctors
                <span class="pull-right">
                        <i class="fa fa-user-md"></i>
                    </span>
            </a>
        </div>

    </div>
</div>

<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Other Links</h3>
    </div>
    <div class="panel-body">
        <div class="list-group">
            <a href="{{ asset('doctor/report') }}" class="list-group-item clearfix">
                Generate Report
                <span class="pull-right">
                        <i class="fa fa-print"></i>
                    </span>
            </a>
            <a href="#resetPasswordModal" data-toggle="modal" class="list-group-item clearfix">
                Change Password
                <span class="pull-right">
                        <i class="fa fa-key"></i>
                    </span>
            </a>
            <a href="{{ url('logout') }}" class="list-group-item clearfix">
                Logout
                <span class="pull-right">
                        <i class="fa fa-sign-out"></i>
                    </span>
            </a>
        </div>

    </div>
</div>