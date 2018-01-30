@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Deliveryboys'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p> 
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/adddeliveryboy') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Deliveryboy'); ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filterdeliveryboy', 'method' => 'get')) !!}
                    <div class="col-md-3 form-group">
                        <label><?php echo trans('messages.Search Field'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-cutlery"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-3 form-group full_selectLists">
                        <label><?php echo trans('messages.Select Branch'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-building"></i> </div>
                            <select class="selectLists" name="branch">
                                <option value=""></option>
								<?php if(count($branches) > 0) { 
									foreach($branches as $branch) {
										$select = (Input::get('branch') == $branch->id) ? 'selected' : '';
								?>
								<option value="<?php echo $branch->id; ?>" <?php echo $select; ?>><?php echo $branch->branch; ?></option>
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
                                <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
                                <option value="deleted" <?php echo (Input::get('status') == 'deleted') ? 'selected' : ''; ?>><?php echo trans('messages.Deleted'); ?></option>
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-3 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Deliveryboy'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Name'); ?></th>
                            <th><?php echo trans('messages.Email'); ?></th>
                            <th><?php echo trans('messages.Mobile'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <?php if ($seg != 'deleted') { ?><th><?php echo trans('messages.Action'); ?></th><?php } ?>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($deliveryboys)) {
                            $i = ($deliveryboys->currentPage() == 1) ? 1 : (($deliveryboys->currentPage() - 1) * $deliveryboys->perPage()) + 1;
                            foreach ($deliveryboys as $deliveryboy) {
							?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $deliveryboy->name; ?></td>
                                    <td><?php echo $deliveryboy->email; ?></td>
                                    <td><?php echo $deliveryboy->mobile; ?></td>
                                    <td class="onoff" width="50">
                                        <?php if ($deliveryboy->is_delete == 0) {
                                            ?>
                                            <input type="checkbox" name="active_inactive" <?php echo ($deliveryboy->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $deliveryboy->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$deliveryboy->id.','.$deliveryboy->status.');"' : ''; ?>>
                                            <label class="onoff_lbl" for="<?php echo $deliveryboy->id; ?>"></label>
                                        <?php } else { ?>
                                            <a href="#" class="delete_btn" title="Deleted"><i class="fa fa-close"></i></a>
                                        <?php } ?>
                                    </td>
                                    <td class="action_btns" width="80">
                                        <?php if ($deliveryboy->is_delete == 0) { ?>
                                            <a href="<?php echo (Session('edit')) ? URL::to('/admin/editdeliveryboy/'.$deliveryboy->id) : '#'; ?>" class="edit_btn"><i class="fa fa-eye"></i></a>
                                            <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_deliveryboy('.$deliveryboy->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                        <?php } else { ?>
                                            <a href="#" <?php echo (Session('delete')) ? 'onclick="restore_deliveryboy('.$deliveryboy->id.');"' : ''; ?> title="Restore" class="edit_btn"><i class="fa fa-reply-all"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Deliveryboy Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($deliveryboys->currentPage() == 1) {
                    $count = $deliveryboys->count();
                } else if ($deliveryboys->perPage() > $deliveryboys->count()) {
                    $count = ($deliveryboys->currentPage() - 1) * $deliveryboys->perPage() + $deliveryboys->count();
                } else {
                    $count = $deliveryboys->currentPage() * $deliveryboys->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($deliveryboys->currentPage() == 1) ? 1 : ($deliveryboys->perPage() * ($deliveryboys->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $deliveryboys->total() . " ".trans('messages.entries')."</span>";
                echo $deliveryboys->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_deliveryboystatus",
            dataType: "json",
            data: {'id': id, 'status': status},
            async: true,
            success: function (result) {
                var newstatus = (status == 1) ? 0 : 1;
                $("#" + id).attr("onclick", "status_change(" + id + "," + newstatus + ");");
                $('#success_msg').attr('class', 'success_msg');
                $('#success_msg').html(result.msg);
            }
        });
    }

    function delete_deliveryboy(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deletedeliveryboy/' + id;
        }
    }

    function restore_deliveryboy(id)
    {
        if (confirm('<?php echo trans("messages.Restore Confirmation"); ?>'))
        {
            window.location = 'restoredeliveryboy/' + id;
        }
    }
</script>
@endsection
