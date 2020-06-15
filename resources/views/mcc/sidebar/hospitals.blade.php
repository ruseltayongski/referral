<?php
    $hospitals = \App\Facility::orderBy('name','asc')
        ->where('id','!=',$user->facility_id)
        ->where('status',1)
        ->get();
?>
<div class="panel panel-jim">
    <div class="panel-heading">
        <h3 class="panel-title">Online Hospital <span class="badge bg-blue hospital_online">0</span></h3>
    </div>

    <div class="panel-body">
        <div class="list-group">
            <?php $count_hospital_online = 0; ?>
            @foreach($hospitals as $row)
            <?php
                $active = \App\Http\Controllers\mcc\HomeCtrl::countOnline($row->id);
                $class = 'danger';
                if($active>0){
                    $class = 'success';
                    $count_hospital_online++;
                }
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
<script>
    $(".hospital_online").text("<?php echo $count_hospital_online; ?>");
</script>