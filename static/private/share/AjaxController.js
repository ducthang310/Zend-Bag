if ('undefined' == $.type(AjaxController)) {
    var AjaxController = {};
}
if (!$.isPlainObject(AjaxController)) {
    AjaxController = {};
}
AjaxController = {
    getSurbub: function (id,div,name) {
        var region = '--Please Select Region--';
        if($('*[name="preferred_region"]').val() != ""){
            $('.service-area').show();
            region = $('*[name="preferred_region"]').val();
        }else{
            $('#pickup_suburb').parent().parent().parent().find('.pseudo').addClass('required').removeClass('optional');
            $('#delivery_suburb').parent().parent().parent().find('.pseudo').addClass('required').removeClass('optional');
            $('.service-area').hide();
        }

        $('.suburb_box_label').html(region);
        var serverData = {
            'region': id
        };
        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/configuration/suburb/getsurbub?ajax',
            data: serverData,
            success: function (data, textStatus, jqXHR) {
                try {
                    var responseObj = $.parseJSON(data);
                }
                catch (e) {
                    return;
                }
                if (Base_Constant_Client.FAILED == responseObj.result) {
                    responseObj.message && alert(responseObj.message);
                    return;
                }
                else {
                    $(div).html('');
                    var labelId = div.split("_")[0];
                    labelId = labelId.split("#")[1];
                    for (i = 0; i < responseObj.result.length; i++) {
                        var Objli = $('<li></li>');
                        var ObjInput = $('<input type="checkbox" />');
                        var ObjLabel = $('<label></label>');
                            ObjInput.attr("value",responseObj.result[i]['id']);
                            ObjInput.attr('id',labelId+'_suburb'+i);
                            ObjInput.attr('name',name+'[]');
                            ObjLabel.attr('for',labelId+'_suburb'+i);
                            ObjLabel.html(' '+responseObj.result[i]['suburb']);
                        Objli.append(ObjInput);
                        Objli.append(ObjLabel);
                        $(div).append(Objli);

                        // set checked all child checkboxes when click to region
                        $('#pickup_suburb, #delivery_suburb').click(function(){
                            $(this).parent().parent().find('input[type="checkbox"]').prop('checked',$(this).is(':checked'));
                            if($(this).parent().parent().find('input[type="checkbox"]').is(":checked")){
                                $(this).parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
                            }else{
                                $(this).parent().parent().parent().find('.pseudo').addClass('required').removeClass('optional');
                                $(this).parent().parent().find('.validate').css('display','block');
                            }
                        });

                        $("#delivery_suburb").parent().parent().find('input[type="checkbox"]').click(function(){
                            var n = $("#delivery_suburb").parent().parent().find('input:checked').length;
                            if(n < 1){
                                $("#delivery_suburb").parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
                                $("#delivery_suburb").parent().parent().find('.validate').css('display','block');
                            }
                            else{
                                $("#delivery_suburb").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
                                $("#delivery_suburb").parent().parent().find('.validate').css('display','none');
                            }
                        });

                        $("#pickup_suburb").parent().parent().find('input[type="checkbox"]').click(function(){
                            var n = $("#pickup_suburb").parent().parent().find('input:checked').length;
                            if(n < 1){
                                $("#pickup_suburb").parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
                                $("#pickup_suburb").parent().parent().find('.validate').css('display','block');
                            }
                            else{
                                $("#pickup_suburb").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
                                $("#pickup_suburb").parent().parent().find('.validate').css('display','none');
                            }
                        });

                        $( "#preferred_region" ).change(function() {
                            var preferred_region = $("#preferred_region").val();
                            if(!preferred_region){
                                $("#preferred_region").parent().parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
                                $("#preferred_region").parent().parent().find(".validate-error").css('display','block');
                            }
                            else{
                                $("#preferred_region").parent().parent().find(".validate-error").css('display','none');
                                $("#preferred_region").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required')
                            }

                        });
                    }

                }
            }
        });
    }

}