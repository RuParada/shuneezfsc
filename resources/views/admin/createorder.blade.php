@extends('adminheader')
@section('content')
<?php $seg = Request::segment(3); ?>
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif 
<?php 
$products             = Cart::content();
$ingredient_subtotal  = 0; 
$ingredient_total     = 0;
$total                = 0;
$vat_tax              = 0;
$subtotal = 0;
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      <?php echo trans('messages.Create Order'); ?>
    </h1>
    @if ( Session::has('dook_error') ) <p class="error_msg"> {!! Session('dook_error') !!} </p> @endif
    @if(Session::has('success'))
    <p class="success_msg">
      <?php echo Session::get('success'); ?>
    </p>@endif 
    @if(Session::has('item_error'))
    <p class="error_msg"><?php echo Session('item_error'); ?></p>
   @endif 
  </section>
<!-- Main content -->
<section class="content">
  {!! Form::open(array('url' => 'admin/createorder')) !!}      
  <div class="row form-horizontal">
    <div class="col-md-6">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            <?php echo trans('messages.Customer Details'); ?>
          </h3>
        </div>
        <!--box-header-->		
        <div class="box-body">
          <div class="form-group">
            <label for="customer" class="col-sm-4 control-label">
              <?php echo trans('messages.Select Order Type'); ?>
              <span class="req">*</span>
            </label>
            <input type="radio" name="order_type" value="d" checked>
            <?php echo trans('messages.Delivery'); ?>
            <input type="radio" name="order_type" value="p" <?php echo (Input::old('order_type') == 'p') ? 'checked' : ''; ?>>
            <?php echo trans('messages.Pickup'); ?>
            <span class="error_msg" id="delivery_error"></span>
          </div>
          <div class="form-group full_selectLists">
            <label for="customer" class="col-sm-4 control-label">
              <?php echo trans('messages.Select Customer'); ?>
             </label>
            <div class="col-sm-8">
              <select name="customer_id" class="selectLists" onchange="getcustomer()" id="customer">
                <option value="">
                  <?php echo trans('messages.Select Customer'); ?>
                </option>
                <?php
				if(count($users) > 0)
				{
				foreach($users as $user) {
					$select = (Input::old('customer_id') == $user->id) ? 'selected' : ''; 
				?>
                <option value="<?php echo $user->id; ?>" <?php echo $select; ?>>
                  <?php echo $user->first_name.' '.$user->last_name.' - '.$user->mobile; ?>
                </option>
                <?php } } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-4 control-label">
              <?php echo trans('messages.First Name'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8">
              <input type="text" name="customer_first_name" class="form-control" id="customer_first_name" placeholder="<?php echo trans('messages.Customer First Name'); ?>" value="<?php echo Input::old('customer_first_name'); ?>">
              @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('customer_first_name') != '') ? $error->first('customer_first_name') : ''; ?>
              </span>@endif
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-4 control-label">
              <?php echo trans('messages.Last Name'); ?>
            </label>
            <div class="col-sm-8">
              <input type="text" name="customer_last_name" class="form-control" id="customer_last_name" placeholder="<?php echo trans('messages.Customer Last Name'); ?>" value="<?php echo Input::old('customer_last_name'); ?>">
			  @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('customer_last_name') != '') ? $error->first('customer_last_name') : ''; ?>
              </span>@endif
            </div>
          </div>
          <div class="form-group">
            <label for="name" class="col-sm-4 control-label">
              <?php echo trans('messages.Email'); ?>
            </label>
            <div class="col-sm-8">
              <input type="hidden" name="customer_key"  id="customer_key"/>
              <input type="text" name="customer_email" class="form-control" id="customer_email" placeholder="<?php echo trans('messages.Customer Email'); ?>" value="<?php echo Input::old('customer_email'); ?>">
              @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('customer_email') != '') ? $error->first('customer_email') : ''; ?>
              </span>@endif
            </div>
          </div>
          <div class="form-group">
            <label for="customer_mobile" class="col-sm-4 control-label">
              <?php echo trans('messages.Mobile'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8">
              <input type="text" maxlength="15" name="customer_mobile" class="form-control" id="customer_mobile" placeholder="<?php echo trans('messages.Customer Mobile'); ?>" value="<?php echo Input::old('customer_mobile'); ?>">
              @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('customer_mobile') != '') ? $error->first('customer_mobile') : ''; ?>
              </span>@endif
            </div>
          </div>
          <div class="form-group">
            <label for="send_sms" class="col-sm-4 control-label">
              <?php echo trans('messages.Update Delivery Address Option'); ?>
            </label>
            <div class="col-sm-8">
              <input type="checkbox" name="send_sms" id="send_sms" value="1" <?php echo (Input::old('send_sms') == 1) ? 'checked' : ''; ?>>
            </div>
          </div>
          <div class="form-group">
            <label for="send_delivery_fee" class="col-sm-4 control-label">
              <?php echo trans('messages.Is Delivery fee is send to foodics'); ?> ?
            </label>
            <div class="col-sm-8">
              <input type="checkbox" name="send_delivery_fee" id="send_delivery_fee" value="1" <?php echo (Input::old('send_delivery_fee') == 1) ? 'checked' : ''; ?>>
            </div>
          </div>
        </div>
      </div>
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">
            <?php echo trans('messages.Address Details'); ?>
          </h3>
        </div>
        <!--box-header-->		
        <div class="box-body">                        
          <input type="hidden" name="latitude" id="latitude">
          <input type="hidden" name="longitude" id="longitude">     
          <div class="form-group">
            <label class="col-sm-3 control-label">
              <?php echo trans('messages.Address'); ?>:
              <span class="req">*</span> :
            </label>
            <div class="col-sm-8">
              <textarea class="form-control" name="address" placeholder="<?php echo trans('messages.Address'); ?>" id="address"><?php echo Input::old('address'); ?></textarea>                          
			  <span class="error_msg" id="address_error"></span>
			  @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('address') != '') ? $error->first('address') : ''; ?>
              </span>@endif
            </div>
          </div>
        </div>
        <!--box-body-->	
      </div>
      <!--box-info-->
    </div>
    <div class="col-md-6">    
      <div class="box">		
        <div class="box-header with-border">
          <h3 class="box-title">
            <?php echo trans('messages.Order Details'); ?>
          </h3>
        </div>
        <!--box-header-->
        <div class="box-body">
          <div class="form-group">
            <label class="col-sm-4 control-label">
              <?php echo trans('messages.Order Date'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8"> 
              <input type="text" name="orderdate" class="form-control" id="dpicToday" placeholder="Select Date" value=
                  <?php echo (Input::old('orderdate') != "") ? date('d-m-Y', strtotime(Input::old('orderdate'))) : date('d-m-Y'); ?>> 
              <span id="date_error" class="error_msg">
              </span>
              @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('orderdate') != '') ? $error->first('orderdate') : ''; ?>
              </span>@endif
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">
              <?php echo trans('messages.Order Time'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8 last bootstrap-timepicker"> 
              <input type="text" name="ordertime" id="ordertime" class="form-control timepicker" value="<?php echo (Input::old('ordertime') != '') ? Input::old('ordertime') : date('H:i'); ?>">
              <span class="error_msg" id="time_error">
              </span> 
              @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('ordertime') != '') ? $error->first('ordertime') : ''; ?>
              </span>@endif
            </div>
          </div>
          <div class="form-group full_selectLists">
            <label for="customer" class="col-sm-4 control-label">
              <?php echo trans('messages.Select Branch'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8">

              <select name="branch" class="selectLists" id="branch" onchange="getdeliveryboys();">
                <option value="">
                  <?php echo trans('messages.Select Branch'); ?>
                </option>
              </select>
              @if(Session::has('error'))
              <p class="error_msg">
                <?php echo ($error->first('branch') != '') ? $error->first('branch') : ''; ?>
              </p>@endif
              <span class="error_msg" id="branch_error"></span>
            </div>
            <div class="box-footer feeback_btns">
              <button type="button" onclick="get_branch();" class="btn btn-primary pull-right">
                <i class="fa fa-floppy-o">
                </i>
                <?php echo trans('messages.Get Branch'); ?>
              </button>
            </div>
          </div>
          <div class="form-group full_selectLists" id="select_deliveryboy">
            <label for="customer" class="col-sm-4 control-label">
              <?php echo trans('messages.Select Deliver boy'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8">
              <select name="deliveryboy" class="selectLists" id="deliveryboy">
                <option value=""><?php echo trans('messages.Select Deliverboy'); ?></option>
              </select>
              @if(Session::has('error'))
              <p class="error_msg">
                <?php echo ($error->first('deliveryboy') != '') ? $error->first('deliveryboy') : ''; ?>
              </p>@endif
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">
              <?php echo trans('messages.Notes'); ?>
            </label>
            <div class="col-sm-8">
              <textarea name="notes" id="notes" class="form-control"><?php echo Input::old('notes'); ?>
              </textarea>
              <span class="error_msg" id="notes">
              </span> 
              @if(Session::has('error'))
              <span class="error_msg">
                <?php echo ($error->first('notes') != '') ? $error->first('notes') : ''; ?>
              </span>@endif
            </div>
          </div>
        </div>
        <div class="box">		
          <div class="box-header with-border">
            <h3 class="box-title">
              <?php echo trans('messages.Address Details'); ?>
            </h3>
          </div>
          <!--box-header-->
          <div class="box-body">
            <div class="form-group" align="center">
              <div id="map-canvas" style="width: 90%; height: 400px;">
              </div>
            </div>
            <!-- form-group -->
          </div>
          <!--box-body-->
        </div>
        <!--box-->
      </div>
    </div>	 
  </div>	  
  <div class="col-md-12">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">
          <?php echo trans('messages.Item Details'); ?>
        </h3>
      </div>
      <!--box-header-->		
      <div class="box-body">
        <div class="form-group full_selectLists col-sm-6">
          <label for="customer" class="col-sm-4 control-label">
            <?php echo trans('messages.Select Category'); ?>
            <span class="req">*
            </span>
          </label>
          <div class="col-sm-8">
            <select name="category" class="selectLists" onchange="getitems()" id="category">
              <option value="">
                <?php echo trans('messages.Select Category'); ?>
              </option>
              <?php
				if(count($categories) > 0)
				{
				foreach($categories as $category) {
			  ?>
              <option value="<?php echo $category->id; ?>">
                <?php echo $category->category; ?>
              </option>
              <?php } } ?>
            </select>
            @if(Session::has('error'))
            <p class="error_msg">
              <?php echo ($error->first('category') != '') ? $error->first('category') : ''; ?>
            </p>@endif
          </div>
        </div>
      </div>
      <div class="box-body" >
        <div class="col-md-7  bor_image" id="items">
        </div>
        <div id="cartdetails">
          <div class="col-md-5  bor_image well" id="cartbox">
            <div class="pro_title_1">
              <span class="pro_title_txt_1">
                <?php echo trans('messages.Item Name'); ?>
                <p style="float:right;">
                  <?php echo trans('messages.Quantity'); ?>
                </p> 
              </span>
            </div>					
            <div class="pro_price_1">
              <span class="pro_title_txt_2">
                <?php echo trans('messages.Price'); ?>
              </span>
            </div>
            <?php $items = Cart::content();
      			if(count($items) > 0)
      			{
      			foreach($items as $item)
      			{
              $size = ($item->options->is_size) ? ' - '.$item->options->size : '';
      			?>
            <div class="pro_title_1">
              <span>
                <?php echo $item->name.$size; ?>
                <p style="float:right;">
                  <button type="button" class="b_plu" onclick="updateqty1('<?php echo $item->rowid; ?>','add')">+
                  </button>
                  <input type="text" readonly id="qty_<?php echo $item->rowid; ?>" style="width:30px;" value='<?php echo $item->qty; ?>'>
                  <button type="button" class="b_plu" onclick="updateqty1('<?php echo $item->rowid; ?>','remove')">-
                  </button>  
                  <button type="button" class="b_update" id="upt_<?php echo $item->rowid; ?>" style='display:none;' onclick="editqty('<?php echo $item->rowid; ?>')">Update
                  </button>  
                </p>
                <?php if($item->options->is_ingredients) { ?>
                <ul>
                  <?php //echo '<pre>'; print_r($item->options); exit;
                  for($i=0; $i<count($item->options->ingredientlist); $i++) { 
                  ?>
                  <li><?php echo $item->options->ingredientlist[$i]; ?>
                      <span class="price-agn-rgt"><?php echo getdefault_currency().' '.$item->options->ingredient_price[$i]; ?></span>
                  </li>
                  <?php } ?>
                </ul>
                <?php } ?>
                <?php if($item->options->is_execlusion) { ?>
                <h4>{!! trans('messages.Execlusions') !!}</h4>
                <ul>
                  <?php //echo '<pre>'; print_r($item->options); exit;
                  for($i=0; $i<count($item->options->execlusions); $i++) { 
                  ?>
                  <li><?php echo $item->options->execlusions[$i]; ?></li>
                  <?php } ?>
                </ul>
                <?php } ?> 
              </span>
            </div>	
            <div class="pro_price_1">
              <span class="pro_title_txt_2">
                <?php 
                $item_price = ($item->options->is_size) ? $item->qty*($item->price + $item->options->size_price) : $item->qty*$item->price; 
                echo $item_price; ?>
              </span>
            </div>
            <div class="pro_del_btn">
              <img src='/assets/images/trash.png' style="cursor:pointer;" class="img-responsive" onclick="deleteitem('<?php echo $item->rowid; ?>')">
            </div>
            <?php 
            $subtotal += $item_price;
            $ingredient_total += $item->options->ingredient_total*$item->qty; 
            } 
            $vat_tax = ((($subtotal + $ingredient_total) * $config_data['vat']) / 100); 
            $total = $subtotal +$ingredient_total +$vat_tax ;
            } ?>
            <!--<button type="button" class="pro_del_btn">x</button>-->
            <div class="pro_title_1">
              <span class="pro_title_txt_1">
                <p style="float:right;"><?php echo trans('messages.Sub Total'); ?></p> 
              </span>
            </div>					
            <div class="pro_price_1">
              <span class="pro_title_txt_2">
                <?php echo $subtotal; ?>
              </span>
            </div>
            <div class="pro_title_1">
              <span class="pro_title_txt_1">
                <p style="float:right;"><?php echo trans('messages.Ingredients Amount'); ?></p> 
              </span>
            </div>					
            <div class="pro_price_1">
              <span class="pro_title_txt_2">
                <?php echo $ingredient_total; ?>
              </span>
            </div>
            <div class="pro_title_1">
              <span class="pro_title_txt_1">
                <p style="float:right;"><?php echo trans('messages.Delivery Fee'); ?></p> 
              </span>
            </div>					
            <div class="pro_price_1">
              <span class="pro_title_txt_2" id="delivery_fee"><?php echo Session('orders.delivery_fee'); ?></span>
            </div>
            <div class="pro_title_1">
              <span class="pro_title_txt_1">
                <p style="float:right;"><?php echo trans('messages.Vat Tax'); ?></p> 
              </span>
            </div>					
            <div class="pro_price_1">
              <span class="pro_title_txt_2">
                <?php echo $vat_tax; ?>
              </span>
            </div>
            <div class="pro_title_1">
              <span class="pro_title_txt_1">
                <p style="float:right;"><?php echo trans('messages.Total Amount'); ?></p> 
              </span>
            </div>					
            <div class="pro_price_1">
              <span class="pro_title_txt_2">
                <?php echo $total + Session('orders.delivery_fee'); ?>
              </span>
            </div>
          </div>  
        </div>
      </div>
    </div> 
    <!-- col-md-12 -->
  </div>
  <div class="box-footer feeback_btns">
    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i>
      <?php echo trans('messages.Place Order'); ?>
    </button>
    <button type="reset" class="btn pull-right">
      <i class="fa fa-refresh">
      </i>
      <?php echo trans('messages.Clear'); ?>
    </button>
    <button type="button" onclick="window.location.href = '{!! URL::to('admin/orders') !!}'" class="btn pull-right">
      <i class="fa fa-chevron-left">
      </i>
      <?php echo trans('messages.Cancel'); ?>
    </button>
  </div>
  <!-- box-footer -->
</section>
<!-- /.content -->
{!! Form::close() !!}
</div>
<!-- /.content-wrapper -->
<div class="modal_load">
</div>	
<div class="modal fade bs-example-modal-sm" id="SelectIngredient" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="editType">        
      </div>
    </div>
  </div>
</div> 
<script type='text/javascript'>
  $(document).ready(function(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(GetAddress);
    }
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
	location: {latitude: latitude, longitude: longitude},
	radius: 300,
	inputBinding: {
	latitudeInput: $('#latitude'),
	longitudeInput: $('#longitude'),  
	locationNameInput: $('#address')       
	}
	});
}
});
</script>
<script>
  function getcustomer()
  {
    var customer_id = $("#customer option:selected").val();
    if(customer_id != '')
    {
      $.ajax({
        beforeSend: function () {
          $("body").addClass("loading");
        }
        ,
        type: "GET",
        dataType: "json",
        url: "<?php echo URL::to('admin/getcustomer'); ?>",
        data: {
          'customer_id': customer_id}
        ,
        async: true,
        success: function (result) {
          //alert(result.email);
          $("#customer_key").val(result.customer_key);
          $("#customer_first_name").val(result.first_name);
          $("#customer_last_name").val(result.last_name);
          $("#customer_email").val(result.email);
          $("#customer_mobile").val(result.mobile);
          $("body").removeClass("loading");
        }
      }
            );
    }
    else
    {
      $("#customer_key").val();
      $("#customer_email").val('');
      $("#customer_first_name").val('');
      $("#customer_last_name").val('');
      $("#customer_mobile").val('');
      $("body").removeClass("loading");
    }
  }
  function get_branch()
  {
  	$(".error_msg").html('');
    var delivery_type = $("input[name='order_type']:checked"). val();
    var latitude = $("#latitude").val();
    var longitude = $("#longitude").val();
    var order_date = $("#dpicToday").val();
    var order_time = $("#ordertime").val();
    var error = 0;
    if(latitude == '' || longitude == '')
    {
		$('#address_error').text('<?php echo trans('messages.Address is required'); ?>');
		error = 1;
	}
	if(order_date == '')
    {
		$('#date_error').text('<?php echo trans('messages.Order date is required'); ?>');
		error = 1;
	}
	if(order_time == '')
    {
		$('#time_error').text('<?php echo trans('messages.Order time is required'); ?>');
		error = 1;
	}
    if(error == 0)
    {
      $.ajax({
        beforeSend: function () {
          $("body").addClass("loading");
        }
        ,
        type: "POST",
        dataType: "json",
        url: "<?php echo URL::to('admin/getorder_branches'); ?>",
        data: {'delivery_type': delivery_type, 'latitude': latitude, 'longitude': longitude, 'order_date': order_date, 'order_time': order_time},
        async: true,
        success: function (result) {
          $("#select2-branch-container").html('Select branch');
          if(result.msg == 1)
          {
			$("#branch").html(result.branches);
		  }
		  else
		  {
			  $("#branch_error").html('Please try again with different location or time');
		  }
		
          $("body").removeClass("loading");
        }
      }
            );
    }
  }
  function getdeliveryboys()
  {
    var branch_id = $("#branch option:selected").val();
    var delivery_fee = $("#branch option:selected").attr('data-delivery');
    if(branch_id != '')
    {
      $.ajax({
        beforeSend: function () {
          $("body").addClass("loading");
        }
        ,
        type: "GET",
        dataType: "json",
        url: "<?php echo URL::to('admin/getbranch_deliveryboys'); ?>",
        data: {
          'branch_id': branch_id, 'delivery_fee': delivery_fee}
        ,
        async: true,
        success: function (result) {
          $("#select2-deliveryboy-container").html('Select Delivery boy');
          $("#deliveryboy").html(result.deliveryboys);
          $("#delivery_fee").text(delivery_fee);
          $("body").removeClass("loading");
        }
      }
            );
    }
    else
    {
      $("#deliveryboy").val('');
      $("body").removeClass("loading");
    }
  }
  function getitems()
  {
    var category_id = $("#category option:selected").val();
    if(category_id != '')
    {
      $.ajax({
        beforeSend: function () {
          $("body").addClass("loading");
        }
        ,
        type: "GET",
        dataType: "json",
        url: "<?php echo URL::to('admin/getcategory_items'); ?>",
        data: {
          'category_id': category_id}
        ,
        async: true,
        success: function (result) {
          $("#items").html(result.items);
          $("body").removeClass("loading");
        }
      }
            );
    }
    else
    {
      $("#deliveryboy").val('');
      $("body").removeClass("loading");
    }
  }
  function selectingredient(id)
  {
    $.ajax({
      beforeSend: function () {
        $("body").addClass("loading");
      }
      ,
      type: "GET",
      url: "<?php echo URL::to('admin/selectingredient'); ?>",
      data: {
        'id': id}
      ,
      async: true,
      success: function (result) {
        $("body").removeClass("loading");
        $('#SelectIngredient').html(result).modal('show');
      }
    }
          );
  }
  function addtocart(id)
  {
    $.ajax({
      beforeSend : function() {
        $("body").addClass("loading");
      }
      ,
      type : "GET",
      dataType : "json",
      url  : "<?php echo URL::to('admin/addtocart'); ?>",
      data : {
        'id' : id, 'quantity' : 1}
      ,
      async: true,
      success: function(result) {
        $("#cartdetails").load(location.href + " #cartbox");
        $("body").removeClass("loading");
      }
    }
          );
  }
 /* Order type radio option change functionality
    */
  $(document).ready(function() {
	  var order_type = '';
    $('input[type=radio][name=order_type]').change(function() {
		order_type = this.value;
      if (this.value == 'p') {
        $('#select_deliveryboy').css('display', 'none');
      }
      else {
        $('#select_deliveryboy').css('display', 'block');
      }
    }
    );
  }
  );
  function updateqty1(id,action)
    {
        var qty = $("#qty_"+id).val();
        if(action == 'add')
        {
            var newqty = parseInt(qty) + 1;
            $("#qty_"+id).val(newqty);
            $("#upt_"+id).show();
        }
        else
        {
            var newqty = parseInt(qty) - 1;
            newqty = (newqty < 1) ? 1 : newqty; 
            $("#qty_"+id).val(newqty);
            $("#upt_"+id).show();
        }    
    }
  function editqty(id)
  {
    var rowid   = id;
    var qty     = $("#qty_"+id).val();
    $.ajax({
      beforeSend : function() {
        $("body").addClass("loading");
      }
      ,
      type : "GET",
      dataType : "json",
      url  : "<?php echo URL::to('admin/add_remove_quantity'); ?>",
      data : {
        'rowid':rowid,'quantity':qty, 'type' : 'add'}
      ,
      async: true,
      success: function(result) {
        $("#cartdetails").load(location.href + " #cartbox");
        $("body").removeClass("loading");
      }
    }
          );
  }
  function deleteitem(id)
  {
    var rowid   = id;
    var qty     = $("#qty_"+id).val();
    if (confirm('Are you sure you want to delete this?')) {
      $.ajax({
        beforeSend : function() {
          $("body").addClass("loading");
        }
        ,
        type : "GET",
        dataType : "json",
        url  : "<?php echo URL::to('admin/delete_cartitem'); ?>",
        data : {
          'rowid':rowid,'qty':qty}
        ,
        async: true,
        success: function(result) {
          $("#cartdetails").load(location.href + " #cartbox");
          $("body").removeClass("loading");
        }
      }
            );
    }
  }
  /* Number Valiadtion For Mobile number */
  jQuery('#customers_mobile').keyup(function () {
    this.value = this.value.replace(/[^0-9\.]/g,'');
  }
  );
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
      url(<?php echo URL::to('assets/images/loading.gif');
    ?>) 
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
