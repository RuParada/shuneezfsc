@extends('header')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="container">
  <div class="checkout-pg">
  <div class="bred1">
	<img src="{!! URL::to('assets/images/back-btn.png') !!}" class="bred"><a href="{!! URL::to('cart') !!}"><?php echo trans('frontend.Back'); ?></a>
  </div>
	</div>
</div>
<div id="exTab2" class="container"> 
<ul class="nav nav-tabs custom_ckout">
      <!-- <li >
        <a  href="#1" data-toggle="tab"><?php echo trans('frontend.Your Order'); ?></a>
      </li> -->
      <li class="active" style="width:100%;text-align:center;"><a href="#2" data-toggle="tab"><?php echo trans('frontend.Secure Checkout'); ?></a>
      </li>
</ul>
{!! Form::open(array('url' => 'payment', 'id' => 'payment_form')) !!}
<div class="tab-pane" id="2">
          <div class="container comm1">
            <?php if(isset(Auth::user()->id)) { ?>
            <p class="check_txt"><?php echo trans('frontend.Contact Details'); ?></p>
            <div class="col-md-4 checkout_box1">
              <div class="col-md-5">
                  <p><?php echo trans('frontend.Full Name'); ?></p>
                </div>
                <div class="col-md-7">
                  <p><?php echo Auth::user()->first_name.' '.Auth::user()->last_name; ?></p>
                </div>
                <div class="clr"></div>
                
                <div class="col-md-5">
                  <p><?php echo trans('frontend.Email'); ?></p>
                </div>
                <div class="col-md-7">
                  <p><?php echo Auth::user()->email; ?></p>
                </div>
                <div class="clr"></div>

                <div class="col-md-5">
                  <p><?php echo trans('frontend.Mobile'); ?></p>
                </div>
                <div class="col-md-7">
                  <p><?php echo Auth::user()->mobile; ?></p>
                </div>
                <input type="hidden" name="user_id" value="<?php echo Auth::user()->id; ?>">
                <div class="clr"></div>
            </div>
            <div class="clr"></div>
            <?php } else { ?>
            <div class="col-md-4 pad1">
              <a href="#" data-toggle="modal" data-target="#login-modal"><button type="button" class="chk_login_btn"><?php echo trans('frontend.Login'); ?></button></a>
              <!--<button type="button" class="chk_login_btn">Signup</button>-->
            </div>
            <div class="clr"></div>
			<?php } ?>
			
			<?php if(!isset(Auth::user()->id)) { ?>
            <!--Before login-->
            <p style="font-size:16px;color:#333;margin-top:20px;margin-bottom:20px;"><?php echo trans('frontend.You can create an account after placing your order'); ?></p>
            <div class="col-md-6 pad4">
              <label><?php echo trans('frontend.First Name'); ?><span class="req">*</span></label>
              <input type="text" class="ck_out_field" name="first_name" value="<?php echo Input::old('first_name'); ?>">
              @if(Session::has('error'))<span class="error_msg"> <?php echo ($error->first('first_name') != '') ? $error->first('first_name') : ''; ?></span>@endif
            </div>
            <div class="col-md-6 pad4">
              <label><?php echo trans('frontend.Last Name'); ?><span class="req">*</span></label>
              <input type="text" class="ck_out_field" name="last_name" value="<?php echo Input::old('last_name'); ?>">
			        @if(Session::has('error'))<span class="error_msg"> <?php echo ($error->first('last_name') != '') ? $error->first('last_name') : ''; ?></span>@endif
            </div>
            <div class="clr_1"></div>
            <div class="col-md-6 pad4">
              <label style="width:100%;float:left;"><?php echo trans('frontend.Mobile'); ?><span class="req">*</span></label>
             
             <input type="text" value="+966" readonly  placeholder="+966" style=" text-align:center;   width: 10%;float: left;display: inline-block;height: 50px;border: 1px solid #ddd;border-right: none;">
              <input type="text" style="width:90%;float:left;display:inline-block;" class="ck_out_field" name="mobile" value="<?php echo Input::old('mobile'); ?>" placeholder="<?php echo trans('frontend.Enter mobile number without country code'); ?>">
              @if(Session::has('error'))<span class="error_msg"> <?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></span>@endif
            </div>
            <div class="col-md-6 pad4">
              <label><?php echo trans('frontend.Email'); ?><span class="req">*</span></label>
              <input type="text" class="ck_out_field" name="email" value="<?php echo Input::old('email'); ?>">
              @if(Session::has('error'))<span class="error_msg"> <?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></span>@endif
            </div>
            <div class="clr_1"></div>

            <div class="col-md-12 pad4">
              <div style='overflow:hidden;height:440px;width:100%;'>
                    <div id='map-canvas' style='height:440px;width:100%;'></div>
               </div>
               
                <div class="col-md-12 pad0">
                  <textarea name="delivery_address" class="ck_tx_are" rows="5" id="address" ><?php echo Input::old('delivery_address'); ?></textarea>
                  @if(Session::has('error'))<span class="error_msg"> <?php echo ($error->first('delivery_address') != '') ? $error->first('delivery_address') : ''; ?></span>@endif
                </div>

            </div>
            <div class="clr_1"></div>
            <hr>
			<?php } else { ?>
			<div class="delivery_add">
            <div class="col-md-9 comm">
				<?php if(Session('orders.delivery_type') == 'd') { ?>
					<?php if(count($default_address) > 0) { ?>
					  <p class="check_txt">Delivery Address</p>
					  <address id="delivery_address">
						<?php
						 $default = explode(',', $default_address->address);
						 for($i=0; $i<count($default); $i++)
						 {
							 $comma = ($i == 0) ? '<br>' : ',<br>';
							echo $comma.$default[$i]; 
						 }
						?>
						<input type="hidden" name="address_id" value="<?php echo $default_address->id; ?>">
					  </address>
					<?php } ?>
				<?php } else { ?>
					<p class="check_txt"><?php echo trans('frontend.Pickup Address'); ?></p>
						<address id="delivery_address">
							<?php echo $branch->branch; ?><br>
							<?php echo $branch->street; ?>,<br>
							<?php echo $branch->city; ?> - <?php echo $branch->zipcode; ?>,<br>
							<?php echo $branch->country; ?>
					    </address>
			    <?php } ?>
              </div>
              <!--<div class="col-md-3" style="padding-left:0px;">
                <button type="button" class="changeaddress_btn"  data-toggle="modal" data-target="#changeaddress-modal">Change Address</button>
              </div>-->
            </div>
            <div class="clr"></div>
            <!--Newly added-->
<?php if(Session('orders.delivery_type') == 'd') { ?>
<!-- Accordion Section-->
          <div class="accordion" id="accordion2">
          <div class="accordion-group" id="oldaddress">
          <div class="accordion-heading">
            <input type="radio" name="newaddress" value="0" id="saveaddress" class="css-checkbox-radio accordion-toggle" checked="checked" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne" />
            <label for="saveaddress" class="css-label radGroup1"><?php echo trans('frontend.Saved Address'); ?></label>
          </div>
          <div id="collapseOne" class="accordion-body collapse">
          <div class="accordion-inner">
			  <?php 
			  if(count($address_books)) { 
			  foreach($address_books as $address_book) 
			  {
			  ?>
              <div class="col-md-4">
                <address class="sav_add">
                <?php
                $address = explode(',', $address_book->address);
                 for($i=0; $i<count($address); $i++)
                 {
					 $comma = ($i == 0) ? '<br>' : ',<br>';
					echo $comma.$address[$i]; 
				 }
				 ?>
				 <br>
                <input title="Make as default" onclick="changedefault_address(<?php echo $address_book->id; ?>);" type="radio" name="default_address" value="1" <?php echo ($address_book->default_address == 1) ? 'checked' : ''; ?>>
              </address>
              </div>
              <?php } } ?>
              <div class="clr"></div>

          </div>
          </div>
          </div>
          <div class="accordion-group" id="showmap">
          <div class="accordion-heading">
          

          <input type="radio" name="newaddress" value="1" id="newaddress" class="css-checkbox-radio accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo" />
            <label for="newaddress" class="css-label radGroup1"><?php echo trans('frontend.Add New Address'); ?></label>

          </div>
          <div id="collapseTwo" class="accordion-body collapse">
          <div class="accordion-inner">
           <div class="">
                <div style='overflow:hidden;height:440px;width:100%;'>
                    <div id='gmap_canvas' style='height:440px;width:100%;'></div>
                </div>
             
                <textarea class="ck_tx_are" name="delivery_address" rows="5" id="address"><?php echo Input::old('delivery_address'); ?></textarea>
				@if(Session::has('error'))<span class="error_msg"> <?php echo ($error->first('delivery_address') != '') ? $error->first('delivery_address') : ''; ?></span>@endif
        
           </div>
           
			<button type="button" onclick="save_newaddress();" class="sub_btn"><?php echo trans('frontend.Save Address'); ?></button>
            
          </div>
          </div>
          </div>
          </div>
			<?php } } ?>

            <div class="col-md-12 pad1">
            
            </div>
            <hr>
            <p class="check_txt"><?php echo trans('frontend.Choose Payment'); ?></p>

 <table>
    <tr>
		<!--<td>
            <input type="radio" name="payment_type" id="payfort" value="1" class="css-checkbox-radio" checked/>
            <label for="payfort" class="css-label radGroup1">< ?php echo trans('frontend.Payfort'); ?></label>
        </td>-->
         <td>
            <input type="radio" name="payment_type" id="cod" value="0" class="css-checkbox-radio" checked />
            <label for="cod" class="css-label radGroup1"><?php echo trans('frontend.Cash on delivery'); ?></label>
        </td>
    </tr>
</table>
	<div class="checkbox" style="margin-bottom:0px;margin-top:10px;">
	    <input type="checkbox" name="terms" value="1" id="terms" class="css-checkbox" <?php echo (Input::old('terms') == 1) ? 'checked' : ''; ?>/>
	    <label for="terms" class="css-label-chekup"><?php echo trans('frontend.I have read and accepted the Terms and Conditions'); ?></label>
	    <span id="term_error" class="error_msg"></span>
    </div>
    <br>
    <div class="checkbox" style="margin-bottom:0px;margin-top:0px;">
	    <input type="checkbox" name="subscribe" id="subscribe" value="1" class="css-checkbox" <?php echo (Input::old('subscribe') == 1) ? 'checked' : ''; ?> />
	    <label for="subscribe" class="css-label-chekup"><?php echo trans('frontend.I Would like to subscribe for Newsletter'); ?></label>
    </div>
    <br>
    <div class="clr"></div>
	<div class="col-md-3 ckpadr ckpadl" style="margin-top:15px;margin-bottom:30px;">
                <input type="hidden" name="latitude" id="latitude" value="<?php echo (Input::old('latitude') != '') ? Input::old('latitude') : Session('orders.latitude'); ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?php echo (Input::old('longitude') != '') ? Input::old('longitude') : Session('orders.longitude'); ?>">
                <button type="button" class="ordernow_btn" onclick="validate_form();"><?php echo trans('frontend.Order Now'); ?></button>
              </div>
            </div>
          </div>
          </div>
        </div>
       
      </div>
      </div>
{!! Form::close() !!}


<div class="modal_load"></div>	
<script>
function validate_form()
{
	if(document.getElementById('terms').checked) {
      $( "#payment_form" ).submit();
	} else {
		$("#term_error").text('Please accept our terms & condition');
	}
}
function changedefault_address(id)
{
	$.ajax({
		beforeSend: function () {
			$("body").addClass("loading");
		},
		type: "GET",
		url: "<?php echo URL::to('changedefault_address'); ?>",
		data: {'address_id': id},
		async: true,
		success: function (result) {
			 $("body").removeClass("loading");
			 $("#delivery_address").html(result);
		}
	}); 
}

function save_newaddress()
{
	var address = $("#address").val();
	var latitude = $("#latitude").val();
	var longitude = $("#longitude").val();
	$.ajax({
		beforeSend: function () {
			$("body").addClass("loading");
		},
		type: "GET",
		url: "<?php echo URL::to('saveaddress'); ?>",
		data: {'address': address, 'longitude': longitude, 'latitude': latitude},
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
navigator.geolocation.getCurrentPosition(GetAddress);
function GetAddress(position) {
	  $("#latitude").val(position.coords.latitude);
    $("#longitude").val(position.coords.longitude);
    var lat = parseFloat(position.coords.latitude);
    var lng = parseFloat(position.coords.longitude);
    var latlng = new google.maps.LatLng(lat, lng);
    $("#latitude").val(lat);
    $("#longitude").val(lng);
    var geocoder = geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'latLng': latlng }, function (results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
            $("#address").val(results[1].formatted_address);
        }
    }
    });

var latitude = $("#latitude").val();
var longitude = $("#longitude").val();
$('#map-canvas').locationpicker({
	location: {
	latitude: latitude, longitude: longitude}
	,
	radius: 300,
	inputBinding: {
	latitudeInput: $('#latitude'),
	longitudeInput: $('#longitude'),  
	locationNameInput: $('#address')       
	}
});
}

function updatedelivery(addressComponents) {
    $('#area_name').val(addressComponents.district);
    
}

function showAddressMap()
{
	var latitude = $("#latitude").val();
	var longitude = $("#longitude").val();
	
	$('#gmap_canvas').locationpicker({
     location: {latitude: latitude, longitude: longitude},
    radius: 300,
    inputBinding: {
	latitudeInput: $('#latitude'),
	longitudeInput: $('#longitude'),
	locationNameInput: $('#address')         
	}
});

}

$('#showmap').bind('click', function() {
	showAddressMap();
	$("#collapseOne").css('height','0');
	$("#collapseOne").removeClass('in');
	$("#collapseOne").attr('aria-expanded', 'false');
	$("#newaddress").attr('checked', 'checked');
});

$('#oldaddress').bind('click', function() {
	$("#collapseTwo").css('height','0');
	$("#collapseTwo").removeClass('in');
	$("#collapseTwo").attr('aria-expanded', 'false');
	$("#newaddress").removeAttr('checked');
	$("input #saveaddress").attr('checked', 'checked');
	
});

$('#branch_form input[type=radio]').on('change', function() {
   var checked = $('input[name=deliveryfee_type]:checked', '#branch_form').val();
   if(checked == 'distance')
   {
	   $("#distance").css('display', 'block');
	   $("#area").css('display', 'none');
   }
   else if(checked == 'area')
   {
	   $("#distance").css('display', 'none');
	   $("#area").css('display', 'block');
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
