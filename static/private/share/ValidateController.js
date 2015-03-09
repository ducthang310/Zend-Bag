if ('undefined' == $.type(ValidateController)) {
    var ValidateController = {};
}
if (!$.isPlainObject(ValidateController)) {
    ValidateController = {};
}
ValidateController = {
    check: function (id,type,text, option) {
        if (option === undefined) {
            option = null;
        }
        var pattern = /^(.{1,})+$/;
        var patternEmail = /^(\')?([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+(\')?$/;
        var patternPass = /^(.{8,})+$/;
        $('#'+id).css('background','#fff');
        if(type == 'email') pattern =  patternEmail;
        if(type == 'password') pattern =  patternPass;
        if(text.match(pattern)){
            $('#'+id).parent().find('.pseudo').removeClass('required').addClass('optional');
        }else{
            $('#'+id).next().css('display','block');
            $('#'+id).parent().find('.pseudo').removeClass('optional').addClass('required');
        }
    },

    changeRequired: function (array) {
        var serverData = {
            page: array['page'],
            user: array['user'],
            name: array['inputName'],
            text: array['value'],
            empty: array['empty'],
            option: array['option']
        };
        var name = array['inputName'];
        var div = array['div'];
        var text = array['value'];
        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/base/validate?ajax',
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
                    var element = $('#' + div + ' *[name="' + name + '"]');
                    if( element.prop('type') == 'select-one' ) {
                        element = $('#' + div + ' *[name="' + name + '"]').parent();
                    }
                    if($(element).is(':checkbox')){
                        element = $('#' + div + ' *[name="' + name + '"]').parent();
                    }
                    if(responseObj.message == 'available'){
                        if(name == 'alternative_email'){
                            if(text == ''){
                                element.parent().parent().find('.pseudo').removeClass('optional required');
                            }else{
                                element.parent().parent().find('.pseudo').addClass('optional').removeClass('required');
                            }
                        }else if(name == 'expiry_month' || name == 'expiry_year'){
                            element.find('.pseudo').addClass('optional').removeClass('required');
                        }else{
                            element.parent().find('.pseudo').addClass('optional').removeClass('required');
                        }
                    }else{

                        if(name == 'alternative_email'){
                            element.parent().find('.pseudo').removeClass('required').removeClass('optional');
                            element.parent().find('.validate').css('display', 'block').html(responseObj.message);
                        }else if(name == 'expiry_month' || name == 'expiry_year'){
                            element.find('.pseudo').addClass('required').removeClass('optional');
                            element.find('.validate').css('display', 'block').html(responseObj.message);
                        }else{
                            element.parent().find('.validate').css('display', 'block').html(responseObj.message);
                            element.parent().find('.pseudo').addClass('required').removeClass('optional');
                        }
                    }
                }
            }
        });
    }

}