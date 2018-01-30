@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.View Enquiry'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.View the below fields'); ?></h3>
                    </div><!--box-header-->

                    
                    <div class="box-body">
                        {!! Form::open(array('class' => 'form-horizontal')) !!}
                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label"><?php echo trans('messages.Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?php echo $enquiry->name; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label"><?php echo trans('messages.Email'); ?> </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?php echo $enquiry->email; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mobile" class="col-sm-3 control-label"><?php echo trans('messages.Mobile'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?php echo $enquiry->mobile; ?>">
                            </div>
                        </div>

                         <div class="form-group">
                            <label for="subject" class="col-sm-3 control-label"><?php echo trans('messages.Subject'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?php echo $enquiry->subject; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message" class="col-sm-3 control-label"><?php echo trans('messages.Message'); ?></label>
                            <div class="col-sm-9">
                                <textarea class="form-control"><?php echo $enquiry->message; ?></textarea>
                            </div>
                        </div>
                        
                    <div class="box-footer">
                       <button type="button" onclick="window.location.href = '{!! URL::to('admin/enquires') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Back'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
