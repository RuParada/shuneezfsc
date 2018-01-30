@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Cuisines') ?></h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
        @if(Session::has('error'))<p class="error_msg"><?php echo Session::get('error'); ?></p>@endif
        <p id="success_msg"></p> 
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/addcuisine') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Cuisines') ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filtercuisines', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Cuisines') ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-cutlery"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group full_selectLists">
                        <label><?php echo trans('messages.Select Status'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-building"></i> </div>
                            <select class="selectLists" name="status">
                                <option value=""><?php echo trans('messages.Select Status') ?></option>
                                <option value="1" <?php echo (Input::get('status') == '1') ? 'selected' : ''; ?>><?php echo trans('messages.Active') ?> </option>
                                <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive') ?></option>
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter') ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Cuisines') ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th><?php echo trans('messages.Cuisines') ?></th>
                            <th><?php echo trans('messages.Status') ?></th>
                            <th><?php echo trans('messages.Action') ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($cuisines) > 0) {
                            $i = ($cuisines->currentPage() == 1) ? 1 : (($cuisines->currentPage() - 1) * $cuisines->perPage()) + 1;
                            foreach ($cuisines as $cuisine) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $cuisine->cuisine; ?></td>
									<td class="onoff" width="50">
										<input type="checkbox" name="active_inactive" <?php echo ($cuisine->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $cuisine->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$cuisine->id.','.$cuisine->status.');"' : ''; ?>>
										<label class="onoff_lbl" for="<?php echo $cuisine->id; ?>"></label>
                                    </td>
                                    <td class="action_btns" width="70">
                                        <a href="<?php echo (Session('edit')) ? URL::to('/admin/editcuisine/'.$cuisine->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
										<a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_cuisine('.$cuisine->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="5" style="text-align:center"><?php echo trans('messages.No Cuisines Found') ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($cuisines->currentPage() == 1) {
                    $count = $cuisines->count();
                } else if ($cuisines->perPage() > $cuisines->count()) {
                    $count = ($cuisines->currentPage() - 1) * $cuisines->perPage() + $cuisines->count();
                } else {
                    $count = $cuisines->perPage() + $cuisines->count();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($cuisines->currentPage() == 1) ? 1 : $count - $cuisines->count() + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>Showing " . $start . " to " . $count . " of " . $cuisines->total() . " entries</span>";
                echo $cuisines->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
                echo '</div>';
                ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->		  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    function status_change(id, status)
    {
        var url = "<?php echo URL::to(''); ?>";
        $.ajax({
            type: "GET",
            url: url + "/admin/change_cuisinestatus",
            dataType: "json",
            data: {'id': id, 'status': status},
            async: true,
            success: function (result) {
                var newstatus = (status == 1) ? 0 : 1;
                $("#" + id).attr("onclick", "status_change(" + id + "," + newstatus + ");");
                $('#success_msg').attr('class', 'success_msg');
                $('#s_msg').css('display', 'none');
                $('#success_msg').html(result.msg);
            }
        });
    }

    function delete_cuisine(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deletecuisine/' + id;
        }
    }

</script>
@endsection
