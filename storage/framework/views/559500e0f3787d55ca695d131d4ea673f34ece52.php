<?php $__env->startSection('content'); ?>
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Categories'); ?></h1>
        <?php if(Session::has('success')): ?><p class="success_msg"><?php echo Session::get('success'); ?></p><?php endif; ?>
        <p id="success_msg"></p> 
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="<?php echo URL::to('admin/addcategory'); ?>" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.New Category'); ?></a></p><?php } ?>
                <div class="row">
                    <?php echo Form::open(array('url' => 'admin/filtercategories', 'method' => 'get')); ?>

                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Category'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-cutlery"></i> </div>
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
                                <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?> </option>
                                <option value="deleted" <?php echo (Input::get('status') == 'deleted') ? 'selected' : ''; ?>><?php echo trans('messages.Deleted'); ?></option>
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <?php echo Form::close(); ?>

                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Category'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <?php if ($seg != 'deleted') { ?><th><?php echo trans('messages.Action'); ?></th><?php } ?>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($categories) > 0) {
                            $i = ($categories->currentPage() == 1) ? 1 : (($categories->currentPage() - 1) * $categories->perPage()) + 1;
                            foreach ($categories as $category) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $category->category; ?></td>
									<td class="onoff" width="50">
                                        <?php if ($category->is_delete == 0) {
                                            ?>
                                            <input type="checkbox" name="active_inactive" <?php echo ($category->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $category->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$category->id.','.$category->status.');"' : ''; ?>>
                                            <label class="onoff_lbl" for="<?php echo $category->id; ?>"></label>
                                        <?php } else { ?>
                                            <a href="#" class="delete_btn" title="Deleted"><i class="fa fa-close"></i></a>
                                        <?php } ?>
                                    </td>
                                    <td class="action_btns" width="100">
                                        <?php if ($category->is_delete == 0) { ?>
                                            <a href="<?php echo (Session('edit')) ? URL::to('/admin/editcategory/'.$category->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                            <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_category('.$category->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                        <?php } else { ?>
                                            <a href="#" <?php echo (Session('edit')) ? 'onclick="restore_category('.$category->id.');"' : ''; ?> title="Restore" class="edit_btn"><i class="fa fa-reply-all"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="5" style="text-align:center">No Categories Found</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($categories->currentPage() == 1) {
                    $count = $categories->count();
                } else if ($categories->perPage() > $categories->count()) {
                    $count = ($categories->currentPage() - 1) * $categories->perPage() + $categories->count();
                } else {
                    $count = $categories->currentPage() * $categories->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($categories->currentPage() == 1) ? 1 : ($categories->perPage() * ($categories->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'> ".trans('messages.Showing')." " . $start . trans('messages.to') . $count .trans('messages.of') . $categories->total() . " ".trans('messages.entries')." </span>";
                echo $categories->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_categorystatus",
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

    function delete_category(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deletecategory/' + id;
        }
    }

    function restore_category(id)
    {
        if (confirm('<?php echo trans("messages.Restore Confirmation"); ?>'))
        {
            window.location = 'restorecategory/' + id;
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminheader', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>