<?php $__env->startSection('content'); ?>

<div style="color:white;font-weight: bold;background-color: green;">
	<span style="padding:20px;">
	<?php echo e(isset($order_status) ? $order_status : ''); ?> /
	</span>

	<span style="padding:20px;">
	<?php echo e(isset($delivery_method) ? $delivery_method : ''); ?> / 
	</span>

	<span style="padding:20px;">
	<?php echo e(isset($message) ? $message : ''); ?>

	</span>
	<?php echo e(isset($error) ? $error : ''); ?>

	
</div>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('Automatic Messages Configuration'); ?> </h1>
		<?php if(Session::has('success')): ?> <p class="success_msg"><?php echo Session::get('success'); ?></p> <?php endif; ?>
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">

	<?php echo Form::open(array('url' => 'admin/notification/auto')); ?>


		
	<input type="hidden" name="" value="">
	<div class="col-md-12">
		<div class="box">
			<div class="box-body col-md-12">
				
				<div class="form-group col-md-8">
					<table class="table table-bordered table-striped dataTable2 no-footer">
						
						<tr role="row" class="odd">
							<td><span> Event: </span></td>
							<!-- <td>
								<select name="order_status" id="order_status" onchange="">
									<option value="pending"><?php echo trans('messages.Pending'); ?></option>
									<option value="accepted"><?php echo trans('messages.Accepted'); ?></option>
									<option value="declined"><?php echo trans('messages.Declined'); ?></option>
										                        <option value="assigned"><?php echo trans('messages.Assigned'); ?></option>
										                        <option value="waiting_pickup"><?php echo trans('Waiting for pickup'); ?></option>
										                        <option value="pickedup"><?php echo trans('Pickedup'); ?></option>
										                        <option value="out_delivery"><?php echo trans('messages.Out for delivery'); ?></option>
										                        <option value="deliveryboy_returned"><?php echo trans('Deliveryboy Returned'); ?></option> 
									<option value="delivered"><?php echo trans('messages.Delivered'); ?></option>
								</select>
								<input type="hidden" name="order_id" value="">
							</td> -->
						<td>
							<select name="order_status" id="order_status" onchange="">
								<option value="p"><?php echo trans('Pending'); ?></option>
								<option value="a"><?php echo trans('Accepted'); ?></option>
								<option value="ca"><?php echo trans('Declined'); ?></option>
							
		                        <option value="as"><?php echo trans('Assigned'); ?></option>
		                        <option value="da"><?php echo trans('Waiting for pickup'); ?></option>
		                        <option value="pi"><?php echo trans('Pickedup'); ?></option>
		                        <option value="o"><?php echo trans('Out for delivery'); ?></option>
		                        <option value="r"><?php echo trans('Deliveryboy Returned'); ?></option>
										                    
								<option value="d"><?php echo trans('Delivered'); ?></option>
							</select>
							<input type="hidden" name="order_id" value="">
						</td>
						</tr>
							
						
						<tr role="row" class="even">
							<td><span> Delivery Method: </span> </td>
							<td>
								<select name="delivery_method" id="" onchange="">
									<option value="email"><?php echo trans('Email'); ?></option>
									<option value="sms"><?php echo trans('SMS'); ?></option>
									<option value="app_notification"><?php echo trans('App Notification'); ?></option>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div class="form-group col-md-8">
					<label for="answer"><?php echo trans('Define text message'); ?><span class="req">*</span></label>
					<textarea name="message" cols="10" rows="20" class="form-control"><?php //echo $msg; ?></textarea>
				</div>
			</div>               
		</div>	
	<div class="box-footer">
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('Save'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset'); ?></button>
		<button type="button" onclick="window.location.href='<?php echo URL::to('admin'); ?>'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
	<?php echo Form::close(); ?>	
	</div> <!-- box update_frontend_languages-->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
<?php $__env->stopSection(); ?>     

<?php echo $__env->make('adminheader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>