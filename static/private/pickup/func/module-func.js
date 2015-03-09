if ('undefined' == $.type(PickupController)) {
    var PickupController = {};
}
if (!$.isPlainObject(PickupController)) {
}

PickupController.check = function(id,value){
    if(value==''){
        $('#'+id).next().next().removeClass('optional');
        $('#'+id).next().next().addClass('required');
    }else{
        $('#'+id).next().next().removeClass('required');
        $('#'+id).next().next().addClass('optional');
    }
}

PickupController.view = function (id) {
    $("li.item"+id).siblings().css('background', '');
    $( "li.item"+id ).css( 'background', '#e2f6de' );
    $("li.item"+id).siblings().removeClass('active');
    $("tr#item"+id).siblings().css('background', '');
    $( "tr#item"+id ).css( 'background', '#e2f6de' );
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/detail?ajax',
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
                $('#rate-courier').hide();
                $('#rate-customer').hide();
                $('#list_make').replaceWith(responseObj.detail).show();
                initMap(responseObj.canDrag);
            }
        }
    });
    $('[id^="message"]').text('');
}
PickupController.changeTotalFee = function () {
    var total = base,
        credit_fee = 0;
    $('[id^="insure_fee"]:checked').each(function () {
        total += parseFloat(insure);
    });
    $('[id^="sign_fee"]:checked').each(function () {
        total += parseFloat(sign);
    });

    credit_fee = parseFloat(credit) * total / 100;
    total += total * credit / 100;
    $('[id^="total_fee"]').text(parseFloat(Math.round(total * 100) / 100).toFixed(2));
    $('[id^="credit_fee"]').text(parseFloat(Math.round(credit_fee * 100) / 100).toFixed(2));
}

PickupController.updatePickup = function (id) {
    var serverData = {
        id: id,
        click: 1,
        pickup_address: $('#pickup_address').val(),
        delivery_address: $('#delivery_address').val(),
        note_pickup_address: $('#note_pickup_address').val(),
        note_delivery_address: $('#note_delivery_address').val()
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/update?ajax',
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
                location.reload();
            }
        }
    });
}

PickupController.cancelPickup = function (id) {
    console.log('cancel');
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/getfee?ajax',
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
                var msg_inform = '';
                if(responseObj.cancel_fee == 0){
                    msg_inform = 'Do you really want to cancel this pickup?';
                }
                else{
                    msg_inform = "Do you really want to cancel this pickup? This will charge you a fee of $" + responseObj.cancel_fee;
                }
                if (confirm(msg_inform)) {
                    var serverData = {
                        id: id,
                        click: 1
                    };
                    $.ajax({
                        type: 'post',
                        url: SERVER.BASE_URL + '/pickup/index/cancel?ajax',
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
                                location.reload();
                            }
                        }
                    });
                }
            }
        }
    });

}

PickupController.cancel = function () {
    $('#list_make').hide();
    $('#rate-courier').hide();
    $('#rate-customer').hide();
    $('[id^="message"]').text('');
}

PickupController.unassignPickup = function (id) {
    if(confirm('Are you sure to unassign Pickup? The courier will be banned when do it.')){
        var serverData = {
            id: id,
            click: 1,
            cur_status: $('#cur_status').val(),
            status: 1
        };
        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/pickup/index/changestatus?ajax',
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
                    location.reload();
                }
            }
        });
    }
}

PickupController.changeStatusPickup = function (id) {
    var serverData = {
        id: id,
        click: 1,
        cur_status: $('#cur_status').val(),
        status: $('#new_status').val()
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/changestatus?ajax',
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
                location.reload();
            }
        }
    });
}

PickupController.rateCourierPickup = function (id) {
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/ratecourier?ajax',
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
                $('#list_make').hide();
                $('#rate-customer').hide();
                $('#rate-courier').replaceWith(responseObj.detail).show();
            }
        }
    });
}

PickupController.rateCustomerPickup = function (id) {
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/ratecustomer?ajax',
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
                $('#list_make').hide();
                $('#rate-courier').hide();
                $('#rate-customer').replaceWith(responseObj.detail).show();
            }
        }
    });
}

PickupController.updateRateCourierPickup = function (id) {
    var serverData = {
        id: id,
        click: 1,
        note: $("[name^='comment']").val(),
        pickup_id: $("[name^='pickup_id']").val(),
        rating_id: $("[name^='rating_id']").val(),
        rating_value: $("[name^='rating_value']").val(),
        flag_rate: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/updaterate?ajax',
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
                $('#list_make').hide();
                $('#rate-customer').hide();
                $('#rate-courier').hide();
                location.reload();
            }
        }
    });
}

PickupController.updateRateCustomerPickup = function (id) {
    var serverData = {
        id: id,
        click: 1,
        note: $("[name^='comment']").val(),
        pickup_id: $("[name^='pickup_id']").val(),
        rating_id: $("[name^='rating_id']").val(),
        rating_value: $("[name^='rating_value']").val(),
        flag_rate: 0
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/updaterate?ajax',
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
                $('#list_make').hide();
                $('#rate-courier').hide();
                $('#rate-customer').hide();
                location.reload();
            }
        }
    });
}

PickupController.changeRate = function (element, value) {
    //alert('')
    // reset background of all rates
    $(element).siblings().css('background', '');

    // set backup of selected rate
    $(element).css('background', '#3ec708');

    // set current value for input rating_value
    $("[name^='rating_value']").val(value);
    //console.log($("[name^='rating_value']").val());
}

PickupController.getList = function (id) {
    var serverData = {
        pickup_id: id
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/index/getcourierlist?ajax',
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
                $('#list_make').replaceWith(responseObj.detail).show();
            }
        }
    });
}

PickupController.assignPickup = function (id) {
    if(!$("#courier_id").val()){
        alert('Select Courier is required');
    }
    else{
        var serverData = {
            id: id,
            click: 1,
            courier_authID: $("#courier_id").val()
        };
        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/pickup/index/assigned?ajax',
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
                    location.reload();
                }
            }
        });
    }
}

PickupController.cancelPickupNoFee = function (id) {

    if (confirm("Do you really want to cancel this pickup? ")) {
        var serverData = {
            id: id,
            click: 1
        };
        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/pickup/index/cancel?ajax',
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
                    location.reload();
                }
            }
        });
    }
}

PickupController.status = function () {
    var ids = [];
    var status = [];
    $('[name="pickup[]"]').each(function(index, element) {
        ids.push($(this).val());
    })
    $('[name="status[]"]').each(function(index, element) {
        status.push($(this).val());
    })
    var serverData = {
        id: ids.join(','),
        status: status.join(','),
        check: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/pickup/check/check?ajax',
        data: serverData,
        success: function (data, textStatus, jqXHR) {
            try {
                var responseObj = $.parseJSON(data);
            }
            catch (e) {
                return;
            }

            if (true == responseObj.flag) {
                var message = 'There are something changed in your view. Do you want to refresh?'
                console.log(responseObj.message);
                if(confirm(message)){
                    location.reload();
                }
                else{
                    var msg = 'Pickup is changed status. You should refresh to update data';
                    $('#check_status').val(0);
                    $('[id^="alert_changed"]').text(msg);
                }
                return;
            }
        }
    });
}

setInterval(function () {
    if($('#check_status').val() == 1){
        PickupController.status();
    }
}, 30000);
