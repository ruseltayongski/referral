<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i> Report <span class="caret"></span></a>
    <ul class="dropdown-menu">
        <li>
            <a href="{{ url('admin/report/online') }}"><i class="fa fa-users"></i>Online Users</a>
        </li>
        <li>
            <a href="{{ url('online/facility') }}"><i class="fa fa-hospital-o"></i>Online Facility</a>
        </li>
        <li>
            <a href="{{ url('offline/facility') }}"><i class="fa fa-times-circle-o"></i>Offline Facility</a>
        </li>
        <li class="dropdown-submenu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-calendar-check-o"></i>Login Status</a>
            <ul class="dropdown-menu">
                @foreach(\App\Province::get() as $prov)
                    <li><a href="{{ asset('weekly/report').'/'.$prov->id }}">{{ $prov->description }} Province</a></li>
                @endforeach
            </ul>
        </li>
        <li><a href="{{ url('onboard/facility') }}"><i class="fa fa-ambulance"></i>Onboard Facility</a></li>
    <!--
        <li><a href="{{ url('onboard/users') }}"><i class="fa fa-ambulance"></i>Onboard Users</a></li>
            <li><a href="{{ url('admin/report/referral') }}"><i class="fa fa-line-chart"></i>Referral Status</a></li>
            <li><a href="{{ url('admin/daily/users') }}"><i class="fa fa-users"></i>Daily Users</a></li>
            <li><a href="{{ url('admin/daily/referral') }}"><i class="fa fa-building"></i>Daily Hospital</a></li>
        -->
        <li><a href="{{ url('admin/report/consolidated/incomingv2') }}"><i class="fa fa-file-archive-o"></i>Consolidated</a></li>
        <li><a href="{{ url('admin/statistics/incoming') }}"><i class="fa fa-certificate"></i>Statistics Report Incoming</a></li>
        <li><a href="{{ url('admin/statistics/outgoing') }}"><i class="fa fa-certificate"></i>Statistics Report Outgoing</a></li>
        <!--
            <li><a href="{{ url('admin/er_ob') }}"><i class="fa fa-certificate"></i>Statistics Report ER OB</a></li>
        -->
        <li><a href="{{ url('admin/average/user_online') }}"><i class="fa fa-certificate"></i>Average User's Online</a></li>
        <!--
            <li><a href="{{ url('admin/report/graph/bar_chart') }}"><i class="fa fa-bar-chart-o"></i>Graph</a></li>
        -->
    </ul>
</li>