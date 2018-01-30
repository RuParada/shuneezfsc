@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Add Adminuser'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Fill the below fields'); ?></h3>
                    </div><!--box-header-->

                    {!! Form::open(array('url' => 'admin/addadminuser', 'class' => 'form-horizontal', 'files' => 1)) !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label"><?php echo trans('messages.Name'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='50' name="name" class="form-control" id="name" placeholder="<?php echo trans('messages.Name'); ?> " value="<?php echo (Input::old('name')) ? Input::old('name') : ''; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('name') != '') ? $error->first('name') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="username" class="col-sm-3 control-label"><?php echo trans('messages.Username'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='50' name="username" class="form-control" id="username" placeholder="<?php echo trans('messages.Username'); ?> " value="<?php echo (Input::old('username')) ? Input::old('username') : ''; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('username') != '') ? $error->first('username') : ''; ?></p>@endif
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
                            <label for="password" class="col-sm-3 control-label"><?php echo trans('messages.Password'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="password" name="password" class="form-control" id="password" placeholder="<?php echo trans('messages.Password'); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('password') != '') ? $error->first('password') : ''; ?></p>@endif
                            </div>
                        </div>
						
						<div class="form-group">
							<label for="add_privilege" class="col-sm-3 control-label"><?php echo trans('messages.Add Privilege'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 onoff">
								<input type="checkbox" name="add_privilege" <?php echo (Input::old('add_privilege') == 1) ? 'checked' : ''; ?> class="onoff_chck" id="add_privilege" value="1">
                                <label class="onoff_lbl" for="add_privilege"></label>
                            </div>
                        </div>
                        
                        <div class="form-group">
							<label for="edit_privilege" class="col-sm-3 control-label"><?php echo trans('messages.Edit Privilege'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 onoff">
								<input type="checkbox" name="edit_privilege" <?php echo (Input::old('edit_privilege') == 1) ? 'checked' : ''; ?> class="onoff_chck" id="edit_privilege" value="1">
                                <label class="onoff_lbl" for="edit_privilege"></label>
                            </div>
                        </div>
                        
                        <div class="form-group">
							<label for="delete_privilege" class="col-sm-3 control-label"><?php echo trans('messages.Delete Privilege'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 onoff">
								<input type="checkbox" name="delete_privilege" <?php echo (Input::old('delete_privilege') == 1) ? 'checked' : ''; ?> class="onoff_chck" id="delete_privilege" value="1">
                                <label class="onoff_lbl" for="delete_privilege"></label>
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
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Adminuser'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/adminusers') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
@endsection
