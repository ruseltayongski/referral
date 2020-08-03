<form action="{{ asset('opcen/transaction/end') }}" method="POST" id="form_submit">
    {{ csrf_field() }}
    <?php
        if(isset($client->reference_number)){
            $reference_number = $client->reference_number;
        } else {
            $reference_number = date('Y').Session::get('auth')->id.date('mdHis').sha1(time())[6];
        }
    ?>
    <table class="table table-hover table-bordered" style="width: 100%;">
        <tr>
            <td >
                <small>Reference Number</small><br>
                &nbsp;&nbsp;<b class="text-green">{{ $reference_number }}</b>
                <input type="hidden" name="reference_number" value="{{ $reference_number }}">
            </td>
            <td >
                <small>Time Started</small><br>
                &nbsp;&nbsp;<b class="text-yellow" id="time_started_text"></b>
                <input type="hidden" name="time_started" id="time_started">
            </td>
            <td>
                <small>Call Classification</small><br>
                &nbsp;&nbsp;<b class="<?php if(isset($client->reference_number)) echo 'text-red'; else echo 'text-blue'; ?>" id="call_classification_text"></b>
                <input type="hidden" id="call_classification" name="call_classification">
            </td>
        </tr>
        <tr>
            <td >
                <small>Name</small>
                <input type="text" name="name" value="<?php if(isset($client->name)) echo $client->name ?>" class="form-control">
            </td>
            <td >
                <small>Age</small>
                <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
            </td>
            <td >
                <small>Gender</small>
                <select name="sex" id="" class="form-control">
                    <option value="">Select Option</option>
                    <option value="male" <?php if(isset($client->sex)){if($client->sex == 'male')echo 'selected';} ?>>Male</option>
                    <option value="female" <?php if(isset($client->sex)){if($client->sex == 'female')echo 'selected';} ?>>Female</option>
                </select>
            </td>
        </tr>
        <tr>
            <td >
                <small>Province</small>
                <select name="province_id" id="" class="select2" onchange="onChangeProvince($(this).val())">
                    <option value="">Select Option</option>
                    @foreach($province as $row)
                    <option value="{{ $row->id }}" <?php if(isset($client->province_id)){if($client->province_id == $row->id)echo 'selected';} ?>>{{ $row->description }}</option>
                    @endforeach
                </select>
            </td>
            <td >
                <small>Municipality:</small>
                <select name="municipality_id" id="municipality" class="select2" onchange="onChangeMunicipality($(this).val())">
                    @if(isset($client->municipality_id))
                        <option value="">Select Option</option>
                        @foreach($municipality as $mun)
                            <option value="{{ $mun->id }}" <?php if($client->municipality_id == $mun->id) echo 'selected'; ?>>{{ $mun->description }}</option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td >
                <small>Barangay</small>
                <select name="barangay_id" id="barangay" class="select2">
                    @if(isset($client->barangay_id))
                        <option value="">Select Option</option>
                        @foreach($barangay as $bar)
                            <option value="{{ $bar->id }}" <?php if($client->barangay_id == $bar->id) echo 'selected'; ?>>{{ $bar->description }}</option>
                        @endforeach
                    @endif
                </select>
            </td>
        </tr>
        <tr>
            <td >
                <small>Sitio</small>
                <input type="text" name="sitio" class="form-control" value="<?php if(isset($client->sitio)) echo $client->sitio; ?>">
            </td>
            <td >
                <small>Active contact number:</small>
                <input type="text" name="contact_number" class="form-control" value="<?php if(isset($client->contact_number)) echo $client->contact_number; ?>">
            </td>
        </tr>
    </table>
    <table class="table table-hover table-bordered">
        <tr>
            <th>Reason for calling:
                <button class="btn-xs btn-info" type="button" onclick="reasonCalling('inquiry')">Inquiry</button>
                <button class="btn-xs btn-warning" type="button" onclick="reasonCalling('referral')">Referral</button>
                <button class="btn-xs btn-success" type="button" onclick="reasonCalling('others')">Others</button>
                <input type="hidden" name="reason_calling" id="reason_calling">
            </th>
        </tr>
    </table>
    <div class="reason_calling"></div>
    <table class="table table-hover table-bordered">
        <tr>
            <th>Status of transaction:
                <button class="btn-xs btn-primary" type="button" onclick="transactionComplete()">Complete</button>
                <button class="btn-xs btn-danger" type="button" onclick="transactionInComplete()">Incomplete</button>
            </th>
        </tr>
    </table>
    <div class="transaction_status"></div>
    <table class="table table-hover table-bordered">
        <tr>
            <td >
                <button class="btn btn-danger" type="submit" onclick="endTransaction($(this))">End Transaction</button>
            </td>
            <th class="pull-right">Time Ended: <input type="text" name="time_ended" id="time_ended" readonly></th>
        </tr>
    </table>
</form>
