$(document).ready(function() {
    $(".title .question").click(function(event) {
        $(this).parents(".title").toggleClass('expaned');
    });
    $('.tabs li a').click(function(){
		$('.tabs li').removeClass('active');
		$('.tab-content').removeClass('active');
		$(this).parent().addClass('active');
		$($(this).attr("href") + '-content').addClass('active');
	 });
    $(".title-formlist").click(function(event) {
    	$(".title-formlist").removeClass('active');
    	$('.tab-content').removeClass('active');
    	$(this).addClass('active');
    	$(this).next('.tab-content').addClass('active');
    });
    if (($(".header-menu ul.tabs li").length) == 0) {
        $(".header-menu").hide();
        $(".goback-mobile").addClass("marginauto");
    };
    $(".header-menu .menu-icon").click(function(event) {
        $(this).next('ul').slideToggle();
    });
    $("a[href*='http'],button").click(function() {
        $("#loading").css('display', 'block');
    });

    $("input").keyup(function (e) {
        if (e.keyCode == 9){
            if($(this).parent().parent().find('.validate').css('display') == 'block'){
                $(this).parent().parent().find('.validate-error').css('display','none');
            }
        }
    });

});
$(function() {
    jcf.setOptions('Select', {
        wrapNative: false,
        wrapNativeOnMobile: false
    });
    jcf.replaceAll();
    jcf.destroy('.check-tc input');
    jcf.destroy('.address-list input');
});
