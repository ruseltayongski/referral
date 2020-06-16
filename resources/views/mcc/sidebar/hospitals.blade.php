<?php
    $hospitals = \App\Facility::
        select("facility.id","facility.name","province.description as province")
        ->leftJoin("province","province.id","=","facility.province")
        ->where('facility.id','!=',$user->facility_id)
        ->where('facility.status',1)
        ->orderBy('facility.name','asc')
        ->get();
?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Online Hospital <span class="badge bg-blue hospital_online">0</span></h3>
    </div>

    <div class="panel-body">
        <div class="list-group">
            <?php $hospital_online_count = 0; ?>
            @foreach($hospitals as $row)
            <?php
                $active = \App\Http\Controllers\mcc\HomeCtrl::countOnline($row->id);
                $class = 'danger';
                $text = '';
                if($active>0){
                    $class = 'success';
                    $text = 'text-green';
                    $hospital_online_count++;
                }
            ?>
            <a href="{{ asset('mcc/report/users') }}" class="list-group-item clearfix {{ $text }}" title="{{ $row->name }}">
                @if(strlen($row->name)>28)
                    {{ substr($row->name,0,28) }}...
                @else
                    {{ $row->name }}
                @endif
                <br><small class="text-yellow">({{ $row->province }})</small>
                <span class="pull-right">
                    <i class="fa fa-circle text-{{ $class }}"></i>
                </span>
            </a>
            @endforeach
            <?php Session::put('hospital_online_count',$hospital_online_count); ?>
        </div>

    </div>
</div>