<?php $__env->startSection('content'); ?>
<?php $seg = Request::segment(3);?>
<?php $status_seg = Request::segment(4);?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Manage Orders'); ?></h1>
		<?php if(Session::has('success')): ?><p class="success_msg"><?php echo Session::get('success'); ?></p><?php endif; ?> 
		<?php if(Session::has('error')): ?><p class="error_msg"><?php echo Session::get('error'); ?></p><?php endif; ?> 
        <p id="accept_order" style="display: none;" class="error_msg"></p>
	</section>

	<!-- Main content -->
	<section class="content">
          
	<div class="box">                
        <div class="box-body">
           <a href="<?php echo URL::to('admin/createorder'); ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i><?php echo trans('messages.Create Order'); ?></a>
		   <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
						<th>S.No</th>
						<th><?php echo trans('messages.Customer First Name'); ?></th>
                        <th><?php echo trans('messages.Customer Last Name'); ?></th>
                        <th><?php echo trans('messages.Order Date'); ?></th>
                        <th><?php echo trans('messages.Order Time'); ?></th>
                        <th><?php echo trans('messages.Total Amount'); ?></th>
                        <th><?php echo trans('messages.Order Status'); ?></th>
                        <th><?php echo trans('messages.Action'); ?></th>
                    </tr>
					  
                </thead>
                <tbody>
                    <?php 
                    if(count($orders) > 0) {
                        $i = ($orders->currentPage() == 1) ? 1 : (($orders->currentPage() - 1) * $orders->perPage()) + 1;
                        foreach($orders as $order) { 
                      
                    ?>
                    <tr>
						<td><?php echo $i; ?></td>
						<td><?php echo $order->customer_first_name; ?></td>
                        <td><?php echo $order->customer_last_name; ?></td>
                        <td><?php echo date('d-M-Y', strtotime($order->order_datetime)); ?></td>
                        <td><?php echo date('g:i A', strtotime($order->order_datetime)); ?></td>
						<td><?php echo $order->order_total; ?></td>
						<td>
							<?php echo Form::open(array('url' => 'admin/update_orderstatus')); ?>

							<select name="order_status" id="order_status" onchange="this.form.submit()" <?php echo ($order->order_status == 'd') ? 'disabled' : ''; ?>>
								<option value="p" <?php echo ($order->order_status == 'p') ? 'selected' : ''; ?>><?php echo trans('messages.Pending'); ?></option>
								<option value="a" <?php echo ($order->order_status == 'a') ? 'selected' : ''; ?>><?php echo trans('messages.Accepted'); ?></option>
								<option value="ca" <?php echo ($order->order_status == 'ca') ? 'selected' : ''; ?>><?php echo trans('messages.Declined'); ?></option>
								<?php if($order->delivery_type == 'd') { ?>
                                    <option value="as" <?php echo ($order->order_status == 'as') ? 'selected' : ''; ?>><?php echo trans('messages.Assigned'); ?></option>
                                    <option value="da" <?php echo ($order->order_status == 'da') ? 'selected' : ''; ?>><?php echo trans('messages.Waiting for pickup'); ?></option>
                                    <option value="pi" <?php echo ($order->order_status == 'pi') ? 'selected' : ''; ?>><?php echo trans('messages.Pickedup'); ?></option>
                                    <option value="o" <?php echo ($order->order_status == 'o') ? 'selected' : ''; ?>><?php echo trans('messages.Out for delivery'); ?></option>
                                    <option value="r" <?php echo ($order->order_status == 'r') ? 'selected' : ''; ?>><?php echo trans('messages.Deliveryboy Returned'); ?></option>
                                <?php } ?>
								<option value="d" <?php echo ($order->order_status == 'd') ? 'selected' : ''; ?>><?php echo trans('messages.Delivered'); ?></option>
							</select>
							<input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
							<?php echo Form::close(); ?>

						</td>
                        <td class="action_btns" width="120">
							<?php if($order->order_status == 'd') { ?>
								Delivered
							<?php 
                            } 
                            else 
                            {   
                                if($order->order_status == 'p' || $order->order_status == 'c')
                                {
                            ?>
                                <a href="javascript:void(0);" title="Assign Order" class="view_btn" onclick="accept_order();"><i class="fa fa-sign-in"></i></a>
                            <?php } else { ?>
                                <a href="<?php echo URL::to('/admin/assign_order/'.$order->id); ?>" title="Assign Order" class="view_btn"><i class="fa fa-sign-in"></i></a>
                            <?php } ?>
								<a href="<?php echo (Session('edit')) ? URL::to('/admin/editorder/'.$order->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                <a href="javascript:void(0);" class="delete_btn" onclick="delete_order(<?php echo $order->id; ?>);"><i class="fa fa-trash"></i></a>
							<?php } ?>
						</td>
                    </tr>
                    <?php $i++; } } 
                    else { ?>
                        <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No data found'); ?></td></tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
                echo "<div class='row-page entry'>";
                if ($orders->currentPage() == 1) {
                    $count = $orders->count();
                } else if ($orders->perPage() > $orders->count()) {
                    $count = ($orders->currentPage() - 1) * $orders->perPage() + $orders->count();
                } else {
                    $count = $orders->currentPage() * $orders->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($orders->currentPage() == 1) ? 1 : ($orders->perPage() * ($orders->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'> ".trans('messages.Showing')." " .$start.' '.trans('messages.to').' '.$count.' '.trans('messages.of').' '.$orders->total()." ".trans('messages.entries')." </span>";
                echo $orders->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
                echo '</div>';
                ?>
        </div><!-- /.box-body -->
    </div><!-- /.box -->		  
          
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->


<script>

    function filter_orders()
    {
    	var status = $("#filter_order option:selected").val();
    	var url = "<?php echo URL::to(''); ?>";
        var seg = "<?php echo $seg; ?>";
        if(status != "")
        {
            window.location = url+'/admin/filterorders/'+seg+'/'+status;
        }
    	else
        {
            window.location = url+'/admin/orders/'+seg;
        }
    }

    function filter_payment()
    {
        var status = $("#filter_payment option:selected").val();
        var url = "<?php echo URL::to(''); ?>";
        var seg = "<?php echo $seg; ?>";
        if(status != "")
        {
            window.location = url+'/admin/filterpayment/'+seg+'/'+status;
        }
        else
        {
            window.location = url+'/admin/orders/'+seg;
        }
    }

    function delete_order(id)
    {
        if(confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deleteorder/'+id;
        }
    }

    function accept_order()
    {
        $("#accept_order").css('display', 'block');
        $("#accept_order").text('<?php echo trans('messages.Please accept the order'); ?>');
    }
    
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminheader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>