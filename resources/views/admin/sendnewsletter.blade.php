@extends('adminheader')

@section('content')
<style>
    .control-label {
        text-align:left !important;
    }
</style>
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif

<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Send Newsletter'); ?></h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            {!! Form::open(array('url' => 'admin/sendnewsletter', 'class' => 'form-horizontal')) !!}
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Fill the below fields'); ?></h3>
                    </div><!--box-header-->		

                    <div class="box-body">
                        <div class="form-group">
                            <label for="subject" class="col-sm-3 control-label"><?php echo trans('messages.Subject'); ?><span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="subject" class="form-control" placeholder="<?php echo trans('messages.Subject'); ?>" value="<?php echo Input::old('subject'); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('subject') != '') ? $error->first('subject') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group product_desc">
                            <label for="description" class="col-sm-12 control-label"><?php echo trans('messages.Description'); ?> <span class="req">*</span></label>
                            <div class="col-sm-12">
                                <textarea class="textarea" name="description" placeholder="<?php echo trans('messages.Description'); ?>"><?php echo Input::old('description'); ?></textarea>
                            </div>
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('description') != '') ? $error->first('description') : ''; ?></p>@endif
                        </div>
                        
                        
                    </div><!--box-body--> 
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Send Newsletter'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                        
                    </div><!-- box-footer -->      

                </div><!--box-info-->
            </div>	  

            {!! Form::close() !!}	          
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    $(function () {
        $(".textarea").wysihtml5();
    });
</script>

@endsection
