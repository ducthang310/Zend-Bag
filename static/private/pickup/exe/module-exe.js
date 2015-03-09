/**
 * execute for module
 */
$(document).ready( function() {
    var click        = 1;
    var to_id        = $('#to_id').val();
    var from_id      = $('#from_id').val();
    var content      = $('#message').val();
    var pickup       = $('#pickup').val();

    $("input#send").click(function () {
        MessageController.send(to_id,from_id,content,pickup,click);
    });
    $('[class="checkbox-x"]').click(function(){
        PickupController.changeTotalFee();

    });
    $('#book').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
} );



