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
        <h1>Edit Page</h1>
        @if(Session::has('error')) <p class="error_msg"> Required(*) fields are missing</p> @endif
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            {!! Form::open(array('url' => 'admin/updatecms', 'class' => 'form-horizontal')) !!}
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title">Edit the below fields</h3>
                    </div><!--box-header-->		

                    <div class="box-body">
                        <div class="form-group">
                            <label for="title" class="col-sm-3 control-label">Page Title <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="title" class="form-control" placeholder="Page Title " value="<?php echo (Input::old('title') != '') ? Input::old('title') : $page->title; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('title') != '') ? $error->first('title') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="order" class="col-sm-3 control-label">Sort Order</label>
                            <div class="col-sm-9">
                                <input type="text" name="order" class="form-control" placeholder="Sort Order " value="<?php echo (Input::old('order') != '') ? Input::old('order') : $page->order; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('order') != '') ? $error->first('order') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group product_desc">
                            <label for="description" class="col-sm-12 control-label">Page Description <span class="req">*</span></label>
                            <div class="col-sm-12">
                                <textarea class="textarea" name="description" placeholder="Place some text here"><?php echo (Input::old('description') != '') ? Input::old('description') : $page->description; ?></textarea>
                            </div>
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('description') != '') ? $error->first('description') : ''; ?></p>@endif
                        </div>
                        
                        <div class="form-group">
                            <label for="title" class="col-sm-3 control-label"> Meta Title</label>
                            <div class="col-sm-9">
                                <input type="text" name="meta_title" class="form-control" placeholder="Meta Title " value="<?php echo (Input::old('meta_title') != '') ? Input::old('meta_title') : $page->meta_title; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('meta_title') != '') ? $error->first('meta_title') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="title" class="col-sm-3 control-label"> Meta Keywords</label>
                            <div class="col-sm-9">
                                <input type="text" name="meta_keyword" class="form-control" placeholder="Meta Keywords" value="<?php echo (Input::old('meta_keyword') != '') ? Input::old('meta_keyword') : $page->meta_keyword; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('meta_keyword') != '') ? $error->first('meta_keyword') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="title" class="col-sm-3 control-label"> Meta Description</label>
                            <div class="col-sm-9">
                                <input type="text" name="meta_description" class="form-control" placeholder="Meta Description" value="<?php echo (Input::old('meta_description') != '') ? Input::old('meta_description') : $page->meta_description; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('meta_description') != '') ? $error->first('meta_description') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label">Status <span class="req">*</span></label>
                            <div class="col-sm-9 radio_btns">
                                <label><input type="radio" name="status" value="1" checked>Active</label>
                                <label><input type="radio" name="status" value="0" <?php echo (Input::old('status') == '0' || $page->status == 0) ? 'checked' : ''; ?>>InActive</label>
                            </div>
                        </div>
                    </div><!--box-body--> 
                    <div class="box-footer">
                        <input type="hidden" name="id" value="<?php echo $page->id; ?>">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i>Update Page</button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i>Clear</button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/cms') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i>Cancel</button>
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
