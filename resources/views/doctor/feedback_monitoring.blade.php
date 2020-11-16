<div class="reco-body">
    @if(count($data)>0)
        @foreach($data as $row)
            <div class="direct-chat-msg left">
                <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-{{ $position }}">DOH MONITORING</span>
                    <span class="direct-chat-timestamp pull-{{ $pull }}">{{ date('d M h:i a',strtotime($row->created_at)) }}</span>
                </div>
                <!-- /.direct-chat-info -->
                <img class="direct-chat-img" title="{{ $row->remark_by }}" src="{{ url('resources/img/doh.png') }}" alt="Message User Image"><!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                    {!! nl2br($row->remarks) !!}
                </div>
                <!-- /.direct-chat-text -->
            </div>
        @endforeach
    @endif
</div>
