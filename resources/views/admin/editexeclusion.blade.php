@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Edit Execlusion'); ?> </h1>

		@if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/updateexeclusion', 'files' => 1)) !!}
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
					$execlusion_name = DB::table('execlusion_description')->where('execlusion_id', $execlusion->id)->where('language', $language->code)->first();
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
								<input type="text" class="form-control" name="execlusion_name[]" placeholder="<?php echo trans('messages.Enter Execlusion name'); ?>" value="<?php echo (Input::old('execlusion_name')[$i] != '') ? Input::old('execlusion_name')[$i] : $execlusion_name->execlusion_name; ?>">
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
								<option value="0" <?php echo (Input::old('status') == '0' || $execlusion->status == 0) ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('status') != '') ? $error->first('status') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				</div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<input type="hidden" name="id" value="<?php echo $execlusion->id; ?>">
		<button type="submit" id="submit_form" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Execlusion'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/execlusions') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
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
	$(document).on("click", ".add-execlusion", function(){
		$("#first_add").remove();
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
		$(".execlusionlist:last a").attr("title", "<?php echo trans('messages.Add field'); ?>");	
		$(".execlusionlist:last a").removeClass("remove-list").addClass("add-execlusion").css("color", "#00BCD4");
		$(".execlusionlist:last a i").removeClass("fa-minus-circle").addClass("fa-plus-circle");
		$(".execlusionlist:last input").val('');
		$(".execlusionlist:last .save-execlusion").remove();
		$(".execlusionlist:last").append('<a href="javascript:void(0);" style="color:#000" class="save-execlusion" title="save"><i class="fa fa-floppy-o fa-fw"></i></a>');
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
		
		//Remove Execlusion List
		$(document).on("click", ".remove-list", function(e){
			var csrfToken = $('meta[name="csrf-token"]').attr("content");		
			var id = $(this).siblings("input[type='hidden']").val();
			var path = "<?php echo Url::to('admin/removeexeclusionlist'); ?>";
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
$(document).on("click", ".save-execlusion", function(){
	var parent = $(this).parent();
	var name_en = parent.find(".execlusionlist_name_en").val();
	var name_ar = parent.find(".execlusionlist_name_ar").val();
	var price = parent.find(".execlusionlist_price").val();
	var path = "<?php echo Url::to('admin/saveexeclusionlist'); ?>";
	if(name_en != '' && name_ar != '' && price != '')
	{
		$.ajax({
			beforeSend : function() {
	    		$("body").addClass("loading");
	    	},
			type: "GET",
			url: path,
			data: { execlusion_id: <?php echo $execlusion->id; ?>, name_en: name_en, name_ar: name_ar, price: price},
			success: function(data){
				parent.find('.save-execlusion').remove();
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
