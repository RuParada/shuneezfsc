@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1>Edit Subcategory </h1>
		@if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/updatesubcategory', 'files' => 1)) !!}
	<div class="box no_top_border">
		<div class="col-md-12">
				<div class="box">
						<div class="box-header with-border">
							 <h3 class="box-title">Category</h3>
						</div> <!-- box-header -->
                
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label">Category <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="category">
								<?php if(count($categories) > 0) { 
									foreach($categories as $category) {
										$select = (Input::old('category') != '') ? selectdrop(Input::old('category'), $category->id) : selectdrop($subcategory->category_id, $category->id);
								?>
								<option value="<?php echo $category->id; ?>" <?php echo $select; ?>><?php echo $category->category; ?></option>
								<?php } } ?>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('category') != '') ? $error->first('category') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				
						
					</div><!-- box-body -->                
              </div>
			</div> <!-- col-md-12 -->
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
					$subcategory_name = DB::table('subcategory_description')->where('subcategory_id', $subcategory->id)->where('language', $language->code)->first();
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
								<label>Name <span class="req">*</span> :</label>
								<input type="text" class="form-control" name="subcategory[]" placeholder="Enter subcategory name" value="<?php echo (Input::old('subcategory')[$i] != '') ? Input::old('subcategory')[$i] : $subcategory_name->subcategory_name; ?>">
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
							 <h3 class="box-title">Details</h3>
						</div> <!-- box-header -->
                
					<div class="box-body">
					<div class="form-group upload_image col-sm-6">
						<label class="full_row">Image:</label>
						<input type="file" class="form-control" id="upload_img" name="image">
						<label for="upload_img" class="upload_lbl">
							<img src="{!! URL::to(($subcategory->image == '') ? 'assets/admin/images/not-found.png' : 'assets/uploads/subcategories/'.$subcategory->image) !!}" class="roundedimg">
						</label>
						<p id="error_msg" class="error_msg"></p>
						@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('image') != '') ? $error->first('image') : ''; ?></p>@endif
					</div><!-- form-group -->

					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label">Status <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="status">
								<option value="1" >Enabled</option>
								<option value="0" <?php echo (Input::old('status') == '0' || $subcategory->status == 0) ? 'selected' : ''; ?>>Disabled</option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('status') != '') ? $error->first('status') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				
						
					</div><!-- box-body -->                
              </div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<input type="hidden" name="id" value="<?php echo $subcategory->id; ?>">
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i>Update Subcategory</button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i>Reset</button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/subcategories') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i>Cancel</button>
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
