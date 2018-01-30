@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif

<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Add Vendor'); ?> </h1>
		@if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/addvendor', 'files' => 1)) !!}
	<div class="box no_top_border">
		<div class="nav-tabs-custom ">
			<ul class="nav nav-tabs">
				<?php
				if(count($languages) > 0)
				{
					$i = 0;
					foreach($languages as $language) {
				?>
				<li class="<?php echo ($i == 0) ? 'active' : ''; ?>"><a href="#<?php echo $language->code; ?>" data-toggle="tab"><?php echo ucfirst($language->language); ?></a></li>
				<?php $i++; } } ?>
			</ul>
			<div class="tab-content">
			<?php
			if(count($languages) > 0)
			{
				$i = 0;
				foreach($languages as $language) {
			?>
			<div class="<?php echo ($i == 0) ? 'active' : ''; ?> tab-pane" id="<?php echo $language->code; ?>">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header with-border">
							 <h3 class="box-title"><?php echo ucfirst($language->language); ?></h3>
						</div> <!-- box-header -->
				
						<div class="box-body col-md-6">
							<div class="form-group">
								<label>Name <span class="req">*</span> :</label>
								<input type="text" class="form-control" name="vendor[]" placeholder="Enter vendor name" value="">
								<input type="hidden" name="language[]" value="<?php echo $language->code; ?>">
							</div><!-- form-group -->
						</div><!-- box-body -->  
						
						<div class="box-body col-md-6">
							<div class="form-group">
								<label>Description <span class="req">*</span> :</label>
								<textarea class="form-control" name="description[]" placeholder="Enter vendor description"></textarea>
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
							 <h3 class="box-title">Details</h3>
						</div> <!-- box-header -->
                
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label">Category <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" multiple name="category_id[]">
								<option value=""></option>
								<?php if(count($categories) > 0) { 
									foreach($categories as $category) {
										$select = selectdrop(Input::old('category'), $category->id);
								?>
								<option value="<?php echo $category->id; ?>" <?php echo $select; ?>><?php echo $category->category; ?></option>
								<?php } } ?>
							</select>
						</div>
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label">Cuisines <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" multiple name="cuisine_id[]">
								<option value=""></option>
								<?php if(count($cuisines) > 0) { 
									foreach($cuisines as $cuisine) {
										$select = selectdrop(Input::old('category'), $cuisine->id);
								?>
								<option value="<?php echo $cuisine->id; ?>" <?php echo $select; ?>><?php echo $cuisine->cuisine; ?></option>
								<?php } } ?>
							</select>
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->   
					
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Email'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="email" placeholder="<?php echo trans('messages.Email'); ?>" value="">
						</div>
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Password'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" name="password" placeholder="<?php echo trans('messages.Password'); ?>" value="">
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->
					
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Mobile'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="mobile" placeholder="<?php echo trans('messages.Mobile'); ?>" value="">
						</div>
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Commission'); ?> (%) <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="commission_percentage" placeholder="<?php echo trans('messages.Commission'); ?>" value="">
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
					
					<div class="form-group upload_image col-sm-6">
						<label class="full_row" for="image"><?php echo trans('messages.Logo'); ?>:</label>
						<input type="file" class="form-control" id="upload_img" name="image">
						<label for="upload_img" class="upload_lbl">
							<img src="{!! URL::to('assets/admin/images/not-found.png') !!}" class="roundedimg">
						</label>
						<p id="error_msg" class="error_msg"></p>
						@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('image') != '') ? $error->first('image') : ''; ?></p>@endif
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Status'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="status">
								<option value="1"><?php echo trans('messages.Active'); ?></option>
								<option value="0" <?php echo (Input::old('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('status') != '') ? $error->first('status') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				
						
					</div><!-- box-body -->                
              </div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Vendor'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/vendors') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
	{!! Form::close() !!}	
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
	function updateControls(addressComponents) {
    $('#street').val(addressComponents.addressLine1);
    $('#city').val(addressComponents.city);
    //$('#us5-state').val(addressComponents.stateOrProvince);
    $('#zipcode').val(addressComponents.postalCode);
    $('#country').val(addressComponents.country);
}
$('#map-canvas').locationpicker({
    location: {latitude: 11.0168, longitude: 76.9558},
    radius: 300,
    onchanged: function (currentLocation, radius, isMarkerDropped) {
        var addressComponents = $(this).locationpicker('map').location.addressComponents;
        updateControls(addressComponents);
    },
    oninitialized: function(component) {
        var addressComponents = $(component).locationpicker('map').location.addressComponents;
        updateControls(addressComponents);
    },
    inputBinding: {
	latitudeInput: $('#latitude'),
	longitudeInput: $('#longitude')       
	}
});
});
</script>
@endsection     
