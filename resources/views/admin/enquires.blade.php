@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Enquires'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?>@endif
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
               
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filterenquires', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Search Field'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    


                    <div class="col-md-4 form-group">
                        <label>Filter</label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Enquiry'); ?></button>
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
                            <th><?php echo trans('messages.Subject'); ?></th>
                            <th><?php echo trans('messages.Action'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($enquires) > 0) {
                            $i = ($enquires->currentPage() == 1) ? 1 : (($enquires->currentPage() - 1) * $enquires->perPage()) + 1;
                            foreach ($enquires as $enquiry) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $enquiry->name; ?></td>
                                    <td><?php echo $enquiry->email; ?></td>
                                    <td><?php echo $enquiry->mobile; ?></td>
                                    <td><?php echo $enquiry->subject; ?></td>
                                    <td class="action_btns" width="70">
                                            <a href="{!! URL::to('/admin/viewenquiry/'.$enquiry->id)!!}" class="edit_btn"><i class="fa fa-eye"></i></a>
                                            <a href="#" class="delete_btn" onclick="delete_enquiry(<?php echo $enquiry->id; ?>);"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Enquires Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($enquires->currentPage() == 1) {
                    $count = $enquires->count();
                } else if ($enquires->perPage() > $enquires->count()) {
                    $count = ($enquires->currentPage() - 1) * $enquires->perPage() + $enquires->count();
                } else {
                    $count = $enquires->currentPage() * $enquires->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($enquires->currentPage() == 1) ? 1 : ($enquires->perPage() * ($enquires->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $enquires->total() . " ".trans('messages.entries')."</span>";
                echo $enquires->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
                echo '</div>';
                ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->		  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
   function delete_enquiry(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'delete_enquiry/' + id;
        }
    }
</script>
@endsection
