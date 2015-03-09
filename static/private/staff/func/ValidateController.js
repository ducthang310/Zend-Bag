if ('undefined' == $.type(ValidateController)) {
    var ValidateController = {};
}
if (!$.isPlainObject(ValidateController)) {
    ValidateController = {};
}
ValidateController = {
    check: function (id,type,text, option) {
        $('#'+id).css('background','#fff');

        var serverData = {
            type: type,
            text: text,
            option: option
        };

        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/staff/staff/validate?ajax',
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
                    if(responseObj.message != 'available'){
                        $('#'+id).next().css('display','block');
                        $('#'+id).next().html(responseObj.message);
                        $('#'+id).next().next().removeClass('optional');
                        $('#'+id).next().next().addClass('required');
                    }else{
                        $('#'+id).next().next().removeClass('required');
                        $('#'+id).next().next().addClass('optional');
                    }
                }
            }
        });
    },
    checkUpdate: function (id,type,text, option) {
        $('#'+id).css('background','#fff');

        var serverData = {
            type: type,
            text: text,
            option: option
        };
        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/staff/staff/validate?ajax',
            data: serverData,
            success: function (data, textStatus, jqXHR) {
                try {
                    var responseObj = $.parseJSON(data);
                }
                catch (e) {
                    return;
                }
                if(responseObj.message != 'available'){
                    $('p.alert-danger').css('display','block');
                    $('p.alert-danger').html(responseObj.message);
                }else{
                    $('p.alert-danger').css('display','none');
                }
            }
        });
    }
};
