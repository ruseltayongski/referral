<!-- Message. Default to the left -->
<?php
    $user = \Illuminate\Support\Facades\Session::get('auth');
    $session = 0;
?>
<div class="reco-body">
    @if(count($data)>0)
        @foreach($data as $row)
            <?php
                $pull = 'right';
                $position = 'left';
                if($session==0){
                    \Illuminate\Support\Facades\Session::put('last_scroll_id',$row->id);
                    $session = 1;
                }
                if($user->id==$row->sender){
                    $pull = 'left';
                    $position = 'right';
                }
            ?>
            <div class="direct-chat-msg {{ $position }}">
                <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-{{ $position }}">{{ ucwords(mb_strtolower($row->fname)) }} {{ ucwords(mb_strtolower($row->lname)) }}</span>
                    <span class="direct-chat-timestamp pull-{{ $pull }}">{{ date('d M h:i a',strtotime($row->date)) }}</span>
                </div>
                <!-- /.direct-chat-info -->
                <?php
                    $icon = 'receiver.png';
                    if($user->id==$row->sender)
                        $icon = 'sender.png';
                ?>
                <img class="direct-chat-img" title="{{ $row->facility }}" src="{{ url('resources/img/'.$icon) }}" alt="Message User Image"><!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                    {!! nl2br($row->message) !!}
                </div>
                <!-- /.direct-chat-text -->
            </div>
        @endforeach
    @endif
</div>
<!-- /.direct-chat-msg -->

{{--<!-- Message to the right -->--}}
{{--<div class="direct-chat-msg right">--}}
    {{--<div class="direct-chat-info clearfix">--}}
        {{--<span class="direct-chat-name pull-right">Sarah Bullock</span>--}}
        {{--<span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-info -->--}}
    {{--<img class="direct-chat-img" src="{{ url('resources/img/receiver.png') }}" alt="Message User Image"><!-- /.direct-chat-img -->--}}
    {{--<div class="direct-chat-text">--}}
        {{--You better believe it!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<div class="direct-chat-msg right" style="margin-top:-13px;">--}}
    {{--<div class="direct-chat-text">--}}
        {{--You better believe it!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<!-- /.direct-chat-msg -->--}}

{{--<!-- Message. Default to the left -->--}}
{{--<div class="direct-chat-msg">--}}
    {{--<div class="direct-chat-info clearfix">--}}
        {{--<span class="direct-chat-name pull-left">Alexander Pierce</span>--}}
        {{--<span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-info -->--}}
    {{--<img class="direct-chat-img" src="{{ url('resources/img/sender.png') }}" alt="Message User Image"><!-- /.direct-chat-img -->--}}
    {{--<div class="direct-chat-text">--}}
        {{--Is this template really for free? That's unbelievable!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<!-- /.direct-chat-msg -->--}}

{{--<!-- Message to the right -->--}}
{{--<div class="direct-chat-msg right">--}}
    {{--<div class="direct-chat-info clearfix">--}}
        {{--<span class="direct-chat-name pull-right">Sarah Bullock</span>--}}
        {{--<span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-info -->--}}
    {{--<img class="direct-chat-img" src="{{ url('resources/img/receiver.png') }}" alt="Message User Image"><!-- /.direct-chat-img -->--}}
    {{--<div class="direct-chat-text">--}}
        {{--You better believe it!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<div class="direct-chat-msg right" style="margin-top:-13px;">--}}
    {{--<div class="direct-chat-text">--}}
        {{--You better believe it!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<!-- /.direct-chat-msg -->--}}

{{--<!-- Message. Default to the left -->--}}
{{--<div class="direct-chat-msg">--}}
    {{--<div class="direct-chat-info clearfix">--}}
        {{--<span class="direct-chat-name pull-left">Alexander Pierce</span>--}}
        {{--<span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-info -->--}}
    {{--<img class="direct-chat-img" src="{{ url('resources/img/sender.png') }}" alt="Message User Image"><!-- /.direct-chat-img -->--}}
    {{--<div class="direct-chat-text">--}}
        {{--Is this template really for free? That's unbelievable!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<!-- /.direct-chat-msg -->--}}

{{--<!-- Message to the right -->--}}
{{--<div class="direct-chat-msg right">--}}
    {{--<div class="direct-chat-info clearfix">--}}
        {{--<span class="direct-chat-name pull-right">Sarah Bullock</span>--}}
        {{--<span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-info -->--}}
    {{--<img class="direct-chat-img" src="{{ url('resources/img/receiver.png') }}" alt="Message User Image"><!-- /.direct-chat-img -->--}}
    {{--<div class="direct-chat-text">--}}
        {{--You better believe it!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<div class="direct-chat-msg right" style="margin-top:-13px;">--}}
    {{--<div class="direct-chat-text">--}}
        {{--You better believe it!--}}
    {{--</div>--}}
    {{--<!-- /.direct-chat-text -->--}}
{{--</div>--}}
{{--<!-- /.direct-chat-msg -->--}}