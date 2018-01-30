@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Add Staff'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Fill the below fields'); ?></h3>
                    </div><!--box-header-->

                    {!! Form::open(array('url' => 'admin/addstaff', 'class' => 'form-horizontal', 'files' => 1)) !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="first_name" class="col-sm-3 control-label"><?php echo trans('messages.Select Branch'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 full_selectLists">
                                <select class="selectLists" name="branch">
								<option value=""></option>
								<?php if(count($branches) > 0) { 
									foreach($branches as $branch) {
										$select = selectdrop(Input::old('branch'), $branch->id);
								?>
								<option value="<?php echo $branch->id; ?>" <?php echo $select; ?>><?php echo $branch->branch; ?></option>
								<?php } } ?>
							</select>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('branch') != '') ? $error->first('branch') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="first_name" class="col-sm-3 control-label"><?php echo trans('messages.Name'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='30' name="name" class="form-control" id="name" placeholder="<?php echo trans('messages.Name'); ?> " value="<?php echo (Input::old('name')) ? Input::old('name') : ''; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('name') != '') ? $error->first('name') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label"><?php echo trans('messages.Email'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='75' name="email" class="form-control" id="email" placeholder="<?php echo trans('messages.Email'); ?>" value="<?php echo (Input::old('email')) ? Input::old('email') : ''; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mobile" class="col-sm-3 control-label"><?php echo trans('messages.Mobile'); ?><span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='15' name="mobile" class="form-control" id="mobile" placeholder="<?php echo trans('messages.Mobile'); ?>" value="<?php echo (Input::old('mobile')) ? Input::old('mobile') : ''; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-sm-3 control-label"><?php echo trans('messages.Password'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" name="password" class="form-control" id="password" placeholder="<?php echo trans('messages.Password'); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('password') != '') ? $error->first('password') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile" class="col-sm-3 control-label"><?php echo trans('messages.Address'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" name="address" class="form-control" id="address" placeholder="<?php echo trans('messages.Address'); ?>" value="<?php echo (Input::old('address')) ? Input::old('address') : ''; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('address') != '') ? $error->first('address') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                         <div class="form-group">
                            <label for="status" class="col-sm-3 control-label"><?php echo trans('messages.Status'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 radio_btns">
                                <input type="radio" name="status" value="1" checked><?php echo trans('messages.Active'); ?>
                                <input type="radio" name="status" value="0" <?php echo (Input::old('status') == '0') ? 'checked' : ''; ?>><?php echo trans('messages.Inactive'); ?>
                            </div>
                        </div>
                    </div><!--box-body-->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Staff'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/staffs') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php 
function selectdrop($val1, $val2)
{
	$select = ($val1 == $val2) ? 'selected' : '';
	return $select; 
}
?>	
@endsection
