@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Edit Currency'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Edit the below fields'); ?></h3>
                    </div><!--box-header-->

                    {!! Form::open(array('url' => 'admin/updatecurrency', 'class' => 'form-horizontal')) !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="currency_name" class="col-sm-3 control-label"><?php echo trans('messages.Currency Name'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='30' name="currency_name" class="form-control" id="currency_name" placeholder="<?php echo trans('messages.Currency Name'); ?> " value="<?php echo (Input::old('currency_name')) ? Input::old('currency_name') : $currency->currency_name; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('currency_name') != '') ? $error->first('currency_name') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="currency_symbol" class="col-sm-3 control-label"><?php echo trans('messages.Currency Symbol'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='5' name="currency_symbol" class="form-control" id="currency_symbol" placeholder="<?php echo trans('messages.Currency Symbol'); ?> " value="<?php echo (Input::old('currency_symbol')) ? Input::old('currency_symbol') : $currency->currency_symbol; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('currency_symbol') != '') ? $error->first('currency_symbol') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="currency_code" class="col-sm-3 control-label"><?php echo trans('messages.Currency Code'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='5' name="currency_code" class="form-control" id="currency_code" placeholder="<?php echo trans('messages.Currency Code'); ?>" value="<?php echo (Input::old('currency_code')) ? Input::old('currency_code') : $currency->currency_code; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('currency_code') != '') ? $error->first('currency_code') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="currency_position" class="col-sm-3 control-label"><?php echo trans('messages.Curency Position'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 radio_btns">
                                <input type="radio" name="currency_position" value="l" checked><?php echo trans('messages.Left'); ?>
                                <input type="radio" name="currency_position" value="r" <?php echo (Input::old('currency_position') == 'r' || $currency->currency_position == 'r') ? 'checked' : ''; ?>><?php echo trans('messages.Right'); ?>
                            </div>
                        </div>
                         
                         <div class="form-group">
                            <label for="status" class="col-sm-3 control-label"><?php echo trans('messages.Status'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 radio_btns">
                                <input type="radio" name="status" value="1" checked><?php echo trans('messages.Active'); ?>
                                <input type="radio" name="status" value="0" <?php echo (Input::old('status') == '0' || $currency->status == 0) ? 'checked' : ''; ?>><?php echo trans('messages.Inactive'); ?>
                            </div>
                        </div>
                        
                         <div class="form-group">
                            <label for="default_currency" class="col-sm-3 control-label"><?php echo trans('messages.Default Currency'); ?></label>
                            <div class="col-sm-9 radio_btns">
                                <input type="checkbox" name="default_currency" value="1" <?php echo (Input::old('default_currency') == '1' || $currency->default_currency == 1) ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </div><!--box-body-->

                    <div class="box-footer">
						<input type="hidden" name="id" value="<?php echo $currency->id; ?>">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Currency'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/currencies') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
