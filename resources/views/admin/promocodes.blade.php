@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Promocode'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p>  
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/addpromocode') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Promocode'); ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filterpromocodes', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Promocode'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->

                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Promocode'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Promocode'); ?></th>
                            <th><?php echo trans('messages.Amount'); ?></th>
                            <th><?php echo trans('messages.Expiry Date'); ?></th>
                            <th><?php echo trans('messages.Action'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($promocodes) > 0) {
                            $i = ($promocodes->currentPage() == 1) ? 1 : (($promocodes->currentPage() - 1) * $promocodes->perPage()) + 1;
                            foreach ($promocodes as $promocode) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $promocode->promocode; ?></td>
                                    <td><?php echo $promocode->amount; ?></td>
                                    <td><?php echo date('d-M-y', strtotime($promocode->expiry_date)); ?></td>
                                    <td class="action_btns">
										<a href="<?php echo (Session('edit')) ? URL::to('/admin/editpromocode/'.$promocode->id) : '#'; ?>" class="edit_btn"><i class="fa fa-pencil"></i></a>
										<a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick="delete_promocode('.$promocode->id.');"' : ''; ?>><i class="fa fa-trash"></i></a>
										<?php if($promocode->expiry_date > date('Y-m-d') && $promocode->is_used == 0) { ?>
											<a href="<?php echo URL::to('/admin/sendpromocode/'.$promocode->id); ?>" class="edit_btn"><i class="fa fa-send"></i></a>
										<?php } else { ?>
											<a href="javascript:void(0)" class="edit_btn" title="<?php echo ($promocode->is_used == 1) ? trans('messages.Already Sent') : ''; ?> <?php echo ($promocode->expiry_date < date('Y-m-d')) ? trans('messages.Expired') : ''; ?>"><i class="fa fa-info-circle"></i></a>
										<?php } ?>
									</td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="6" style="text-align:center"><?php echo trans('messages.No Promocode Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($promocodes->currentPage() == 1) {
                    $count = $promocodes->count();
                } else if ($promocodes->perPage() > $promocodes->count()) {
                    $count = ($promocodes->currentPage() - 1) * $promocodes->perPage() + $promocodes->count();
                } else {
                    $count = $promocodes->currentPage() * $promocodes->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($promocodes->currentPage() == 1) ? 1 : ($promocodes->perPage() * ($promocodes->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $promocodes->total() . " ".trans('messages.entries')."</span>";
                echo $promocodes->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
                echo '</div>';
                ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->		  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
    function delete_promocode(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'deletepromocode/' + id;
        }
    }
</script>
@endsection
