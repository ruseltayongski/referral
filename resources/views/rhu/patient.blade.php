@extends('layouts.app')

@section('content')
    <div class="col-md-3">
        @include('sidebar.filter_profile')
        @include('sidebar.rhu')
    </div>
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">Patient List</h3>

            <div class="box">
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>Barangay</th>
                                <th style="width:18%;">Action</th>
                            </tr>
                            <tr>
                                <td>
                                    Anna Baclayon<br />
                                    <small class="text-info">03072018-0001-212701</small>
                                </td>
                                <td>Female</td>
                                <td>26</td>
                                <td>Guadalupe</td>
                                <td>
                                    <a href="#pregnantModal" data-toggle="modal" class="btn btn-primary btn-xs"><i class="fa fa-stethoscope"></i> Refer </a>
                                    <a href="#" class="btn btn-warning btn-xs"><i class="fa fa-line-chart"></i> History </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Carlo Dumaguete<br />
                                    <small class="text-info">03065018-0056-212502</small>
                                </td>
                                <td>Male</td>
                                <td>31</td>
                                <td>Banawa</td>
                                <td>
                                    <a href="#normalFormModal" data-backdrop="static" data-toggle="modal" class="btn btn-primary btn-xs"><i class="fa fa-stethoscope"></i> Refer </a>
                                    <a href="#" class="btn btn-warning btn-xs"><i class="fa fa-line-chart"></i> History </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Ester Forester<br />
                                    <small class="text-info">03122018-0056-080412</small>
                                </td>
                                <td>Female</td>
                                <td>45</td>
                                <td>Banawa</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-stethoscope"></i> Refer </a>
                                    <a href="#" class="btn btn-warning btn-xs"><i class="fa fa-line-chart"></i> History </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Garry Harlem<br />
                                    <small class="text-info">02222018-0012-072516</small>
                                </td>
                                <td>Male</td>
                                <td>17</td>
                                <td>Guadalupe</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-stethoscope"></i> Refer </a>
                                    <a href="#" class="btn btn-warning btn-xs"><i class="fa fa-line-chart"></i> History </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    <ul class="pagination pagination-sm no-margin pull-right">
                        <li><a href="#">«</a></li>
                        <li><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#">»</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @include('modal.pregnantModal')
    @include('modal.normal_form')
    @include('modal.pregnant_form')
@endsection

@section('js')

@endsection

