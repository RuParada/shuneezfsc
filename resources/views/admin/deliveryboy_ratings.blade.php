@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Manage Ratings'); ?> </h1>
        @if(Session::has('success'))<p class="success_msg" id="s_msg"><?php echo Session::get('success'); ?></p>@endif
        <p id="success_msg"></p>  
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="box">                
            <div class="box-body">
                <div class="row">
                    {!! Form::open(array('url' => 'admin/filter-ratings', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group full_selectLists">
                        <label><?php echo trans('messages.Select User'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                            <select class="selectLists" name="user">
                                <option value=""><?php echo trans('messages.Select User'); ?></option>
                                @if(count($users))
                                    @foreach ( $users as $user ) 
                                        <option value="{!! $user->id !!}" {!! ( Input::get('user') == $user->id ) ? 'selected' : '' !!}>{!! $user->first_name.' '.$user->last_name !!}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div> <!-- form-group -->

                    <div class="col-md-4 form-group full_selectLists">
                        <label><?php echo trans('messages.Select Deliveryboy'); ?></label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                            <select class="selectLists" name="deliveryboy">
                                <option value=""><?php echo trans('messages.Select Deliveryboy'); ?></option>
                                @if(count($deliveryboys))
                                    @foreach ( $deliveryboys as $deliveryboy ) 
                                        <option value="{!! $deliveryboy->id !!}" {!! ( Input::get('deliveryboy') == $deliveryboy->id ) ? 'selected' : '' !!}>{!! $deliveryboy->deliveryboy !!}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i><?php echo trans('messages.Filter Rating'); ?></button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo trans('messages.S.No'); ?></th>
                            <th><?php echo trans('messages.Order Id'); ?></th>
                            <th><?php echo trans('messages.Customer'); ?></th>
                            <th><?php echo trans('messages.Deliveryboy'); ?></th>
                            <th><?php echo trans('messages.Rating'); ?></th>
                            <th><?php echo trans('messages.Review'); ?></th>
                        </tr>

                    </thead>
                    <tbody>
                        <?php
                        if (count($ratings) > 0) {
                            $i = ($ratings->currentPage() == 1) ? 1 : (($ratings->currentPage() - 1) * $ratings->perPage()) + 1;
                            foreach ($ratings as $rating) {
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $rating->order_key; ?></td>
                                    <td><?php echo $rating->customer_first_name.' '.$rating->customer_last_name; ?></td>
                                    <td><?php echo $rating->deliveryboy; ?></td>
                                    <td><?php echo $rating->deliveryboy_rating; ?></td>
                                    <td><?php echo $rating->deliveryboy_review; ?></td>
                                </tr>
                                <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No Ratings Found'); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php
                echo "<div class='row-page entry'>";
                if ($ratings->currentPage() == 1) {
                    $count = $ratings->count();
                } else if ($ratings->perPage() > $ratings->count()) {
                    $count = ($ratings->currentPage() - 1) * $ratings->perPage() + $ratings->count();
                } else {
                    $count = $ratings->currentPage() * $ratings->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($ratings->currentPage() == 1) ? 1 : ($ratings->perPage() * ($ratings->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'>".trans('messages.Showing')." " . $start . " ".trans('messages.to')." " . $count . " ".trans('messages.of')." " . $ratings->total() . " ".trans('messages.entries')."</span>";
                echo $ratings->appends(['user' => Input::get('user'), 'deliveryboy' => Input::get('deliveryboy')])->render();
                echo '</div>';
                ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->		  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
@endsection
