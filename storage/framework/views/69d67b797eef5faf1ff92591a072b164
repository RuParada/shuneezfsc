<?php $__env->startSection('content'); ?>
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Adminusers'); ?> </h1>
        <?php if(Session::has('success')): ?><p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p><?php endif; ?>
        <p id="success_msg"></p>  
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="<?php echo URL::to('admin/addadminuser'); ?>" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Admin'); ?></a></p><?php } ?>
                <div class="row">
                    <?php echo Form::open(array('url' => 'admin/filteradminusers', 'method' => 'get')); ?>

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
                            <select class="selectList" name="status">
                                <option value=""><?php echo trans('messages.Select Status'); ?></option>
                                <option value="1" <?php echo (Input::get('status') == '1') ? 'selected' : ''; ?>><?php echo trans('messages.Active'); ?> </option>
                                <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search User'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <?php echo Form::close(); ?>

                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Name'); ?></th>
                            <th><?php echo trans('messages.Username'); ?></th>
                            <th><?php echo trans('messages.Email'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <th><?php echo trans('messages.Action'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($adminusers) > 0) {
                            $i = ($adminusers->currentPage() == 1) ? 1 : (($adminusers->currentPage() - 1) * $adminusers->perPage()) + 1;
                            foreach ($adminusers as $adminuser) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $adminuser->name; ?></td>
                                    <td><?php echo $adminuser->username; ?></td>
                                    <td><?php echo $adminuser->email; ?></td>
                                   <td class="onoff" width="50">
                                        <input type="checkbox" name="active_inactive" <?php echo ($adminuser->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $adminuser->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$adminuser->id.','.$adminuser->status.');"' : ''; ?>>
                                        <label class="onoff_lbl" for="<?php echo $adminuser->id; ?>"></label>
                                    </td>
                                    <td class="action_btns">
                                        <a href="<?php echo (Session('edit')) ? URL::to('/admin/getadminuser/'.$adminuser->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                         <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_adminuser('.$adminuser->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Adminusers Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($adminusers->currentPage() == 1) {
                    $count = $adminusers->count();
                } else if ($adminusers->perPage() > $adminusers->count()) {
                    $count = ($adminusers->currentPage() - 1) * $adminusers->perPage() + $adminusers->count();
                } else {
                    $count = $adminusers->currentPage() * $adminusers->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($adminusers->currentPage() == 1) ? 1 : ($adminusers->perPage() * ($adminusers->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $adminusers->total() . " ".trans('messages.entries')."</span>";
                echo $adminusers->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_adminuserstatus",
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

    function delete_adminuser(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deleteadminuser/' + id;
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminheader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>