@extends('branch_header')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Add Deliveryboy'); ?> </h1>
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'branch/adddeliveryboy', 'files' => 1)) !!}
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
								<input type="text" class="form-control" name="name[]" placeholder="<?php echo trans('messages.Enter Delivery name'); ?> " value="<?php echo Input::old('name')[$i]; ?>">
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
							<input readonly type="text" maxlength='30' class="form-control" id="branch" placeholder="<?php echo trans('messages.Branch'); ?> " value="<?php echo Session('name'); ?>">
                            <input type="hidden" name="branch" value="<?php echo Session('branch_id'); ?>">
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('branch') != '') ? $error->first('branch') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Email'); ?> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="email" placeholder="<?php echo trans('messages.Email'); ?>" value="<?php echo Input::old('email'); ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->
					
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Mobile'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="mobile" placeholder="<?php echo trans('messages.Mobile'); ?>" value="<?php echo Input::old('mobile'); ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Address'); ?> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="address" placeholder="<?php echo trans('messages.Address'); ?>" value="<?php echo Input::old('address'); ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('address') != '') ? $error->first('address') : ''; ?></p>@endif
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
								<option value="1"><?php echo trans('messages.Active'); ?></option>
								<option value="0" <?php echo (Input::old('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('status') != '') ? $error->first('status') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				
						
					</div><!-- box-body -->  
					<div class="box-body">
					<div class="form-group upload_image col-sm-6">
						<label class="full_row" for="image"><?php echo trans('messages.Image'); ?>:</label>
						<input type="file" class="form-control" id="upload_img" name="image">
						<label for="upload_img" class="upload_lbl">
							<img src="{!! URL::to('assets/admin/images/not-found.png') !!}" class="roundedimg">
						</label>
						<p id="error_msg" class="error_msg"></p>
						@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('image') != '') ? $error->first('image') : ''; ?></p>@endif
					</div><!-- form-group -->
					
					</div>              
              </div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Deliveryboy'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('branch/deliveryboys') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
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
