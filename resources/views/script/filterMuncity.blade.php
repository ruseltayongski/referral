<script>
    var muncity_id = 0;
    $('.filter_muncity').on('change',function(){
        muncity_id = $(this).val();
        $('.filter_muncity').val(muncity_id);
        var brgy = getBarangay();
        $('.barangay').empty()
            .append($('<option>', {
                value: '',
                text : 'All'
            }));
        jQuery.each(brgy, function(i,val){
            $('.barangay').append($('<option>', {
                value: val.id,
                text : val.description
            }));

        });
    });

    function getBarangay()
    {
        $('.loading').show();
        var url = "{{ url('location/barangay/') }}";
        var tmp;
        $.ajax({
            url: url+"/"+muncity_id,
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