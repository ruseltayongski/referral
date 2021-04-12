<style>
    input[type=radio] {
        width: 20%;
        height: 2em;
    }
</style>
<form action="<?php
if(isset($vaccine->typeof_vaccine)){
    echo asset('vaccine/update');
}
else{
    echo asset('vaccine/saved');
}
?>" method="POST" id="form_submit" autocomplete="off">
    {{ csrf_field() }}
    <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
    <br>
    <table class="table table-striped" style="font-size: 8pt">
        <thead class="bg-gray">
        <tr>
            <th>Dose Date</th>
            <th>Number of Vaccinated</th>
            <th>Mild</th>
            <th>Serious</th>
            <th>Refused</th>
            <th>Deferred</th>
            <th>Wastage</th>

        </tr>
        </thead>
        <tbody class="tbody_content">
        @include("vaccine.tbody_content")
        </tbody>
        <tr>
            <td colspan="7">
                <a href="#" onclick="addTbodyContent()" class="pull-right red" id="workAdd"><i class="fa fa-user-plus"></i> Add Daily Accomplishment</a>
            </td>
        </tr>
    </table>
</form>
<script>
    var count = 0;
    function addTbodyContent() {
        count++;
        $('.loading').show();
        var url = "<?php echo asset('vaccine/tbody/content'); ?>"+"/"+count;
        $.get(url,function(data){
            $('.tbody_content').append(data);
            $('.loading').hide();
            $(".select2").select2({ width: '100%' });
        });
    }

</script>

