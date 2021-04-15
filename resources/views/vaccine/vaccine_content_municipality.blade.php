<style>
    input[type=radio] {
        width: 20%;
        height: 2em;
    }
</style>
<div class="modal-header">
    <h3 id="myModalLabel"><i class="fa fa-location-arrow" style="color:green"></i> {{ \App\Muncity::find($muncity_id)->description }}</h3>
</div>
 <form action="{{ asset('vaccine/saved') }}" method="POST" id="form_submit" autocomplete="off">
        {{ csrf_field() }}
        <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
        <br>
        <table class="table table-striped" style="font-size: 8pt">
            <thead class="bg-gray">
                <tr>
                    <th>Dose Date</th>
                    <th>Type Of Vaccine</th>
                    <th>Priority</th>
                    <th>Vaccinated</th>
                    <th>Mild</th>
                    <th>Serious</th>
                    <th>Refused</th>
                    <th>Deferred</th>
                    <th>Wastage</th>

                </tr>
            </thead>

            <tbody id="tbody_content_vaccine">

            </tbody>
            <tr>
                <td colspan="9">
                    <a href="#" onclick="addTbodyContent('<?php echo $province_id; ?>','<?php echo $muncity_id; ?>')" class="pull-right red" id="workAdd"><i class="fa fa-user-plus"></i> Add Daily Accomplishment</a>
                </td>
            </tr>
            <tr>
                <td colspan="9">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default btn-md" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-md"><i class="fa fa-send"></i> Submit</button>
                    </div>
                </td>
            </tr>
    </table>
</form>

<script>
    var count = 0;
    function addTbodyContent(province_id,muncity_id) {
        count++;
        $('.loading').show();
        var url = "<?php echo asset('vaccine/tbody/content'); ?>"+"/"+count+"/"+province_id+"/"+muncity_id;
        $.get(url,function(data){
            console.log(data);
            setTimeout(function(){
                $('.loading').hide();
                $('#tbody_content_vaccine').append(data);
                $(".select2").select2({ width: '100%' });
            },200);
        });
    }

</script>


