@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Add Address Type'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Fill the below fields'); ?></h3>
                    </div><!--box-header-->

                    {!! Form::open(array('url' => 'admin/add_addresstype', 'class' => 'form-horizontal')) !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="addresstype" class="col-sm-3 control-label"><?php echo trans('messages.Address Type'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='75' name="addresstype" class="form-control" id="addresstype" placeholder="<?php echo trans('messages.Address Type'); ?> " value="<?php echo Input::old('addresstype'); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('addresstype') != '') ? $error->first('addresstype') : ''; ?></p>@endif
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
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Address Type'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/addresstype') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
