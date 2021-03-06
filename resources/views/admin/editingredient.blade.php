@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Edit Ingredient'); ?> </h1>

		@if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/updateingredient', 'files' => 1)) !!}
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
					$ingredient_name = DB::table('ingredient_description')->where('ingredient_id', $ingredient->id)->where('language', $language->code)->first();
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
								<label><?php echo trans('messages.Ingredient'); ?> <span class="req">*</span> :</label>
								<input type="text" class="form-control" name="ingredient_name[]" placeholder="<?php echo trans('messages.Enter Ingredient name'); ?>" value="<?php echo (Input::old('ingredient_name')[$i] != '') ? Input::old('ingredient_name')[$i] : $ingredient_name->ingredient_name; ?>">
								<input type="hidden" name="language[]" value="<?php echo $language->code; ?>">
								@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('category') != '') ? $error->first('category') : ''; ?></p>@endif
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
						<label class="col-sm-3 control-label"><?php echo trans('messages.Status'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectLists" name="status">
								<option value="1"><?php echo trans('messages.Active'); ?></option>
								<option value="0" <?php echo (Input::old('status') == '0' || $ingredient->status == 0) ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('status') != '') ? $error->first('status') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				
					<?php
					$ingredientlist_count = DB::table('ingredientlist')->where('ingredient_id', $ingredient->id)->count();
					?>	
					</div><!-- box-body -->
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-7 control-label"><?php echo trans('messages.Want to add list of Ingredients'); ?> <span class="req">*</span> :</label>
						<div class="col-sm-5">
							<input type="radio" class="ingredient_list_option" name="ingredient_list" value="1" <?php echo ($ingredientlist_count) ? 'checked' : ''; ?>><?php echo trans('messages.Yes');?>
							<input type="radio" class="ingredient_list_option" name="ingredient_list" value="0" <?php echo ($ingredientlist_count == 0) ? 'checked' : ''; ?> ><?php echo trans('messages.No'); ?>
						</div>
					</div><!-- form-group -->
					</div><!-- box-body -->
					
					<div class="ingredientlist_type" style="display:<?php echo ($ingredientlist_count) ? 'block' : 'none'; ?>">
					<?php
					$ingredientlists = DB::table('ingredientlist')->where('ingredient_id', $ingredient->id)->get();
					if(count($ingredientlists) > 0)
					{
						$i = 0;
						foreach($ingredientlists as $ingredientlist)
						{
					?>
					<div class="box-body ingredientlist">
						<?php
							if(count($languages) > 0)
							{
								foreach($languages as $language) {
									$ingredientlist_name = DB::table('ingredientlist_description')->where('ingredientlist_id', $ingredientlist->id)->where('language', $language->code)->first();
					?>
					<div class="form-group full_selectList col-md-3">
						<div class="col-sm-12">
							<input type="text" class="form-control col-md-3 ingredientlist_name_<?php echo $language->code; ?>" name="ingredientlist[<?php echo $i; ?>][name][<?php echo $language->code; ?>]" placeholder="<?php echo trans('messages.Ingredient Name'); ?> <?php echo $language->language; ?>" value="<?php echo $ingredientlist_name->ingredientlist_name; ?>"/>
						</div>
					</div><!-- form-group -->
					<?php } } ?>
					<div class="form-group full_selectList col-md-3">
						<div class="col-sm-12">
							<input type="text" class="form-control col-md-3 allowOnlyPrice ingredientlist_price" placeholder="<?php echo trans('messages.Price'); ?>" name="ingredientlist[<?php echo $i; ?>][price]" value="<?php echo $ingredientlist->price; ?>"/>
						</div>
					</div><!-- form-group -->
					<a href="javascript:void(0);" style="color:#C20C0C" class="remove-list" title="<?php echo trans('messages.remove Ingredient'); ?>">
						<i class="fa fa-minus-circle fa-fw"></i>
					</a>
					<input type="hidden" name="ingredientlist_id" value="<?php echo $ingredientlist->id; ?>">
					</div><!-- box-body -->
					<?php $i++; } ?>
					<div class="col-sm-9"></div>
					<a href="javascript:void(0);" class="add-ingredient" title="<?php echo trans('messages.Add field'); ?>" id="first_add">
						<i class="fa fa-plus-circle fa-fw"></i>
					</a>
					<?php } else { ?>
					<div class="box-body ingredientlist">
					<?php
					if(count($languages) > 0)
					{
						foreach($languages as $language) {
					?>	
					<div class="form-group full_selectList col-md-3">
						<div class="col-sm-12">
							<input type="text" class="form-control col-md-3 ingredientlist_name_<?php echo $language->code; ?>" name="ingredientlist[0][name][<?php echo $language->code; ?>]" placeholder="<?php echo trans('messages.Ingredient Name'); ?> <?php echo $language->language; ?>" value=""/>
						</div>
					</div><!-- form-group -->
					<?php  } } ?>
					<div class="form-group full_selectList col-md-3">
						<div class="col-sm-12">
							<input type="text" class="form-control col-md-3 allowOnlyPrice ingredientlist_price" placeholder="<?php echo trans('messages.Price'); ?>" name="ingredientlist[0][price]" value=""/>
						</div>
					</div><!-- form-group -->
					<a href="javascript:void(0);" class="add-ingredient" title="<?php echo trans('messages.Add field'); ?>" id="first_add">
						<i class="fa fa-plus-circle fa-fw"></i>
					</a>
					<a href="javascript:void(0);" style="color:#000" class="save-ingredient" title="save"><i class="fa fa-floppy-o fa-fw"></i></a>
					</div><!-- box-body -->
					<?php } ?>
					</div> 
					              
              </div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<input type="hidden" name="id" value="<?php echo $ingredient->id; ?>">
		<button type="submit" id="submit_form" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Ingredient'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/ingredients') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
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
$(document).ready(function(){
	$(document).on("click", ".add-ingredient", function(){
		$("#first_add").remove();
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
		$(".ingredientlist:last a").attr("title", "<?php echo trans('messages.Add field'); ?>");	
		$(".ingredientlist:last a").removeClass("remove-list").addClass("add-ingredient").css("color", "#00BCD4");
		$(".ingredientlist:last a i").removeClass("fa-minus-circle").addClass("fa-plus-circle");
		$(".ingredientlist:last input").val('');
		$(".ingredientlist:last .save-ingredient").remove();
		$(".ingredientlist:last").append('<a href="javascript:void(0);" style="color:#000" class="save-ingredient" title="save"><i class="fa fa-floppy-o fa-fw"></i></a>');
		return false;
	});
	
	$(document).on("click", ".remove-ingredient", function(){
		$add_ingredient = $(".add-ingredient").clone();
		$(this).parents(".ingredientlist").slideUp(function(){ $(this).remove(); });
		return false;
	});
	
	//Allow only numbers and dot for price
	$(document).on("keypress", ".allowOnlyPrice", function(e){
		if(!((e.which >= 48 && e.which <= 57) || e.which == 46))
			return false;
	});
	
	//Form Submission
		$(document).on("click", "#submit_form", function(e){
			$error = false;
			
			if($(".ingredientlist").length > 1 && parseInt($(".ingredient_list_option:checked").val()))
			{
				$(".ingredientlist").each(function(){
					$index = $(this).index();
					
					//Ingredient Name Validation
					$elementToCompare = $(this).find("input:first");
					$elementDuplicate = false;
					$valueToCompare = $(this).find("input:first").val();
					$nameToCompare = $(this).find("input:first").attr("name");
					
					$element = ".ingredientlist:not(:eq(" + $index + "))";
					$($element).each(function(){
						if(($valueToCompare == $(this).find("input:first").val()) && ($nameToCompare != $(this).find("input:first").attr("name")) && ($(this).find("input:first").val().length > 0))
						{
							$(this).find("input:first").addClass("has-error").css("border-color", "#A94442").attr("title", 'Duplicate Ingredient Name');
							$error = true;
							$elementDuplicate = true;
						}
					});
					
					if($elementDuplicate)
						$($elementToCompare).addClass("has-error").css("border-color", "#A94442").attr("title", 'Duplicate Ingredient Name');
					else
					{
						//Empty Validation - Ingredient Name
						if($(this).find("input:first").val().length == 0)
						{
							$(this).find("input:first").addClass("has-error").css("border-color", "#A94442").attr("title", 'Ingredient Name Cannot be empty');
							$error = true;
						}
						else
							$($elementToCompare).removeClass("has-error").attr('title', '').css('border-color', '#e5e9ec');
					}
				});	
			}
			else
			{
				//Empty Validation - Ingredient Name
				if(parseInt($("input[name='ingredient_list']:checked").val()))
				{
					if($(".ingredientlist").find("input:first").val().length == 0)
					{
						$(".ingredientlist").find("input:first").addClass("has-error").css("border-color", "#A94442").attr("title", 'Ingredient Name Cannot be empty');
						$error = true;
					}
				}
			}
			
			//Number Validation - Ingredient Price
			$(".allowOnlyPrice").each(function(){
				if($(this).val().length != 0)
				{
					//Numeric and Positive Value Validation
					if((!$.isNumeric($(this).val())) ||(parseFloat($(this).val()) < 0))
					{
						$(this).addClass("has-error").css("border-color", "#A94442").attr("title", 'Ingredient Price must be a positive number');
						$error = true;
					}
					else
						$(this).removeClass("has-error").css("border-color", "#e5e9ec").attr("title", '');
				}
				else
					$(this).removeClass("has-error").css("border-color", "#e5e9ec").attr("title", '');
				
			});
			
			if($error)
			{ 
				e.preventDefault(e); 
			}
			else
			{
				return true;
			}
		});
		
		//Remove Ingredient List
		$(document).on("click", ".remove-list", function(e){
			var csrfToken = $('meta[name="csrf-token"]').attr("content");		
			var id = $(this).siblings("input[type='hidden']").val();
			var path = "<?php echo Url::to('admin/removeingredientlist'); ?>";
			$.ajax({
				type: "GET",
				url: path,
				data: { id: id},
				success: function(data){
					//alert("hai");
					if(parseInt(data))
						location.reload();
				},
				error:function(xhr, msg, code){
					document.write(msg);
					return false;
					e.preventDefault();
				}
			});
			
			//alert($('meta[name="csrf-token"]').attr("content"));
			return false;
		});
});
$(document).on("click", ".save-ingredient", function(){
	var parent = $(this).parent();
	var name_en = parent.find(".ingredientlist_name_en").val();
	var name_ar = parent.find(".ingredientlist_name_ar").val();
	var price = parent.find(".ingredientlist_price").val();
	var path = "<?php echo Url::to('admin/saveingredientlist'); ?>";
	if(name_en != '' && name_ar != '' && price != '')
	{
		$.ajax({
			beforeSend : function() {
	    		$("body").addClass("loading");
	    	},
			type: "GET",
			url: path,
			data: { ingredient_id: <?php echo $ingredient->id; ?>, name_en: name_en, name_ar: name_ar, price: price},
			success: function(data){
				parent.find('.save-ingredient').remove();
				$("body").removeClass("loading");
			},
			error:function(xhr, msg, code){
				$("body").removeClass("loading");
				document.write(msg);
				return false;
				e.preventDefault();
			}
		});
	}
})
</script>


<script type="text/javascript">
$(document).ready(function(){
    $('input[type="radio"]').click(function(){
        if($(this).attr("value")=="1"){
            $(".ingredientlist_type").css('display', 'block');
        }
        if($(this).attr("value")=="0"){
          
		   $(".ingredientlist_type").css('display', 'none');
        }
        
    });
});
</script>	 
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
@endsection     
