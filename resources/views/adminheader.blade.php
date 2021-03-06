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
				
					<li><a href="{!! URL::to('admin/settings') !!}" class="settings"><i class="fa fa-cog"></i> <?php echo trans('messages.Settings'); ?></a></li>
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
						<span class="hidden-xs"><?php echo (Session('new_name')) ? Session('new_name') : Session('name'); ?></span></a>
					
					<ul class="dropdown-menu">                  
						<li class="user-header">
							<img src="{!! URL::to('assets/admin/images/admin.png') !!}" class="img-circle" alt="User Image">
							<p><?php echo (Session('new_name')) ? Session('new_name') : Session('name'); ?>
								<small>
									<?php
									if(Session('is_admin') == 1)
									{
										echo 'Super Admin';
									}
									elseif(Session('user_role') == 0)
									{
										echo 'Store Manager';
									}
									?>
								</small></p>
						</li>                  
					<!-- Menu Footer-->
						<li class="user-footer">
							<div class="pull-left">
								<a href="{!! URL::to('admin/settings') !!}" class="btn btn-info sign_out"><?php echo trans('messages.Profile'); ?> </a>
							</div>
							<div class="pull-right">
								<a href="{!! URL::to('admin/logout') !!}" class="btn btn-primary sign_out"><?php echo trans('messages.Sign out'); ?></a>
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
            <li><a href="{!! URL::to('admin/dashboard') !!}"> <i class="fa fa-home"></i> <span><?php echo trans('messages.Dashboard'); ?></span> </a></li>
           <li class="treeview">
				<a> <i class="fa fa-users"></i> <span><?php echo trans('messages.Manage Branches'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<!--<li><a href="{!! URL::to('admin/vendors') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Branches'); ?></a></li>-->
					<li><a href="{!! URL::to('admin/branches') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Branches'); ?></a></li>
					<li><a href="{!! URL::to('admin/staffs') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Staffs'); ?></a></li>
					<li><a href="{!! URL::to('admin/ingredients') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Ingredients'); ?></a></li>
					<li><a href="{!! URL::to('admin/ingredientlist') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Ingredient Type'); ?></a></li>
					<li><a href="{!! URL::to('admin/execlusions') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Execlusions'); ?></a></li>
					<li><a href="{!! URL::to('admin/vendoritems') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Items'); ?></a></li>
				</ul>
			</li>
			<li class="treeview">
				<a> <i class="fa fa-cutlery"></i> <span><?php echo trans('messages.Manage Categories'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<li><a href="{!! URL::to('admin/categories') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Categories'); ?></a></li>
					<li><a href="{!! URL::to('admin/subcategories') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Subcategories'); ?></a></li>
				</ul>
			</li>
			<li class="treeview">
				<a> <i class="fa fa-users"></i> <span><?php echo trans('messages.Manage Users'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<li><a href="{!! URL::to('admin/users') !!}"> <i class="fa fa-angle-right"></i> <span><?php echo trans('messages.Manage Users'); ?></span> </a></li>
					<li><a href="{!! URL::to('admin/sendnewsletter') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Send Newsletter'); ?></a></li>
					<li><a href="{!! URL::to('admin/subscribers') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Subscribers'); ?></a></li>
                </ul>
			</li>
			<?php if(Session('is_admin')) { ?><li><a href="{!! URL::to('admin/adminusers') !!}"> <i class="fa fa-users"></i> <span><?php echo trans('messages.Manage Adminusers'); ?></span> </a></li><?php } ?>
			<li class="treeview">
				<a> <i class="fa fa-users"></i> <span><?php echo trans('messages.Manage Deliveryboys'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<li><a href="{!! URL::to('admin/deliveryboys') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Deliveryboys'); ?></a></li>
					<li><a href="{!! URL::to('admin/track-deliveryboys') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Track Deliveryboys'); ?></a></li>
					<li><a href="{!! URL::to('admin/deliveryboy-ratings') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Deliveryboy Ratings'); ?></a></li>
				</ul>
			</li>
			
			<li><a href="{!! URL::to('admin/addresstype') !!}"> <i class="fa fa-users"></i> <span><?php echo trans('messages.Manage Address Type'); ?></span> </a></li>
			<li><a href="{!! URL::to('admin/orders') !!}"> <i class="fa fa-shopping-cart"></i> <span><?php echo trans('messages.Manage Orders'); ?></span> </a></li>

			<li><a href="#"> <i class="fa fa-cogs"></i> <span><?php echo trans('messages.Manage Masters'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<li><a href="{!! URL::to('admin/promocodes') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Promocode'); ?></a></li>
					<li><a href="{!! URL::to('admin/currencies') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Currency'); ?></a></li>
					<!--<li><a href="{!! URL::to('admin/citylist') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Manage Cities'); ?></a></li>-->
				</ul>
			</li>
			<li><a href="#"> <i class="fa fa-cogs"></i> <span><?php echo trans('messages.Manage Languages'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<li><a href="{!! URL::to('admin/backend_languages') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Update Backend Languages'); ?></a></li>
					<li><a href="{!! URL::to('admin/frontend_languages') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Update Frontend Languages'); ?></a></li>
					<li><a href="{!! URL::to('admin/api_languages') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Update Api Languages'); ?></a></li>
				</ul>
			</li>
			<li><a href="{!! URL::to('admin/faqs') !!}"> <i class="fa fa-envelope"></i> <span><?php echo trans('messages.Manage Faq'); ?></span> </a></li>
			<li><a href="#"> <i class="fa fa-file-text-o"></i> <span><?php echo trans('messages.Manage Reports'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<li><a href="{!! URL::to('admin/branch_report') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Branch Sales'); ?></a></li>
					<li><a href="{!! URL::to('admin/item_report') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Item Sales'); ?></a></li>
					<li><a href="{!! URL::to('admin/sales_report') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Sales Report'); ?></a></li>
					<li><a href="{!! URL::to('admin/deliveryboy_sales_report') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Deliveryboy Sales'); ?></a></li>
					<li><a href="{!! URL::to('admin/branch_hour_report') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Branch Hour Report'); ?></a></li>
					<li><a href="{!! URL::to('admin/deliveryboy_hour_report') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Deliveryboy Hour Report'); ?></a></li>
				</ul>
			</li>
			<li><a href="{!! URL::to('admin/enquires') !!}"> <i class="fa fa-envelope"></i> <span><?php echo trans('messages.Manage Enquires'); ?></span> </a></li>
			<li><a href="#"> <i class="fa fa-envelope"></i> <span><?php echo trans('messages.Integration'); ?></span> <i class="fa fa-angle-left pull-right"></i> </a> 
				<ul class="treeview-menu">
					<li><a href="#"><i class="fa fa-angle-right"></i><?php echo trans('messages.Foodics Mapping'); ?></a>
					<ul class="treeview-menu">
						<li>
							<a href="{!! URL::to('admin/branch-integration') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Branch Mapping'); ?></a>
						</li>
						<li>
							<a href="{!! URL::to('admin/item_integration') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Item Mapping'); ?></a>
						</li>
						<li>
							<a href="{!! URL::to('admin/execlusion_integration') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Execlusion Mapping'); ?></a>
						</li>
					</ul>
					</li>
				</ul>
				<ul class="treeview-menu">
					<li><a href="#"><i class="fa fa-angle-right"></i><?php echo trans('messages.Dook Mapping'); ?></a>
					<ul class="treeview-menu">
						<li>
							<a href="{!! URL::to('admin/teams') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Dook Teams'); ?></a>
						</li>
						<li>
							<a href="{!! URL::to('admin/pickup-points') !!}"><i class="fa fa-angle-right"></i><?php echo trans('messages.Pickup Points'); ?></a>
						</li>
					</ul>
					</li>
					
				</ul>
			</li>
			<li class="treeview">
				<a> <i class="fa fa-users"></i> <span>Notification Center</span> <i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
					<li><a href="{!! URL::to('admin/notification/auto') !!}"><i class="fa fa-angle-right"></i>Automatic Messages Configiration</a></li>
					<li><a href="{!! URL::to('admin/notification/send') !!}"><i class="fa fa-angle-right"></i>Send Message Manually</a></li>
				</ul>
			</li>
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

<script type="text/javascript" src='https://maps.google.com/maps/api/js?sensor=true&key=AIzaSyB5S43ovL2gYBhNreg2EeVzTPSAZARKqrY&libraries=places,drawing,geometry'></script>
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
			placeholder: function(){
        		$(this).data('placeholder');
    		}
		});
	});

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
	window.location.href = url+'/admin/changelanguage/'+language;
}
</script>
</body>
</html>
