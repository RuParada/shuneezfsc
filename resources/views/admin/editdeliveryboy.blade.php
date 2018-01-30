@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Edit Deliveryboy'); ?> </h1>
		@if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/updatedeliveryboy', 'files' => 1)) !!}
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
					$deliveryboy_name = DB::table('deliveryboy_description')->where('deliveryboy_id', $deliveryboy->id)->where('language', $language->code)->first();
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
								<input type="text" class="form-control" name="name[]" placeholder="<?php echo trans('messages.Enter Delivery name'); ?>" value="<?php echo (Input::old('name')[$i] != '') ? Input::old('name')[$i] : $deliveryboy_name->deliveryboy_name; ?>">
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
						<label class="col-sm-3 control-label"><?php echo trans('messages.Branch'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="branch">
								<option value=""></option>
								<?php if(count($branches) > 0) { 
									foreach($branches as $branch) {
										$select = (Input::old('branch') != '') ? selectdrop(Input::old('branch'), $branch->id) : selectdrop($deliveryboy->branch_id, $branch->id);
								?>
								<option value="<?php echo $branch->id; ?>" <?php echo $select; ?>><?php echo $branch->branch; ?></option>
								<?php } } ?>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('branch') != '') ? $error->first('branch') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Email'); ?> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="email" placeholder="<?php echo trans('messages.Email'); ?>" value="<?php echo (Input::old('email') != '') ? Input::old('email') : $deliveryboy->email; ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->
					
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Mobile'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="mobile" placeholder="<?php echo trans('messages.Mobile'); ?>" value="<?php echo (Input::old('mobile') != '') ? Input::old('mobile') : $deliveryboy->mobile; ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Address'); ?> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="address" placeholder="<?php echo trans('messages.Address'); ?>" value="<?php echo (Input::old('address') != '') ? Input::old('address') : $deliveryboy->address; ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('address') != '') ? $error->first('address') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->   
					
					<div class="box-body">
					
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Password'); ?> :</label>
						<div class="col-sm-8">
							<input type="password" class="form-control" name="password" placeholder="<?php echo trans('messages.Password'); ?>" value="">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('password') != '') ? $error->first('password') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Status'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="status">
								<option value="1" selected><?php echo trans('messages.Active'); ?></option>
								<option value="0" <?php echo (Input::old('status') == '0' || $deliveryboy->status == 0) ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('status') != '') ? $error->first('status') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				</div><!-- box-body --> 
				<div class="box-body">
						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Vehicle Type'); ?> :</label>
							<div class="col-sm-8">
							<select class="selectLists" name="vehicle_type" id="vehicle_type">
								<option value="">{!! trans('messages.Select Vehicle') !!}</option>
								<option value="1" <?php echo ($deliveryboy->vehicle_type == '1') ? 'selected' : ''; ?>><?php echo trans('messages.Motor Bike'); ?></option>
								<option value="2" <?php echo ($deliveryboy->vehicle_type == '2') ? 'selected' : ''; ?>><?php echo trans('messages.Sedan'); ?></option>
								<option value="3" <?php echo ($deliveryboy->vehicle_type == '3') ? 'selected' : ''; ?>><?php echo trans('messages.Suv'); ?></option>
								<option value="4" <?php echo ($deliveryboy->vehicle_type == '4') ? 'selected' : ''; ?>><?php echo trans('messages.Pickup'); ?></option>
								<option value="5" <?php echo ($deliveryboy->vehicle_type == '5') ? 'selected' : ''; ?>><?php echo trans('messages.Van'); ?></option>
							</select>
							</div>
						</div><!-- form-group -->

						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Vehicle Attribute'); ?> :</label>
							<div class="col-sm-8">
								<select class="selectLists" name="vehicle_attribute" id="vehicle_type">
									<option value="">{!! trans('messages.Select Attribute') !!}</option>
									<option value="hot food" <?php echo ($deliveryboy->vehicle_attribute == 'hot food') ? 'selected' : ''; ?>><?php echo trans('messages.Hot food'); ?></option>
									<option value="fridge" <?php echo ($deliveryboy->vehicle_attribute == 'fridge') ? 'selected' : ''; ?>><?php echo trans('messages.Fridge'); ?></option>
								</select>
							</div>
						</div><!-- form-group -->
					</div><!-- box-body --> 

					<div class="box-body">
						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Select Country'); ?> :</label>
							<div class="col-sm-8">
							<select class="selectLists" name="country" id="country" onchange="getcity();">
                                <option value="">{!! trans('messages.Select City') !!}</option>
                                <?php if(count($countries) > 0) { 
                                    foreach($countries as $country) {
                                        $select = selectdrop($deliveryboy->country_id, $country->id);
                                ?>
                                <option value="<?php echo $country->id; ?>" <?php echo $select; ?>><?php echo $country->name->en; ?></option>
                                <?php } } ?>
                            </select>
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('country') != '') ? $error->first('country') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->

						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Select City'); ?> :</label>
							<div class="col-sm-8">
								<select class="selectLists" name="city" id="city">
                                    <option value="">{!! trans('Select City') !!}</option>
                                    <?php if(count($cities) > 0) { 
                                    foreach($cities as $city) {
                                        $select = selectdrop($deliveryboy->city_id, $city->id);
	                                ?>
	                                <option value="<?php echo $city->id; ?>" <?php echo $select; ?>><?php echo $city->name->en; ?></option>
	                                <?php } } ?>
                                </select>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('city') != '') ? $error->first('city') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->
					</div><!-- box-body --> 

					<div class="box-body">
						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Commission Percent'); ?> <span class="req">*</span> :</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="commission_percent" placeholder="<?php echo trans('messages.Commission Percent'); ?>" value="<?php echo $deliveryboy->commission_percent; ?>">
								@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('commission_percent') != '') ? $error->first('commission_percent') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->
						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Team'); ?> <span class="req">*</span> :</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="team" placeholder="<?php echo trans('messages.Select branch 1st'); ?>" value="<?php echo $deliveryboy->team; ?>" readonly id="team">
								@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('team_id') != '') ? $error->first('team_id') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->
					</div><!-- box-body --> 
				<div class="box-body">
					
					<div class="form-group upload_image col-sm-6">
						<label class="full_row" for="image"><?php echo trans('messages.Logo'); ?>:</label>
						<input type="file" class="form-control" id="upload_img" name="image">
						<label for="upload_img" class="upload_lbl">
							<img src="{!! URL::to(($deliveryboy->image != '') ? 'assets/uploads/deliveryboys/'.$deliveryboy->image : 'assets/admin/images/not-found.png') !!}" class="roundedimg">
						</label>
						<p id="error_msg" class="error_msg"></p>
						@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('image') != '') ? $error->first('image') : ''; ?></p>@endif
					</div><!-- form-group -->
				</div>               
              </div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<input type="hidden" name="id" value="<?php echo $deliveryboy->id; ?>">
		<!--<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Deliveryboy'); ?></button> 
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset'); ?></button>-->
		<button type="button" onclick="window.location.href='{!! URL::to('admin/deliveryboys') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Back'); ?></button>
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
