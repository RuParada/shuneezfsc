@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Edit Promocode'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Edit the below fields'); ?></h3>
                    </div><!--box-header-->

                    {!! Form::open(array('url' => 'admin/updatepromocode', 'class' => 'form-horizontal', 'files' => 1)) !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="promocode" class="col-sm-3 control-label"><?php echo trans('messages.Promocode'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='20' name="promocode" class="form-control" id="promocode" placeholder="<?php echo trans('messages.Promocode'); ?> " value="<?php echo (Input::old('promocode')) ? Input::old('promocode') : $promocode->promocode; ?>">
                                <a href="#" onclick="generate_promocode();"><?php echo trans('messages.Generate Promocode'); ?></a>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('promocode') != '') ? $error->first('promocode') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount" class="col-sm-3 control-label"><?php echo trans('messages.Amount'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='30' name="amount" class="form-control" id="amount" placeholder="<?php echo trans('messages.Amount'); ?> " value="<?php echo (Input::old('amount')) ? Input::old('amount') : $promocode->amount; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('amount') != '') ? $error->first('amount') : ''; ?></p>@endif
                            </div>
                        </div>

						<div class="form-group">
                            <label for="expiry_date" class="col-sm-3 control-label"><?php echo trans('messages.Expiry Date'); ?><span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="expiry_date" class="form-control" id="dpicToday" placeholder="<?php echo trans('messages.Expiry Date'); ?>" value="<?php echo (Input::old('expiry_date')) ? date('d-m-Y', strtotime(Input::old('expiry_date'))) : date('d-m-Y', strtotime($promocode->expiry_date)); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('expiry_date') != '') ? $error->first('expiry_date') : ''; ?></p>@endif
                            </div>
                        </div>

                       <div class="form-group">
                            <label for="discount_type" class="col-sm-3 control-label"><?php echo trans('messages.Discount Type'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 radio_btns">
                                <input type="radio" name="discount_type" value="a" <?php echo (Input::old('discount_type') == 'a' || $promocode->discount_type == 'a') ? 'checked' : ''; ?>><?php echo trans('messages.Amount Based'); ?>
                                <input type="radio" name="discount_type" value="p" <?php echo (Input::old('discount_type') == 'p' || $promocode->discount_type == 'p') ? 'checked' : ''; ?>><?php echo trans('messages.Percentage Based'); ?>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('discount_type') != '') ? $error->first('discount_type') : ''; ?></p>@endif
                            </div>
                        </div>
                    </div><!--box-body-->

                    <div class="box-footer">
						<input type="hidden" name="id" value="<?php echo $promocode->id; ?>">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Promocode'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/promocodes') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>
function generate_promocode()
{ 
	var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    for( var i=0; i < 8; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    $('#promocode').val(text);
}
</script>
@endsection
