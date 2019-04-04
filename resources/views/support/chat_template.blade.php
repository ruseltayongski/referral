<?php
$user = \Illuminate\Support\Facades\Session::get('auth');
$session = 0;
?>
@if(count($data)>0)
    @foreach($data as $row)
        <?php
        if($session==0){
            \Illuminate\Support\Facades\Session::put('last_scroll_id',$row->id);
            $session = 1;
        }
        ?>
        <div class="direct-chat-msg @if($user->id==$row->sender) right @endif">
            <div class="direct-chat-info clearfix">
                <span class="direct-chat-name pull-left">{{ ucwords(mb_strtolower($row->fname)) }} {{ ucwords(mb_strtolower($row->lname)) }}</span>
                <span class="direct-chat-timestamp pull-right">{{ date('d M h:i a',strtotime($row->date)) }}</span>
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