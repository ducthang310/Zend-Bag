$(document).ready(function() {
    // Hide validate alert
    $(".validate-error").click(function () {
        $(this).css('display', 'none');
    });
    if($('*[name="preferred_region"]').val() != ""){
        $('.service-area').show();
    }else{
        $('.service-area').hide();
        $('#cpreferences-content ul li.pickup-suburbs').css('display','none');
        $('#cpreferences-content ul li.delivery-suburbs').css('display','none');
    }

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
    $("input:checkbox[class=pickup_suburb]").click(function() {
        if($(this).is(':checked') == false){
            $('#pickup_suburb').prop('checked', false);
        }
        if($('#pickup_suburb').parent().parent().find('input[class="pickup_suburb"]').not(':checked').length === 0){
            $('#pickup_suburb').prop('checked', true);
        }
    });
    $("input:checkbox[class=delivery_suburb]").click(function() {
        if($(this).is(':checked') == false){
            $('#delivery_suburb').prop('checked', false);
        }
        if($('#delivery_suburb').parent().parent().find('input[class="delivery_suburb"]').not(':checked').length === 0){
            $('#delivery_suburb').prop('checked', true);
        }
    });

    $("input:radio[name=preference_area]").click(function() {
        var value = $(this).val();
        if(value == 0){
            $('#cpreferences-content ul li.pickup-suburbs').css('display','block');
            $('#cpreferences-content ul li.delivery-suburbs').css('display','block');
            if($("#pickup_suburb").parent().parent().find('input:checked').length < 1){
                $("#pickup_suburb").parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
                $("#pickup_suburb").parent().parent().find('.validate').css('display','block');
            }else{
                $("#pickup_suburb").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
                $("#pickup_suburb").parent().parent().find('.validate').css('display','none');
            }
            if($("#delivery_suburb").parent().parent().find('input:checked').length < 1){
                $("#delivery_suburb").parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
                $("#delivery_suburb").parent().parent().find('.validate').css('display','block');
            }else{
                $("#delivery_suburb").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
                $("#delivery_suburb").parent().parent().find('.validate').css('display','none');
            }
        }else{
            $('#cpreferences-content ul li.pickup-suburbs').css('display','none');
            $('#cpreferences-content ul li.delivery-suburbs').css('display','none');
        }
    });

    var prefArea = $("input:radio[name=preference_area]:checked").val();
        if(prefArea == 0 &&  $('.service-area').css("display") != 'none'){
            $('li.pickup-suburbs').show();
            $('li.delivery-suburbs').show();
        }else{
            $('#cpreferences-content ul li.pickup-suburbs').css('display','none');
            $('#cpreferences-content ul li.delivery-suburbs').css('display','none');
        }

    if($('#pickup_suburb').parent().parent().find('input[class="pickup_suburb"]').not(':checked').length === 0){
        $('#pickup_suburb').prop('checked', true);
    }
    if($('#delivery_suburb').parent().parent().find('input[class="delivery_suburb"]').not(':checked').length === 0){
        $('#delivery_suburb').prop('checked', true);
    }
    $( "#preferred_region" ).change(function() {
        var preferred_region = $("#preferred_region").val();
        if(!preferred_region){
            $('#cpreferences-content ul li.pickup-suburbs').css('display','none');
            $('#cpreferences-content ul li.delivery-suburbs').css('display','none');
            $("#preferred_region").parent().parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
            $("#preferred_region").parent().parent().find(".validate-error").css('display','block');
            $("#all_area").prop("checked", true);
            $("#select_area").prop("checked", false);
            $('#pickup_suburb').prop('checked', false);
            $('#delivery_suburb').prop('checked', false);
        }else{
            $("#preferred_region").parent().parent().parent().parent().find('.pseudo').removeClass('optional').addClass('optional');
            $("#preferred_region").parent().parent().find(".validate-error").css('display','none');
            $("#all_area").prop("checked", true);
            $("#select_area").prop("checked", false);
            $('#pickup_suburb').prop('checked', false);
            $('#delivery_suburb').prop('checked', false);
        }
        var n = $("#delivery_suburb").parent().parent().find('input:checked').length;
        if(n < 1){
            $("#delivery_suburb").parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
            $("#delivery_suburb").parent().parent().find('.validate').css('display','block');
        }
        else{
            $("#delivery_suburb").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
            $("#delivery_suburb").parent().parent().find('.validate').css('display','none');
        }

        var m = $("#pickup_suburb").parent().parent().find('input:checked').length;
        if(m < 1){
            $("#pickup_suburb").parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
            $("#pickup_suburb").parent().parent().find('.validate').css('display','block');
        }
        else{
            $("#pickup_suburb").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
            $("#pickup_suburb").parent().parent().find('.validate').css('display','none');
        }
    });

    $('#register-company-content form input,#register-company-content form select').change(function(){
        ValidateController.changeRequired(
            {   page: 'registration',
                user: 'courier_company',
                inputName: this.name,
                div: 'register-company-content',
                value: this.value.trim(),
                empty:'alternative_email'
            }
        );
    });
    $('#register-individual-content form input,#register-individual-content form select').change(function(){
        ValidateController.changeRequired(
            {   page: 'registration',
                user: 'courier_individual',
                inputName: this.name,
                div: 'register-individual-content',
                value: this.value.trim(),
                empty:'alternative_email'
            }
        );
    });
    $('#cupload-id-content form input,#cupload-id-content form select').change(function(){
        ValidateController.changeRequired(
            {
                page: 'registration',
                user: 'courier_company',
                inputName: this.name,
                div: 'cupload-id-content',
                value: this.value.trim()
            }
        );
    });

    //Profile
    $('#profile-company-content form input,#profile-company-content form select').change(function(){
        ValidateController.changeRequired(
            {   page: 'edit_profile',
                user: 'courier',
                inputName: this.name,
                div: 'profile-company-content',
                value: this.value.trim(),
                empty:'alternative_email'
            }
        );
    });
    /**
     * Auto data for app username in register
     * */
    var individual = '#register-individual-content form';
    var company = '#register-company-content form';
    /**
     * individual * */
    $(individual + ' input[name="email"]').change(function () {
        var email = $(individual + ' input[name="email"]');
        $(individual + ' input[name="app_email"]').val(email.val());

        var app_email = $(individual + ' input[name="app_email"]');
        app_email.val(email.val());

        app_email.parent().find('.validate').css('display', 'none');
        ValidateController.changeRequired(
            {   page: 'registration',
                user: 'courier_individual',
                inputName: 'app_email',
                div: 'register-individual-content',
                value: app_email.val().trim(),
                empty:''
            }
        );
    });
    /**
     * company * */
    $(company + ' input[name="email"]').change(function () {
        var email = $(company + ' input[name="email"]');
        $(company + ' input[name="app_email"]').val(email.val());

        var app_email = $(company + ' input[name="app_email"]');
        app_email.val(email.val());

        app_email.parent().find('.validate').css('display', 'none');
        ValidateController.changeRequired(
            {   page: 'registration',
                user: 'courier_company',
                inputName: 'app_email',
                div: 'register-company-content',
                value: app_email.val().trim(),
                empty:''
            }
        );
    });
});
