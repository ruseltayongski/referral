<div class="timeline-footer">
    <div class="form-inline">
        @if( ($row->status == 'referred' || $row->status == 'seen' || $row->status == 'redirected' || $row->status == 'transferred') && $user->department_id == $row->department_id )
            <div class="form-group">
                <a class="btn btn-warning btn-xs btn-refer" href="{{ $modal }}"
                   data-toggle="modal"
                   data-code="{{ $row->code }}"
                   data-item="#item-{{ $row->id }}"
                   data-status="{{ $row->status }}"
                   data-type="{{ $row->type }}"
                   data-id="{{ $row->id }}"
                   data-referred_from="{{ $row->referred_from }}"
                   data-backdrop="static">
                    <i class="fa fa-folder"></i> View Form
                </a>
            </div>
        @endif
        @if($seen > 0)
            <div class="form-group">
                <a href="#seenModal" data-toggle="modal"
                   data-id="{{ $row->id }}"
                   class="btn btn-success btn-xs btn-seen"><i class="fa fa-user-md"></i> Seen
                    @if($seen>0)
                        <small class="badge bg-green-active">{{ $seen }}</small>
                    @endif
                </a>
            </div>
        @endif
        @if($caller_md > 0)
            <div class="form-group">
                <a href="#callerModal" data-toggle="modal"
                   data-id="{{ $row->id }}"
                   class="btn btn-primary btn-xs btn-caller col-xs-12"><i class="fa fa-phone"></i> Caller
                    @if($caller_md>0)
                        <small class="badge bg-blue-active">{{ $caller_md }}</small>
                    @endif
                </a>
            </div>
        @endif
        @if($redirected > 0)
            <div class="form-group">
                <a href="#" data-toggle="modal"
                   data-id="{{ $row->id }}"
                   class="btn btn-danger btn-xs btn-caller col-xs-12"><i class="fa fa-chevron-circle-right"></i> Redirected
                    @if($redirected>0)
                        <small class="badge bg-red-active">{{ $redirected }}</small>
                    @endif
                </a>
            </div>
        @endif
        <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal"
                data-target="#feedbackModal"
                data-code="{{ $row->code }}">
            <i class="fa fa-comments"></i>
            ReCo
            @if($feedback>0)
                <span class="badge bg-blue">{{ $feedback }}</span>
            @endif
        </button>

    </div>
</div>