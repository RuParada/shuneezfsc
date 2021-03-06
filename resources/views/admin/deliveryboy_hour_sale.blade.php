@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Delivery Hour Report'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
          
    <div class="box">                
        <div class="box-body">
        {!! Form::open(['url' => 'admin/export_order']) !!}
        <input type="hidden" name="from_date" value="<?php echo Input::get('from_date'); ?>">
        <input type="hidden" name="to_date" value="<?php echo Input::get('to_date'); ?>">
        <!--<a href="javascript:void(0);" ><button class="btn btn-primary" type="submit"><i class="fa fa-plus-circle"></i>Export CSV</button></a>-->
        {!! Form::close() !!}    
            <div class="row"> 
                {!! Form::open(array('url' => 'admin/deliveryboy_hour_report', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label>Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-calendar-o" aria-hidden="true"></i> </div>
                            <input type="text" class="form-control spicToday" name="hour_date" value="<?php echo Input::get('hour_date'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Filter Report</button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
            </div>
           <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th><?php echo trans('messages.S.No'); ?></th>
                        <th><?php echo trans('messages.Deliveryboy'); ?></th>
                        <th><?php echo trans('messages.Hour'); ?></th>
                        <th><?php echo trans('messages.Number of Orders'); ?></th>
                        <th><?php echo trans('messages.Total Amount'); ?></th>
                    </tr>
                      
                </thead>
                <tbody>
                    <?php 
                    if(count($data) > 0) {
                        $i = 1;
                        foreach ($data as $sale) {
                     ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $sale->deliveryboy; ?></td>
                        <td><?php echo $sale->start_time; ?></td>
                        <td><?php echo $sale->total_orders; ?></td>
                        <td><?php echo ($sale->total_sales) ? $sale->total_sales : 0; ?></td>
                    </tr>
                    <?php $i++; } } else { ?>
                        <tr><td colspan="5" style="text-align:center"><?php echo trans('messages.No data found'); ?></td></tr>
                     <?php } ?>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->          
          
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>

function export_csv(id)
{
    var from_date = '<?php echo Input::get('from_date'); ?>';
    var to_date = '<?php echo Input::get('to_date'); ?>';
    $.ajax({
        beforeSend: function () {
            $("body").addClass("loading");
        },
        type: "GET",
        url: "<?php echo URL::to('admin/export_order'); ?>",
        data: {'from_date': from_date, 'to_date': to_date},
        async: true,
        success: function (result) {
             $("body").removeClass("loading");
        }
    }); 
}
</script>
@endsection
