@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Subscribers'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
               
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filtersubscribers', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Email'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Newsletter'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Email'); ?></th>
                            <th><?php echo trans('messages.Subscribed At'); ?></th>
                            <th><?php echo trans('messages.Action'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($subscribers) > 0) {
                            $i = ($subscribers->currentPage() == 1) ? 1 : (($subscribers->currentPage() - 1) * $subscribers->perPage()) + 1;
                            foreach ($subscribers as $subscriber) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $subscriber->email; ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($subscriber->created_at)); ?></td>
                                    <td class="action_btns" width="70">
                                            <a href="#" class="delete_btn" onclick="delete_subscriber(<?php echo $subscriber->id; ?>);"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="4" style="text-align:center"><?php echo trans('messages.No Subscribers Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($subscribers->currentPage() == 1) {
                    $count = $subscribers->count();
                } else if ($subscribers->perPage() > $subscribers->count()) {
                    $count = ($subscribers->currentPage() - 1) * $subscribers->perPage() + $subscribers->count();
                } else {
                    $count = $subscribers->currentPage() * $subscribers->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($subscribers->currentPage() == 1) ? 1 : ($subscribers->perPage() * ($subscribers->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $subscribers->total() . " ".trans('messages.entries')."</span>";
                echo $subscribers->appends(['name' => Input::get('name')])->render();
                echo '</div>';
                ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->		  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
   function delete_subscriber(id)
    {
        if (confirm('Are you sure you want delete this subscriber ?'))
        {
            window.location = 'deletesubscriber/' + id;
        }
    }
</script>
@endsection
