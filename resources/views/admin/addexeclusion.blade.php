@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>

<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Add Execlusion'); ?> </h1>
		@if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/addexeclusion', 'files' => 1)) !!}
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
								<label><?php echo trans('messages.Execlusion'); ?> <span class="req">*</span> :</label>
								<input type="text" class="form-control" name="execlusion_name[]" placeholder="<?php echo trans('messages.Enter Execlusion name'); ?>" value="<?php echo Input::old('execlusion_name')[$i]; ?>">
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
		<button type="submit" id="submit_form" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Execlusion'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/execlusions') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
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
$(document).ready(function(){
	$(document).on("click", ".add-execlusion", function(){
		$clone = $(".execlusionlist:last").clone();
		$clone.find(".remove_button").remove();
		if($(this).siblings().hasClass("remove_button"))
		$(this).remove();
		$(this).siblings(".remove-list").remove();
		$(".execlusionlist a").attr("title", "<?php echo trans('messages.remove'); ?>");	
		$(".execlusionlist a").removeClass("add-execlusion").addClass("remove-execlusion").css("color", "#C20C0C");
		$(".execlusionlist a i").removeClass("fa-plus-circle").addClass("fa-minus-circle");
		$index = $(".execlusionlist").length;
		$clone.find("input").each(function(){
			$(this).attr("name", ($(this).attr("name").replace(/\[([0-9]+)\]/g, "[" + $index + "]")));
		});
		$(".execlusionlist_type").append($clone);
		$(".execlusionlist:last input").val('');
		return false;
	});
	
	$(document).on("click", ".remove-execlusion", function(){
		$add_execlusion = $(".add-execlusion").clone();
		$(this).parents(".execlusionlist").slideUp(function(){ $(this).remove(); });
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
			
			if($(".execlusionlist").length > 1 && parseInt($(".execlusion_list_option:checked").val()))
			{
				$(".execlusionlist").each(function(){
					$index = $(this).index();
					
					//Execlusion Name Validation
					$elementToCompare = $(this).find("input:first");
					$elementDuplicate = false;
					$valueToCompare = $(this).find("input:first").val();
					$nameToCompare = $(this).find("input:first").attr("name");
					
					$element = ".execlusionlist:not(:eq(" + $index + "))";
					$($element).each(function(){
						if(($valueToCompare == $(this).find("input:first").val()) && ($nameToCompare != $(this).find("input:first").attr("name")) && ($(this).find("input:first").val().length > 0))
						{
							$(this).find("input:first").addClass("has-error").css("border-color", "#A94442").attr("title", 'Duplicate Execlusion Name');
							$error = true;
							$elementDuplicate = true;
						}
					});
					
					if($elementDuplicate)
						$($elementToCompare).addClass("has-error").css("border-color", "#A94442").attr("title", 'Duplicate Execlusion Name');
					else
					{
						//Empty Validation - Execlusion Name
						if($(this).find("input:first").val().length == 0)
						{
							$(this).find("input:first").addClass("has-error").css("border-color", "#A94442").attr("title", 'Execlusion Name Cannot be empty');
							$error = true;
						}
						else
							$($elementToCompare).removeClass("has-error").attr('title', '').css('border-color', '#e5e9ec');
					}
				});	
			}
			else
			{
				//Empty Validation - Execlusion Name
				if(parseInt($("input[name='execlusion_list']:checked").val()))
				{
					if($(".execlusionlist").find("input:first").val().length == 0)
					{
						$(".execlusionlist").find("input:first").addClass("has-error").css("border-color", "#A94442").attr("title", 'Execlusion Name Cannot be empty');
						$error = true;
					}
				}
			}
			
			//Number Validation - Execlusion Price
			$(".allowOnlyPrice").each(function(){
				if($(this).val().length != 0)
				{
					//Numeric and Positive Value Validation
					if((!$.isNumeric($(this).val())) ||(parseFloat($(this).val()) < 0))
					{
						$(this).addClass("has-error").css("border-color", "#A94442").attr("title", 'Execlusion Price must be a positive number');
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
});
</script>


<script type="text/javascript">
$(document).ready(function(){
    $('input[type="radio"]').click(function(){
        if($(this).attr("value")=="1"){
            $(".execlusionlist_type").css('display', 'block');
        }
        if($(this).attr("value")=="0"){
          
		   $(".execlusionlist_type").css('display', 'none');
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
@endsection     
