@extends('header')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error') ?> @endif
<?php 
$products = Cart::content();
$ingredient_subtotal = 0; 
$ingredient_total     = 0;
$total                = 0;
$vat_tax              = 0;
$subtotal = 0;
?>
<div class="container">
  <div class="address_sec">
	  {!! Form::open(array('url' => 'getitems', 'id' => 'listing_form')) !!}
		<div class="col-md-3 "><button onclick="showaddress()": type="button" class="deliver-btn change-areabtn"><?php echo trans('messages.Back'); ?></button></div>
			<div class="col-md-5">
				<!--<div style="display:none;" id="address_box">
					<input type="text" class="form-control new_cc1" id="delivery_area" name="delivery_area" value="" placeholder="Enter your Area / locality" autocomplete="off"><span class="input-group-btn">
					<input type="hidden" name="delivery_type" id="delivery_type" value="<?php echo Session('orders.delivery_type'); ?>">
					<button class="search-btn" type="submit">Search</button>
					@if(Session::has('error'))<span class="error_msg"> <?php echo $error; ?></span>@endif
				</div>-->
			</div>

		{!! Form::close() !!}
		<?php if(count($branch) == 0) { ?>
		<div class="clr"></div>	
		<div class="e_box">
			<?php $type = (Session('orders.delivery_type') == 'd') ? 'delivery' : 'pickup'; ?>
				<p style="font-size:36px;color:#662d91;text-align:center;font-weight:bold;"><?php echo trans('frontend.Sorry, this restaurants is not providing '.$type); ?> </p>
				<p style="font-size:36px;color:#39b54a;text-align:center;"> <?php echo trans('frontend.Please try a different service'); ?> </p>
		</div>
	<?php } else { ?>
	<div class="col-md-4">
	<div class="address_block">
	  <p style="font-size:18px;color:#000;"><?php echo trans('frontend.Branch Location'); ?></p>
	  	<address style="color:#3e3e3e;">
		<?php echo $branch->branch; ?><br>
		<?php echo $branch->street; ?>,<br>
		<?php echo $branch->city; ?> - <?php echo $branch->zipcode; ?>,<br>
		<?php echo $branch->country; ?>
	  </address>
	  
	</div>
	</div>
	
	<div class="clr"></div>	

  </div>
</div>
 <!-- Tab Section-->
  <div class="container" style="margin-bottom:30px;margin-top:30px;">
  
	<div class="col-md-9">
	<!-- Nav tabs --><div class="card">
	<?php if(count($items) > 0) {  $i = 0; //print_r($items); exit; ?>
	<ul class="nav nav-tabs" role="tablist">
		<?php foreach($items as $item) { 
			if(count($item['items'])) { ?>
		<li role="presentation" class="<?php echo ($i == 0) ? 'active' : ''; ?>"><a href="#cat<?php echo $item['category']->id; ?>" aria-controls="cat<?php echo $item['category']->id; ?>" role="tab" data-toggle="tab"><?php echo $item['category']->category; ?></a></li>
		<?php } $i++; } ?>
	</ul>
	<?php } ?>

	<!-- Tab panes -->
	<div class="tab-content">
		<?php
			if(count($items) > 0) {
				$j = 0; //echo '<pre>'; print_r($items); exit;
				foreach($items as $item) { 
		?>
		<div role="tabpanel" class="tab-pane <?php echo ($j == 0) ? 'active' : ''; ?>" id="cat<?php echo $item['category']->id; ?>">
		  <?php foreach($item['items'] as $row) { ?> 
		  <div class="col-md-4 col-xs-12 for_image_margin_top">
			<div id="effect-5">
			<div class="img">
			<div class="img_fit">
			  <img src="{!! URL::to('assets/uploads/vendor_items/'.$row->image) !!}" style="float:left; width:100%;height:100%;" alt="">
			  </div>
			  <div class="overlay">
				  <?php if($row->is_ingredients == 1 || $row->is_execlusion == 1 || $row->is_size == 1) { ?>
					<a href="#" onclick="selectingredient(<?php echo $row->id; ?>);" data-toggle="modal" data-target="#SelectIngredient" class="expand">+</a>
				  <?php } else { ?>
					<a href="#cartbox" onclick="addtocart(<?php echo $row->id; ?>);" class="expand">+</a> 
				  <?php } ?> 
				  <a class="close-overlay hidden">x</a>
			  </div>
			</div>  
			</div>  
			<div class="txt">
				<?php if($row->is_ingredients == 1) { ?>
				<a href="#" onclick="selectingredient(<?php echo $row->id; ?>);" data-toggle="modal" data-target="#SelectIngredient"><?php echo $row->item_name; ?></a>
			   <?php } else { ?>
				  <a href="#cartbox" onclick="addtocart(<?php echo $row->id; ?>);" ><?php echo $row->item_name; ?></a> 
			   <?php } ?> 
				<span class="price"><?php echo $default_currency.' '.$row->price; ?></span></div>
          </div>
            <?php } ?>
		</div>
		<?php $j++; } } ?>
		</div>
    </div>
    </div>
		
		<div class="col-md-3 col-xs-12" style="margin-top:55px;">
            <div class="title">
              <h3 style="color:#fff;text-align:center;font-size:24px;"><?php echo trans('frontend.Your Cart'); ?></h3>
            </div>
            <div class="ur-cart" id="cart_details">
            <div id="cartbox">
				<?php if(count($products)) { 
					foreach($products as $product) { 
						$ingredient_subtotal = 0; 
						$size = ($product->options->is_size) ? ' - '.$product->options->size : '';
						$item_price = ($product->options->is_size) ? $product->qty*($product->price + $product->options->size_price) : $product->qty*$product->price; 
				?>
				
				<div class="aa">
					<img class="u_cart" style="float:left; width:100%;height:100%;" src="{!! URL::to('assets/uploads/vendor_items/'.$product['options']->image) !!}">
					</div>
				   <div class="bb"> 
				  <span class="ur-cart-txt" style="position:relative;bottom:10px;"><?php echo $product->name.$size; ?></span><br>
				  <button type="button" class="plusbtn_n1" onclick="updateqty('<?php echo $product->rowid; ?>', 'add');">+</button>
				  <input type="text" min="1" style="width:36px;height:24px;border-radius:1px;border:1px solid #999;text-align:center;" name="quantity" id="quantity_<?php echo $product->rowid; ?>" value="<?php echo $product->qty; ?>">
				  <button type="button" class="minusbtn_n1" onclick="updateqty('<?php echo $product->rowid; ?>', 'remove');">-</button>
				  <button id="delete" class="del-btn-rt del-btn-lt" onclick="remove_product('<?php echo $product->rowid; ?>');"></button>
				  <span class="btns yellow" style="display:none; cursor:pointer" id="update_cart<?php echo $product->rowid; ?>" onclick="update_cart('<?php echo $product->rowid; ?>')"><i class="fa fa-refresh"></i>Update</span>
				  </div>
				  <div class="clr"></div>
				  <div class="cart-price">
					<p class="ur-cart-txt" style="margin-top:10px;"><?php echo $product->name.$size; ?><span class="price-agn-rgt"><?php echo $default_currency.' '.$item_price; ?></span></p>
					<?php if($product->options->is_ingredients) { ?>
					<div class="ingredients-price">
						<?php 
						for($i=0; $i<count($product->options->ingredientlist); $i++) { 
							//$ingredient_subtotal += $product->options->ingredient_price[$i];
						?>
						<p class="ingredients-txt"><?php echo $product->options->ingredientlist[$i]; ?><span class="price-agn-rgt"><?php echo $default_currency.' '.$product->options->ingredient_price[$i]; ?></span></p>
						<?php } ?>
					</div>
					<?php } ?>
					<?php if($product->options->is_execlusion) { ?>
						<p class="ur-cart-txt" style="margin-top:10px;">{!! trans('messages.Execlusions') !!}</p>
						<div class="ingredients-price">
							<?php 
							for($i=0; $i<count($product->options->execlusions); $i++) { 
							?>
								<p class="ingredients-txt"><?php echo $product->options->execlusions[$i]; ?></p>
							<?php } ?>
						</div>
					<?php } ?>
				 </div>
				<?php 
					$subtotal += $item_price;
					$ingredient_total += $product->options->ingredient_total; 
				} 
				?>
				<?php
				    $vat_tax = ((($subtotal + $ingredient_total) * $config_data['vat']) / 100); 
					$total = $subtotal + Session('orders.delivery_fee') + $ingredient_total + $vat_tax;
				?>
				  <div class="tot-price">
					<p class="total-txt"><?php echo trans('frontend.Sub Total'); ?>
						<span class="price-agn-rgt"><?php echo $default_currency.' '.($subtotal + $ingredient_total); ?></span>
					</p>
					<?php if(Session('orders.delivery_type') == 'd') { ?>
					<p class="total-txt"><?php echo trans('frontend.Delivery Fee'); ?>
						<span class="price-agn-rgt"><?php echo $default_currency.' '.Session('orders.delivery_fee'); ?></span>
					</p>
					<?php } ?>
					<p class="total-txt"><?php echo trans('frontend.Vat Tax'); ?>
						<span class="price-agn-rgt"><?php echo $default_currency.' '.$vat_tax; ?></span>
					</p>
				  </div>
				  <div class="tot-price1">
					<p class="total-txt1"><?php echo trans('frontend.Total'); ?>
						<span class="price-agn-rgt"><?php echo $default_currency.' '.$total; ?></span>
					</p>
				  </div>
				
				<?php } ?>
				</div>
				</div>
				<div id="checkout_btn">
					<?php if(count($products)) { ?>
					<div class="col-md-12 agn-btn" style="margin-top:20px;margin-bottom:20px;">
						<button id="deliver-btn" type="button" onclick="location.href = '<?php echo URL::to("cart"); ?>';" class="deliver-btn"><?php echo trans('frontend.Checkout'); ?></button>
					</div>
					<?php } ?>
				</div>
            </div>
 <?php } ?>

</div>
<!--Chicken Gravy Modal-->
<div class="modal fade" id="SelectIngredient" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
      
    </div>
  </div>
</div>
</div>
<!--Chicken Gravy Modal Ends-->
<div class="modal_load"></div>	

<style>
  .modal-content{width:876px;border-radius:0px;}
  .modal-bdy1 {padding:15px;border-bottom:1px solid #ccc;}
  .modal_bdy2{padding:15px;}
  .price-tg{color:#3e3e3e;font-size:24px;font-weight:600;}
  .price-tg1{color:#3e3e3e;font-size:16px;}
  .ptg {top: 0.0em!important;    position: relative;font-size: 100%!important;line-height: 0;vertical-align: baseline;}
  .modal-foot1 {
    padding: 15px;
    
    border-top: 1px solid #e5e5e5;
}
.pce{margin-top:20px;}
</style>
<script>
/*	
window.onload=function(){
var place;
var autocomplete = new google.maps.places.Autocomplete(delivery_area);

google.maps.event.addListener(autocomplete, 'place_changed', function () {
        place = autocomplete.getPlace();
        console.log(place);
});
}*/
    $(document).ready(function(){
        if (Modernizr.touch) {
            // show the close overlay button
            $(".close-overlay").removeClass("hidden");
            // handle the adding of hover class when clicked
            $(".img").click(function(e){
                if (!$(this).hasClass("hover")) {
                    $(this).addClass("hover");
                }
            });
            // handle the closing of the overlay
            $(".close-overlay").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                if ($(this).closest(".img").hasClass("hover")) {
                    $(this).closest(".img").removeClass("hover");
                }
            });
        } else {
            // handle the mouseenter functionality
            $(".img").mouseenter(function(){
                $(this).addClass("hover");
            })
            // handle the mouseleave functionality
            .mouseleave(function(){
                $(this).removeClass("hover");
            });
        }
    });
    
    function selectingredient(id)
    {
           $.ajax({
                beforeSend: function () {
                    $("body").addClass("loading");
                },
                type: "GET",
                url: "<?php echo URL::to('selectingredient'); ?>",
                data: {'id': id},
                async: true,
                success: function (result) {
					 $("body").removeClass("loading");
					 $('#SelectIngredient').html(result).modal('show');
                }
            }); 
    }
    
    function addtocart(id)
	{
		$.ajax({
		beforeSend : function() {
			$("body").addClass("loading");
		},
		type : "GET",
		dataType : "json",
		url  : "<?php echo URL::to('addtocart'); ?>",
		data : {'id' : id, 'quantity' : 1},
		async: true,
		success: function(result) {
					$("#cart_details").load(location.href + " #cartbox");
					$("#checkout_btn").load(location.href + " #deliver-btn");
					$("body").removeClass("loading");
				}
			});
	}
	
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
					$("#cart_details").load(location.href + " #cartbox");
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
	
	function showaddress()
	{
		var url = '<?php echo URL::to('/'); ?>';
		window.location.href = url;
		//$("#address_box").css('display', 'block');
	}
</script>
<script>
$(document).ready(function()
{

	function updateControls(addressComponents) {
    $('#address').val(addressComponents.address);
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
