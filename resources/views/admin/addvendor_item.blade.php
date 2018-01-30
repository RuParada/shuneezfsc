@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Add Vendor Item'); ?></h1>
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/addvendoritem', 'id' => 'add_vendor_form', 'files' => 1)) !!}
	<div class="box no_top_border">
		<div class="col-md-12">
				<div class="box">
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-4 control-label"><?php echo trans('messages.Category'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="category" id="category" onchange="getsubcategories();">
								<option value=""></option>
								<?php if(count($categories) > 0) { 
									foreach($categories as $category) {
										$select = selectdrop(Input::old('category'), $category->id);
								?>
								<option value="<?php echo $category->id; ?>" <?php echo $select; ?>><?php echo $category->category; ?></option>
								<?php } } ?>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('category') != '') ? $error->first('category') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-4 control-label"><?php echo trans('messages.Subcategory'); ?> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="subcategory" id="subcategory_id">
								<option value=""></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('subcategory') != '') ? $error->first('subcategory') : ''; ?></p>@endif
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
				
						<div class="box-body col-md-12">
							<div class="form-group col-md-6">
								<label><?php echo trans('messages.Name'); ?> <span class="req">*</span> :</label>
								<input type="text" class="form-control" name="item_name[]" placeholder="<?php echo trans('messages.Enter Item Name'); ?>" value="<?php echo Input::old('item_name')[$i]; ?>">
								<input type="hidden" name="language[]" value="<?php echo $language->code; ?>">
								@if(count($array_valid))<p class="error_msg"><?php echo ($array_valid[$i] != '') ? trans('messages.Name field is required') : ''; ?></p>@endif
							</div><!-- form-group -->
							
							<div class="form-group col-md-6">
								<label><?php echo trans('messages.Description'); ?>:</label>
								<textarea class="form-control" name="item_description[]" placeholder="<?php echo trans('messages.Enter Item description'); ?>"><?php echo Input::old('item_description')[$i]; ?></textarea>
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
							<label class="col-sm-3 control-label"><?php echo trans('messages.Price'); ?> <span class="req">*</span> :</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="price" placeholder="<?php echo trans('messages.Price'); ?>" value="<?php echo Input::old('price'); ?>">
								@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('price') != '') ? $error->first('price') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->

						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Weight'); ?> :</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="weight" placeholder="<?php echo trans('messages.Weight'); ?>" value="<?php echo Input::old('weight'); ?>">
								@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('weight') != '') ? $error->first('weight') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->
						</div><!-- box-body -->  
						
						<div class="box-body">
						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Unit'); ?> :</label>
							<div class="col-sm-8">
							<select class="selectLists" name="unit" id="unit">
								<option value=""></option>
								<option value="g" <?php echo (Input::old('unit') == 'g') ? 'selected' : ''; ?>><?php echo trans('messages.gram'); ?></option>
								<option value="kg" <?php echo (Input::old('unit') == 'kg') ? 'selected' : ''; ?>><?php echo trans('messages.kilogram'); ?></option>
								<option value="pieces" <?php echo (Input::old('unit') == 'pieces') ? 'selected' : ''; ?>><?php echo trans('messages.pieces'); ?></option>
								<option value="people" <?php echo (Input::old('unit') == 'people') ? 'selected' : ''; ?>><?php echo trans('messages.people'); ?></option>
							</select>
							</div>
						</div><!-- form-group -->

						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Serve For'); ?> :</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="serve_for" placeholder="<?php echo trans('messages.Serve For'); ?>" value="<?php echo Input::old('serve_for'); ?>">
								@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('serve_for') != '') ? $error->first('serve_for') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->
						</div><!-- box-body -->  
						
						<div class="box-body">
						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-3 control-label"><?php echo trans('messages.Size'); ?> :</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="size" placeholder="<?php echo trans('messages.Size'); ?>" value="<?php echo Input::old('size'); ?>">
								@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('size') != '') ? $error->first('size') : ''; ?></p>@endif
							</div>
						</div><!-- form-group -->
					
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Featured'); ?>:</label>
						<div class="col-sm-8">
							<select class="selectLists" name="featured">
								<option value="1"><?php echo trans('messages.Yes'); ?></option>
								<option value="0" <?php echo (Input::old('featured') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.No'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('featured') != '') ? $error->first('featured') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
						</div><!-- box-body -->   
                
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Availability'); ?> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="availability">
								<option value="1"><?php echo trans('messages.Yes'); ?></option>
								<option value="0" <?php echo (Input::old('availability') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.No'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('availability') != '') ? $error->first('availability') : ''; ?></p>@endif
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
					</div>
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Sort'); ?> :</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="sort_number" placeholder="<?php echo trans('messages.Sort'); ?>" value="<?php echo Input::old('sort_number'); ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('sort_number') != '') ? $error->first('sort_number') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
					<div class="form-group upload_image col-sm-6">
						<label class="full_row" for="image"><?php echo trans('messages.Image'); ?><span class="req">*</span>:</label>
						<input type="file" class="form-control" id="upload_img" name="image">
						<label for="upload_img" class="upload_lbl">
							<img src="{!! URL::to('assets/admin/images/not-found.png') !!}" class="roundedimg">
						</label>
						<p id="error_msg" class="error_msg"></p>
						@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('image') != '') ? $error->first('image') : ''; ?></p>@endif
					</div><!-- form-group -->
					</div>

					<div class="box-body">
						<div class="form-group full_selectList col-md-6">
							<label class="col-sm-7 control-label"><?php echo trans('messages.Want to add size'); ?>:</label>
							<div class="col-sm-5">
								<input type="radio" name="is_size" class="ingredient_list_option" value="1" > <?php echo trans('messages.Yes'); ?>
								<input type="radio" name="is_size" checked class="ingredient_list_option" value="0"  ><?php echo trans('messages.No'); ?>
							</div>
						</div><!-- form-group -->
					</div><!-- box-body -->

					<div class="size_section" style="<?php echo (Input::old('is_size') == 1) ? 'display:block' : 'display:none'; ?>">
						<div class="box-body size_type">
							<?php
							if(count($languages) > 0)
							{
								$i = 0;
								foreach($languages as $language) {
							?>	
							<div class="form-group full_selectList col-md-3">
								<div class="col-sm-12">
									<input type="text" class="form-control col-md-3" name="size[0][name][<?php echo $language->code; ?>]" placeholder="<?php echo trans('messages.Size'); ?> <?php echo $language->language; ?>" value=""/>
								</div>
							</div><!-- form-group -->
							<?php  } } ?>
							<div class="form-group full_selectList col-md-3">
								<div class="col-sm-12">
									<input type="text" class="form-control col-md-3 allowOnlyPrice" placeholder="<?php echo trans('messages.Price'); ?>" name="size[0][price]" value=""/>
								</div>
							</div><!-- form-group -->
							<a href="javascript:void(0);" class="add-size" title="<?php echo trans('messages.Add field'); ?>">
								<i class="fa fa-plus-circle fa-fw"></i>
							</a>
						</div><!-- box-body -->
					</div> 
					
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-7 control-label"><?php echo trans('messages.Want to add list of Ingredients'); ?>:</label>
						<div class="col-sm-5">
							<input type="radio" name="is_ingredients" class="ingredient_list_option" value="1" > <?php echo trans('messages.Yes'); ?>
							<input type="radio" name="is_ingredients" checked class="ingredient_list_option" value="0"  ><?php echo trans('messages.No'); ?>
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->
					<div class="ingredientlist_type" style="display:none">
					<div class="list_type">
					<div class="box-body ingredient_type"> 
						<div class="form-group full_selectList col-md-12">
							<label class="col-sm-2 control-label"><?php echo trans('messages.Ingredient Type'); ?>  <span class="req">*</span> :</label>
							<div class="col-sm-5 entry">
								<select class="selectLists" name="ingredient[]" level='1' id="test" onchange="getingredientlist(this);" >
									<option value=""><?php echo trans('messages.Select Ingredient'); ?></option>
									<?php if(count($ingredients) > 0) { 
										foreach($ingredients as $ingredient) {
									?>
									<option value="<?php echo $ingredient->id; ?>"><?php echo $ingredient->ingredient; ?></option>
									<?php } } ?>
								</select>
								<p style="color:red"></p>
							</div>
						</div><!-- form-group -->
						<div class="form-group full_selectList col-md-12">
							<label class="col-sm-2 control-label"><?php echo trans('messages.Sort'); ?> :</label>
							<div class="col-sm-5">
								<input type="text" class="ingredient_sort form-control" name="ingredient_sort[]" placeholder="<?php echo trans('messages.Sort'); ?>" value="">
							</div>
						</div><!-- form-group -->
					</div>
					<div class="box-body ingredientlist" id="ingredientlist1">
					</div>
				<div class="box-body minmax" id="minmax1"></div>
				<div align="center" id="ingerror_msg" style="color:red;"></div>
				<button type="button" id="add" class="add" disabled="disabled"><?php echo trans('messages.Add Ingredients'); ?></button>
			</div> <!-- col-md-12 -->
			</div>
			
			<div class="box-body">
				<div class="form-group full_selectList col-md-6">
					<label class="col-sm-7 control-label"><?php echo trans('messages.Want to add list of Execlusions'); ?>:</label>
					<div class="col-sm-5">
						<input type="radio" name="is_execlusion" class="ingredient_list_option" value="1" > <?php echo trans('messages.Yes'); ?>
						<input type="radio" name="is_execlusion" checked class="ingredient_list_option" value="0"  ><?php echo trans('messages.No'); ?>
					</div>
				</div><!-- form-group -->
			</div><!-- box-body -->

			<div class="execlusion_section" style="<?php echo (Input::old('is_execlusion') == 1) ? 'display:block' : 'display:none'; ?>">
				<div class="box-body">
					<?php
					if(count($execlusions) > 0)
					{
						foreach($execlusions as $execlusion) {
					?>	
					<div class="form-group full_selectList col-md-5">
						<div class="col-md-9">
							<input type="text" class="form-control col-md-12" value="{!! $execlusion->execlusion !!}"/>
						</div>	
						<div class="col-md-3">
							<input type="checkbox" class="col-md-2" name="execlusion_id[]" value="{!! $execlusion->id !!}">
						</div>
					</div><!-- form-group -->
					<?php  } } ?>
					<span class="error_msg" id="execlusion_msg"></span>
				</div><!-- box-body -->
			</div> 
	
	<div class="box-footer">
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Item'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/vendoritems') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
	{!! Form::close() !!}	
	</div> <!-- box -->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
<div class="modal_load"></div>	
<?php 
function selectdrop($val1, $val2)
{
	$select = ($val1 == $val2) ? 'selected' : '';
	return $select; 
}
?>	 
<script type="text/javascript">

function minMaxValidation($min, $max, $required, $total, $error)
	{ 
		if($max > $total)
		{
			$($error).css("display", "block").html("Maximum value cannot be greater than " + $total);
			$($error).addClass("has_error");
		}

		if($required && ($min == '' || $max == ''))
		{
			$($error).css("display", "block").html("Minimum and Maximum value cannot be empty.");
			$($error).addClass("has_error");
		}
		else if($required && ($min == 0 || $max == 0))
		{
			$($error).css("display", "block").html("Minimum and Maximum value cannot be zero.");
			$($error).addClass("has_error");
		}
		else if($min > $total)
		{
			$($error).css("display", "block").html("Minimum value cannot be greater than " + $total);
			$($error).addClass("has_error");
		}
		else if($min > $max)
		{
			$($error).css("display", "block").html("Minimum value cannot be greater than maximum value");
			$($error).addClass("has_error");
		}
		else if($max > $total)
		{
			$($error).css("display", "block").html("Maximum value cannot be greater than " + $total);
			$($error).addClass("has_error");
		}
		else if($max == 0 || $max == '')
		{
			$($error).css("display", "block").html("Maximum value cannot be empty");
			$($error).addClass("has_error");
		}
		else
		{
			$($error).html("");
			$($error).removeClass("has_error");
		}
	}

//Minimum value Validation
	$(document).on("keyup", ".minimum", function(){
		$parent 		= $(this).parents(".minmax");
		$min_value 		= $(this).val();	
		$max_value 		= $($parent).find(".maximum").val();
		$required_check = $(this).parents(".minmax").find(".required_control:checked").prop("checked");
		$total 			= $($parent).find(".ing_count").val();
		$errorElement 	= $(".required_control").parent().siblings(".errors");
		minMaxValidation($min_value, $max_value, $required_check, $total, $errorElement);
	});
	
	//Maximum value Validation
	$(document).on("keyup", ".maximum", function(){
		$parent 		= $(this).parents(".minmax");
		$min_value 		= $($parent).find(".minimum").val();
		$max_value 		= $(this).val();
		$required_check = $(this).parents(".minmax").find(".required_control").prop("checked");
		$total 			= $($parent).find(".ing_count").val();
		$errorElement 	= $(".required_control").parent().siblings(".errors");
		minMaxValidation($min_value, $max_value, $required_check, $total, $errorElement);
	});

	//Required Validation
	$(document).on("click", ".required_control", function(){
		$parent 		= $(this).parents(".minmax");
		$min_value 		= $($parent).find(".minimum").val();
		$max_value 		= $($parent).find(".maximum").val();
		$total 			= $($parent).find(".ing_count").val();
		$errorElement 	= $(this).parent().siblings(".errors");
		if($(this).is(":checked"))
		{
			$($parent).find(".required_disable").attr('disabled', 'disabled');
		}
		else
		{
			$($parent).find(".required_disable").removeAttr('disabled');
		}
		minMaxValidation($min_value, $max_value, $(this).is(":checked"), $total, $errorElement);
	})

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

function getcategories()
{
	//alert('hi');
    var vendor_id = $( "#vendor option:selected" ).val();
    $.ajax({
    beforeSend : function() {
    	$("body").addClass("loading");
    },
    type : "GET",
    dataType : "json",
    url  : "<?php echo URL::to('admin/getvendorcategories'); ?>",
    data : {'vendor_id' : vendor_id},
    async: true,
    success: function(result) {
    			$("#category").html(result.category);
    			$("body").removeClass("loading");
    		}
    	});
}

function getsubcategories()
{
	var category_id = $( "#category option:selected" ).val();
    $.ajax({
    beforeSend : function() {
    	$("body").addClass("loading");
    },
    type : "GET",
    dataType : "json",
    url  : "<?php echo URL::to('admin/getvendorsubcategories'); ?>",
    data : {'category_id' : category_id},
    async: true,
    success: function(result) {
				$("#subcategory_id").html(result.subcategories);
    			$("body").removeClass("loading");
    		}
    	});
}

var ingredients = new Array();  
function getingredientlist(thiss)
{ 	
		var level = ($(thiss).attr('level'));
		var ingredient_id = $( thiss ).val();
		$.ajax({
		beforeSend : function() {
			$("body").addClass("loading");
		},
		type : "GET",
		dataType : "json",
		url  : "<?php echo URL::to('admin/getingredientlist'); ?>",
		data : {'ingredient_id' : ingredient_id},
		async: true,
		success: function(result) {
					$("#ingredientlist"+level).html(result.ingredientlist);
					$("#minmax"+level).html(result.minmax);
					$(".add").removeAttr('disabled');
					$("body").removeClass("loading");
				}
			});
	
}

$(document).ready(function(){
	
	$(document).on("click", ".add", function(){
		var count = $(".selectLists:last").attr('level') +1;
		$clone = $(".list_type:last").clone();
		$(".selectLists").select2("destroy");
		$(".list_type").find('.add:last').attr('class','remove').html('<?php echo trans('messages.Delete Item'); ?>');
		$(".ingredientlist_type").append($clone);
		$(".list_type:last").find('.selection:last').remove();
		$(".list_type:last").find('.ingredientlist').html('');
		$(".list_type:last").find('.ingredientlist:last').attr('id', 'ingredientlist'+count);
		$(".list_type:last").find('.minmax:last').attr('id', 'minmax'+count);
		$(".list_type:last").find('.selectLists:last').attr('level', count);
		$(".list_type:last").find('.minmax:last').html('');
		$(".list_type:last").find('.ingredient_sort:last').val('');
		$(".list_type").find('.add').attr('disabled','disabled');
		$(".selectLists").select2();
		return false;
	});
	
	$(document).on("click", ".remove", function(){
		$(this).parents(".list_type").slideUp(function(){ $(this).remove(); });
		return false;
	});

	 $("input:radio[name=is_ingredients]").click(function(){
        if($(this).attr("value")=="1"){
            $(".ingredientlist_type").css('display', 'block');
        }
        if($(this).attr("value")=="0"){
          
		   $(".ingredientlist_type").css('display', 'none');
        }
        
    });

	 $("input:radio[name=is_size]").click(function(){
        if($(this).attr("value")=="1"){
            $(".size_section").css('display', 'block');
        }
        if($(this).attr("value")=="0"){
          
		   $(".size_section").css('display', 'none');
        }
        
    });

	 $("input:radio[name=is_execlusion]").click(function(){
        if($(this).attr("value")=="1"){
            $(".execlusion_section").css('display', 'block');
        }
        if($(this).attr("value")=="0"){
          
		   $(".execlusion_section").css('display', 'none');
        }
        
    });
    
    $(document).on("click", ".add-ingredient", function(){
		$clone = $(".ingredientlist:last").clone();
		$clone.find(".remove_button").remove();
		if($(this).siblings().hasClass("remove_button"))
		$(this).remove();
		$(this).siblings(".remove-list").remove();
		$(".ingredientlist a").attr("title", "<?php echo trans('messages.remove'); ?>");	
		$(".ingredientlist a").removeClass("add-ingredient").addClass("remove-ingredient").css("color", "#C20C0C");
		$(".ingredientlist a i").removeClass("fa-plus-circle").addClass("fa-minus-circle");
		$index = $(".ingredientlist").length;
		$clone.find("input").each(function(){
			$(this).attr("name", ($(this).attr("name").replace(/\[([0-9]+)\]/g, "[" + $index + "]")));
		});
		$(".ingredientlist_type").append($clone);
		return false;item1_ar
	});
	
	$(document).on("click", ".remove-ingredient", function(){
		console.log(($(this).parent(".ingredientlist")));
		//$add_ingredient = $(".add-ingredient").clone();
		$(this).parent(".ingredientlist").slideUp(function(){ $(this).remove(); });
		return false;
	});
});


/* form submit function */
$(function(){
	$('#add_vendor_form').on('submit',function(e)
	{ 
		if($('.ingredientlist_type').is(':visible'))
		{
			var totalitem 	= $("#ing_count").val();
			var min 		= $("#min_val").val();
			var max 		= $("#max_val").val();
			
			if($(".has_error").length) {			 
				return false;
			}
			else
			{
				$("#ingerror_msg").html('');
				return true;
			}

		}
		if($('.execlusion_section').is(':visible'))
		{
			if($('[name="execlusion_id[]"]:checked').length == 0)
			{
				$("#execlusion_msg").html('Atleast select one execlusion');
				return false;
			}
			else
			{
				$("execlusion_msg").html('');
				return true;
			}
		}
	});	 
});

$(document).ready(function(){
	$(document).on("click", ".add-size", function(){
		$clone = $(".size_type:last").clone();
		$clone.find(".remove_button").remove();
		if($(this).siblings().hasClass("remove_button"))
		$(this).remove();
		$(this).siblings(".remove-list").remove();
		$(".size_type a").attr("title", "<?php echo trans('messages.remove'); ?>");	
		$(".size_type a").removeClass("add-size").addClass("remove-size").css("color", "#C20C0C");
		$(".size_type a i").removeClass("fa-plus-circle").addClass("fa-minus-circle");
		$index = $(".size_type").length;
		$clone.find("input").each(function(){
			$(this).attr("name", ($(this).attr("name").replace(/\[([0-9]+)\]/g, "[" + $index + "]")));
		});
		$(".size_section").append($clone);
		$(".size_type:last input").val('');
		return false;
	});
	
	$(document).on("click", ".remove-size", function(){
		$add_ingredient = $(".add-size").clone();
		$(this).parents(".size_type").slideUp(function(){ $(this).remove(); });
		return false;
	});
});
</script>

<style>
.modal_load {
    display:    none;
    position:   fixed;
    z-index:    1000;
    top:        0;
    left:       0;
    height:     100%;
    width:      100%;
    background: rgba( 255, 255, 255, .8 ) 
                url(<?php echo URL::to('assets/images/loading.gif'); ?>) 
                50% 50% 
                no-repeat;
}


body.loading {
    overflow: hidden;   
}

body.loading .modal_load {
    display: block;
}
</style>
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
