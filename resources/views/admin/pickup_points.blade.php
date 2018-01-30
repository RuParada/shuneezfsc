@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Pickup Points'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p>  
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/create-pickup-point') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Pickup Point'); ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filter-pickup-points', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Address/Title'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group full_selectLists">
                        <label><?php echo trans('messages.Select Team'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <select class="selectLists" name="team">
                                <option value=""><?php echo trans('messages.Select Team'); ?></option>
                                @if ( count($teams) )
                                    @foreach($teams as $team)
                                        <option value="{!! $team->dook_id !!}" {!! ($team->dook_id == Input::get('team')) ? 'selected' : '' !!}>{!! $team->name !!}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Data'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Team'); ?></th>
                            <th><?php echo trans('messages.Title'); ?></th>
                            <th><?php echo trans('messages.Latitude'); ?></th>
                            <th><?php echo trans('messages.Longitude'); ?></th>
                            <th><?php echo trans('messages.Action'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($pickup_points) > 0) {
                            $i = ($pickup_points->currentPage() == 1) ? 1 : (($pickup_points->currentPage() - 1) * $pickup_points->perPage()) + 1;
                            foreach ($pickup_points as $pickup_point) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $pickup_point->name; ?></td>
                                    <td><?php echo $pickup_point->title; ?></td>
                                    <td><?php echo $pickup_point->latitude; ?></td>
                                    <td><?php echo $pickup_point->longitude; ?></td>
                                    <td class="action_btns">
                                        <a href="#" class="delete_btn" <?php echo (Session('edit')) ? 'onclick=delete_pickup("'.$pickup_point->dook_id.'");' : ''; ?>><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No data found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($pickup_points->currentPage() == 1) {
                    $count = $pickup_points->count();
                } else if ($pickup_points->perPage() > $pickup_points->count()) {
                    $count = ($pickup_points->currentPage() - 1) * $pickup_points->perPage() + $pickup_points->count();
                } else {
                    $count = $pickup_points->currentPage() * $pickup_points->perPage();
                }
                if ($count > 0) {
                    $start = ($pickup_points->currentPage() == 1) ? 1 : ($pickup_points->perPage() * ($pickup_points->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $pickup_points->total() . " ".trans('messages.entries')."</span>";
                echo $pickup_points->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
                echo '</div>';
                ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->		  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">
    function delete_pickup(id)
    {
        if (confirm('<?php echo trans("messages.Delete Confirmation"); ?>'))
        {
            window.location = 'delete-pickup-point/' + id;
        }
    }
</script>
@endsection
