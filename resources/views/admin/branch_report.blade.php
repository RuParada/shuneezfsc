@extends('adminheader')

@section('content')
<?php $seg = Request::segment(3); ?>
<div class="content-wrapper">

	<section class="content-header">
		<h1><?php echo trans('messages.Branch Sales Report'); ?></h1>
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
                    {!! Form::open(array('url' => 'admin/branch_report', 'method' => 'get')) !!}
                    <div class="col-md-4 form-group">
                        <label>From Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-calendar-o" aria-hidden="true"></i> </div>
                            <input type="text" class="form-control spicToday" name="from_date" value="<?php echo Input::get('from_date'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    <div class="col-md-4 form-group">
                        <label>To Date</label>
                        <div class="input-group">
                            <div class="input-group-addon"> <i class="fa fa-calendar-o" aria-hidden="true"></i> </div>
                            <input type="text" class="form-control spicToday" name="to_date" value="<?php echo Input::get('to_date'); ?>">
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->


                    <div class="col-md-4 form-group">
                        <label><?php echo trans('messages.Filter'); ?></label>
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>Filter Report</button>
                        </div> <!-- input-group -->
                    </div> <!-- form-group -->
                    {!! Form::close() !!}
                </div> <!-- row -->
           <table id="dataTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
						<th><?php echo trans('messages.S.No'); ?></th>
						<th><?php echo trans('messages.Branch'); ?></th>
                        <th><?php echo trans('messages.Number of Orders'); ?></th>
                        <th><?php echo trans('messages.Total Amount'); ?></th>
                    </tr>
					  
                </thead>
                <tbody>
                    <?php 
                    if(count($data) > 0) {
                        $i = ($data->currentPage() == 1) ? 1 : (($data->currentPage() - 1) * $data->perPage()) + 1;
                        foreach($data as $row) { 
                    ?>
                    <tr>
						<td><?php echo $i; ?></td>
                        <td><?php echo $row->branch; ?></td>
                        <td><?php echo $row->order_count; ?></td>
						<td><?php echo $row->total_amount; ?></td>
                    </tr>
                    <?php $i++; } } 
                    else { ?>
                        <tr><td colspan="8" style="text-align:center"><?php echo trans('messages.No data found'); ?></td></tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
                echo "<div class='row-page entry'>";
                if ($data->currentPage() == 1) {
                    $count = $data->count();
                } else if ($data->perPage() > $data->count()) {
                    $count = ($data->currentPage() - 1) * $data->perPage() + $data->count();
                } else {
                    $count = $data->currentPage() * $data->perPage();
                }
                // $count=($images->currentPage()==1)?$images->perPage():$images->perPage()+$images->count();
                if ($count > 0) {
                    $start = ($data->currentPage() == 1) ? 1 : ($data->perPage() * ($data->currentPage() - 1)) + 1;
                } else {
                    $start = 0;
                }

                echo "<span style='float:right'> ".trans('messages.Showing')." " .$start.' '.trans('messages.to').' '.$count.' '.trans('messages.of').' '.$data->total()." ".trans('messages.entries')." </span>";
                echo $data->appends(['name' => Input::get('name'), 'status' => Input::get('status')])->render();
                echo '</div>';
                ?>
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
