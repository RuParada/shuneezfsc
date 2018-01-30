@extends('header')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error') ?> @endif
<?php $seg = Request::segment(2);
$order_key = Request::segment(3);
?>
<div class="container">
<?php if($order->is_address_change == 0 && $order->order_status != 'a' || 1 == 1) { ?>
           
          		<div class="new_agnt">
          		 <select name="addresstype_id" class="ckout-sel-new">
                	<option value="">Select Address Type</option>
                	<?php
                	if(count($addresstype))
                	{
                		foreach ($addresstype as $type) {
                	?>
                	<option value="<?php echo $type->id; ?>"><?php echo $type->addresstype; ?></option>
                	<?php } } ?>
                </select>
                </div>
                <div style='overflow:hidden;height:440px;width:100%;'>
                    <div id='map-canvas' style='height:440px;width:100%;'></div>
                </div>
               
                <textarea class="ck_tx_are" name="delivery_address" rows="5" id="address"><?php echo $address->address; ?></textarea>
				@if(Session::has('error'))<span class="error_msg"> <?php echo ($error->first('delivery_address') != '') ? $error->first('delivery_address') : ''; ?></span>@endif
        
           
           
			   <button type="button" onclick="update_newaddress();" class="sub_btn"><?php echo trans('frontend.Update Address'); ?></button>
            
         
	
                <input type="hidden" name="latitude" id="latitude" value="<?php echo $address->latitude; ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?php echo $address->longitude; ?>">
               

<?php } else { ?>
<h3 style="text-align:center; color:green; margin:100px 0;"><?php echo ($order->order_status == 'a') ? trans('frontend.Your order has been out for delivery') : trans('frontend.Delivery address has been changed'); ?></h3>
<?php } ?>
      </div>
<div class="clr"></div>
<div class="modal_load"></div>	
<script>

function update_newaddress()
{
	var address = $("#address").val();
	var address_key = '<?php echo $seg; ?>';
	var order_key = '<?php echo $order_key; ?>';
	var latitude = $("#latitude").val();
	var longitude = $("#longitude").val();
	$.ajax({
		beforeSend: function () {
			$("body").addClass("loading");
		},
		type: "GET",
		url: "<?php echo URL::to('update_orderaddress'); ?>",
		data: {'address': address, 'address_key': address_key, 'longitude': longitude, 'latitude': latitude, 'order_key' : order_key},
		async: true,
		success: function (result) {
			 //$("body").removeClass("loading");
			 window.location.reload();
		}
	}); 
}
</script>
<script>
$(document).ready(function()
{
var latitude = $("#latitude").val();
var longitude = $("#longitude").val();
$('#map-canvas').locationpicker({
    location: {latitude: latitude, longitude: longitude},
    radius: 300,
    inputBinding: {
	latitudeInput: $('#latitude'),
	longitudeInput: $('#longitude'),
	locationNameInput: $('#address')       
	}
});



});

</script>
<style>
	#gmap_canvas img {
		max-width: none!important;
		background: none!important
	}
</style>
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
