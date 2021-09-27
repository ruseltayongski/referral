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
            $('.others_holder_'+filter_type).addClass('hide');
            $('.others'+filter_type).attr('required',false);
        } else {
            $('.'+filter_type+'_holder').hide();
            $('.'+filter_type).attr('required',true);
            $('.others_holder_'+filter_type).removeClass('hide');
            $('.others'+filter_type).attr('required',false);
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