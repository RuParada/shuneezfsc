@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Vendors'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p> 
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <!--< ?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/addvendor') !!}" class="btn btn-primary"><i class="fa fa-plus"></i>< ?php echo trans('messages.Add New Vendor'); ?></a></p>< ?php } ?>-->
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filtervendors', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Name'); ?></label>
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
                                <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>><?php echo trans('messages.Inactive'); ?></option>
                                <option value="deleted" <?php echo (Input::get('status') == 'deleted') ? 'selected' : ''; ?>><?php echo trans('messages.Deleted'); ?></option>
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Vendor'); ?></button>
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
                            <th><?php echo trans('messages.Account Balance'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <?php if ($seg != 'deleted') { ?><th><?php echo trans('messages.Action'); ?></th><?php } ?>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($vendors)) {
                            $i = ($vendors->currentPage() == 1) ? 1 : (($vendors->currentPage() - 1) * $vendors->perPage()) + 1;
                            foreach ($vendors as $vendor) {
								$amount = DB::table('vendor_payment')->where('status', 0)->sum('amount');
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $vendor->vendor; ?></td>
                                    <td><?php echo $vendor->email; ?></td>
                                    <td><?php echo $amount; ?></td>
									<td class="onoff" width="50">
                                        <?php if ($vendor->is_delete == 0) {
                                            ?>
                                            <input type="checkbox" name="active_inactive" <?php echo ($vendor->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $vendor->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$vendor->id.','.$vendor->status.');"' : ''; ?>>
                                            <label class="onoff_lbl" for="<?php echo $vendor->id; ?>"></label>
                                        <?php } else { ?>
                                            <a href="#" class="delete_btn" title="Deleted"><i class="fa fa-close"></i></a>
                                        <?php } ?>
                                    </td>
                                    <td class="action_btns" width="80">
                                        <?php if ($vendor->is_delete == 0) { ?>
                                            <a href="<?php echo (Session('edit')) ? URL::to('/admin/editvendor/'.$vendor->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                            <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_vendor('.$vendor->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                        <?php } else { ?>
                                            <a href="#" <?php echo (Session('delete')) ? 'onclick="restore_vendor('.$vendor->id.');"' : ''; ?> title="Restore" class="edit_btn"><i class="fa fa-reply-all"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Vendors Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($vendors->currentPage() == 1) {
                    $count = $vendors->count();
                } else if ($vendors->perPage() > $vendors->count()) {
                    $count = ($vendors->currentPage() - 1) * $vendors->perPage() + $vendors->count();
                } else {
                    $count = $vendors->perPage() + $vendors->count();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($vendors->currentPage() == 1) ? 1 : $count - $vendors->count() + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $vendors->total() . " ".trans('messages.entries')."</span>";
                echo $vendors->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_vendorstatus",
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

    function delete_vendor(id)
    {
        if (confirm('Are you sure you want delete this vendor ?'))
        {
            window.location = 'deletevendor/' + id;
        }
    }

    function restore_vendor(id)
    {
        if (confirm('Are you sure you want restore this vendor ?'))
        {
            window.location = 'restorevendor/' + id;
        }
    }
</script>
@endsection
