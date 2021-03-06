@extends('adminheader')
@section('content')
<?php $seg = Request::segment(2); ?>
<style type="text/css">
.activity > a 
{
  color: #838383 !important;
}
.form-group
{
	width:200px !important;
	padding-top: 10px;
}
</style>
<div class="content-wrapper">

	<section class="content-header header_filter">
		<h1 class="col-md-12"><?php echo trans('messages.Dashboard'); ?> <small><?php echo trans('messages.Control Panel'); ?></small> </h1>		
	<!--<div class="form-group full_selectList">
		<div class="input-group">
			<a href="javascript:void(0)" class="btn btn-primary pull-right">Filter Data</a>
		</div>
	</div>
	<div class="form-group full_selectList">
		<div class="input-group">
		<div class="input-group-addon"> <i class="fa fa-building"></i> </div>
			<input type="text" name="to_date" class="spicToday form-control" value="" placeholder="To Date">
		</div>
	</div> <!-- form-group -
	<div class="form-group full_selectList">
		<div class="input-group">
		<div class="input-group-addon"> <i class="fa fa-building"></i> </div>
			<input type="text" name="from_date" class="spicToday form-control" value="" placeholder="From Date">
		</div>
	</div> <!-- form-group -
	<div class="form-group full_selectList">
	 <div class="input-group">
		<div class="input-group-addon"> <i class="fa fa-building"></i> </div>
			<select class="selectList" name="deliveryboy_id" id="deliveryboy_id" onchange="filter_orders();">
				<option value=""><?php echo trans('messages.Select Deliveryboy'); ?></option>
				< ?php if(count($deliveryboys))
				{
					foreach($deliveryboys as $deliveryboy)
					{
						$select = ($seg == $deliveryboy->id) ? 'selected' : ''; 
				?>
				<option value="< ?php echo $deliveryboy->id; ?>" < ?php echo $select; ?>>< ?php echo $deliveryboy->name; ?></option>
				< ?php } } ?>
			</select>
		</div>
	</div>
	<div class="form-group full_selectList">
		<div class="input-group">
		<div class="input-group-addon"> <i class="fa fa-building"></i> </div>
			<select class="selectList" name="branch_id" id="branch_id" onchange="filter_orders();">
				<option value=""><?php echo trans('messages.Select Branch'); ?></option>
				< ?php if(count($branches))
				{
					foreach($branches as $branch)
					{
						$select = ($seg == $branch->id) ? 'selected' : ''; 
				?>
				<option value="< ?php echo $branch->id; ?>" < ?php echo $select; ?>>< ?php echo $branch->branch; ?></option>
				< ?php } } ?>
			</select>
		</div>
	</div> <!-- form-group -->
		
	</section>
	
	<div class="full_row"></div>

	<!-- Main content -->
	<section class="content">
	<div class="row">
	
		<div class="col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg_green"><i class="fa fa-usd"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo trans('messages.Total Sales'); ?></span>
					<span class="info-box-number"><small>$ </small><?php echo ($sale['overall']->order_total) ? $sale['overall']->order_total : 0; ?></span>
				</div><!-- info-box-content -->
			</div><!-- info-box -->
		</div>
		
		<div class="col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg_yellow"><i class="fa fa-usd"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo trans('messages.Sales of This Month'); ?></span>
					<span class="info-box-number"><small>$ </small><?php echo ($sale['month']->order_total) ? $sale['month']->order_total : 0; ?></span>
				</div><!-- info-box-content -->
			</div><!-- info-box -->
		</div>	
	
		<div class="col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg_blue"><i class="fa fa-usd"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo trans('messages.Today Sales'); ?></span>
					<span class="info-box-number"><small>$ </small><?php echo ($sale['today']->order_total) ? $sale['today']->order_total : 0; ?></span>
				</div><!-- info-box-content -->
			</div><!-- info-box -->
		</div>
	</div> <!-- row --> 

	<div class="row">
	
		<div class="col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg_green"><i class="fa fa-usd"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo trans('messages.Total Orders'); ?></span>
					<span class="info-box-number"><?php echo $sale['overall']->order_count; ?></span>
				</div><!-- info-box-content -->
			</div><!-- info-box -->
		</div>
		
		<div class="col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg_yellow"><i class="fa fa-usd"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo trans('messages.Orders of This Month'); ?></span>
					<span class="info-box-number"><?php echo $sale['month']->order_count; ?></span>
				</div><!-- info-box-content -->
			</div><!-- info-box -->
		</div>	
	
		<div class="col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg_blue"><i class="fa fa-usd"></i></span>
				<div class="info-box-content">
					<span class="info-box-text"><?php echo trans('messages.Today Orders'); ?></span>
					<span class="info-box-number"><?php echo $sale['today']->order_count; ?></span>
				</div><!-- info-box-content -->
			</div><!-- info-box -->
		</div>
	</div> <!-- row --> 
	
	<div class="row mt10">	
		<div class="col-md-12">
			<div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><i class="fa fa-shopping-cart"></i> Deliveryboy Order Details</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="table-responsive latest_orders">
                    <table class="table no-margin table-bordered">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Deliveryboy</th>
                          <th>Delivered Orders</th>
                          <th>Cancelled Orders</th>
                          <th>Total Orders</th>
                          <th>Total Amount</th>
                        </tr>
                      </thead>
                      <tbody>
						<?php
						if(count($deliverboy_orders) > 0)
						{
							$i = 1;
							foreach($deliverboy_orders as $deliverboy_order) {
						?>
						<tr>
							<td><?php echo $i; ?></td>
							<td><?php echo $deliverboy_order->deliveryboy_name; ?></td>
							<td><?php echo $deliverboy_order->accepted_order; ?></td>
							<td><?php echo $deliverboy_order->cancelled_order; ?></td>
							<td><?php echo $deliverboy_order->accepted_order + $deliverboy_order->cancelled_order; ?></td>
                        	<td></td>
                        </tr>
                        <?php $i++; } } ?>
                        </tbody>
                    </table>

                  </div><!-- table-responsive -->
              </div><!-- box-body -->
              
			  </div> <!-- box-info -->
		</div> <!-- col-md-8 -->
		
		<!--<div class="col-md-4">
			<div class="box box-info">
                <div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-calendar"></i> Recent Activities</h3>
                </div><!-- /.box-header 
                <div class="box-body">
					<ul class="recent_activity">
            < ?php
            if(count($notifications) > 0)
            {
              foreach ($notifications as $notification) {
            ?>
						<li>
							<span class="activity">< ?php echo $notification->notification; ?></span>
							<span class="date">< ?php echo date('d-M-Y', strtotime($notification->created_at)); ?> <i class="fa fa-clock-o"></i> < ?php echo date('g:i A', strtotime($notification->created_at)); ?></span>
						</li><!-- activity --
            < ?php } } ?>
					</ul>
				</div><!-- box-body -
			</div> <!-- box-info --
		</div> <!-- col-md-4 -->		
	</div> <!-- row -->	
          
    </section><!-- content -->
</div><!-- content-wrapper -->
  

<script>
function filter_orders()
    {
      var store_id = $("#store_id option:selected").val();
      var url = "<?php echo URL::to(''); ?>";
        if(store_id != "")
        {
            window.location = url+'/filterorders/'+store_id;
        }
        else
        {
            window.location = url+'/dashboard';
        }
    }
</script>
@endsection
