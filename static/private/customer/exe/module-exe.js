$(document).ready(function() {
    // Hide validate alert
    $(".validate-error").click(function () {
        $(this).css('display', 'none');
    });
    // set checked all child checkboxes when click to region
    $('#pickup_suburb').click(function(){
        $(this).parent().parent().find('input[type="checkbox"]').prop('checked',$(this).is(':checked'));
    });
    $('#delivery_suburb').click(function(){
        $(this).parent().parent().find('input[type="checkbox"]').prop('checked',$(this).is(':checked'));
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
        console.log(n);
        if(n < 1){
            $("#pickup_suburb").parent().parent().parent().find('.pseudo').removeClass('optional').addClass('required');
            $("#pickup_suburb").parent().parent().find('.validate').css('display','block');
        }
        else{
            $("#pickup_suburb").parent().parent().parent().find('.pseudo').addClass('optional').removeClass('required');
            $("#pickup_suburb").parent().parent().find('.validate').css('display','none');
        }
    });

    $('#register-personal-content form input,#register-personal-content form select').change(function(){
        ValidateController.changeRequired(
            {   page: 'registration',
                user: 'customer',
                inputName: this.name,
                div: 'register-personal-content',
                value: this.value.trim(),
                empty:'alternative_email'
            }
        );

    });
    $('#cpreferences-content form input,#cpreferences-content form select').change(function(){
        ValidateController.changeRequired(
            {   page: 'registration',
                user: 'customer_preferences',
                inputName: this.name,
                div: 'cpreferences-content',
                value: this.value.trim(),
                empty:'alternative_email'
            }
        );
    });
    $('#cupload-id-content form input,#cupload-id-content form select').change(function(){
        ValidateController.changeRequired(
            {
                page: 'registration',
                user: 'customer',
                inputName: this.name,
                div: 'cupload-id-content',
                value: this.value.trim()
            }
        );
    });

    //Profile
    $('#profile-personal-content form input,#profile-personal-content form select').change(function(){
        ValidateController.changeRequired(
            {   page: 'edit_profile',
                user: 'customer',
                inputName: this.name,
                div: 'profile-personal-content',
                value: this.value.trim(),
                empty:'alternative_email'
            }
        );
    });

   /**
    * Auto data for app username in customer register
    * */
   $('#register-personal-content form input[name="email"]').change(function(){
       var email = $('#register-personal-content form input[name="email"]');
       var app_email = $('#register-personal-content form input[name="app_email"]');
       app_email.val(email.val());

       app_email.parent().find('.validate').css('display', 'none');
       ValidateController.changeRequired(
           {   page: 'registration',
               user: 'customer',
               inputName: 'app_email',
               div: 'register-personal-content',
               value: app_email.val().trim(),
               empty:''
           }
       );
   });
 });
