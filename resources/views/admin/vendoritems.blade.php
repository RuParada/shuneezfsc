@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Items'); ?></h1>
        @if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif
        @if(Session::has('delete_error'))<p class="error_msg"><?php echo Session::get('delete_error'); ?></p>@endif
        <p id="success_msg"></p> 
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/addvendor_item') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Item'); ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filtervendor_items', 'method' => 'get')) !!}
                    <div class="col-md-3 form-group">
                        <label><?php echo trans('messages.Item'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-cutlery"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                   <div class="col-md-3 form-group full_selectLists">
                        <label><?php echo trans('messages.Select Category'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-building"></i> </div>
                            <select class="selectLists" name="category">
								<option value=""></option>
                                <?php if(count($categories) > 0) {
								   foreach($categories as $category) { 
									   $select = ($category->id == Input::get('category')) ? 'selected' : ''; 
								?>
                               <option <?php echo $select; ?> value="<?php echo $category->id; ?>" ><?php echo $category->category; ?> </option>
                                <?php } } ?>
                            </select>
                        </div>
                    </div> <!-- form-group -->
                    <div class="col-md-3 form-group full_selectLists">
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


                    <div class="col-md-3 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Items'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Item'); ?></th>
                            <th><?php echo trans('messages.Category'); ?></th>
                            <th><?php echo trans('messages.Sort'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <?php if ($seg != 'deleted') { ?><th><?php echo trans('messages.Action'); ?></th><?php } ?>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($items) > 0) {
                            $i = ($items->currentPage() == 1) ? 1 : (($items->currentPage() - 1) * $items->perPage()) + 1;
                            foreach ($items as $item) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $item->item; ?></td>
                                    <td><?php echo $item->category; ?></td>
                                    <td><?php echo $item->sort_number; ?></td>
                                    <td class="onoff" width="50">
                                        <?php if ($item->is_delete == 0) {
                                            ?>
                                            <input type="checkbox" name="active_inactive" <?php echo ($item->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $item->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$item->id.','.$item->status.');"' : ''; ?>>
                                            <label class="onoff_lbl" for="<?php echo $item->id; ?>"></label>
                                        <?php } else { ?>
                                            <a href="#" class="delete_btn" title="Deleted"><i class="fa fa-close"></i></a>
                                        <?php } ?>
                                    </td>
                                    <td class="action_btns" width="80">
                                        <?php if ($item->is_delete == 0) { ?>
                                            <a href="<?php echo (Session('edit')) ? URL::to('/admin/editvendor_item/'.$item->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                            <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_item('.$item->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                        <?php } else { ?>
                                            <a href="#" <?php echo (Session('edit')) ? 'onclick="restore_item('.$item->id.');"' : ''; ?> title="Restore" class="edit_btn"><i class="fa fa-reply-all"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="7" style="text-align:center"><?php echo trans('messages.No Items Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($items->currentPage() == 1) {
                    $count = $items->count();
                } else if ($items->perPage() > $items->count()) {
                    $count = ($items->currentPage() - 1) * $items->perPage() + $items->count();
                } else {
                    $count = $items->currentPage() * $items->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($items->currentPage() == 1) ? 1 : ($items->perPage() * ($items->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'> " .trans('messages.Showing')." ". $start." ".trans('messages.to') . $count .trans('messages.of') . $items->total() . " ".trans('messages.entries')." </span>";
                echo $items->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_vendoritemstatus",
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

    function delete_item(id)
    {
		if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deletevendoritem/' + id;
        }
    }

    function restore_item(id)
    {
        if (confirm('<?php echo trans("messages.Restore Confirmation"); ?>'))
        {
            window.location = 'restorevendoritem/' + id;
        }
    }
</script>
@endsection
