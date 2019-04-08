<?php
    $hospitals = \App\Facility::orderBy('name','asc')
        ->where('id','!=',$user->facility_id)
        ->where('status',1)
        ->get();
?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Other Links</h3>
    </div>

    <div class="panel-body">
        <div class="list-group">
            @foreach($hospitals as $row)
            <?php
                $active = \App\Http\Controllers\mcc\HomeCtrl::countOnline($row->id);
                $class = 'danger';
                if($active>0)
                    $class = 'success';
            ?>
            <a href="{{ asset('mcc/report/users') }}" class="list-group-item clearfix" title="{{ $row->name }}">
                @if(strlen($row->name)>28)
                    {{ substr($row->name,0,28) }}...
                @else
                    {{ $row->name }}
                @endif
                <span class="pull-right">
                    <i class="fa fa-circle text-{{ $class }}"></i>
                </span>
            </a>
            @endforeach
        </div>

    </div>
</div>