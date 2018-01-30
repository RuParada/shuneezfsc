@extends('branch_header')

@section('content')
<?php $seg = Request::segment(3);?>
<?php $status_seg = Request::segment(4);?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Manage Orders'); ?></h1>
		@if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif 
		@if(Session::has('error'))<p class="error_msg"><?php echo Session::get('error'); ?></p>@endif 
	</section>

	<!-- Main content -->
	<section class="content">
          
	<div class="box">                
        <div class="box-body">
           <a href="{!! URL::to('branch/createorder')!!}" class="btn btn-primary"><i class="fa fa-plus-circle"></i><?php echo trans('messages.Create Order'); ?></a>
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
							{!! Form::open(array('url' => 'branch/update_orderstatus')) !!}
							<select name="order_status" id="order_status" onchange="this.form.submit()" <?php echo ($order->order_status == 'd') ? 'disabled' : ''; ?>>
								<option value="p" <?php echo ($order->order_status == 'p') ? 'selected' : ''; ?>><?php echo trans('messages.Pending'); ?></option>
								<option value="c" <?php echo ($order->order_status == 'c') ? 'selected' : ''; ?>><?php echo trans('messages.Confirmed'); ?></option>
								<option value="ca" <?php echo ($order->order_status == 'ca') ? 'selected' : ''; ?>><?php echo trans('messages.Declined'); ?></option>
								<?php if($order->order_status == 'as') { ?><option value="as" selected><?php echo trans('messages.Assigned'); ?></option><?php } ?>
								<?php if($order->order_status == 'd') { ?><option value="d" selected><?php echo trans('messages.Delivered'); ?></option><?php } ?>
								<?php if($order->order_status == 'o') { ?><option value="o" selected><?php echo trans('messages.Out for delivery'); ?></option><?php } ?>
							</select>
							<input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
							{!! Form::close() !!}
						</td>
                        <td class="action_btns">
							<?php if($order->order_status == 'd') { ?>
								Delivered
							<?php } else { ?>	
								<a href="{!! ($order->order_status == 'c') ? URL::to('/branch/assign_order/'.$order->id) : '#' !!}" title="Assign Order" class="view_btn"><i class="fa fa-sign-in"></i></a>
								<a href="<?php echo URL::to('/branch/editorder/'.$order->id); ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
							<?php } ?>
						</td>
                    </tr>
                    <?php $i++; } } 
                    else { ?>
                        <tr><td colspan="7" style="text-align:center"><?php echo trans('messages.No data found'); ?></td></tr>
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
                    $count = $orders->perPage() + $orders->count();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($orders->currentPage() == 1) ? 1 : $count - $orders->count() + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'> ".trans('messages.Showing')." " . $start . trans('messages.to') . $count .trans('messages.of') . $orders->total() . " ".trans('messages.entries')." </span>";
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
    
</script>
@endsection
