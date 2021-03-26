<style>
    input[type=radio] {
        width: 20%;
        height: 2em;
    }
</style>
<form action="{{ asset('opcen/transaction/end') }}" method="POST" id="form_submit" autocomplete="off">
    {{ csrf_field() }}
    <div class="row">
       <div class="col-md-4">
           <small>Type of Vaccine</small>
           <select name="company" id="" class="select2" required>
               <option value="">Select Option</option>
               <option value="sinovac" <?php if(isset($client->company)){if($client->company == 'nga')echo 'selected';} ?>>Sinovac</option>
               <option value="astrazeneca" <?php if(isset($client->company)){if($client->company == 'ngo')echo 'selected';} ?>>Astrazeneca</option>
               <option value="moderna" <?php if(isset($client->company)){if($client->company == 'ngo')echo 'selected';} ?>>Moderna</option>
               <option value="pfizer" <?php if(isset($client->company)){if($client->company == 'ngo')echo 'selected';} ?>>Pfizer</option>
           </select>
       </div>
        <div class="col-md-4">
            <small>Priority</small>
            <select name="company" id="" class="select2">
                <option value="">Select Option</option>
                <option value="nga" <?php if(isset($client->company)){if($client->company == 'nga')echo 'selected';} ?>>Frontline Health Workers</option>
                <option value="ngo" <?php if(isset($client->company)){if($client->company == 'ngo')echo 'selected';} ?>>Indigent Senior Citizens</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>Remaining Indigent Population</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>Uniform Personnel</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>Teachers & School Workers</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>All Government Workers (National & Local)</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>Essential Workers</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>OFW's</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>Other remaining workforce</option>
                <option value="lgu" <?php if(isset($client->company)){if($client->company == 'lgu')echo 'selected';} ?>>Remaining Filipino Citizen</option>
                <option value="etc" <?php if(isset($client->company)){if($client->company == 'etc')echo 'selected';} ?>>ETC.</option>
            </select>
        </div>
        <div class="col-md-4">
            <small>Sub-Priority</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>Province</small>
            <select name="company" id="" class="select2">
                <option value="">Select Option</option>
                @foreach($province as $row)
                <option value="{{ $row->id }}">{{ $row->description }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <small>Municipality</small>
            <select name="company" id="" class="select2">
                <option value="">Select Option</option>
                @foreach($muncity as $row)
                    <option value="{{ $row->id }}">{{ $row->description }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <small>Facility</small>
            <select name="company" id="" class="select2">
                <option value="">Select Option</option>
                @foreach($facility as $row)
                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </select>
        </div>

    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>No. of Eligble Population A1.1-A1.7 </small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Ownership</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>No. of Vaccine Allocated</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>Fist Dose</small>
            <input type="text" id="date_picker" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Second Dose</small>
            <input type="text" id="date_picker1" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Date of Delivery</small>
            <input type="text" id="date_picker2" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>Target Dose Per Day</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>No. of Vaccinated</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Mild</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>Serious</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Deffered</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Refused</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>% Coverage A1.1-A1.7</small>
            <input type="text" name="age" value="<?php if(isset($client->age)) echo $client->age ?>" class="form-control">
        </div>
    </div>
    <br>
    <button type="button" class="btn btn-block btn-success btn-lg"><i class="fa fa-send"></i> Submit</button>
</form>
<script>
    $("#date_picker").daterangepicker({
       "singleDatePicker":true
    });
    $("#date_picker1").daterangepicker({
        "singleDatePicker":true
    });
    $("#date_picker2").daterangepicker({
        "singleDatePicker":true
    });



</script>

