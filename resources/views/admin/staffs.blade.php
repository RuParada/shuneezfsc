@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Staffs'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p>  
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/addstaff') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Staff'); ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filterstaffs', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Search Field'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group full_selectLists">
                        <label><?php echo trans('messages.Select Status'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-building"></i> </div>
                            <select class="selectLists" name="status">
                                <option value=""><?php echo trans('messages.Select Status'); ?></option>
                                <option value="1" <?php echo (Input::get('status') == '1') ? 'selected' : ''; ?>><?php echo trans('messages.Active'); ?> </option>
                                <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Staffs'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Branch'); ?></th>
                            <th><?php echo trans('messages.Name'); ?></th>
                            <th><?php echo trans('messages.Email'); ?></th>
                            <th><?php echo trans('messages.Mobile'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <th><?php echo trans('messages.Action'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($staffs) > 0) {
                            $i = ($staffs->currentPage() == 1) ? 1 : (($staffs->currentPage() - 1) * $staffs->perPage()) + 1;
                            foreach ($staffs as $staff) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $staff->branch; ?></td>
                                    <td><?php echo $staff->name; ?></td>
                                    <td><?php echo $staff->email; ?></td>
                                    <td><?php echo $staff->mobile; ?></td>
                                   <td class="onoff" width="50">
                                        <input type="checkbox" name="active_inactive" <?php echo ($staff->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $staff->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$staff->id.','.$staff->status.');"' : ''; ?>>
                                        <label class="onoff_lbl" for="<?php echo $staff->id; ?>"></label>
                                    </td>
                                    <td class="action_btns">
                                        <a href="<?php echo (Session('edit')) ? URL::to('/admin/getstaff/'.$staff->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                        <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_staff('.$staff->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Staffs Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($staffs->currentPage() == 1) {
                    $count = $staffs->count();
                } else if ($staffs->perPage() > $staffs->count()) {
                    $count = ($staffs->currentPage() - 1) * $staffs->perPage() + $staffs->count();
                } else {
                    $count = $staffs->currentPage() * $staffs->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($staffs->currentPage() == 1) ? 1 : ($staffs->perPage() * ($staffs->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $staffs->total() . " ".trans('messages.entries')."</span>";
                echo $staffs->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_staffstatus",
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

    function delete_staff(id)
    {
		var url = "<?php echo URL::to(''); ?>";
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = url+'/admin/deletestaff/' + id;
        }
    }
</script>
@endsection
