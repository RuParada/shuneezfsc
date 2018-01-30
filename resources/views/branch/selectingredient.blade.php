<?php $current_language = (isset($_SESSION['language'])) ? $_SESSION['language'] : 'en'; ?>
{!! Form::open(array('url' => 'branch/additem', 'id' => 'ingredient_form')) !!}
<div class="modal-header">
   <button type="button" class="close2" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h2 class="modal-title" id="myModalLabel">Choose product choices... </h2>
 </div>
 <div class="modal-bdy1">
 <div class="col-md-12">
   <div class="col-md-3"><img src="{!! URL::to('assets/uploads/vendor_items/'.$vendor_item->image) !!}"></div>
   <div class="col-md-9">
	 <h4><?php echo $vendor_item->item; ?> : <?php echo getdefault_currency().' '.$vendor_item->price; ?></h4>
		<input type="hidden" name="item_id" value="<?php echo $vendor_item->id; ?>">
		 <span>Quantity<button type="button" onclick="updateqty('add');" class="plusbtn" style="margin-left:20px;">+</button>
		 <input type="text" id="qty" name="quantity" style="width:40px;height:27px;border-radius:1px;border:1px solid #999;" value = "1">
		 <button type="button"  onclick="updateqty('remove');" class="minusbtn">-</button></span>
  </div>
 </div>
 <div class="clr"></div>
 </div>
 	<div class="modal_bdy2">
 	<?php if(count($size_list))
 	{
 	?>
 	
 	<div class="col-md-12">
	 	<ul style="list-style:none;padding-left:0px;margin-top:30px;">
		   <?php foreach ($size_list as $size) { ?>
		   <li>
			 <p><input type="radio" class="regular-checkbox" name="size" value="<?php echo $size->size.'|'.$size->id.'|'.$size->price; ?>">&nbsp;<span><?php echo $size->size; ?></span> - <span class="price-tg"><?php echo getdefault_currency().' '.$size->price; ?></span> </p>
		   </li>
		   <?php } ?>
		   <span id="size_error" class="error_msg"></span>
		 </ul>
	 </div>
	 <?php } ?>
	 <?php if(count($ingredients) > 0)
	 {
		 foreach($ingredients as $ingredient) {
	?>
   <div class="col-md-12 ingredients" data-required=<?php echo $ingredient['ingredients']->required; ?> data-minimum =<?php echo $ingredient['ingredients']->minimum; ?> data-maximum= <?php echo $ingredient['ingredients']->maximum; ?>>
   	
   	<h3><?php $ingredient['ingredients']->ingredient_name; ?><span style="float:right;font-size:14px;">Minimum <?php echo $ingredient['ingredients']->minimum; ?> and Maximum <?php echo $ingredient['ingredients']->maximum; ?></span></h3>
	<ul style="list-style:none;padding-left:0px;margin-top:30px;">
	   <?php foreach($ingredient['ingredientlists'] as $ingredientlist) { ?>
	   <li>
		 <p><input type="checkbox" class="regular-checkbox" name="ingredient_list[]" value="<?php echo $ingredientlist->ingredientlist_name.'|'.$ingredientlist->ingredientlist_id.'|'.$ingredientlist->price; ?>">&nbsp;<span><?php echo $ingredientlist->ingredientlist_name; ?></span> - <span class="price-tg"><?php echo getdefault_currency().' '.$ingredientlist->price; ?></span> </p>
	   </li>
	   <?php } ?>
	 </ul>
	<span class="error_msg"></span>	 
   </div>
   <?php  } } ?>

   <?php if(count($execlusions))
 	{
 	?>
 	<div class="col-md-12">
 		<h3>Execlusion</h3>
	 	<ul style="list-style:none;padding-left:0px;margin-top:30px;">
		   <?php foreach ($execlusions as $execlusion) { ?>
		   <li>
			 <p><input type="checkbox" class="regular-checkbox" name="execlusion_id[]" value="<?php echo $execlusion->execlusion.'|'.$execlusion->id; ?>">&nbsp;<span><?php echo $execlusion->execlusion; ?></span></p>
		   </li>
		   <?php } ?>
		 </ul>
	 </div>
	 <?php } ?>
 </div>
 <div class="clr"></div>
<div class="modal-footer forgot_btn">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type="submit" class="btn btn-primary">Submit</button>	
</div>
<div align='center' id='error_msg' style='color:red'>
</div>
{!! Form::close() !!}

<style>

div#SelectIngredient {
width: 50%;
background: white;
max-height: 500px;
margin: 7% auto;
}
</style>

<script>
function updateqty(action)
{
	var qty = $("#qty").val();
	if(action == 'add')
	{
		var newqty = parseInt(qty) + 1;
		$("#qty").val(newqty);
	}
	else
	{
		var newqty = parseInt(qty) - 1;
		newqty = (newqty < 1) ? 1 : newqty; 
		$("#qty").val(newqty);
	}
	
}

$(function(){

$('#ingredient_form').on('submit',function(e)
{
    $('span').removeClass('has_error');
		var size = $('input[name=size]:checked').length;
		if(size == 0)
		{
			 $("#size_error").addClass('has_error').html('Please select a size');
		}
	    $.ajaxSetup({
	        header:$('meta[name="_token"]').attr('content')
	    })
       	e.preventDefault(e);
        $(".ingredients").each(function()
        {
          var totalitem   = $(this).find('input[name="ingredient_list[]"]:checked').length;
          var minimum     = $(this).attr('data-minimum');
          var maximum     = $(this).attr('data-maximum');
          if($(this).attr('data-required') == "1")
          { 
            if(totalitem == 0)
            {
              $(this).find('span.error_msg').addClass("has_error");
              $(this).find('span.error_msg').text("Select atleast " + minimum + " ingredient");
            }
          }
          if(totalitem > 0)
          {
            if(totalitem > maximum && maximum != '0')
            {
              $(this).find('span.error_msg').addClass("has_error");
              $(this).find('span.error_msg').text("Cannot be select more than " + maximum);
            }
            if(totalitem < minimum && minimum != '0')
            {
              $(this).find('span.error_msg').addClass("has_error");
              $(this).find('span.error_msg').text("Please select atleast " + minimum);
            }
          }
        })
        if($(".has_error").length) {       
          return false;
        }
        else
        {
  	 		  $(".has_error").html('');
	        $.ajax({
		        type:"GET",
		        url:'/branch/additem',
		        data:$(this).serialize(),
		        dataType: 'json',
		        success: function(data){
					//$("#cartbox").html(data.items);
					$('#SelectIngredient').modal('hide');
					$("#cartdetails").load(location.href + " #cartbox");
		        },
		        error: function(data){
		        }
	    	})
	    }
	});	 
});


</script>
