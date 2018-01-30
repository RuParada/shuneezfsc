@extends('adminheader')
@section('content')

<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Api Language File'); ?> </h1>
		@if(Session::has('success')) <p class="success_msg"><?php echo Session::get('success'); ?></p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/update_api_languages')) !!}
	<div class="col-md-12">
		<div class="box">
			<div class="box-body col-md-12">
				<div class="form-group col-md-8">
					<label for="answer"><?php echo trans('messages.Update Backend Language File'); ?><span class="req">*</span></label>
					<textarea name="message" cols="10" rows="20" class="form-control"><?php echo $msg; ?></textarea>
				</div>
			</div>               
		</div>	
	<div class="box-footer">
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Language'); ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset'); ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/backend_languages') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
    </div>
	{!! Form::close() !!}	
	</div> <!-- box -->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
@endsection     
