<?php $__env->startSection('content'); ?>
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Users'); ?> </h1>
        <?php if(Session::has('success')): ?><p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p><?php endif; ?>
        <p id="success_msg"></p>  
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="<?php echo URL::to('admin/adduser'); ?>" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New User'); ?></a></p><?php } ?>
                <div class="row">
                    <?php echo Form::open(array('url' => 'admin/filterusers', 'method' => 'get')); ?>

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
                                <option value="deleted" <?php echo (Input::get('status') == 'deleted') ? 'selected' : ''; ?>><?php echo trans('messages.Deleted'); ?></option>
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
                            <th><?php echo trans('messages.First Name'); ?></th>
                            <th><?php echo trans('messages.Last Name'); ?></th>
                            <th><?php echo trans('messages.Email'); ?></th>
                            <th><?php echo trans('messages.Mobile'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <?php if ($seg != 'deleted') { ?><th><?php echo trans('messages.Action'); ?></th><?php } ?>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($users) > 0) {
                            $i = ($users->currentPage() == 1) ? 1 : (($users->currentPage() - 1) * $users->perPage()) + 1;
                            foreach ($users as $user) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $user->first_name; ?></td>
                                    <td><?php echo $user->last_name; ?></td>
                                    <td><?php echo $user->email; ?></td>
                                    <td><?php echo $user->mobile; ?></td>
                                   <td class="onoff" width="50">
                                        <?php if ($user->is_delete == 0) {
                                            ?>
                                            <input type="checkbox" name="active_inactive" <?php echo ($user->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $user->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$user->id.','.$user->status.');"' : ''; ?>>
                                            <label class="onoff_lbl" for="<?php echo $user->id; ?>"></label>
                                        <?php } else { ?>
                                            <a href="#" class="delete_btn" title="Deleted"><i class="fa fa-close"></i></a>
                                        <?php } ?>
                                    </td>
                                    <td class="action_btns">
                                        <?php if ($user->is_delete == 0) { ?>
                                            <a href="<?php echo (Session('edit')) ? URL::to('/admin/getuser/'.$user->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                            <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_user('.$user->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                        <?php } else { ?>
                                            <a href="#" <?php echo (Session('edit')) ? 'onclick="restore_user('.$user->id.');"' : ''; ?> title="Restore" class="edit_btn"><i class="fa fa-reply-all"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Users Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($users->currentPage() == 1) {
                    $count = $users->count();
                } else if ($users->perPage() > $users->count()) {
                    $count = ($users->currentPage() - 1) * $users->perPage() + $users->count();
                } else {
                    $count = $users->currentPage() * $users->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($users->currentPage() == 1) ? 1 : ($users->perPage() * ($users->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $users->total() . " ".trans('messages.entries')."</span>";
                echo $users->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_userstatus",
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

    function delete_user(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deleteuser/' + id;
        }
    }

    function restore_user(id)
    {
        if (confirm('<?php echo trans("messages.Restore Confirmation"); ?>'))
        {
            window.location = 'restoreuser/' + id;
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminheader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>