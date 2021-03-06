@extends('branch_header')

@section('content')
<?php 
use App\Order;
$seg = Request::segment(3); 
$products             = Cart::content();
$ingredient_subtotal  = 0; 
$ingredient_total     = 0;
$vat_tax              = 0;
$total                = 0;
$subtotal             = 0;
?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Edit Order'); ?></h1>
        @if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif 
        @if(Session::has('error'))<p class="error_msg">Required(*) Fields are missing<?php $error = Session::get('error'); ?></p></p>@endif 
    </section>

    <!-- Main content -->
    <section class="content">

        {!! Form::open(array('url' => 'branch/updateorder', 'class' => 'form-horizontal', 'files' => 1)) !!}     
        <div class="row form-horizontal">
            <div class="col-md-6">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Customer Details'); ?></h3>

                    </div><!--box-header-->	
                  
                    <input type='hidden' name='order_id' value='<?php echo $order->id; ?>'>
                     <input type='hidden' name='customer_id' value='<?php echo $order->customer_id; ?>'>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="customer" class="col-sm-4 control-label"><?php echo trans('messages.Select Order Type'); ?><span class="req">*</span></label>
                            <div class="col-sm-8 radio_btns">
                                <input type="radio" name="order_type" value="p" checked><?php echo trans('messages.Pickup'); ?>
                                <input type="radio" name="order_type" value="d" <?php echo (Input::old('delivery_type') == 'd' || $order->delivery_type == 'd') ? 'checked' : ''; ?>><?php echo trans('messages.Delivery'); ?>
                            </div>
                        </div>                    

                        <div class="form-group">
                            <label for="name" class="col-sm-4 control-label"><?php echo trans('messages.First Name'); ?><span class="req">*</span></label>
                            <div class="col-sm-8">
								<input type="text" name="customer_first_name" class="form-control" id="customer_first_name" placeholder="Customer First Name" value="<?php echo Input::old('customer_first_name')? Input::old('customer_first_name') : $order->customer_first_name; ?>">
                                @if(Session::has('error'))<span class="error_msg"><?php echo ($error->first('customer_first_name') != '') ? $error->first('customer_first_name') : ''; ?></span>@endif
                            </div>
                        </div>
                          <div class="form-group">
                            <label for="name" class="col-sm-4 control-label"><?php echo trans('messages.Last Name'); ?></label>
                            <div class="col-sm-8">
                                <input type="text" name="customer_last_name" class="form-control" id="customer_last_name" placeholder="Customer Last Name" value="<?php echo Input::old('customer_last_name')? Input::old('customer_last_name') : $order->customer_last_name; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="name" class="col-sm-4 control-label"><?php echo trans('messages.Email'); ?></label>
                            <div class="col-sm-8">
                                <input type="hidden" name="customer_key"  id="customer_key"/>
                                <input type="text" name="customer_email" class="form-control" id="customer_email" placeholder="Customer Email" value="<?php echo Input::old('customer_email')? Input::old('customer_email') : $order->customer_email; ?>">
                                @if(Session::has('error'))<span class="error_msg"><?php echo ($error->first('customer_email') != '') ? $error->first('customer_email') : ''; ?></span>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_mobile" class="col-sm-4 control-label"><?php echo trans('messages.Mobile'); ?><span class="req">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" name="customer_mobile" class="form-control" id="customer_mobile" placeholder="Customer Mobile" value="<?php echo Input::old('customer_mobile')? Input::old('customer_mobile') : $order->customer_mobile; ?>">
                                @if(Session::has('error'))<span class="error_msg"><?php echo ($error->first('customer_mobile') != '') ? $error->first('customer_mobile') : ''; ?></span>@endif
                            </div>
                        </div>
                    
					</div>
					</div>
					<div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Address Details'); ?></h3>
                    </div><!--box-header-->		

                    <div class="box-body">
                                <input type="hidden" name="latitude" id="latitude" value="<?php echo $order->latitude;?>">
                                <input type="hidden" name="longitude" id="longitude" value="<?php echo $order->longitude;?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><?php echo trans('messages.Address'); ?>:<span class="req">*</span> :</label>
                            <div class="col-sm-8">                             
                                  <textarea name="address" id="address" class="form-control" value=""><?php echo Input::old('address')? Input::old('address') : $order->address; ?></textarea>                       
                        </div>
                        </div>
                    </div><!--box-body-->   
                   
                </div><!--box-info-->

            </div>

            <div class="col-md-6">    
                <div class="box">       
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Order Details'); ?></h3>
                    </div><!--box-header-->

                    <div class="box-body">
                        <div class="form-group">
            <label class="col-sm-4 control-label">
              <?php echo trans('messages.Branch'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8"> 
                <input readonly type="text" maxlength='30' class="form-control" id="branch" placeholder="<?php echo trans('messages.Branch'); ?> " value="<?php echo Session('name'); ?>">
				<input type="hidden" name="branch" value="<?php echo Session('branch_id'); ?>">
				@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('branch') != '') ? $error->first('branch') : ''; ?></p>@endif
            </div>
          </div>
           <div class="form-group full_selectLists" id="select_deliveryboy">
            <label for="customer" class="col-sm-4 control-label">
              <?php echo trans('messages.Select Deliver boy'); ?>
              <span class="req">*</span>
            </label>
            <div class="col-sm-8">
              <select name="deliveryboy" class="selectLists" id="deliveryboy">
                <option value="">
                  <?php echo trans('messages.Select Deliverboy'); ?>
                </option>
                <?php
				if(count($deliveryboys))
				{
				foreach($deliveryboys as $deliveryboy) {
					$select = (Input::old('deliveryboy') != '') ? selectdrop(Input::old('deliveryboy'), $deliveryboy->id) : selectdrop($order->deliveryboy_id, $deliveryboy->id);
				?>
				<option <?php echo $select; ?> value="<?php echo $deliveryboy->id; ?>"><?php echo $deliveryboy->name; ?></option>
				<?php } } ?>
              </select>
              @if(Session::has('error'))
              <p class="error_msg">
                <?php echo ($error->first('deliveryboy') != '') ? $error->first('deliveryboy') : ''; ?>
              </p>@endif
            </div>
          </div>
          <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo trans('messages.Order Date'); ?></label>
                            <div class="col-sm-8"> 

                                <input type="text" name="orderdate" class="form-control" id="dpicToday" placeholder="Select Date" value="<?php echo Input::old('order_datetime') ? Input::old('order_datetime') : date('d-m-Y', strtotime($order->order_datetime)); ?>"> <span id="appointmentdate_error" class="error_msg"></span>
                                @if(Session::has('error'))<span class="error_msg"><?php echo ($error->first('orderdate') != '') ? $error->first('orderdate') : ''; ?></span>@endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo trans('messages.Order Time'); ?></label>
                            <div class="col-sm-8 last bootstrap-timepicker"> 
                                <input type="text" name="ordertime" id="ordertime" class="form-control timepicker" value="<?php echo Input::old('order_datetime') ? Input::old('order_datetime') : $order->order_datetime;?>"><span class="error_msg" id="ordertime_error"></span> 
                                @if(Session::has('error'))<span class="error_msg"><?php echo ($error->first('appointmentime') != '') ? $error->first('ordertime') : ''; ?></span>@endif
                            </div>
                        </div>

                         <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo trans('messages.Notes'); ?></label>
                            <div class="col-sm-8">
                                <textarea name="notes" id="notes" class="form-control" value=""><?php echo Input::old('notes')? Input::old('notes') : $order->notes; ?></textarea>
                                <span class="error_msg" id="notes"></span> 
                                                       
                                @if(Session::has('error'))<span class="error_msg"><?php echo ($error->first('notes') != '') ? $error->first('notes') : ''; ?></span>@endif
                            </div>
                        </div>
                    </div>


                        <div class="box">       
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Address Details'); ?></h3>
                    </div><!--box-header-->

                    <div class="box-body">
                         <div class="form-group" align="center">
                            <div id="map-canvas" style="width: 90%; height: 400px;"></div>
                        </div><!-- form-group -->
                    </div><!--box-body-->
                </div><!--box-->
 
                </div>
            </div>   
            </div>
         <div class="col-md-12">
			 <!--<div class="box">
				<table class="table table-bordered table-striped">
                <thead>
                    <tr>
						<th>S.No</th>
						<th>Item Name</th>
						<th>Quantity</th>
						<th>Price</th>
						<th>Ingredients</th>
						<th>Action</th>
                    </tr>
					  
                </thead>
                <tbody>
					<?php $i = 1;
					foreach($data['items'] as $item) {
					?>
					<tr id="item<?php echo $item->id; ?>"> 
						<td><?php echo $i; ?></td>
						<td><?php echo $item->item_name; ?></td>
						<td width=30><input type="number" id="quantity<?php echo $item->id; ?>" autocomplete="off" name="quantity" value="<?php echo $item->quantity; ?>" min=1></td>
						<td>$ <?php echo $item->price; ?></td>
						<td>
						<?php
						if($item->is_ingredients == 1)
						{
							foreach($item->ingredients as $ingredient)
							{
								echo $ingredient->ingredient_name.' - $'.$ingredient->ingredient_price.'<br>';
							}
						}
						?>
						</td>
						<td class="action_btns">
							<a href="javascript:void(0);" title="Save" class="edit_btn" <?php echo (Session('edit')) ? 'onclick="updateqty('.$item->id.');"' : ''; ?>><i class="fa fa-floppy-o"></i></a>
							<a href="javascript:void(0);" title="Delete" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_item('.$item->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
						</td>
                    </tr>
                    <?php $i++; } ?>
                    <tr>
						<th colspan="6" style="text-align:right"> Total : $<?php echo $order->order_total; ?></th>
                    </tr>
                </tbody>
            </table>
            </div>-->
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Item Details'); ?></h3>
                    </div><!--box-header-->     

                   <div class="box-body">
						<div class="form-group full_selectLists col-sm-6">
                            <label for="customer" class="col-sm-4 control-label"><?php echo trans('messages.Select Category'); ?><span class="req">*</span></label>
                            <div class="col-sm-8">
                                <select name="category" class="selectLists" onchange="getitems()" id="category">
                                    <option value=""><?php echo trans('messages.Select Category'); ?></option>
                                    <?php
									if(count($categories) > 0)
									{
										foreach($categories as $category) {
									?>
                                   	<option value="<?php echo $category->id; ?>"><?php echo $category->category; ?></option>
                                	<?php } } ?>
                                </select>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('category') != '') ? $error->first('category') : ''; ?></p>@endif
                            </div>
                        </div>
					</div>
					<div class="box-body" >
						<div class="col-md-7  bor_image "  id="items"></div>
                        <div id="cartdetails">
						<div class="col-md-5  bor_image well" id="cartbox">
							<div class="pro_title_1"><span class="pro_title_txt_1"><?php echo trans('messages.Item Name'); ?><p style="float:right;"><?php echo trans('messages.Quantity'); ?></p> </span></div>					
							<div class="pro_price_1"><span class="pro_title_txt_2"><?php echo trans('messages.Price'); ?></span></div>
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
                  $ingredient_subtotal = 0;
                  for($i=0; $i<count($item->options->ingredientlist); $i++) { 
                     $ingredient_subtotal += $item->options->ingredient_price[$i];
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
                echo $item_price;
                ?>
              </span>
            </div>
            <div class="pro_del_btn">
              <img src='/assets/images/trash.png' style="cursor:pointer;" class="img-responsive" onclick="deleteitem('<?php echo $item->rowid; ?>')">
            </div>
            <?php 
            $subtotal += $item_price;
            $ingredient_total += $ingredient_subtotal*$item->qty; 
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
            </div> <!-- col-md-12 -->
            </div>
    
            <div class="box-footer feeback_btns">
				<input type="hidden" name="address_id" value="<?php echo $order->address_id; ?>">
                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Order'); ?></button>
                <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                <button type="button" onclick="window.location.href = '{!! URL::to('branch/orders') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
            </div><!-- box-footer -->
	
    </section><!-- /.content -->
    {!! Form::close() !!}
</div><!-- /.content-wrapper -->

<div class="modal_load"></div>	
 <div class="modal fade bs-example-modal-sm" id="SelectIngredient" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="editType">        

            </div>
        </div>
    </div>
</div> 
<style>
    .modal1 {
        display:    none;
        position:   fixed;
        z-index:    1000;
        top:        0;
        left:       0;
        height:     100%;
        width:      100%;
        background: rgba( 255, 255, 255, .8 ) 
            url('http://i.stack.imgur.com/FhHRx.gif') 
            50% 50% 
            no-repeat;
    }


    body.loading {
        overflow: hidden;   
    }

    body.loading .modal1 {
        display: block;
    }
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
<script type='text/javascript'>
$(document).ready(function()
{
    var lat = $("#latitude").val();
    var lng = $("#longitude").val();
    $('#map-canvas').locationpicker({
        location: {latitude: lat, longitude: lng},
        radius: 300,
        inputBinding: {
        latitudeInput: $('#latitude'),
        longitudeInput: $('#longitude'),
        locationNameInput: $('#address')           
        }
    });
});
   function getdeliveryboys()
    {
        var branch_id = $("#branch option:selected").val();
        if(branch_id != '')
        {
            $.ajax({
                beforeSend: function () {
                    $("body").addClass("loading");
                },
                type: "GET",
                dataType: "json",
                url: "<?php echo URL::to('branch/getbranch_deliveryboys'); ?>",
                data: {'branch_id': branch_id},
                async: true,
                success: function (result) {
					$("#select2-deliveryboy-container").html('Select Delivery boy');
                    $("#deliveryboy").html(result.deliveryboys);
                    $("body").removeClass("loading");
                }
            });
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
                },
                type: "GET",
                dataType: "json",
                url: "<?php echo URL::to('branch/getcategory_items'); ?>",
                data: {'category_id': category_id},
                async: true,
                success: function (result) {
                    $("#items").html(result.items);
                    $("body").removeClass("loading");
                }
            });
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
                },
                type: "GET",
                url: "<?php echo URL::to('branch/selectingredient'); ?>",
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
        url  : "<?php echo URL::to('branch/addtocart'); ?>",
        data : {'id' : id, 'quantity' : 1},
        async: true,
        success: function(result) {
                    $("#cartbox").html(result.items);
                    $("body").removeClass("loading");
                }
            });
    }
    
    function updateqty(id)
    {
		var qty = $("#quantity"+id).val();
		if(qty != 0)
		{
			$.ajax({
			beforeSend : function() {
				$("body").addClass("loading");
			},
			type : "GET",
			dataType : "json",
			url  : "<?php echo URL::to('branch/updateqty'); ?>",
			data : {'id' : id, 'quantity' : qty},
			async: true,
			success: function(result) {
						$("#quantity"+id).html(qty);
						$("body").removeClass("loading");
					}
				});
		}
    }
    
    function delete_item(id)
    {
		if(confirm('Are you sure you want remove this item'))
		{
			$.ajax({
			beforeSend : function() {
				$("body").addClass("loading");
			},
			type : "GET",
			dataType : "json",
			url  : "<?php echo URL::to('branch/delete_item'); ?>",
			data : {'id' : id},
			async: true,
			success: function(result) {
						$("#item"+id).hide();
						$("body").removeClass("loading");
					}
				});
		}
    }
    
    function addtocart(id)
	{
		$.ajax({
		beforeSend : function() {
			$("body").addClass("loading");
		},
		type : "GET",
		dataType : "json",
		url  : "<?php echo URL::to('branch/addtocart'); ?>",
		data : {'id' : id, 'quantity' : 1},
		async: true,
		success: function(result) {
					$("#cartdetails").load(location.href + " #cartbox");
					$("body").removeClass("loading");
				}
			});
	}
	
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
    var order_id   = <?php echo $order->id; ?>;
    var rowid   = id;
    var qty     = $("#qty_"+id).val();
    $.ajax({
        beforeSend : function() {
            $("body").addClass("loading");
        },
        type : "GET",
        dataType : "json",
        url  : "<?php echo URL::to('branch/add_remove_quantity'); ?>",
        data : {'rowid' : rowid, 'quantity' : qty, 'type' : 'edit', 'order_id' : order_id},
        async: true,
        success: function(result) {
                    $("#cartdetails").load(location.href + " #cartbox");
                    $("body").removeClass("loading");
                }
            });
}
function deleteitem(id)
{
	var order_id   = <?php echo $order->id; ?>;
    var rowid   = id;
    if (confirm('Are you sure you want to delete this?')) {
        $.ajax({
            beforeSend : function() {
                $("body").addClass("loading");
            },
            type : "GET",
            dataType : "json",
            url  : "<?php echo URL::to('branch/delete_cartitem'); ?>",
            data : {'rowid' : rowid, 'type' : 'edit', 'order_id' : order_id},
            async: true,
            success: function(result) {
                        $("#cartdetails").load(location.href + " #cartbox");
                        $("body").removeClass("loading");
                    }
            });
    }
}
    
var ingredients = new Array();  
function getingredientlist(thiss)
{ 
    console.log();
    var exits = $.inArray($(thiss).val(), ingredients);
    if (exits == -1) 
    {
        $(thiss).siblings().eq($(thiss).index()+2).html("");
        ingredients.push($(thiss).val());
        $(thiss).closest('p').html("");
    
        var level = ($(thiss).attr('level'));
        var ingredient_id = $( thiss ).val();
        $.ajax({
        beforeSend : function() {
            $("body").addClass("loading");
        },
        type : "GET",
        dataType : "json",
        url  : "<?php echo URL::to('branch/getingredientlist'); ?>",
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
    else
    {
        $(thiss).siblings().eq($(thiss).index()+2).html("This ingredient already exits");       
    }
}
 /* Order type radio option change functionality
    */
    $(document).ready(function() {
        $('input[type=radio][name=order_type]').change(function() {
            var order_type  = this.value;
            if (this.value == 'p') {
                $('#deliveryboy').prop('disabled', true);          
            }
            else {               
                $('#deliveryboy').prop('disabled', false);               
            }
            $.ajax({
                    beforeSend: function () {
                        $("body").addClass("loading");
                },
                    type: "GET",
                    dataType: "json",
                    url: "<?php echo URL::to('/branch/getbranch_delivery'); ?>",
                    data: {'order_type': order_type},
                    async: true,
                    success: function (result) {
                        $("#branch").html(result.branches);
                        $("body").removeClass("loading");
                    }
            });
        });
    });
</script>
<?php
function selectdrop($val1, $val2)
{
    $select = ($val1 == $val2) ? 'selected' : '';
    return $select; 
}
?>
@endsection
