@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Edit User'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Edit the below fields'); ?></h3>
                    </div><!--box-header-->

                    {!! Form::open(array('url' => 'admin/updateuser', 'class' => 'form-horizontal', 'files' => 1)) !!}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="first_name" class="col-sm-3 control-label"><?php echo trans('messages.First Name'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='30' name="first_name" class="form-control" id="name" placeholder="<?php echo trans('messages.First Name'); ?>" value="<?php echo (Input::old('first_name')) ? Input::old('first_name') : $user->first_name; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('first_name') != '') ? $error->first('first_name') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name" class="col-sm-3 control-label"><?php echo trans('messages.Last Name'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='30' name="last_name" class="form-control" id="name" placeholder="<?php echo trans('messages.Last Name'); ?>" value="<?php echo (Input::old('last_name')) ? Input::old('last_name') : $user->last_name; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('last_name') != '') ? $error->first('last_name') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label"><?php echo trans('messages.Email'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='75' name="email" class="form-control" id="email" placeholder="<?php echo trans('messages.Email'); ?>" value="<?php echo (Input::old('email')) ? Input::old('email') : $user->email; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mobile" class="col-sm-3 control-label"><?php echo trans('messages.Mobile'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='15' name="mobile" class="form-control" id="mobile" placeholder="<?php echo trans('messages.Mobile'); ?>" value="<?php echo (Input::old('mobile')) ? Input::old('mobile') : $user->mobile; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label"><?php echo trans('messages.Status'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 radio_btns">
                                <input type="radio" name="status" value="1" checked><?php echo trans('messages.Active'); ?>
                                <input type="radio" name="status" value="0" <?php echo (Input::old('status') == '0' || $user->status == 0) ? 'checked' : ''; ?>><?php echo trans('messages.Inactive'); ?>
                            </div>
                        </div>
                    </div><!--box-body-->

                    <div class="box-footer">
                        <input type="hidden" name="id" value="<?php echo $user->id; ?>" >
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update User'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Reset'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/users') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<div class="modal"></div>
<script>

    function setchangeimg(val)
    {
        $('#txt_changeprofileimage').html(val);
    }
    $(document).on("change", "#upload_img", function () {
        console.log("The text has been changed.");
        var file = this.files[0];
        var imagefile = file.type;
        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
        {
            document.getElementById('error').innerHTML = 'The image must be a file of type: jpg, jpeg, png';
            return false;
        } else
        {
            document.getElementById('error').innerHTML = '';
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
        $("#txt_changeprofileimage").html(file.name);
    });

    function imageIsLoaded(e)
    {
        var image = new Image();
        image.src = e.target.result;
        image.onload = function () {
            $(".roundedimg").attr('src', e.target.result);
        }
    }

    function getstate()
    {
        var country_id = $("#country_id option:selected").val();
        $.ajax({
            beforeSend: function () {
                $("body").addClass("loading");
            },
            type: "GET",
            dataType: "json",
            url: "<?php echo URL::to('admin/getstate'); ?>",
            data: {'country_id': country_id},
            async: true,
            success: function (result) {
                document.getElementById("select2-state-container").innerHTML = "Select State";
                $("#state").html(result.statelist);
                $("body").removeClass("loading");
            }
        });
    }

    function getcity()
    {
        var state_id = $("#state_id option:selected").val();
        $.ajax({
            beforeSend: function () {
                $("body").addClass("loading");
            },
            type: "GET",
            dataType: "json",
            url: "<?php echo URL::to('admin/getcity'); ?>",
            data: {'state_id': state_id},
            async: true,
            success: function (result) {
                document.getElementById("select2-city-container").innerHTML = "Select City";
                $("#city").html(result.citylist);
                $("body").removeClass("loading");
            }
        });
    }
    
    function get_timezone()
    {
        var state = $("#state option:selected").text();
        var city = $("#city option:selected").text();
       
        $.ajax({
            beforeSend: function () {
                $("body").addClass("loading");
            },
            type: "GET",
            dataType: "json",
            url: "<?php echo URL::to('get_timezone'); ?>",
            data: {'state': state, 'city': city},
            async: true,
            success: function (result) {
                //alert(result.timeZoneId)
                $("#timezone").val(result.timeZoneId);
                $("body").removeClass("loading");
            }
        });
    }
</script>
<style>
    .modal {
        display:    none;
        position:   fixed;
        z-index:    1000;
        top:        0;
        left:       0;
        height:     100%;
        width:      100%;
        background: rgba( 255, 255, 255, .8 ) 
            url('http://i.stack.imgur.com/FhHRx.gif') 
            50% 50% 
            no-repeat;
    }


    body.loading {
        overflow: hidden;   
    }

    body.loading .modal {
        display: block;
    }
</style>
<?php

function selectdrop($val1, $val2) {
    $select = ($val1 == $val2) ? 'selected' : '';
    return $select;
}
?>	
@endsection
