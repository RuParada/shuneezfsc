@extends('adminheader')
@section('content')

<div style="color:white;font-weight: bold;background-color: green;">
	<span style="padding:20px;">
	{{ $delivery_method or '' }}
	</span>
	<br />
	<span style="padding:20px;">
	{{ $message or '' }}
	</span>
	{{ $error or '' }}
	
</div>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('Send Message Manually'); ?> </h1>
		@if(Session::has('success')) <p class="success_msg"><?php echo Session::get('success'); ?></p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/notification/send')) !!}

		
	<input type="hidden" name="" value="">
	<div class="col-md-12">
		<div class="box">
			<div class="box-body col-md-12">
				<div class="col-md-8 title_notificate">
					<span>The message will be sent to all customers</span>
				</div>
				<div class="form-group col-md-8">
					<table class="table table-bordered table-striped dataTable2 no-footer">
						
						<tr role="row">
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
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('Send'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
	{!! Form::close() !!}	
	</div> <!-- box update_frontend_languages-->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
@endsection     
