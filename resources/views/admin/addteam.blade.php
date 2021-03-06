@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Add Team'); ?> </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">

                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Fill the below fields'); ?></h3>
                    </div><!--box-header-->

                    {!! Form::open(array('url' => 'admin/create-team', 'class' => 'form-horizontal')) !!}
                    <div class="box-body">
                        <div class="form-group full_selectList">
                            <label class="col-sm-3 control-label"><?php echo trans('messages.Branch'); ?> <span class="req">*</span> :</label>
                            <div class="col-sm-9">
                                <select class="selectLists" name="branch">
                                    <option value="">{!! trans('Select Branch') !!}</option>
                                    <?php if(count($branches) > 0) { 
                                        foreach($branches as $branch) {
                                            $select = selectdrop(Input::old('branch'), $branch->id);
                                    ?>
                                    <option value="<?php echo $branch->id; ?>" <?php echo $select; ?>><?php echo $branch->branch; ?></option>
                                    <?php } } ?>
                                </select>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('branch') != '') ? $error->first('branch') : ''; ?></p>@endif
                            </div>
                        </div><!-- form-group -->
                        <div class="form-group">
                            <label for="first_name" class="col-sm-3 control-label"><?php echo trans('messages.Name'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" maxlength='30' name="name" class="form-control" id="name" placeholder="<?php echo trans('messages.Name'); ?> " value="<?php echo (Input::old('name')) ? Input::old('name') : ''; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('name') != '') ? $error->first('name') : ''; ?></p>@endif
                            </div>
                        </div>
                        
                        <div class="form-group full_selectList">
                            <label class="col-sm-3 control-label"><?php echo trans('messages.Country'); ?> <span class="req">*</span> :</label>
                            <div class="col-sm-9">
                                <select class="selectLists" name="country" id="country" onchange="getcity();">
                                    <option value="">{!! trans('messages.Select City') !!}</option>
                                    <?php if(count($countries) > 0) { 
                                        foreach($countries as $country) {
                                            $select = selectdrop(Input::old('country'), $country->id);
                                    ?>
                                    <option value="<?php echo $country->id; ?>" <?php echo $select; ?>><?php echo $country->name->en; ?></option>
                                    <?php } } ?>
                                </select>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('country') != '') ? $error->first('country') : ''; ?></p>@endif
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group full_selectList">
                            <label class="col-sm-3 control-label"><?php echo trans('messages.City'); ?> <span class="req">*</span> :</label>
                            <div class="col-sm-9">
                                <select class="selectLists" name="city" id="city" onchange="getCityName();">
                                    <option value="">{!! trans('Select City') !!}</option>
                                </select>
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('city') != '') ? $error->first('city') : ''; ?></p>@endif
                            </div>
                        </div><!-- form-group -->

                        <div class="form-group">
                            <label for="status" class="col-sm-3 control-label"><?php echo trans('messages.Auto Assign'); ?> <span class="req">*</span></label>
                            <div class="col-sm-9 radio_btns">
                                <input type="radio" name="auto_assign" value="1" checked><?php echo trans('messages.On'); ?>
                                <input type="radio" name="auto_assign" value="0" <?php echo (Input::old('auto_assign') == '0') ? 'checked' : ''; ?>><?php echo trans('messages.Off'); ?>
                            </div>
                        </div>
                    </div><!--box-body-->

                    <div class="box-footer">
                        <input type="hidden" name="country_name" id="country_name">
                        <input type="hidden" name="city_name" id="city_name">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Save Team'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Clear'); ?></button>
                        <button type="button" onclick="window.location.href = '{!! URL::to('admin/teams') !!}'" class="btn pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}

                </div><!--box-info-->

            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<div class="modal"></div>
<?php 
function selectdrop($val1, $val2)
{
    $select = ($val1 == $val2) ? 'selected' : '';
    return $select; 
}
?>
<script>

    function getcity()
    {
        var country_id = $("#country option:selected").val();
        $.ajax({
            beforeSend: function () {
                $("body").addClass("loading");
            },
            type: "GET",
            dataType: "json",
            url: "<?php echo URL::to('admin/get-dook-city'); ?>",
            data: {'country_id': country_id},
            async: true,
            success: function (result) {
                document.getElementById("select2-city-container").innerHTML = "Select City";
                $("#city").html(result.citylist);
                $("#country_name").val($("#country option:selected").text());
                $("body").removeClass("loading");
            }
        });
    }

    function getCityName()
    {
        var city = $("#city option:selected").val();
        $("#city_name").val($("#city option:selected").text());
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
@endsection
