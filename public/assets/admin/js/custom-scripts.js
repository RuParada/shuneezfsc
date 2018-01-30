$(document).ready(function () {
    $(document).on('click', '.close[data-dismiss="modal"]', function () {
       // window.location.reload(); 
    });
});

$(document).ready(function () {
    $(document).on('click', 'button[data-dismiss="modal"]', function () {
        //window.location.reload(); 
    });
});

$(function() {
     var pgurl = window.location.href.substr(window.location.href
.lastIndexOf("/")+1);
     $(".sidebar-menu a").each(function(){
          if($(this).attr("href") == pgurl || $(this).attr("href") == '' )
          $(this).addClass("active");
     })
});

// Data Tables
$(function () {
$.fn.dataTable.ext.errMode = 'none';
	$('#manageUsers').dataTable({
    "bLengthChange": false,
    "bFilter": true });
	
	$('#dataTable').dataTable({
    "bLengthChange": false,
    "bPaginate": false,
    "bInfo": false,
    "bFilter": false,
    "aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ -1,-2 ] }
       ] 
     });
    
});

// Date Picker
$(document).ready(function () {
	$('#startDate').datepicker({
    format: "dd-mm-yyyy" }); 
	$('#endDate').datepicker({
    format: "dd-mm-yyyy" });
	
	$('#startDate1').datepicker({
    format: "dd-mm-yyyy" }); 
	$('#endDate1').datepicker({
    format: "dd-mm-yyyy" });
	
	$('#dob').datepicker({
    format: "dd-mm-yyyy" }); 
	$('#datepicker').datepicker({
    format: "dd-mm-yyyy" });
	
    var today = new Date();
	$('.datepicker').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true,
        endDate: today
    });

var date = new Date();
	date.setDate(date.getDate());
	$('#dpicToday').datepicker({ 
		startDate: date,
		autoclose:true,
        format: "dd-mm-yyyy" 
	});	
	$('.spicToday').datepicker({ 
		endDate: date,
		autoclose:true,
        format: "dd-mm-yyyy" 
	});	

});

//Timepicker
$(function () {
$(".timepicker").timepicker({
	showInputs: false,
    minuteStep: 1,
    defaultTime : '',
    maxHours : 24,
    showMeridian : false
});
});

//Rating
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

//for show next tab
jQuery("#details1").click(function() {
	    $("#delivery").hide();
	    $("#workingtime").hide();
});

jQuery("#delivery1").click(function() {
	    $("#delivery").show();
	    $("#workingtime").hide();
});


//for show next tab
jQuery("#go_next").click(function() {
	    $("#delivery").show();
	     $("#workingtime").hide();
});

// for nxt tab showing
jQuery("#gototab3").click(function() {
	    $("#workingtime").show();
	    $("#delivery").hide();
	    $("#workingtime1").addClass("ui-state-active active");
	    $("#delivery1").removeClass("ui-state-active active");
	    $("#details1").removeClass("ui-state-active active");
});
