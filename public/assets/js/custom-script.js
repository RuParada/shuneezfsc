// Date Picker
$(document).ready(function () {
	$('#datepicker').datepicker({
    format: "dd-mm-yyyy", autoclose:true }); 
	$('#datepicker1').datepicker({
    format: "dd-mm-yyyy", autoclose:true }); 
	$('#datepicker2').datepicker({
    format: "dd-mm-yyyy", autoclose:true });	
	$('#datepicker3').datepicker({
    format: "dd-mm-yyyy", autoclose:true }); 
	$('#datepicker4').datepicker({
    format: "dd-mm-yyyy", autoclose:true });
	
	var date = new Date();
	date.setDate(date.getDate()+2);
	var startdate = new Date();
	startdate.setDate(startdate.getDate()+0);
	$('#dpicToday').datepicker({ 
		format: "dd-mm-yyyy",
		startDate: startdate,
		endDate: date,
		autoclose:true
	});
	
	var input = $('.clock_picker');
	input.clockpicker({
		autoclose: true
	});	
});

//for model close 
jQuery("#close_sign_up").click(function() {
	$('#signup-modal').hide();
    $("body").removeClass("modal-backdrop fade in");
    $( ".close" ).trigger( "click" );
});

//for model close 
jQuery("#close_login_up").click(function() {
    $('#login-modal').hide();
    $("body").removeClass("modal-backdrop fade in");
    $( ".close" ).trigger( "click" );
});

//for model close 
jQuery("#forgot_pwd").click(function() {
	$('#login-modal').hide();
	$("body").removeClass("modal-backdrop fade in");
    $( ".close" ).trigger( "click" );
});

$(document).on('show','.accordion', function (e) {
         //$('.accordion-heading i').toggleClass(' ');
         $(e.target).prev('.accordion-heading').addClass('accordion-opened');
    });
    
    $(document).on('hide','.accordion', function (e) {
        $(this).find('.accordion-heading').not($(e.target)).removeClass('accordion-opened');
        //$('.accordion-heading i').toggleClass('fa-chevron-right fa-chevron-down');
    });


jQuery("#saveaddress").click(function() {
	    $("#collapseTwo").collapse('hide');
	    $(".one_r").addClass("custom_radio_bg");
	    $(".one_r_t").removeClass("custom_radio_bg");
});

jQuery("#newaddress").click(function() {
	    $("#collapseOne").collapse('hide');
	    
	    $(".one_r_t").addClass("custom_radio_bg");
});


/*Star Rating*/
jQuery(function() {
      jQuery('.starbox').each(function() {
        var starbox = jQuery(this);
        starbox.starbox({
          average: starbox.attr('data-start-value'),
          changeable: starbox.hasClass('unchangeable') ? false : starbox.hasClass('clickonce') ? 'once' : true,
          ghosting: starbox.hasClass('ghosting'),
          autoUpdateAverage: starbox.hasClass('autoupdate'),
          buttons: starbox.hasClass('smooth') ? false : starbox.attr('data-button-count') || 5,
          stars: starbox.attr('data-star-count') || 5
        }).bind('starbox-value-changed', function(event, value) {
          if(starbox.hasClass('random')) {
            var val = Math.random();
            starbox.next().text('Random: '+val);
            return val;
          } else {
            $('input[name="rating"]').val(value);
          }
        }).bind('starbox-value-moved', function(event, value) {
          $('input[name="rating"]').val(value);
        });
      });
    });
