if('undefined' == $.type(ConfigurationController)) {
    var ConfigurationController = {};
}
if(!$.isPlainObject(ConfigurationController)) {
    ConfigurationController = {};
}

ConfigurationController.editbase = function(id) {
    $("tr.item"+id).siblings().css('background', '');
    $( "tr.item"+id ).css( 'background', '#e2f6de' );
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/base/edit?ajax',
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

    $('[id^="message"]').text('');
}

ConfigurationController.updatebase = function(id) {
    var serverData = {
        id: id,
        click: 2,
        config_value: $('#config_value').val(),
        config_key: $('#key').val()
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/base/edit?ajax',
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
}

ConfigurationController.cancel = function() {
    $('#list_make').hide();
}

ConfigurationController.deletebase = function(id) {
    var serverData = {
        id: id,
        click: 2,
        config_value: $('#config_value').val(),
        config_key: $('#key').val()
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/base/edit?ajax',
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
}

ConfigurationController.editRegion = function(id) {
    //alert('edit region');
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/region/edit?ajax',
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
}



ConfigurationController.updateRegion = function(id) {
    var serverData = {
        id: id,
        click: 2,
        country: $('#country').val(),
        state: $('#state').val(),
        region: $('#region').val(),
        suburb: $('#suburb').val(),
        postcode: $('#postcode').val()
    };
    //alert(country);
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/region/edit?ajax',
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
                location.href = responseObj.url;
            }
        }
    });
}

ConfigurationController.editRating = function(id) {
    //alert('edit region');
    var serverData = {
        id: id,
        click: 1
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/rating/edit?ajax',
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
}

ConfigurationController.updateRating = function(id) {
    var serverData = {
        id: id,
        click: 2,
        question: $('#question').val()
    };
    //alert(country);
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/rating/edit?ajax',
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
}


ConfigurationController.activeRating = function(id) {
    var serverData = {
        id: id
    };
    $.ajax({
        type: 'post',
        url: SERVER.BASE_URL + '/configuration/rating/active?ajax',
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
}
