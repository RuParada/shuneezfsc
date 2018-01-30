<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Shuneez | <?php echo trans('messages.Control Panel'); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" href="{!! URL::to('assets/images/favicon.ico') !!}" type="image/x-icon" />
    {!! Html::style('assets/admin/css/bootstrap.min.css') !!}
    {!! Html::style('assets/admin/css/font-awesome.min.css') !!}
    {!! Html::style('assets/admin/css/AdminLTE.min.css') !!}
    {!! Html::style('assets/admin/css/all-skins.min.css') !!}
    {!! Html::style('assets/admin/css/select2.min.css') !!}
    {!! Html::style('assets/admin/css/datepicker.css') !!}
    {!! Html::style('assets/admin/css/dataTables.bootstrap.css') !!}
	{!! Html::style('assets/admin/css/jquery-clockpicker.min.css') !!}
      
    
    

    {!! Html::script('assets/admin/js/jQuery-2.1.4.min.js') !!}
        
    {!! Html::script('assets/admin/js/jquery-clockpicker.min.js') !!}
    
    <!--[if lt IE 9]>
		<script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
 
<body class="hold-transition skin-green sidebar-mini ">
  
<div class="wrapper">

	<header class="main-header">
		
		<a href="#" class="logo"> SHUNEEZ </a>
		
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">          
			<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> </a>
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">  
					<li class="dropdown" style="margin-top:10px;">
						<select name="language" id="language" onchange="select_language();">
							<option value=""><?php echo trans('messages.Select Language'); ?></option>
							<?php
							$languages = DB::table('languages')->where('status', 1)->orderby('language', 'asc')->get();
							if(count($languages) > 0)
							{
								foreach($languages as $language) { 
									$select = (isset($_SESSION['language']) && $_SESSION['language'] == $language->code) ? 'selected' : '';   
							?>
							<option <?php echo $select; ?> value="<?php echo $language->code; ?>"><?php echo $language->language; ?></option>
							<?php } } ?>
						</select>
					</li>	
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<img src="{!! URL::to('assets/admin/images/admin.png') !!}" class="user-image" alt="User Image">
						<span class="hidden-xs">
							<?php if(Session('is_manager'))
							{
								echo (Session('new_name')) ? Session('new_name') : Session('name'); 
							}
							else
							{
								echo (Session('new_staff_name')) ? Session('new_staff_name') : Session('staff_name');
							}
							?>
							</span></a>
					
					<ul class="dropdown-menu">                  
						<li class="user-header">
							<img src="{!! URL::to('assets/admin/images/admin.png') !!}" class="img-circle" alt="User Image">
							<p>
							<?php if(Session('is_manager'))
							{
								echo (Session('new_name')) ? Session('new_name') : Session('name'); 
							}
							else
							{
								echo (Session('new_staff_name')) ? Session('new_staff_name') : Session('staff_name');
							}
							?>
							</p>
						</li>                  
					<!-- Menu Footer-->
						<li class="user-footer">
							<div class="pull-left">
								<a href="{!! (Session('is_manager')) ? URL::to('branch/editbranch') : URL::to('branch/getstaff/'.Session('staff_id')) !!}" class="btn btn-info sign_out"><?php echo trans('messages.Profile'); ?> </a>
							</div>
							<div class="pull-right">
								<a href="{!! URL::to('branch/logout') !!}" class="btn btn-primary sign_out"><?php echo trans('messages.Sign out'); ?></a>
							</div>
						</li>
					</ul>
					</li>
            </ul>
          </div>
        </nav>
    </header>
	
      <!-- Left side column -->
      <aside class="main-sidebar">
        
        <section class="sidebar">
          <!-- sidebar menu -->
          <ul class="sidebar-menu">            
            <li><a href="{!! URL::to('branch/dashboard') !!}"> <i class="fa fa-home"></i> <span><?php echo trans('messages.Dashboard'); ?></span> </a></li>
           <?php if(Session('is_manager')) { ?> <li><a href="{!! URL::to('branch/staffs') !!}"> <i class="fa fa-users"></i> <span><?php echo trans('messages.Manage Staffs'); ?></span> </a></li><?php } ?>
			<li><a href="{!! URL::to('branch/deliveryboys') !!}"> <i class="fa fa-users"></i> <span><?php echo trans('messages.Manage Deliveryboys'); ?></span> </a></li>
			<li><a href="{!! URL::to('branch/orders') !!}"> <i class="fa fa-shopping-cart"></i> <span><?php echo trans('messages.Manage Orders'); ?></span> </a></li>
			<li><a href="{!! URL::to('branch/branch_workingtime/'.Session('branch_id')) !!}"> <i class="fa fa-calendar"></i> <span><?php echo trans('messages.Update Branch Work Time'); ?></span> </a></li>
		</ul>
        </section> <!-- /.sidebar -->
      </aside>

   @yield('content')   
   
{!! Html::script('assets/admin/js/jQuery-2.1.4.min.js') !!}
<footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b><?php echo trans('messages.Version'); ?></b> 1.0
        </div>
        <?php echo trans('messages.Copyright'); ?> &<?php echo trans('messages.copy'); ?>; <?php echo date('Y'); ?> <strong><a href="#">Shuneez <?php echo trans('messages.Admin Panel'); ?></a>.</strong> <?php echo trans('messages.All rights reserved'); ?>.
      </footer>
	  
</div><!-- ./wrapper -->

<script type="text/javascript" src='https://maps.google.com/maps/api/js?key=AIzaSyB5S43ovL2gYBhNreg2EeVzTPSAZARKqrY&sensor=false&libraries=places,drawing,geometry'></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

{!! Html::script('assets/js/locationpicker.jquery.js') !!}
{!! Html::script('assets/admin/js/jquery-ui.min.js') !!}
{!! Html::script('assets/admin/js/custom-scripts.js') !!}
{!! Html::script('assets/admin/js/bootstrap-datepicker.js') !!}
{!! Html::script('assets/admin/js/bootstrap.min.js') !!}
{!! Html::script('assets/admin/js/raphael-min.js') !!}
{!! Html::script('assets/admin/js/select2.full.min.js') !!}
{!! Html::script("assets/admin/js/jquery.dataTables.min.js") !!}
{!! Html::script("assets/admin/js/buttons.html5.min.js") !!}
{!! Html::script("assets/admin/js/dataTables.buttons.min.js") !!}
{!! Html::script("assets/admin/js/dataTables.tableTools.js") !!}
{!! Html::script("assets/admin/js/dataTables.bootstrap.min.js") !!}
{!! Html::script('assets/admin/js/app.min.js') !!}
{!! Html::script('assets/admin/js/bootstrap3-wysihtml5.all.min.js') !!}
{!! Html::style("assets/admin/css/buttons.dataTables.min.css") !!}
    <script>
		$(function () {
                
			$(".selectLists").select2({
				placeholder: "<?php echo trans('messages.Please Select'); ?>"
			});
		});
	</script>
<script>
function changestatus()
{

    $.ajax({
    type : "GET",
    dataType : "json",
    url  : "<?php echo URL::to('changestatus'); ?>",
    async: true,
    success: function(result) {
      document.getElementById('notification_count').innerHTML = 0; 
        }
      });
}

function select_language()
{
	var url = "<?php echo URL::to(''); ?>";
	var language = $( "#language option:selected" ).val();
	if(language != '')
	window.location.href = url+'/branch/changelanguage/'+language;
}
</script>
</body>
</html>
