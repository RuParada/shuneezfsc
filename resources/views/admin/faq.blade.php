@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage FAQ'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p> 
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/addfaq') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add FAQ'); ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filterfaqs', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Question/Answer'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group full_selectLists">
                        <label>Select Status</label>
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
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search FAQ'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Question'); ?></th>
                            <th><?php echo trans('messages.Answer'); ?></th>
                            <th><?php echo trans('messages.Status'); ?></th>
                            <th><?php echo trans('messages.Action'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($faqs) > 0) {
                            $i = ($faqs->currentPage() == 1) ? 1 : (($faqs->currentPage() - 1) * $faqs->perPage()) + 1;
                            foreach ($faqs as $faq) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $faq->question; ?></td>
                                    <td><?php echo $faq->answer; ?></td>
                                    <td class="onoff" width="50">
                                        <input type="checkbox" name="active_inactive" <?php echo ($faq->status == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $faq->id; ?>" <?php echo (Session('edit')) ? 'onclick="status_change('.$faq->id.','.$faq->status.');"' : ''; ?>>
                                        <label class="onoff_lbl" for="<?php echo $faq->id; ?>"></label>
                                    </td>
                                    <td class="action_btns" width="70">
                                        <a href="<?php echo (Session('edit')) ? URL::to('/admin/getfaq/'.$faq->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
                                        <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_faq('.$faq->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans("messages.No data found"); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($faqs->currentPage() == 1) {
                    $count = $faqs->count();
                } else if ($faqs->perPage() > $faqs->count()) {
                    $count = ($faqs->currentPage() - 1) * $faqs->perPage() + $faqs->count();
                } else {
                    $count = $faqs->currentPage() * $faqs->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($faqs->currentPage() == 1) ? 1 : ($faqs->perPage() * ($faqs->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $faqs->total() . " ".trans('messages.entries')."</span>";
                echo $faqs->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
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
            url: url + "/admin/change_faqstatus",
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

    function delete_faq(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deletefaq/' + id;
        }
    }
</script>
@endsection
