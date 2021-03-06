@extends('adminheader')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<?php $array_valid = (Session::has('array_valid')) ? Session::get('array_valid') : []; ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Edit Cuisines') ?> </h1>
		@if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
	</section>

	<!-- Main content -->
	<section class="content product">
	<div class="row margin0">
	{!! Form::open(array('url' => 'admin/updatecuisine', 'files' => 1)) !!}
	<div class="box no_top_border">
		<div class="nav-tabs-custom ">
			<ul class="nav nav-tabs">
				<?php
				if(count($languages) > 0)
				{
					$i = 0;
					foreach($languages as $language) {
						if(count($array_valid)){
							$active = showvalidmsg($i, $array_valid);
				?>
				<li class="<?php echo $active; ?>"><a href="#<?php echo $language->code; ?>" data-toggle="tab"><?php echo ucfirst($language->language); ?></a></li>
				<?php } else { ?>
				<li class="<?php echo ($i == 0) ? 'active' : ''; ?>"><a href="#<?php echo $language->code; ?>" data-toggle="tab"><?php echo ucfirst($language->language); ?></a></li>
				<?php } $i++; } } ?>
			</ul>
			<div class="tab-content">
			<?php
			if(count($languages) > 0)
			{
				$i = 0; 
				foreach($languages as $language) {
					$cuisine_name = DB::table('cuisine_description')->where('cuisine_id', $cuisine->id)->where('language', $language->code)->first();
					if(count($array_valid)){
					$active = showvalidmsg($i, $array_valid);		
			?>
			<div class="<?php echo $active; ?> tab-pane" id="<?php echo $language->code; ?>">
			<?php } else { ?>
			<div class="<?php echo ($i == 0) ? 'active' : ''; ?> tab-pane" id="<?php echo $language->code; ?>">
			<?php } ?>
				<div class="col-md-12">
					<div class="box">
						<div class="box-header with-border">
							 <h3 class="box-title"><?php echo ucfirst($language->language); ?></h3>
						</div> <!-- box-header -->
				
						<div class="box-body col-md-6">
							<div class="form-group">
								<label><?php echo trans('messages.Name') ?> <span class="req">*</span> :</label>
								<input type="text" class="form-control" name="cuisine[]" placeholder="<?php echo trans('messages.Enter cuisine name') ?>" value="<?php echo (Input::old('cuisine')[$i] == '') ? $cuisine_name->cuisine_name : Input::old('cuisine')[$i]; ?>">
								<input type="hidden" name="language[]" value="<?php echo $language->code; ?>">
								@if(count($array_valid))<p class="error_msg"><?php echo ($array_valid[$i] != '') ? trans('messages.Name field is required') : ''; ?></p>@endif
							</div><!-- form-group -->
						</div><!-- box-body -->                
				   </div>
				</div> <!-- col-md-6 -->		
			</div>
			<?php $i++; } } ?>
		
			</div> <!-- tab-content -->
		
		</div> <!-- nav-tabs-custom -->
		<div class="col-md-12">
				<div class="box">
						<div class="box-header with-border">
							 <h3 class="box-title"><?php echo trans('messages.Details') ?></h3>
						</div> <!-- box-header -->
                
					<div class="box-body">
					<div class="form-group full_selectList col-md-6">
						<label class="col-sm-3 control-label"><?php echo trans('messages.Status') ?> <span class="req">*</span> :</label>
						<div class="col-sm-8">
							<select class="selectList" name="status">
								<option value="1" ><?php echo trans('messages.Enabled') ?></option>
								<option value="0" <?php echo (Input::old('status') == '0' || $cuisine->status == 0) ? 'selected' : ''; ?>><?php echo trans('messages.Disabled') ?></option>
							</select>
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('status') != '') ? $error->first('status') : ''; ?></p>@endif
						</div>
					</div><!-- form-group -->
				
						
					</div><!-- box-body -->                
              </div>
			</div> <!-- col-md-12 -->
	
	<div class="box-footer">
		<input type="hidden" name="id" value="<?php echo $cuisine->id; ?>">
		<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Cuisine') ?></button>
		<button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset') ?></button>
		<button type="button" onclick="window.location.href='{!! URL::to('admin/categories') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel') ?></button>
    </div>
	{!! Form::close() !!}	
	</div> <!-- box -->
	</div>	
    </section><!-- content -->

</div><!-- content-wrapper -->
<?php 
function selectdrop($val1, $val2)
{
	$select = ($val1 == $val2) ? 'selected' : '';
	return $select; 
}
?>	 
<?php
function showvalidmsg($i, $array_valid)
{
	$active = '';
	if($i == 0)
	{
		$active = ($array_valid[$i] != '') ? 'active' : '';
	}
	else
	{
		$j = $i-1;	
		$active = ($array_valid[$i] != '' && $array_valid[$j] == '') ? 'active' : '';
	}
	return $active;
}
?>
@endsection     
