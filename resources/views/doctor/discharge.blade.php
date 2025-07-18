<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ url('resources/plugin/daterange/daterangepicker.css') }}" />
@endsection

@section('content')
    <style>
        .facility {
            color: #ff8456;
        }
    .discharged-file:hover {
        cursor: pointer;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

</style>
    </style>
    <div class="col-md-12">
        <div class="jim-content">
            <div class="pull-right">
                <form class="form-inline" action="{{ url('doctor/discharge') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Code,Firstname,Lastname" value="{{ \Illuminate\Support\Facades\Session::get('keywordDischarged') }}" name="keyword">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control form-control-sm" id="daterange" max="{{ date('Y-m-d') }}" name="daterange">
                    </div>
                    <button type="submit" class="btn btn-md btn-success" style="padding: 8px 15px;"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <h3 class="page-header">{{ $title }} <small class="text-danger">TOTAL: {{ number_format($data->total()) }}</small> </h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- The time line -->
                    @if(count($data)>0)
                        <div class="hide info alert alert-success">
                        <span class="text-success">
                            <i class="fa fa-check"></i> <span class="message"></span>
                        </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="bg-gray">
                                <tr>
                                    <th>Referring Facility</th>
                                    <th>Patient Name/Code</th>
                                    <th>Date Discharged</th>
                                    <th>File Upload</th>
                                    {{--<th>Status</th>--}}
                                    {{--<th>Record</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $row)
                                    <?php
                                    $modal = ($row->type=='normal') ? '#normalFormModal' : '#pregnantFormModal';
                                    $type = ($row->type=='normal') ? 'Non-Pregnant' : 'Pregnant';
                                    ?>
                                    <tr>
                                        <td style="white-space: nowrap;">
                                    <span class="facility" title="{{ $row->name }}">
                                    @if(strlen($row->name)>25)
                                            {{ substr($row->name,0,25) }}...
                                        @else
                                            {{ $row->name }}
                                        @endif
                                    </span>
                                            <br />
                                            <span class="text-muted">{{ $type }}</span>
                                        </td>
                                        <td>
                                            <a data-toggle="modal" href="#referralForm"
                                                data-type="{{ $row->type }}"
                                                data-id="{{ $row->id }}"
                                                data-code="{{ $row->code }}"
                                                data-referral_status="referring"
                                                class="view_form">
                                                <span class="text-primary">{{ $row->patient_name }}</span>
                                                <br />
                                                <small class="text-warning">{{ $row->code }}</small>
                                            </a>
                                        </td>
                                        <?php
                                            $status = \App\Http\Controllers\doctor\PatientCtrl::getDischargeDate('discharged',$row->code);
                                        ?>
                                        <td>{{ $status }}</td>
                                        {{--<td>{{ strtoupper($row->status) }}</td>--}}
                                        {{--<td>
                                            <a href="{{ url('doctor/referred?referredCode='.$row->code) }}" class="btn btn-block btn-success" target="_blank">
                                                <i class="fa fa-stethoscope"></i> Track
                                            </a>
                                        </td>--}}
                                        <td>
                                            <img src="{{ asset('resources/img/DischargedFolder.png') }}" 
                                                onclick="DischargeFileResult('{{$row->code}}')"
                                                class="img-thumbnail discharged-file" 
                                                data-code="{{$row->code}}"
                                                style="width:40px; height:40px; object-fit:cover; display:none">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="text-center">
                                {{ $data->links() }}
                            </div>
                        </div>

                    @else
                        <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No data found!
                        </span>
                        </div>
                    @endif
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
    @include('modal.accept_reject')
@endsection
{{--@include('script.firebase')--}}
@section('js')
@include('script.referred')
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    @include('script.datetime')
    @include('script.accepted')

    <script src="{{ url('resources/plugin/daterange/moment.min.js') }}"></script>
    <script src="{{ url('resources/plugin/daterange/daterangepicker.js') }}"></script>
    <?php
    $start = \Illuminate\Support\Facades\Session::get('startDischargedDate');
    $end = \Illuminate\Support\Facades\Session::get('endDischargedDate');
    if(!$start)
        $start = \Carbon\Carbon::now()->startOfYear()->format('m/d/Y');

    if(!$end)
        $end = \Carbon\Carbon::now()->endOfYear()->format('m/d/Y');

    $start = \Carbon\Carbon::parse($start)->format('m/d/Y');
    $end = \Carbon\Carbon::parse($end)->format('m/d/Y');
    ?>
    <script>
        $('#daterange').daterangepicker({
            "startDate": "{{ $start }}",
            "endDate": "{{ $end }}",
            "opens": "left"
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
            console.log("{{ $start }}");
        });

        //hiding folder image  empty files
        function checkDischargeFiles(code) {
            const url = "{{ asset('get-discharge-files') }}";
            
            return $.ajax({
                url: `${url}/${code}`,
                type: "GET",
                dataType: "json",
                success: function(files) {
                    const folderIcon = document.querySelector(`.discharged-file[data-code="${code}"]`);
                    
                    if (!files || !Array.isArray(files) || files.length === 0) {
                        if (folderIcon) {
                            folderIcon.style.display = "none";
                        }
                        return;
                    }

                    // Show folder icon only if files exist
                    if (folderIcon) {
                        folderIcon.style.display = "inline-block";
                    }
                },
                error: function(error) {
                    console.error(`Error checking files for code ${code}:`, error);
                    // Hide folder icon on error
                    const folderIcon = document.querySelector(`.discharged-file[data-code="${code}"]`);
                    if (folderIcon) {
                        folderIcon.style.display = "none";
                    }
                }
            });
        }

        //hiding folder image  empty files
        function initializeFolderIcons() {
            //get folder icon
            const folderIcons = document.querySelectorAll('.discharged-file');
            //check nato ang isa isa ka files
            folderIcons.forEach(icon => {
                const code = icon.getAttribute('data-code');
                if (code) {
                    checkDischargeFiles(code);
                }
            });
        }

        function DischargeFileResult(code) {
            const url = "{{ asset('get-discharge-files') }}";

            $.ajax({
                url: `${url}/${code}`, // Endpoint URL
                type: "GET",
                dataType: "json",
                success: function(files) {
                    // console.log("Files received:", files);

                    const folderIcon = document.querySelector(`.discharged-file[data-code="${code}"]`);

                    if (!files || !Array.isArray(files) || files.length === 0) {
                         
                        return;
                    }

                    // Remove any existing modal before creating a new one
                    $("#DischargeModal").remove();

                    let filesListHtml = files.map(fileUrl => {
                        let filename = fileUrl.substring(fileUrl.lastIndexOf("/") + 1);
                        let fileExtension = filename.split('.').pop().toLowerCase();
                        let iconSrc = "";

                        // Determine icon based on file type
                        if (fileExtension === 'pdf') {
                            iconSrc = '{{ asset("resources/img/pdf_icon.png") }}';
                        } else if (['doc', 'docx'].includes(fileExtension)) {
                            iconSrc = '{{ asset("resources/img/document_icon.png") }}';
                        } else if (['xls', 'xlsx'].includes(fileExtension)) {
                            iconSrc = '{{ asset("resources/img/sheet_icon.png") }}';
                        } else if (['png', 'jpg', 'jpeg', 'gif'].includes(fileExtension)) {
                            iconSrc = '{{ asset("resources/img/fileImage.png") }}';
                        } else {
                            iconSrc = '{{ asset("resources/img/default_file_icon.png") }}';
                        }

                        return `
                            <div class="col-6 col-sm-4 col-md-3 text-center file-item">
                                <div class="file-box">
                                    <a href="${fileUrl}" target="_blank" class="file-preview">
                                        <img src="${iconSrc}" class="img-thumbnail file-img" alt="${filename}">
                                    </a>
                                    <div class="file-name">${filename}</div>
                                </div>
                            </div>
                        `;
                    }).join("");

                    // Modal HTML
                    var modalHtml = `
                        <div class="modal fade" id="DischargeModal" tabindex="-1" role="dialog" aria-labelledby="folderModalLabel">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content modal-vertical-list">
                                    <div class="modal-header px-2 py-2">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" id="folderModalLabel">Discharged Document Result</h4>
                                    </div>
                                    <div class="modal-body py-2">
                                        <div class="container-fluid">
                                            <div class="row">${filesListHtml}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .file-box {
                                padding: 10px;
                                border-radius: 8px;
                                background: #f8f9fa;
                                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
                                margin-bottom: 15px;
                                transition: all 0.3s ease-in-out;
                            }
                            .file-box:hover {
                                transform: translateY(-3px);
                                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
                            }
                            .file-img {
                                width: 100px;
                                height: 100px;
                                object-fit: cover;
                                border-radius: 5px;
                            }
                            .file-name {
                                margin-top: 8px;
                                font-size: 12px;
                                font-weight: 600;
                                color: #333;
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                                max-width: 100px;
                                text-align: center;
                            }
                        </style>
                    `;

                    // Append modal to body
                    $("body").append(modalHtml);

                    // Show modal
                    $("#DischargeModal").modal("show");
                    $('.popoverReferral').popover('hide');

                },
                error: function(error) {
                    console.error("Error fetching files:", error);
                    alert("Failed to load files. Please try again.");
                }
            });
        }
        //hiding folder image  empty files
        $(document).ready(function() {
            initializeFolderIcons();
        });

    </script>
@endsection

