<script>

    function filterSidebar(data,filter_type){
        var id = data.val();
        if(id!='others') {
            $('.filter_'+filter_type).val(id);

            if(id)
                var result = getFilter(filter_type,id);

            $('.'+filter_type).empty();
            var $newOption = $("<option selected='selected'></option>").val("").text('Select '+filter_type.charAt(0).toUpperCase() + filter_type.slice(1));
            $('.'+filter_type).append($newOption).trigger('change');

            jQuery.each(result, function(i,val){
                $('.'+filter_type).append($('<option>', {
                    value: val.id,
                    text : val.description
                }));
            });

            $('.'+filter_type+'_holder').show();
            $('.'+filter_type).attr('required',true);
        } else {
            $('.'+filter_type+'_holder').hide();
            $('.'+filter_type).attr('required',true);
        }
    }

    function othersRegion(data,data_province) {
        if(data.val() != "Region VII"){
            $(".province_holder").html("<input type='text' class='form-control' name='province_others' required>");
            $(".muncity_holder").html("<input type='text' class='form-control' name='muncity_others' required>");
            $(".barangay_holder").html("<input type='text' class='form-control' name='brgy_others' required>");
        }
        else {

            $(".province_holder").html("<select class=\"form-control province select2\" name=\"province\" onchange=\"filterSidebar($(this),'muncity')\" required>\n" +
                "\n" +
                "                                    </select>");

            $('.province').empty();
            var $newOption = $("<option selected='selected'></option>").val("").text('Select Province');
            $('.province').append($newOption).trigger('change');

            jQuery.each(JSON.parse(data_province), function(i,val){
                $('.province').append($('<option>', {
                    value: val.id,
                    text : val.description
                }));
            });

            $(".muncity_holder").html("<select class=\"form-control muncity select2\" name=\"muncity\" onchange=\"filterSidebar($(this),'barangay')\" required>\n" +
                "\n" +
                "                                    </select>");

            $(".barangay_holder").html("<select class=\"form-control barangay select2\" name=\"brgy\" required>\n" +
                "                                        <option value=\"\">Select Barangay</option>\n" +
                "                                    </select>");

            $(".select2").select2({ width: '100%' });
        }
    }

    function getFilter(filter_type,id) {
        $('.loading').show();
        var url = "{{ asset('location') }}"+"/"+filter_type;
        var tmp;
        $.ajax({
            url: url+"/"+id,
            type: 'get',
            async: false,
            success : function(data){
                tmp = data;
                setTimeout(function(){
                    $('.loading').hide();
                },500);
            }
        });
        return tmp;
    }
</script>