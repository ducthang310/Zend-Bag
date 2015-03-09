if('undefined' == $.type(StaffController)) {
    var StaffController = {};
}
if(!$.isPlainObject(StaffController)) {
    StaffController = {};
}

StaffController.downloadDocument = function(type){
    var name_file = $('#'+type).val();
    var serverData = {
        download: 1
    };
    var url = SERVER.BASE_URL + '/staff/courier/download?file='+ name_file;
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/courier/download?file='+ name_file,
        data: serverData,
        success: function (data, textStatus, jqXHR) {
            if(data == true){
                alert('This file is not available for download.');
            }else{
                window.location = SERVER.BASE_URL + '/staff/courier/download?file='+ name_file ;
            }
        }
    });

};

StaffController.updateProfile = function(id){
    var serverData = {
        id: id,
        email: $('#email').val(),
        app_password: $('#app_password').val(),
        update: 1
    };

    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/profile/update?ajax',
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
                location.href = responseObj.url;
            }
        }
    });
};
StaffController.editstaff = function(id) {
    $("tr.user"+id).siblings().css('background', '');
    $( "tr.user"+id ).css( 'background', '#e2f6de' );
    $('#update_field').show();
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/staff/edit?ajax',
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
                $('#update_field').empty();
                $('#update_field').html(responseObj.detail);
            }
        }
    });
    $('[id^="message"]').text('');
};


StaffController.profileCustomer = function(id) {
    $("tr.user"+id).siblings().css('background', '');
    $( "tr.user"+id ).css( 'background', '#e2f6de' );
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/customer/profile?ajax',
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
                //alert(responseObj.message);
                $('#list_make').replaceWith(responseObj.detail).show();
            }
        }
    });

    $('[id^="message"]').text('');
};
StaffController.update = function(id) {
    var serverData = {
        id: id,
        update: 1,
        auth_id: $('#auth_id').val(),
        email: $('#email').val(),
        role: $('#role').val(),
        area_ids: $('#area_ids').val(),
        password: $('#app_password').val()
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/staff/update?ajax',
        data: serverData,
        success: function (data, textStatus, jqXHR) {
            try {
                var responseObj = $.parseJSON(data);
            }
            catch (e) {
                return;
            }
            if (Base_Constant_Client.FAILED == responseObj.result) {
                if(responseObj.message != 'available'){
                    $('p.alert-danger').css('display','block');
                    $('p.alert-danger').html(responseObj.message);
                }
            }
            else {
                location.href = responseObj.url;
            }
        }
    });
    $('[id^="message"]').text('');
};

StaffController.cancel = function() {
    $('#update_field').hide();
};

StaffController.create = function() {
    var area_ids = $('input:checkbox:checked.area_ids').map(function () {
        return this.value;
    }).get();
    var app_email = document.getElementsByName("app_email")[0].value;
    var role = document.getElementsByName("role")[0].value;
    var email = document.getElementsByName("email")[0].value;
    var password = document.getElementsByName("app_password")[0].value;
    var re_password = document.getElementsByName("re_password")[0].value;
    var serverData = {
        create: 1,
        app_email: app_email,
        email: email,
        role: role,
        app_password: password,
        re_password: re_password,
        area_ids: area_ids
    };

    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/staff/add?ajax',
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
                location.href = responseObj.url;
                return;
            }
            else {
                location.href = responseObj.url;
            }
        }
    });
};

StaffController.close = function(){
    $('#list_make').hide();
};

StaffController.detailCourier = function(id) {
    $("tr.user"+id).siblings().css('background', '');
    $( "tr.user"+id ).css( 'background', '#e2f6de' );
    var serverData = {
        id: id,
        approved: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/courier/profile?ajax',
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
                //alert(responseObj.message);
                $('#list_make').replaceWith(responseObj.detail).show();
            }
        }
    });

    $('[id^="message"]').text('');
};

StaffController.approved = function(id) {
    var can_assign = $('#courier_can_assign').val();
    var head_office_approved =0
    if ($('#head_office_approved').is(":checked"))
            {
                head_office_approved = 1;
            }
    var serverData = {
        id: id,
        confirm: 1,
        can_assign: can_assign,
        head_office_approved: head_office_approved
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/courier/confirm?ajax',
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
                location.href = responseObj.url;
            }
        }
    });
    $('[id^="message"]').text('');
};

StaffController.rejectDocument = function(id, type){
    var content = prompt("Please enter the reasons for your rejection.");
    if (content != null) {
        var serverData = {
            id: id,
            reject: 1,
            type: type,
            message: content
        };
        $.ajax({
            type: 'post',
            url: SERVER.BASE_URL + '/staff/courier/reject?ajax',
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
                    $('#'+type).empty();
                    $('#'+type).append("<p class='rejected'>rejected</p>");
                }
            }
        });
    }
    $('[id^="message"]').text('');
};

StaffController.approvedDocument = function(id, type , status){
    var serverData = {
        id: id,
        approved: 1,
        type: type
    };
    var status = status;
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/courier/approved?ajax',
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
                $('#'+type).empty();
                $('#'+type).append("<p id='" + type + "' class='approved'>APPROVED</p>");
                var photo = $('p#photo_approved').text();
                var bank_statement =  $('p#bank_statement_approved').text();
                var utility_bill =  $('p#utility_bill_approved').text()
            }
            if(photo == "APPROVED" && bank_statement == "APPROVED" && utility_bill == "APPROVED"){
                $('#head_office').show();
            }
        }
    });
    $('[id^="message"]').text('');
};

StaffController.change = function(){

};

StaffController.confirm = function(id,status) {
    var can_assign = $('#courier_can_assign').val();
    var approved = $('input#head_office_approved:checked').val();
    var head_office_approved ;
    if(undefined != approved && status == approved){
        head_office_approved = status;
    }else{
         head_office_approved = 0;
    }
    var serverData = {
        id: id,
        confirm: 1,
        head_office_approved : head_office_approved,
        can_assign: can_assign
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/staff/courier/confirm?ajax',
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
                location.href = responseObj.url;
            }
        }
    });
    $('[id^="message"]').text('');
};
