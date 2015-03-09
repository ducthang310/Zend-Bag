$(function() {
    $( "#search_form #date_from").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
});
$(function() {
    $( "#search_form #date_to").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
$(function() {
    if($('.table-wrapper').length){
        $('.table-wrapper')[0].scrollIntoView( true );
    } if($('.no-result-search').length){
        $('.no-result-search')[0].scrollIntoView( true );
    }
});
})