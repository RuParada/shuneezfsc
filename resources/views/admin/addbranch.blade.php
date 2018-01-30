@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Add Branch'); ?> </h1>
		@if(Session::has('error')) <p class="error_msg"> <?php echo trans('messages.Required'); ?></p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
		<div class="nav-tabs-custom" id="myTabs">
			<ul class="nav nav-tabs">
				<li class="active" id="details1"><a href="#details" data-toggle="tab"><?php echo trans('messages.Branch Details'); ?></a></li>
				<li id="delivery1"><a href="#delivery" data-toggle="tab"><?php echo trans('messages.Delivery Area Details'); ?></a></li>
			</ul>
			{!! Form::open(array('url' => 'admin/addbranch', 'files' => 1, 'id' => 'branch_form')) !!}
			<div class="tab-content">
				<div class="active tab-pane" id="details">
	<div class="box no_top_border">
		<div class="nav-tabs-custom ">
			<ul class="nav nav-tabs">
				<?php
				if(count($languages) > 0)
				{
					$i = 0;
					foreach($languages as $language) {
						if(count($array_valid)){
							$active = showvalidmsg($i, $array_valid);
				?>
				<li class="<?php echo $active; ?>"><a href="#<?php echo $language->code; ?>" data-toggle="tab"><?php echo ucfirst($language->language); ?></a></li>
				<?php } else { ?>
				<li class="<?php echo ($i == 0) ? 'active' : ''; ?>"><a href="#<?php echo $language->code; ?>" data-toggle="tab"><?php echo ucfirst($language->language); ?></a></li>
				<?php } $i++; } } ?>
			</ul>
			<div class="tab-content">
			<?php
			if(count($languages) > 0)
			{
				$i = 0;
				foreach($languages as $language) {
					if(count($array_valid)){
						$active = showvalidmsg($i, $array_valid);		
			?>
			<div class="<?php echo $active; ?> tab-pane" id="<?php echo $language->code; ?>">
			<?php } else { ?>
			<div class="<?php echo ($i == 0) ? 'active' : ''; ?> tab-pane" id="<?php echo $language->code; ?>">
			<?php } ?>
				<div class="col-md-12">
					<div class="box">
						<div class="box-header with-border">
							 <h3 class="box-title"><?php echo ucfirst($language->language); ?></h3>
						</div> <!-- box-header -->
				
						<div class="box-body col-md-6">
							<div class="form-group">
								<label><?php echo trans('messages.Name'); ?> <span class="req">*</span> :</label>
								<input type="text" class="form-control" maxlength="75" name="branch[]" placeholder="<?php echo trans('messages.Name'); ?> " value="<?php echo Input::old('branch')[$i]; ?>">
								<input type="hidden" name="language[]" value="<?php echo $language->code; ?>">
								@if(count($array_valid))<p class="error_msg"><?php echo ($array_valid[$i] != '') ? trans('messages.Name field is required') : ''; ?></p>@endif
							</div><!-- form-group -->
						</div><!-- box-body -->  
				   </div>
				</div> <!-- col-md-6 -->		
			</div>
			<?php $i++; } } ?>
		
			</div> <!-- tab-content -->
		
		</div> <!-- nav-tabs-custom -->
		<div class="col-md-12">
				<div class="box">
						<div class="box-header with-border">
							 <h3 class="box-title"><?php echo trans('messages.Details'); ?></h3>
						</div> <!-- box-header -->
                
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Mobile'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="mobile" placeholder="<?php echo trans('messages.Mobile'); ?>" value="<?php echo Input::old('mobile'); ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Email'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="email" placeholder="<?php echo trans('messages.Email'); ?>" value="<?php echo Input::old('email'); ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->   
					
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Password'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" name="password" placeholder="<?php echo trans('messages.Password'); ?>" value="">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('password') != '') ? $error->first('password') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Status'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="status">
								<option value="1"><?php echo trans('messages.Enable'); ?></option>
								<option value="0" <?php echo (Input::old('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Disable'); ?></option>
							</select>
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->
						
					<div class="box-body">
						<div class="form-group full_selectList col-md-6">
							<div class="col-md-12" style='margin-bottom:10px;'>
								<label class="col-sm-3 control-label"><?php echo trans('messages.Street'); ?>:<span class="req">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="street" placeholder="<?php echo trans('messages.Street'); ?>" value="" id="street">
								</div>
							</div>
							<div class="col-md-12" style='margin-bottom:10px;'>
								<label class="col-sm-3 control-label"><?php echo trans('messages.City'); ?><span class="req">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="city" placeholder="<?php echo trans('messages.City'); ?>" value="" id="city">
								</div>
							</div>
							<div class="col-md-12" style='margin-bottom:10px;'>
								<label class="col-sm-3 control-label"><?php echo trans('messages.Country'); ?>:<span class="req">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="country" placeholder="<?php echo trans('messages.Country'); ?>" value="" id="country">
								</div>
							</div>
							<div class="col-md-12" style='margin-bottom:10px;'>
								<label class="col-sm-3 control-label"><?php echo trans('messages.Zipcode'); ?><span class="req">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="zipcode" placeholder="<?php echo trans('messages.Zipcode'); ?>" value="" id="zipcode">
									<input type="hidden" name="latitude" id="latitude">
									<input type="hidden" name="longitude" id="longitude">
								</div>
							</div>
						</div><!-- form-group -->
						
						
						<div class="form-group full_selectList col-md-6">
							<div id="map-canvas" style="width: 100%; height: 400px;"></div>
						</div><!-- form-group -->
					</div><!-- box-body -->   
					
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Preorder'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="preorder">
								<option value="1"><?php echo trans('messages.Enable'); ?></option>
								<option value="0" <?php echo (Input::old('preorder') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Disable'); ?></option>
							</select>
						</div>
					</div><!-- form-group -->
					
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Delivery Type'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="delivery_type">
								<option value="d"><?php echo trans('messages.Delivery'); ?></option>
								<option value="p" <?php echo (Input::old('delivery_type') == 'p') ? 'selected' : ''; ?>><?php echo trans('messages.Pickup'); ?></option>
								<option value="b" <?php echo (Input::old('delivery_type') == 'b') ? 'selected' : ''; ?>><?php echo trans('messages.Both'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('delivery_type') != '') ? $error->first('delivery_type') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					<div class="form-group full_selectList col-md-12">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Meta Keywords'); ?>:</label>
						<div class="col-md-12">
						  <textarea name="keywords" id="keywords" class="form-control"><?php echo Input::old('keywords'); ?></textarea>
						</div>
					</div><!-- form-group -->
					</div>
					               
              </div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<button type="button" class="btn btn-primary pull-right" id="go_next" onclick="gonext('delivery');"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Next'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/branches') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
    </div>
    </div>
    <div class="tab-pane" id="delivery">
    <div class="box no_top_border">
		<div class="col-md-12">
				<div class="box">
                	<div class="box-body" id="distance">
						<div class="form-group full_selectList col-md-12">
							<div class="col-md-12" style='margin-bottom:10px;'>
								<label class="col-sm-3 control-label"><?php echo trans('messages.Distance in KM'); ?>:<span class="req">*</span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" name="distance" placeholder="<?php echo trans('messages.Distance in KM'); ?>" value="<?php echo Input::old('distance'); ?>" id="area">
									@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('distance') != '') ? $error->first('distance') : ''; ?></p>@endif
								</div>
							</div>
							<div class="col-md-12" style='margin-bottom:10px;'>
								<label class="col-sm-3 control-label"><?php echo trans('messages.Delivery Fee'); ?><span class="req">*</span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" name="delivery_fee" placeholder="<?php echo trans('messages.Delivery Fee'); ?>" value="<?php echo Input::old('delivery_fee'); ?>">
									@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('delivery_fee') != '') ? $error->first('delivery_fee') : ''; ?></p>@endif
								</div>
							</div>
							<div class="col-md-12" style='margin-bottom:10px;'>
								<label class="col-sm-3 control-label"><?php echo trans('messages.Additional Charge'); ?><span class="req">*</span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" name="additional_charge" placeholder="<?php echo trans('messages.Additional Charge'); ?>" value="<?php echo Input::old('additional_charge'); ?>">
									@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('additional_charge') != '') ? $error->first('additional_charge') : ''; ?></p>@endif
								</div>
							</div>
						</div><!-- form-group -->
			
					</div><!-- box-body -->                 
              </div>
			</div> <!-- col-md-12 -->
		</div>
		<div class="box-footer">
			<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Branch'); ?></button>
			<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
			<button type="button" onclick="window.location.href='{!! URL::to('admin/branches') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    	</div>
    </div>
  
	{!! Form::close() !!}	
	</div>
	</div> <!-- box -->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
<?php 
function selectdrop($val1, $val2)
{
	$select = ($val1 == $val2) ? 'selected' : '';
	return $select; 
}
?>	
 
<script type="text/javascript">

function setchangeimg(val)
{ 
	$('#txt_changeprofileimage').html(val);}
	$(document).on("change", "#upload_img", function () { 
	console.log("The text has been changed.");
	var file = this.files[0];  
	var imagefile = file.type;  
	var match= ["image/jpeg","image/png","image/jpg"];
	if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
	{
		document.getElementById('error_msg').innerHTML = 'The image must be a file of type: jpg, jpeg, png';
		return false;
	}
	else
	{
		document.getElementById('error_msg').innerHTML = '';
		var reader = new FileReader();
		reader.onload = imageIsLoaded;
		reader.readAsDataURL(this.files[0]);
	}
	$("#txt_changeprofileimage").html(file.name);
});

function imageIsLoaded(e) 
{  
	var image = new Image(); 
	image.src = e.target.result;   
	image.onload = function() {
		$(".roundedimg").attr('src', e.target.result);
	}
}
$(document).ready(function()
{
	$(document).on("click", ".add", function(){
		$clone = $(".working_time:last").clone();
		$(".actiontype i").attr('class','fa fa-minus');
		$(".actiontype button").attr('class','plus_minus remove');
		$(".daylist").append($clone);
		
	});
	$(document).on("click", ".remove", function(){
		console.log(($(this).parent(".working_time")));
		$(this).parents().eq(3).slideUp(function(){ $(this).remove(); });
		return false;
	});


	 if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(GetAddress);
    }
function GetAddress(position) {
	$("#latitude").val(position.coords.latitude);
    $("#longitude").val(position.coords.longitude);
    var lat = parseFloat(position.coords.latitude);
    var lng = parseFloat(position.coords.longitude);
    $("#latitude").val(lat);
    $("#longitude").val(lng);
    var latlng = new google.maps.LatLng(lat, lng);
    var geocoder = geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'latLng': latlng }, function (results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
            $("#address").val(results[1].formatted_address);
        }
    }
    });

 var latitude = $("#latitude").val();
 var longitude = $("#longitude").val();

 $('#map-canvas').locationpicker({
	location: {
	latitude: latitude, longitude: longitude}
	,
	radius: 300,
	inputBinding: {
	latitudeInput: $('#latitude'),
	longitudeInput: $('#longitude')  
	},
	onchanged: function (currentLocation, radius, isMarkerDropped) {
        var addressComponents = $(this).locationpicker('map').location.addressComponents;
        updateControls(addressComponents);
    },
    oninitialized: function(component) {
        var addressComponents = $(component).locationpicker('map').location.addressComponents;
        updateControls(addressComponents);
    }

});
}

function updateControls(addressComponents) {
    $('#street').val(addressComponents.addressLine1);
    $('#city').val(addressComponents.city);
    //$('#us5-state').val(addressComponents.stateOrProvince);
    $('#zipcode').val(addressComponents.postalCode);
    $('#country').val(addressComponents.country);
}


function updatedelivery(addressComponents) {
    $('#area_name').val(addressComponents.district);
    
}

function showAddressMap()
{
	$('#canvas').locationpicker({
    location: {latitude: 11.0168, longitude: 76.9558},
    radius: 300,
    onchanged: function (currentLocation, radius, isMarkerDropped) {
        var addressComponents = $(this).locationpicker('map').location.addressComponents;
        updatedelivery(addressComponents);
    },
    oninitialized: function(component) {
        var addressComponents = $(component).locationpicker('map').location.addressComponents;
        updatedelivery(addressComponents);
    },
    inputBinding: {
	latitudeInput: $('#delivery_latitude'),
	longitudeInput: $('#delivery_longitude')       
	}
	});

}
$('#myTabs').tabs();
$('#delivery1').bind('click', function() {
	showAddressMap();
});

$('#branch_form input[type=radio]').on('change', function() {
   var checked = $('input[name=deliveryfee_type]:checked', '#branch_form').val();
   if(checked == 'distance')
   {
	   $("#distance").css('display', 'block');
	   $("#area").css('display', 'none');
   }
   else if(checked == 'area')
   {
	   $("#distance").css('display', 'none');
	   $("#area").css('display', 'block');
   }
});

});

function gonext(value)
{
	if(value == 'delivery')
	{
		$('#delivery1').addClass('active');
		$('#details1').removeClass('active');
		$('#workingtime1').removeClass('active');
		$('#delivery').addClass('active');
		$('#details').removeClass('active');
		$('#workingtime').removeClass('active');
	}
	else if(value == 'details')
	{
		$('#delivery1').removeClass('active');
		$('#details1').addClass('active');
		$('#workingtime1').removeClass('active');
		$('#delivery').removeClass('active');
		$('#details').addClass('active');
		$('#workingtime').removeClass('active');
	}
	else
	{
		$('#delivery1').removeClass('active');
		$('#details1').addClass('active');
		$('#workingtime1').addClass('active');
		$('#delivery').removeClass('active');
		$('#details').removeClass('active');
		$('#workingtime').addClass('active');
	}
}
</script>
<script>
var input = $('.clock_picker');
input.clockpicker({
    autoclose: true
});

// Manual operations
$('#button-a').click(function(e){
    // Have to stop propagation here
    e.stopPropagation();
    input.clockpicker('show')
            .clockpicker('toggleView', 'minutes');
});
$('#button-b').click(function(e){
    // Have to stop propagation here
    e.stopPropagation();
    input.clockpicker('show')
            .clockpicker('toggleView', 'hours');
});

$(document).on("blur", ".close_time", function(){
	
	$parent  = $(this).parents(".working_time");
	$start_time = $($parent).find(".start_time").val();
	$close_time = $($parent).find(".close_time").val();
	$($parent).find("span.time_error").html('');
	
	if($start_time > $close_time)
	{
		$($parent).find("span.time_error").html('Close time must be greater than start time');
	}
	if($start_time == '')
	{
		$($parent).find("span.time_error").html('Start time cannot be empty');
	}
});

$(document).on("blur", ".start_time", function(){
	
	$parent  = $(this).parents(".working_time");
	$start_time = $($parent).find(".start_time").val();
	$close_time = $($parent).find(".close_time").val();
	$($parent).find("span.time_error").html('');
	
	if($start_time > $close_time)
	{
		$($parent).find("span.time_error").html('Start time must be smaller than start time');
	}
	if($close_time == '')
	{
		$($parent).find("span.time_error").html('Close time cannot be empty');
	}
});

$(document).on("click", "#savetime", function(e){
	$.ajaxSetup({
	        header:$('meta[name="_token"]').attr('content')
	    })
	e.preventDefault(e);
	$('span.time_error').removeClass("has_error");
	$(".working_time").each(function()
	{
		var day = $('.working_day').val();
		var start_time = $(this).find('.start_time').val();
		var close_time = $(this).find('.close_time').val();
		if(day == '')
		{
			$(this).find("span.time_error").html('Working day cannot be empty');
			$(this).find('span.time_error').addClass("has_error");
		}
		if(start_time <= close_time)
		{
			$(this).find("span.time_error").html('Close time must be greater than start time');
			$(this).find('span.time_error').addClass("has_error");
		}
		if(close_time == '')
		{
			$(this).find("span.time_error").html('Close time cannot be empty');
			$(this).find('span.time_error').addClass("has_error");
		}
		if(start_time == '')
		{
			$(this).find("span.time_error").html('Start time cannot be empty');
			$(this).find('span.time_error').addClass("has_error");
		}
	})

		if($(".has_error").length) {       
          return false;
        }
        else
        {
        	var day = $('.selectLists').val();
        	var start_time = $('.start_time').val();
			var close_time = $('.close_time').val();
  	 		$(".has_error").html('');
	        $.ajax({
		        type:"POST",
		        url:'/admin/addbranch_time',
		        data:{day : day, start_time : start_time, close_time : close_time},
		        dataType: 'json',
		        success: function(data){
				},
	    	})
	    }
	})
</script>

<?php
function showvalidmsg($i, $array_valid)
{
	$active = '';
	if($i == 0)
	{
		$active = ($array_valid[$i] != '') ? 'active' : '';
	}
	else
	{
		$j = $i-1;	
		$active = ($array_valid[$i] != '' && $array_valid[$j] == '') ? 'active' : '';
	}
	return $active;
}
?>
@endsection     
