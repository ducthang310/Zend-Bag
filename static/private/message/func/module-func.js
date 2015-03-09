if('undefined' == $.type(MessageController)) {
    var MessageController = {};
}
if(!$.isPlainObject(MessageController)) {
    MessageController = {};
}
MessageController.view = function(id) {
    $("#item"+id).removeClass('read');
    $("#item"+id).addClass('read-yes');
    $("#item"+id).siblings().css('background', '');
    $("#item"+id ).css( 'background', '#e2f6de' );
    var serverData = {
        message_id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/message/index/detail?ajax',
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
                $('#view_message').empty();
                $('#view_message').html(responseObj.detail);
            }
        }
    });
}