@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1>Manage Page </h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p> 
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <p class="top_add_btn"><a href="{!! URL::to('admin/addcms') !!}" class="btn btn-primary"><i class="fa fa-plus"></i>Add CMS</a></p>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filtercms', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label>Title</label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group full_selectLists">
                        <label>Select Status</label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-building"></i> </div>
                            <select class="selectLists" name="status">
                                <option value="">Select Status</option>
                                <option value="1" <?php echo (Input::get('status') == '1') ? 'selected' : ''; ?>>Active </option>
                                <option value="0" <?php echo (Input::get('status') == '0') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label>Filter</label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Search CMS</button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Title</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($pages) > 0) {
                            $i = ($pages->currentPage() == 1) ? 1 : (($pages->currentPage() - 1) * $pages->perPage()) + 1;
                            foreach ($pages as $page) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $page->title; ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($page->created_at)); ?></td>
                                    <td class="onoff" width="50">
                                        <input type="checkbox" name="active_inactive" <?php echo ($page->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $page->id; ?>" onclick="status_change(<?php echo $page->id; ?>, <?php echo $page->status; ?>);">
                                        <label class="onoff_lbl" for="<?php echo $page->id; ?>"></label>
                                    </td>
                                    <td class="action_btns" width="70">
                                        <a href="{!! URL::to('/admin/getcms/'.$page->id)!!}" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                        <a href="#" class="delete_btn" onclick="delete_page(<?php echo $page->id; ?>);"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center">No Page Found</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($pages->currentPage() == 1) {
                    $count = $pages->count();
                } else if ($pages->perPage() > $pages->count()) {
                    $count = ($pages->currentPage() - 1) * $pages->perPage() + $pages->count();
                } else {
                    $count = $pages->perPage() + $pages->count();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($pages->currentPage() == 1) ? 1 : $count - $pages->count() + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>Showing " . $start . " to " . $count . " of " . $pages->total() . " entries</span>";
                echo $pages->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_pagestatus",
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

    function delete_page(id)
    {
        if (confirm('Are you sure you want delete this page ?'))
        {
            window.location = 'deletepage/' + id;
        }
    }
</script>
@endsection
