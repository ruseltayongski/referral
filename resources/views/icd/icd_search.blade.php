@if(!empty($icd) && count($icd) > 0)
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Code</th>
            <th>Description</th>
            <th>Group</th>
            <th>CR</th> <!--Case Rate -->
            <th>PF</th> <!-- Professional Fee -->
            <th>HCF</th> <!-- Health Care Fee -->
            <th>Source</th>
            <th>Option</th>
        </tr>
        </thead>
        @foreach($icd as $row)
            <tr>
                <td>{{ $row->code }}</td>
                <td>{{ $row->description }}</td>
                <td>{{ $row->group }}</td>
                <td>{{ $row->case_rate }}</td>
                <td>{{ $row->professional_fee }}</td>
                <td>{{ $row->health_care_fee }}</td>
                <td>{{ strtoupper($row->source) }}</td>
                <td>
                    <div class="custom-control custom-checkbox checkbox-xl">
                        <input type="checkbox" class="custom-control-input" value="{{ $row->id }}" name="icd_checkbox[]" style="height: 30px;width: 30px;cursor: pointer;">
                    </div>
                </td>
            </tr>
        @endforeach
    </table>
@else
    <div class="alert alert-warning">
        <div class="text-warning">
        <!--
            <i class="fa fa-warning"></i> Referrals that are not accepted within 72 hours will be <a href="{{ asset('doctor/archived') }}" style="color: #ff405f"> <b><u>archived</u></b></a><br>
            -->
            <i class="fa fa-warning"></i> NO DATA!
        </div>
    </div>
@endif