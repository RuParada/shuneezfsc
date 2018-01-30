@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Teams'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p>  
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <?php if(Session('add')) { ?><p class="top_add_btn"><a href="{!! URL::to('admin/create-team') !!}" class="btn btn-primary"><i class="fa fa-plus"></i><?php echo trans('messages.Add New Team'); ?></a></p><?php } ?>
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filter-teams', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Name'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-users"></i> </div>
                            <input type="text" class="form-control" name="name" value="<?php echo Input::get('name'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Search Team'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Branch'); ?></th>
                            <th><?php echo trans('messages.Name'); ?></th>
                            <th><?php echo trans('messages.CIty'); ?></th>
                            <th><?php echo trans('messages.Country'); ?></th>
                            <th><?php echo trans('messages.Auto Assign'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($teams) > 0) {
                            $i = ($teams->currentPage() == 1) ? 1 : (($teams->currentPage() - 1) * $teams->perPage()) + 1;
                            foreach ($teams as $team) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $team->branch; ?></td>
                                    <td><?php echo $team->name; ?></td>
                                    <td><?php echo $team->city; ?></td>
                                    <td><?php echo $team->country; ?></td>
                                   <td class="onoff" width="50">
                                        <input type="checkbox" name="active_inactive" <?php echo ($team->auto_assign == 1) ? 'checked' : ''; ?> class="onoff_chck" id="<?php echo $team->id; ?>" disabled>
                                        <label class="onoff_lbl" for="<?php echo $team->id; ?>"></label>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Teams Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($teams->currentPage() == 1) {
                    $count = $teams->count();
                } else if ($teams->perPage() > $teams->count()) {
                    $count = ($teams->currentPage() - 1) * $teams->perPage() + $teams->count();
                } else {
                    $count = $teams->currentPage() * $teams->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($teams->currentPage() == 1) ? 1 : ($teams->perPage() * ($teams->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $teams->total() . " ".trans('messages.entries')."</span>";
                echo $teams->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
                echo '</div>';
                ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->		  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection
