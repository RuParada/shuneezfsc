@extends('header')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error') ?> @endif
<?php 
$products             = Cart::content();
$ingredient_subtotal  = 0; 
$ingredient_total     = 0;
$service_tax          = 0;
$total                = 0;
$subtotal = 0;
?>
<div class="container">
  <div class="checkout-pg">
  <div class="bred1">
	<img src="{!! URL::to('assets/images/back-btn.png') !!}" class="bred"><a href="{!! URL::to('listings') !!}"><?php echo trans('frontend.Back'); ?></a>
  </div>
	</div>
</div>
<div id="exTab2" class="container"> 
<ul class="nav nav-tabs custom_ckout">
      <li class="active" style="width:100%;text-align:center;">
        <a  href="#1" data-toggle="tab"><?php echo trans('frontend.Your Order'); ?></a>
      </li>
      <!--<li class="active" style="width:50%;text-align:center;"><a href="#2" data-toggle="tab"><?php echo trans('frontend.Secure Checkout'); ?></a>
      </li>-->
</ul>

      <div class="tab-content ">
        <div class="tab-pane active" id="1">

          <div class="container ck_2" style="margin-bottom:30px;">
         
        <div class="col-md-12 ck_1">
        <div class="checkout_box table-responsive">
       <table style="width:100%;">
  <tbody>
    <tr class="bor">
      <th><?php echo trans('frontend.S.No'); ?></th>
      <th><?php echo trans('frontend.Item Name'); ?></th>
      <th></th>
      <th></th>
      <th><?php echo trans('frontend.Price'); ?></th>
    </tr>
    <?php if(count($products)) { 
		$i = 1;
		foreach($products as $product) { 
			$ingredient_subtotal = 0; 
      $size = ($product->options->is_size) ? ' - '.$product->options->size : '';
      $item_price = ($product->options->is_size) ? $product->qty*($product->price + $product->options->size_price) : $product->qty*$product->price; 
	?>
    <tr class="bor">
      <td><p class="check_txt" style="margin-left:10px;"><?php echo $i++; ?></p></td>
      <td>
      <p class="check_txt"><?php echo $product->name.$size; ?></p>
      <div class="tes1">
      <button type="button" class="checkout-minusbtn" onclick="updateqty('<?php echo $product->rowid; ?>', 'remove');">-</button>  
              <input class="mn" type="text" name="quantity" id="quantity_<?php echo $product->rowid; ?>" value="<?php echo $product->qty; ?>" >
              <button type="button" class="checkout-plusbtn" onclick="updateqty('<?php echo $product->rowid; ?>', 'add');">+</button>
              <span class="btns yellow" style="display:none; cursor:pointer" id="update_cart<?php echo $product->rowid; ?>" onclick="update_cart('<?php echo $product->rowid; ?>')"><i class="fa fa-refresh"></i>Update</span>
              </td>
              
              </div>
              
      <td>
		  <?php 
		  if($product->options->is_ingredients) 
		  { 
  			for($i=0; $i<count($product->options->ingredientlist); $i++) 
  			{ 
  				//$ingredient_subtotal += $product->options->ingredient_price[$i];
  				echo $product->options->ingredientlist[$i].' : '.$default_currency.' '.$product->options->ingredient_price[$i].'<br>';
  			}
		  }
		  ?>
      <?php 
      if($product->options->is_execlusion) 
      {  
      ?>
        <h4>{!! trans('messages.Execlusions') !!}</h4>
      <?php
        for($i=0; $i<count($product->options->execlusions); $i++) 
        { 
          echo $product->options->execlusions[$i].'<br>';
        }
      }
      ?>
	  </td>
      <td></td>
      <td><p class="check_txt"><?php echo $default_currency.' '.$item_price; ?></p></td>
    </tr>
    <?php 
    $subtotal += $item_price;
		$ingredient_total += $product->options->ingredient_total; 
		} 
		$vat_tax = ((($subtotal + $ingredient_total) * $config_data['vat']) / 100); 
		$total = $subtotal + Session('orders.delivery_fee') + $ingredient_total + $vat_tax;
	?>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td style="color:#999;font-size:16px;"><?php echo trans('frontend.Sub Total'); ?></td>
      <td style="color:#999;font-size:16px;"><?php echo $default_currency.' '.($subtotal + $ingredient_total); ?></td>
      
    </tr>
    <tr id="deliveryfee" style="display: <?php echo (Session('orders.delivery_type') == 'd') ? '' : 'none'; ?>">
      <td></td>
      <td></td>
      <td></td>
      <td style="color:#999;font-size:16px;"><?php echo trans('frontend.Delivery Fee'); ?></td>
      <td style="color:#999;font-size:16px;"><?php echo $default_currency.' '.Session('orders.branch_delivery_fee'); ?></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td style="color:#999;font-size:16px;"><?php echo trans('frontend.Vat Tax'); ?></td>
      <td style="color:#999;font-size:16px;"><?php echo $default_currency.' '.$vat_tax; ?></td>
     
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td ><p class="check_txt1"><?php echo trans('frontend.Total'); ?></p></td>
      <td><p class="check_txt1" id="total"><?php echo $default_currency.' '.$total; ?></p></td>
      
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
</div>
{!! Form::open(array('url' => 'getdelivery_time', 'id' => 'deliveryform')) !!}
        <div class="order_in">
         <p class="check_txt"><?php echo trans('frontend.Order Information'); ?></p>
		<input type="radio" name="delivery_type" id="delivery" class="css-checkbox-radio" value="d" checked />
            <label for="delivery" class="css-label radGroup1"><?php echo trans('frontend.Delivery'); ?></label><br>
        <hr>
            <input type="radio" name="delivery_type" id="pickup" class="css-checkbox-radio" value="p" <?php echo (Session('orders.delivery_type') == 'p' || Input::old('delivery_type') == 'p') ? 'checked' : ''; ?>/>
            <label for="pickup"  class="css-label radGroup1"><?php echo trans('frontend.Pickup'); ?> </label><br>
            @if(Session::has('service_error'))<span class="error_msg"> <?php echo Session::get('service_error'); ?></span>@endif
		
	     <hr>
	     
         </div>
	   <input type="hidden" name="delivery" value="0">


	 <br>
     <div class="clr"></div>
<input type="hidden" name="subtotal" value="<?php echo $subtotal + $ingredient_total; ?>">
<input type="hidden" name="vat" value="<?php echo $vat_tax; ?>">    
<input type="hidden" name="total" value="<?php echo $total; ?>">
<div class="col-md-3 ckpadl ckpadr" style="padding-left:0px;"><button type="submit" class="ch-out-btn pad_top "><?php echo trans('frontend.Checkout'); ?></button></div>

</div>
{!! Form::close() !!}          
        </div>
        

  <!--<label><input type="checkbox" value="">I have read and accepted the Terms and Conditions</label>
</div>
<div class="checkbox">
  <label><input type="checkbox" value="">I Would like to subscribe for Newsletter</label>
</div>
<div class="checkbox">
  <label><input type="checkbox" value="">I Would like to receive promos from Shunez through</label>-->
</div>
</div>
<script>
	function update_cart(rowid)
	{
		var qty = $("#quantity_"+rowid).val();
		$.ajax({
		beforeSend : function() {
			$("body").addClass("loading");
		},
		type : "GET",
		dataType : "json",
		url  : "<?php echo URL::to('update_cart'); ?>",
		data : {'rowid' : rowid, 'qty' : qty},
		async: true,
		success: function(result) {
					$("body").removeClass("loading");
					window.location.reload();
				}
			});
	}
	
	function remove_product(rowid)
	{
		var url = "<?php echo URL::to(''); ?>"; 
		if(confirm('Are you sure you want remove this product?'))
		{
			window.location.href = url+'/remove_product/'+rowid;
		}
	}
	
	function updateqty(id, action)
	{
		var qty = $("#quantity_"+id).val();
		if(action == 'add')
		{
			var newqty = parseInt(qty) + 1;
			$("#quantity_"+id).val(newqty);
		}
		else
		{
			var newqty = parseInt(qty) - 1;
			newqty = (newqty < 1) ? 1 : newqty; 
			$("#quantity_"+id).val(newqty);
		}
		$("#update_cart"+id).css('display','block');
		
	}
	
	function getdelivery_time()
	{	if ($("#today_delivery").prop("checked")) {
			$("#selctdate").css('display', 'none');
        }
        else{ 
          $("#selctdate").css('display', 'block');
        }
        
    }
    $('input[name=delivery_type]').on('change', function()
    {
      if($('input[name=delivery_type]:checked').val() == 'p')
      {
        var total = <?php echo ($total - Session('orders.delivery_fee')); ?>;
        var currency = '<?php echo $default_currency; ?>';
        $("#total").html(currency+' '+total);
        $("#deliveryfee").hide();
      }
      else
      {
        var total = <?php echo ($total); ?>;
        var currency = '<?php echo $default_currency; ?>';
        $("#total").html(currency+' '+total);
        $("#deliveryfee").show();
      }
    });
    
</script>
@endsection
