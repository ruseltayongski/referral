@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Notification History</h3>
                    <button type="button" class="btn btn-default btn-sm pull-right" id="mark-all-websocket-read">
                        <i class="fa fa-trash"></i> Clear notifications
                    </button>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>Status</th><th>Notification</th><th>Date</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr class="{{ $notification->read_at ? '' : 'info' }}">
                                    <td>{{ $notification->read_at ? 'Read' : 'Unread' }}</td>
                                    <td><strong>{{ $notification->title }}</strong><br>{{ $notification->body }}</td>
                                    <td>{{ optional($notification->occurred_at ?: $notification->created_at)->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <a class="btn btn-warning btn-xs websocket-notification-open"
                                           href="{{ $notification->destination_url }}"
                                           data-id="{{ $notification->id }}"
                                           data-type="{{ $notification->event_type }}"
                                           data-code="{{ $notification->related_code }}"
                                           data-related-id="{{ $notification->related_id }}">
                                            <i class="fa fa-external-link"></i> Open
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No notifications yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        const baseUrl = @json(url('/'));
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        function markNotificationRead(id) {
            return $.ajax({
                url: baseUrl + '/websocket-notifications/' + encodeURIComponent(id) + '/read',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
        }

        $('.websocket-notification-open').on('click', function (event) {
            event.preventDefault();
            const link = $(this);
            if (link.data('type') === 'reco_message') {
                sessionStorage.setItem('reco_payload', JSON.stringify({
                    code: link.data('code'),
                    reco_id: link.data('related-id')
                }));
            }
            markNotificationRead(link.data('id')).always(function () {
                window.location.href = link.attr('href');
            });
        });

        $('#mark-all-websocket-read').on('click', function () {
            $.ajax({
                url: baseUrl + '/websocket-notifications/read-all',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            }).always(function () {
                window.location.reload();
            });
        });
    });
</script>
@endsection