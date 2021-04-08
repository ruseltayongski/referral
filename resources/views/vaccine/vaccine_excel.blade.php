<table class="table table-striped" style="font-size: 8pt">
    <thead class="bg-gray">
    <tr>
        <th>Type of Vaccine</th>
        <th>Priority</th>
        <th>Encoded by</th>
        <th>Province</th>
        <th>Municipality</th>
        <th>Facility</th>
        <th>No. of eligible population</th>
        <th>Ownership</th>
        <th>No. of Vaccine Allocated</th>
        <th>Target Dose </th>
        <th>Date of Delivery</th>
        <th >First Dose</th>
        <th>Number of Vaccinated</th>
        <th>AEFI</th>
        <th>AEFI Qty</th>
        <th>Deferred</th>
        <th>Refused</th>
        <th>Wastage</th>
        <th>Date of Delivery 2</th>
        <th >Second Dose</th>
        <th>Number of Vaccinated 2</th>
        <th>AEFI 2 </th>
        <th>AEFI Qty 2</th>
        <th>Deferred 2</th>
        <th>Refused 2</th>
        <th>Wastage 2</th>
        <th>Percentage Coverage</th>
        <th>Consumption Rate</th>
        <th>Remaining Unvaccinated</th>
    </tr>
    </thead>
    <tbody>
    @foreach($vaccine as $row)
        <tr>
            <td>
                <b class="text-blue" style="font-size:11pt;cursor: pointer;" onclick="updateVaccinatedView('<?php echo $row->id; ?>',$(this))">{{ $row->typeof_vaccine }}</b>
            </td>
            <td>
                <?php
                if($row->priority =='frontline_health_workers'){
                    echo 'Frontline Health Workers';
                }
                elseif($row->priority =='indigent_senior_citizens'){
                    echo 'Indigent Senior Citizens';
                }
                elseif($row->priority =='remaining_indigent_population'){
                    echo 'Remaining Indigent Population';
                }
                elseif($row->priority =='uniform_personnel'){
                    echo 'Uniform Personnel';
                }
                elseif($row->priority =='teachers_school_workers'){
                    echo 'Teachers & School Workers';
                }
                elseif($row->priority =='all_government_workers'){
                    echo 'TAll Government Workers (National & Local';
                }
                elseif($row->priority =='essential_workers'){
                    echo 'Essential Workers';
                }
                elseif($row->priority =='socio_demographic'){
                    echo 'Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)';
                }
                elseif($row->priority =='ofw'){
                    echo "OFW's";
                }
                elseif($row->priority =='remaining_workforce'){
                    echo "Other remaining workforce";
                }
                elseif($row->priority =='remaining_filipino_citizen'){
                    echo "Remaining Filipino Citizen";
                }
                elseif($row->priority =='etc'){
                    echo "ETC.";
                }
                ?>
            </td>
            <td>
                <?php
                $transacted_by = \App\User::find($row->encoded_by);
                $transacted_by = $transacted_by->fname.' '.ucfirst($transacted_by->mname[0]).'. '.$transacted_by->lname;
                ?>
                <span>{{ $transacted_by }}</span><br>

            </td>
            <td>
                <?php
                $province = \App\Province::find($row->province_id);
                ?>
                <span>{{ $province->description }}</span><br>
            </td>
            <td>
                <?php
                $municipality = \App\Muncity::find($row->muncity_id);
                ?>
                <span>{{ $municipality->description }}</span><br>
            </td>
            <td>
                <?php
                $facility = \App\Facility::find($row->facility_id);
                ?>
                <span>{{ $facility->name}}</span><br>
            </td>
            <td>
                {{ $row->no_eli_pop }}
            </td>
            <td>
                {{ $row->ownership }}
            </td>
            <td>
                {{ $row->nvac_allocated }}
            </td>
            <td>
                {{ $row->tgtdoseper_day }}
            </td>
            <td>
                @if($row->dateof_del)
                    {{ date('F j, Y',strtotime($row->dateof_del)) }}
                @else
                    <span class="label label-danger">Pending</span>
                @endif
            </td>
            <td>
                @if($row->first_dose)
                    {{ date('F j, Y',strtotime($row->first_dose)) }}
                @else
                    <span class="label label-danger">Pending</span>
                @endif
            </td>
            <td>
                {{ $row->numof_vaccinated }}
            </td>
            <td>
                {{ $row->aefi }}
            </td>
            <td>
                {{ $row->aefi_qty }}
            </td>
            <td>
                {{ $row->deferred }}
            </td>
            <td>
                {{ $row->refused }}
            </td>
            <td>
                {{ $row->wastage }}
            </td>
            <td>
                @if($row->dateof_del2)
                    {{ date('F j, Y',strtotime($row->dateof_del2)) }}
                @else
                    <span class="label label-danger">Pending</span>
                @endif
            </td>
            <td>
                @if($row->second_dose)
                    {{ date('F j, Y',strtotime($row->second_dose)) }}
                @else
                    <span class="label label-danger">Pending</span>
                @endif
            </td>
            <td>
                {{ $row->numof_vaccinated2 }}
            </td>
            <td>
                {{ $row->aefi2 }}
            </td>
            <td>
                {{ $row->aefi_qty2 }}
            </td>
            <td>
                {{ $row->deferred2 }}
            </td>
            <td>
                {{ $row->refused2 }}
            </td>
            <td>
                {{ $row->wastage2 }}
            </td>
            <td>
                {{ number_format(($row->numof_vaccinated/$row->no_eli_pop) * 100, 2) }}%

            </td>
            <td>
                {{ number_format(($row->numof_vaccinated * 100) /$row->nvac_allocated,2) }}
            </td>
            <td>
                {{ $row->no_eli_pop - $row->numof_vaccinated }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
