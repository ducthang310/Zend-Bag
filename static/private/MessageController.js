if ('undefined' == $.type(MessageController)) {
    var MessageController = {};
}
if (!$.isPlainObject(MessageController)) {
    MessageController = {};
}
MessageController.currentPickupId = null;
MessageController.currentAccountId = null;
MessageController.send = function () {

    var click = 1;
    var content = document.getElementsByName("message")[0].value;
    if(!content){
        alert('Message not empty !');
    }
    var serverData = {
        content: content,
        click: click
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/message/private/send?ajax',
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
                //$('.content-item').empty();
                //$('#message-container').html(responseObj.items);
                var items = responseObj.items;
                var messageHtml;
                var i;
                for (i in items) {
                    var id = items[i].style == 'their' ? 'to_template' : 'from_template';
                    messageHtml = bindJs($('#' + id).html(), {message: items[i].content});
                    $('#message_private').append(messageHtml);
                }
                    document.getElementsByName("message")[0].value = "";

            }
        }
    });
};

MessageController.status = function () {
    var ids = [];
    $('[name="pickup[]"]').each(function(index, element) {
        ids.push($(this).val());
    });
    var serverData = {
        id: ids.join(','),
        click: 1
    };

    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/message/private/status?ajax',
        data: serverData,
        success: function (data, textStatus, jqXHR) {

            try {
                var responseObj = $.parseJSON(data);
            }
            catch (e) {
                return;
            }
            var result = responseObj.result;
            var i;
            //console.log(result);
            for (i in result) {
                if(result[i]) {
                    $('#pickup' + i).removeClass('yes');
                }
                else {
                    $('#pickup' + i).addClass('yes');
                }
            }

        }
    });
};

MessageController.statusMain = function(){
    var id = $('#message_id').val();
    if (undefined != id || !id) {
        MessageController.currentAccountId = id;
    } else {
        id = MessageController.currentAccountId;
    }
    if (!id) {
        return;
    }
    var serverData = {
        id: id
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/message/broadcast/status?ajax',
        data: serverData,
        success: function (data, textStatus, jqXHR) {
            try {
                var responseObj = $.parseJSON(data);
            }
            catch (e) {
                return;
            }
            var result = responseObj.result;
            if(result){
                $('#right-message').addClass('has-message');
            }else{
                $('#right-message').removeClass('has-message');
            }
        }
    });
};

MessageController.close = function(){
    $( ".list-ms li" ).removeClass( "active" );
    $('#message-container').empty();
};
MessageController.view = function (id) {
    $("li.item" + id).siblings().removeClass("active");
    $("li.item" + id ).addClass( "active" );
    if (undefined != id || !id) {
        MessageController.currentPickupId = id;
    } else {
        id = MessageController.currentPickupId;
    }
    if (!id) {
        return;
    }
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/message/private/view?ajax',
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
                $('#message-container').appendTo('.item'+id);
                $('#message-container').html(responseObj.list);
            }
        }
    });
};

MessageController.loadMessage = function (id) {
    if (undefined != id || !id) {
        MessageController.currentPickupId = id;
    } else {
        id = MessageController.currentPickupId;
    }
    if (!id) {
        return;
    }
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/message/private/load?ajax',
        data: serverData,
        success: function (data, textStatus, jqXHR) {
            try {
                var responseObj = $.parseJSON(data);
            }
            catch (e) {
                return;
            }
            var items = responseObj.items;
            var messageHtml;
            var i;
            for (i in items) {
                var id = items[i].from ? 'to_template' : 'from_template';
                messageHtml = bindJs($('#' + id).html(), {message: items[i].content});

            }

            $('#message').attr('value', "");
            $('#message_private').append(messageHtml);
        }
    });
};

MessageController.autoloadPrivate = function () {
    var serverData = {
        id: $('#pickup').val(),
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/message/private/load?ajax',
        data: serverData,
        success: function (data, textStatus, jqXHR) {
            try {
                var responseObj = $.parseJSON(data);
            }
            catch (e) {
                return;
            }
            var items = responseObj.items;
            var messageHtml;
            var i;
            for (i in items) {
                var id = items[i].from ? 'to_template' : 'from_template';
                messageHtml = bindJs($('#' + id).html(), {message: items[i].content});
            }
            $('#message').attr('value', "");
            $('#message_private').append(messageHtml);
        }
    });
};
    setInterval(function () {
            if($('#message-box').val() == 1 && undefined != $('#message-box').val()){
                MessageController.status();
            }

            if($('#auto-load').val() == 1 && undefined != $('#auto-load').val()){
                MessageController.autoloadPrivate();
            }

            if($('#message_id').val() != null && undefined != $('#message_id').val()){
                MessageController.statusMain();
            }
    }, 3000);

