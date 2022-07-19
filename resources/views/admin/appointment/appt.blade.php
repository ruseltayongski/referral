<?php
$counter = 1;
$morrow = new DateTime('tomorrow'); $morrow = $morrow->format('Y-m-d')
?>

@extends('layouts.app')

<style>
    .bg_yellow_orange {
        background-color: #ffc297b0;
    }

    .bg_light_blue {
        background-color: #97d4df4f;
    }

    .bg_gray {
        background-color: #b5bbc873;
    }
</style>

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right">
                <form action="{{ asset('admin/appointment') }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group" style="margin-bottom: 10px;">
                            <input type="text" class="form-control" name="appt_keyword" value="{{ $keyword }}" id="keyword" placeholder="Search...">
                        <button type="submit" class="btn btn-success btn-sm btn-flat">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <button type="submit" value="view_all" name="view_all" class="btn btn-warning btn-sm btn-flat">
                            <i class="fa fa-eye"></i> View All
                        </button><br><br>
                        <select class="form-control select" id="status_filter" name="status_filter">
                            <option value="">Select status...</option>
                            <option value="new"> New </option>
                            <option value="seen"> Seen </option>
                            {{--<option value="approved"> Approved </option>--}}
                        </select>
                        <input type="date" name="date_filter" id="date_filter" class="form-control">
                        <button type="submit" value="view_all" name="view_all" class="btn btn-info btn-sm btn-flat">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
            <h2>APPOINTMENTS</h2>
            <div class="form-inline">
                <h5><b>Legend:</b>&nbsp;&nbsp;
                    <span style="border: 1px solid black; background-color: #ffc297b0;">&emsp;&nbsp;</span> New &emsp;
                    <span style="border: 1px solid black; background-color: #97d4df4f;">&emsp;&nbsp;</span> Seen &emsp;
                    {{--<span style="border: 1px solid black; background-color: #b5bbc873;">&emsp;&nbsp;</span> Approved--}}
                </h5>
            </div>
        </div>
        <div class="box-body appointments">
            @if(count($data)>0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr class="bg-success bg-navy-active">
                            <th class="text-center">Requester</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Contact</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Date Requested</th>
                            <th class="text-center">Date of Appointment</th>
                        </tr>
                        @foreach($data as $row)
                            <?php
                            if($row->status == 'new')
                                $bg= 'bg_yellow_orange';
                            else if($row->status == 'seen')
                                $bg = 'bg_light_blue';
//                            else if($row->status == 'approved')
//                                $bg = 'bg_gray';
                            ?>
                            <tr class="{{ $bg }}" id="bgcolor{{$row->id}}" style="font-size: 13px">
                                <td style="white-space: nowrap;">
                                    <b>
                                        <a
                                            href="#appt_modal"
                                            data-toggle="modal"
                                            onclick="ApptBody('<?php echo $row->id; ?>')"
                                        >
                                            {{ $row->name }}
                                        </a>
                                    </b>
                                </td>
                                <td> {{ $row->email }} </td>
                                <td class="text-center"> {{ $row->contact }} </td>
                                <td class="text-center"> {{ $row->category}} </td>
                                <td class="text-center"> {{ date_format($row->created_at, 'F d, Y') }} </td>
                                <td class="text-center">
                                    <?php $date = new DateTime($row->preferred_date);
                                    $date = date_format($date, 'F d, Y');?>
                                    <b>{{ $date }}</b>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="text-center">
                        {!! $data->links() !!}
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <span class="text-warning">
                        <i class="fa fa-warning"></i> No appointments found!
                    </span>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" role="dialog" id="appt_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><b>APPOINTMENT REQUEST</b></h4>
                </div>
                <div class="modal-body appt_body">
                    <form method="post" action="{{ asset('admin/appointment/approve') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="modal_id">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <b>Name:</b><br>
                                <input type="text" class="form-control" id="modal_name" style="background-color: white" readonly>
                            </div>
                            <div class="col-md-4 form-group mt-3 mt-md-0">
                                <b>Email:</b><br>
                                <input type="text" class="form-control" id="modal_email" style="background-color: white" readonly>
                            </div>
                            <div class="col-md-4 form-group mt-3 mt-md-0">
                                <b>Contact Number:</b><br>
                                <input type="text" class="form-control" id="modal_contact" style="background-color: white" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group mt-3">
                                <b>Date:</b><br>
                                <input type="date" style="background-color: white" name="date" id="modal_date" class="form-control" min="{{ $morrow }}" readonly>
                                <small class="text-danger" id="warning_date">&emsp;Invalid Date!</small>
                            </div>
                            <div class="col-md-4 form-group mt-3">
                                <b>Category:</b><br>
                                <input type="text" class="form-control" id="modal_category" style="background-color: white" readonly>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <b>Message:</b><br>
                            <textarea class="form-control" rows="5" style="resize: none; background-color: white" id="modal_message" readonly></textarea>
                        </div>

                        <div class="form-group pull-right">
                            <button class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i> Close </button>
                            {{--<button type="submit" id="approve_btn" class="btn btn-success btn-flat btn-submit"><i class="fa fa-check"></i> Approve</button>--}}
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection
@section('js')
    <script>
        @if(Session::get('appt_notif'))
        Lobibox.notify('success', {
            title: "",
            msg: "<?php echo Session::get("appt_msg"); ?>",
            size: 'mini',
            rounded: true
        });
        <?php
        Session::put("appt_notif",false);
        Session::put("appt_msg",false)
        ?>
        @endif

        function ApptBody(id){
            var json = {
                "id" : id,
                "_token" : "<?php echo csrf_token()?>"
            };
            var url = "<?php echo asset('admin/appointment/details') ?>";
            $.post(url,json,function(data){
                $('#modal_id').val(data.id);
                $('#modal_name').val(data.name);
                $('#modal_email').val(data.email);
                $('#modal_contact').val(data.contact);
                $('#modal_date').val(data.preferred_date);
                $('#modal_category').val(data.category);
                $('#modal_message').val(data.message);

                $('#warning_date').hide();

                if(data.status === 'seen') {
                    $('#bgcolor'+id).attr('class','bg_light_blue');
                }
            });
        }

        $('#modal_date').on('change', function() {
            var val = $(this).val();
            if(val <= new Date().toISOString().slice(0, 10)) {
                $('#warning_date').show();
                $('#approve_btn').prop('disabled', true);
            }
            else {
                $('#warning_date').hide();
                $('#approve_btn').prop('disabled', false);
            }
        });
    </script>
@endsection
