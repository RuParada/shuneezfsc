<?php $__env->startSection('content'); ?>

<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('Notification Center Manually'); ?> </h1>
		<?php if(Session::has('success')): ?> <p class="success_msg"><?php echo Session::get('success'); ?></p> <?php endif; ?>
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	<?php echo Form::open(array('url' => 'admin/')); ?>


		
	<input type="hidden" name="" value="">
	<div class="col-md-12">
		<div class="box">
			<div class="box-body col-md-12">
				<div class="form-group col-md-8">
					<table class="table table-bordered table-striped dataTable2 no-footer">
						
						<tr role="row">
							<td><span> Delivery Method: </span> </td>
							<td>
								<select name="" id="" onchange="this.form.submit()">
									<option value="p"><?php echo trans('Email'); ?></option>
									<option value="a"><?php echo trans('SMS'); ?></option>
									<option value="a"><?php echo trans('App Notification'); ?></option>
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
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('Send'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset'); ?></button>
		<button type="button" onclick="window.location.href='<?php echo URL::to('admin/backend_languages'); ?>'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
	<?php echo Form::close(); ?>	
	</div> <!-- box update_frontend_languages-->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
<?php $__env->stopSection(); ?>     

<?php echo $__env->make('adminheader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>