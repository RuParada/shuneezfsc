@extends('adminheader')

@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif
<div class="content-wrapper">

    <section class="content-header">
        <h1><?php echo trans('messages.Admin Configuration'); ?> <button onclick="window.history.back();" class="btn btn-primary pull-right"><i class="fa fa-chevron-left"></i><?php echo trans('messages.Back'); ?></button></h1>
        @if(Session::has('success'))<p class="success_msg"><?php echo Session::get('success'); ?></p>@endif
    </section>

    <!-- Main content -->
    <section class="content settings">
        <div class="row">

            <div class="col-md-6">    
                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Admin Settings'); ?></h3>
                    </div><!--box-header-->	
                    {!! Form::open(array('url' => 'admin/updateadmin')) !!}
                    <div class="box-body two_col_form">
                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Username'); ?><span class="req">*</span></label>
                                <input type="text" name="username" class="form-control" value="<?php echo (Session::has('new_username')) ? Session('new_username') : Session('username'); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('username') != '') ? $error->first('username') : ''; ?></p>@endif
                            </div>
                        </div>	

                        <div class="col-sm-6 right">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Password'); ?></label>
                                <input type="password" name="password" class="form-control" >
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('password') != '') ? $error->first('password') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Display Name'); ?><span class="req">*</span></label>
                                <input type="text" name="name" class="form-control" value="<?php echo (Session::has('new_name')) ? Session('new_name') : Session('name'); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('name') != '') ? $error->first('name') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="col-sm-6 right">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Email'); ?><span class="req">*</span></label>
                                <input type="text" name="email" class="form-control" value="<?php echo (Session::has('new_email')) ? Session('new_email') : Session('email'); ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
                            </div>
                        </div>

                    </div><!--box-body-->  

                    <div class="box-footer">
                        <input type="hidden" name="id" value="<?php echo Session('admin_userid'); ?>">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Settings'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}
                </div><!--box-->	

                <div class="box">
                    <?php
                    if (count($smtp_settings) > 0) {
                        foreach ($smtp_settings as $smtp_setting) {
                            $config[$smtp_setting->setting_name] = $smtp_setting->setting_value;
                        }
                    }
                    ?>	
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.SMTP Settings'); ?></h3>
                    </div><!--box-header-->	
                    {!! Form::open(array('url' => 'admin/updatesmtp_settings')) !!}
                    <div class="box-body two_col_form">
                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.SMTP Host'); ?><span class="req">*</span></label>
                                <input type="text" name="host" class="form-control" value="<?php echo (Input::old('host') != '') ? Input::old('host') : $config['host']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('host') != '') ? $error->first('host') : ''; ?></p>@endif
                            </div>
                        </div>	

                        <div class="col-sm-6 right">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.SMTP Username'); ?><span class="req">*</span></label>
                                <input type="text" name="smtp_username" class="form-control" value="<?php echo (Input::old('smtp_username') != '') ? Input::old('smtp_username') : $config['smtp_username']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('smtp_username') != '') ? $error->first('smtp_username') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.SMTP Password'); ?><span class="req">*</span></label>
                                <input type="password" name="smtp_password" class="form-control" value="<?php echo (Input::old('smtp_password') != '') ? Input::old('smtp_password') : $config['smtp_password']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('smtp_password') != '') ? $error->first('smtp_password') : ''; ?></p>@endif
                            </div>
                        </div>

                        <div class="col-sm-6 right">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.SMTP Port'); ?><span class="req">*</span></label>
                                <input type="text" name="port" class="form-control" value="<?php echo (Input::old('port') != '') ? Input::old('port') : $config['port']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('port') != '') ? $error->first('port') : ''; ?></p>@endif
                            </div>
                        </div>
                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Transport Layer Security'); ?><span class="req">*</span></label>
                                <input type="text" name="security" class="form-control" value="<?php echo (Input::old('security') != '') ? Input::old('security') : $config['security']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('security') != '') ? $error->first('security') : ''; ?></p>@endif
                            </div>
                        </div>

                    </div><!--box-body-->  

                    <div class="box-footer">
                        <input type="hidden" name="id" value="<?php echo Session('admin_userid'); ?>">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Settings'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}
                </div><!--box-->
                
                	

					<?php
					if (count($settings) > 0) {
						foreach ($settings as $setting) {
							$config_data[$setting->setting_name] = $setting->setting_value;
						}
					}
					?>					
                  <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Image Settings'); ?></h3>
                    </div><!--box-header-->	
                    {!! Form::open(array('url' => 'admin/updateimage_settings', 'files' => 1)) !!}
                    <div class="box-body two_col_form">
                        <div class="form-group">
                            <label class="col-sm-6 control-label"><?php echo trans('messages.Upload Banner'); ?>(1920 * 600) </label>
                            <div class="col-sm-6">
                                <input type="file" name="banner" class="form-control upload_hide" id="upload_banner">
                                <label for="upload_banner" class="upload_lbl">
                                    <img src="<?php echo URL::to(($config_data['banner'] != '') ? 'assets/uploads/settings/' . $config_data['banner'] : 'assets/admin/images/not-found.png'); ?>" class="bannerimg">
                                </label><br>
                                <span class="error_msg" id="error">@if(Session::has('error'))<?php echo ($error->first('banner') != '') ? $error->first('banner') : ''; ?>@endif</span>
                            </div>
                        </div>


                    </div><!--box-body-->  

                    <div class="box-footer">
                        <input type="hidden" name="id" value="<?php echo Session('admin_userid'); ?>">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Settings'); ?></button>
                    </div><!-- box-footer -->
                    {!! Form::close() !!}
                </div><!--box-->

            </div><!--col-md-8-->

            <div class="col-md-6">    
                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo trans('messages.Website Contact'); ?></h3>
                    </div><!--box-header-->
                    {!! Form::open(array('url' => 'admin/updatesite_settings')) !!}
                    <div class="box-body two_col_form">

                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Email ID'); ?></label>
                                <input type="text" name="email" class="form-control" value="<?php echo (Input::old('email') != '') ? Input::old('email') : $config_data['email']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif 
                            </div>
                        </div>	

                        <div class="col-sm-6 right">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Mobile'); ?></label>
                                <input type="text" name="mobile" class="form-control" value="<?php echo (Input::old('mobile') != '') ? Input::old('mobile') : $config_data['mobile']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></p>@endif 
                            </div>
                        </div>
                        
                        <div class="col-sm-12 plr0">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Copyright'); ?></label>
                                <input type="text" name="copyright" class="form-control" value="<?php echo (Input::old('copyright') != '') ? Input::old('copyright') : $config_data['copyright']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('copyright') != '') ? $error->first('copyright') : ''; ?></p>@endif 
                            </div>
                        </div>

                        <div class="col-sm-12 plr0">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Address'); ?></label>
                                <textarea name="address" class="form-control"><?php echo (Input::old('address') != '') ? Input::old('address') : $config_data['address']; ?></textarea>
                            </div>
                        </div>
                        <h4 class="subhead"><?php echo trans('messages.Meta Details'); ?></h4>
                        <div class="col-sm-12 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Meta Keyword'); ?></label>
                                <input type="text" name="meta_keyword" class="form-control" value="<?php echo (Input::old('meta_keyword') != '') ? Input::old('meta_keyword') : $config_data['meta_keyword']; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('meta_keyword') != '') ? $error->first('meta_keyword') : ''; ?></p>@endif 
                            </div>
                        </div>	


                        <div class="col-sm-12 plr0">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Meta Description'); ?></label>
                                <textarea name="meta_description" class="form-control"><?php echo (Input::old('meta_description') != '') ? Input::old('meta_description') : $config_data['meta_description']; ?></textarea>
                            </div>
                        </div>

                        <!--<div class="col-sm-12 plr0">                    
                            <div class="form-group">
                                <label class="control-label">< ?php echo trans('messages.Website Description'); ?></label>
                                <textarea name="website_content" class="form-control">< ?php echo (Input::old('website_content') != '') ? Input::old('website_content') : $config_data['website_content']; ?></textarea>
                            </div>
                        </div>-->

                        <h4 class="subhead"><?php echo trans('messages.Social Links'); ?></h4>

                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Facebook'); ?></label>
                                <input type="text" name="facebook" class="form-control" value="<?php echo (Input::old('facebook') != '') ? Input::old('facebook') : $config_data['facebook']; ?>">
                            </div>
                        </div>	

                        <div class="col-sm-6 right">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Twitter'); ?></label>
                                <input type="text" name="twitter" class="form-control" value="<?php echo (Input::old('twitter') != '') ? Input::old('twitter') : $config_data['twitter']; ?>">
                            </div>
                        </div>

                        <div class="col-sm-6 left">					
                            <div class="form-group">
                                <label class="control-label"><?php echo trans('messages.Google+'); ?></label>
                                <input type="text" name="googleplus" class="form-control" value="<?php echo (Input::old('googleplus') != '') ? Input::old('googleplus') : $config_data['googleplus']; ?>">
                            </div>
                        </div>
                        
                        <table class="other_settings">
					<tr>
						<th><?php echo trans('messages.Configuration Items'); ?></th>
						<th><?php echo trans('messages.Action'); ?></th>
					</tr>
					<tr>
						<td><?php echo trans('messages.Auto Allocation of Delivery boy'); ?> </td>
						<td class="onoff">
							<input type="checkbox" name="auto_allocation" class="onoff_chck" id="onoff" value="1" <?php echo ($config_data['auto_allocation'] == 1) ? 'checked' : ''; ?>>
							<label class="onoff_lbl" for="onoff"></label>							
						</td>
					</tr>					
					<tr>
						<td><?php echo trans('messages.Order Accept Time Limit in Minutes'); ?></td>
						<td> 
							<input type="text" name="order_accept_timelimit" class="form-control" value="<?php echo (Input::old('order_accept_timelimit') != '') ? Input::old('order_accept_timelimit') : $config_data['order_accept_timelimit'] / 60; ?>"> 
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('order_accept_timelimit') != '') ? $error->first('order_accept_timelimit') : ''; ?></p>@endif
						</td>
					</tr>
					<tr>
						<td><?php echo trans('messages.Delivery boy Radius in KM'); ?></td>
						<td> 
							<input type="text" name="deliveryboy_radius" class="form-control" value="<?php echo (Input::old('deliveryboy_radius') != '') ? Input::old('deliveryboy_radius') : $config_data['deliveryboy_radius']; ?>">
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('deliveryboy_radius') != '') ? $error->first('deliveryboy_radius') : ''; ?></p>@endif 
						</td>
					</tr>					
					<tr>
						<td colspan="2" class="sub_head"><?php echo trans('messages.Tax Amount'); ?> </td>
					</tr>					
					<!--<tr>
						<td>< ?php echo trans('messages.Service tax in'); ?> % </td>
						<td> 
							<input type="text" name="service_tax" class="form-control" value="< ?php echo (Input::old('service_tax') != '') ? Input::old('service_tax') : $config_data['service_tax']; ?>"> 
							@if(Session::has('error'))<p class="error_msg">< ?php echo ($error->first('service_tax') != '') ? $error->first('service_tax') : ''; ?></p>@endif 
						</td>
					</tr>-->
					<tr>
						<td><?php echo trans('messages.Vat tax in'); ?> %</td>
						<td> 
							<input type="text" name="vat" class="form-control" value="<?php echo (Input::old('vat') != '') ? Input::old('vat') : $config_data['vat']; ?>"> 
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('vat') != '') ? $error->first('vat') : ''; ?></p>@endif 
						</td>
					</tr>
					
					<tr>
						<td colspan="2" class="sub_head"><?php echo trans('messages.Time Setting'); ?> </td>
					</tr>
					<tr>
						<td><?php echo trans('messages.Delivery time in minutes'); ?></td>
						<td> 
							<input type="text" name="delivery_time" class="form-control" value="<?php echo (Input::old('delivery_time') != '') ? Input::old('delivery_time') : $config_data['delivery_time']; ?>"> 
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('delivery_time') != '') ? $error->first('delivery_time') : ''; ?></p>@endif 
						</td>
					</tr>
					<tr>
						<td><?php echo trans('messages.Pickup time in minutes'); ?></td>
						<td> 
							<input type="text" name="pickup_time" class="form-control" value="<?php echo (Input::old('pickup_time') != '') ? Input::old('pickup_time') : $config_data['pickup_time']; ?>"> 
							@if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('pickup_time') != '') ? $error->first('pickup_time') : ''; ?></p>@endif 
						</td>
					</tr>
                    <tr>
                        <td><?php echo trans('messages.Order Pickup Start Time in minutes'); ?></td>
                        <td> 
                            <input type="text" name="start_time" class="form-control" value="<?php echo (Input::old('start_time') != '') ? Input::old('start_time') : $config_data['start_time']; ?>"> 
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('start_time') != '') ? $error->first('start_time') : ''; ?></p>@endif 
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo trans('messages.Order Pickup End Time in minutes'); ?></td>
                        <td> 
                            <input type="text" name="end_time" class="form-control" value="<?php echo (Input::old('end_time') != '') ? Input::old('end_time') : $config_data['end_time']; ?>"> 
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('end_time') != '') ? $error->first('end_time') : ''; ?></p>@endif 
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo trans('messages.Dook Order Assign Time in minutes'); ?></td>
                        <td> 
                            <input type="text" name="dook_assign_time" class="form-control" value="<?php echo (Input::old('dook_assign_time') != '') ? Input::old('dook_assign_time') : $config_data['dook_assign_time']; ?>"> 
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('dook_assign_time') != '') ? $error->first('dook_assign_time') : ''; ?></p>@endif 
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo trans('messages.Dook Order Assign Status'); ?></td>
                        <td> 
                            <select name="dook_order_status">
                                <option value="a" {!! ( $config_data['dook_order_status'] == 'a' ) ? 'selected' : '' !!}>{!! trans('messages.Accepted') !!}</option>
                            </select>
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('dook_assign_time') != '') ? $error->first('dook_assign_time') : ''; ?></p>@endif 
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo trans('messages.Dook Access Token'); ?></td>
                        <td> 
                            <input type="text" name="dook_access_token" class="form-control" value="<?php echo (Input::old('dook_access_token') != '') ? Input::old('dook_access_token') : $config_data['dook_access_token']; ?>"> 
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('dook_access_token') != '') ? $error->first('dook_access_token') : ''; ?></p>@endif 
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo trans('messages.Dook Company Id'); ?></td>
                        <td> 
                           <input type="text" name="dook_company_id" class="form-control" value="<?php echo (Input::old('dook_company_id') != '') ? Input::old('dook_company_id') : $config_data['dook_company_id']; ?>"> 
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('dook_company_id') != '') ? $error->first('dook_company_id') : ''; ?></p>@endif 
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo trans('messages.Dook Fleet Owner Id'); ?></td>
                        <td> 
                            <input type="text" name="dook_fleet_owner_id" class="form-control" value="<?php echo (Input::old('dook_fleet_owner_id') != '') ? Input::old('dook_fleet_owner_id') : $config_data['dook_fleet_owner_id']; ?>"> 
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('dook_fleet_owner_id') != '') ? $error->first('dook_fleet_owner_id') : ''; ?></p>@endif 
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo trans('messages.Foodics Access Token'); ?></td>
                        <td> 
                            <input type="text" name="foodics_access_token" class="form-control" value="<?php echo (Input::old('foodics_access_token') != '') ? Input::old('foodics_access_token') : $config_data['foodics_access_token']; ?>"> 
                            @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('foodics_access_token') != '') ? $error->first('foodics_access_token') : ''; ?></p>@endif 
                        </td>
                    </tr>					
			
				</table>	

                    </div><!--box-body-->  

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i><?php echo trans('messages.Update Settings'); ?></button>
                        <button type="reset" class="btn pull-right"><i class="fa fa-refresh"></i><?php echo trans('messages.Cancel'); ?></button>
                    </div><!-- box-footer -->					
                    {!! Form::close() !!}
                </div><!--box-->

                

            </div>	  
        </div>  
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script>

    function setchangeimg(val)
    {
        $('#txt_changeprofileimage').html(val);
    }
    $(document).on("change", "#upload_logo", function () {
        console.log("The text has been changed.");
        var file = this.files[0];
        var imagefile = file.type;
        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
        {
            document.getElementById('error').innerHTML = 'The image must be a file of type: jpg, jpeg, png';
            return false;
        } else
        {
            document.getElementById('error').innerHTML = '';
            var reader = new FileReader();
            reader.onload = logoIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
        $("#txt_changeprofileimage").html(file.name);
    });

    $(document).on("change", "#upload_favicon", function () {
        console.log("The text has been changed.");
        var file = this.files[0];
        var imagefile = file.type;
        var match = ["image/jpeg", "image/png", "image/jpg", "image/vnd.microsoft.icon"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]) || (imagefile == match[3])))
        {
            document.getElementById('error').innerHTML = 'The image must be a file of type: jpg, jpeg, png, ico';
            return false;
        } else
        {
            document.getElementById('error').innerHTML = '';
            var reader = new FileReader();
            reader.onload = faviconIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
        $("#txt_changeprofileimage").html(file.name);
    });

    $(document).on("change", "#upload_img", function () {
        console.log("The text has been changed.");
        var file = this.files[0];
        var imagefile = file.type;
        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
        {
            document.getElementById('error').innerHTML = 'The image must be a file of type: jpg, jpeg, png';
            return false;
        } else
        {
            document.getElementById('error').innerHTML = '';
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
        $("#txt_changeprofileimage").html(file.name);
    });

    $(document).on("change", "#upload_banner", function () {
        var file = this.files[0];
        var imagefile = file.type;
        var match = ["image/jpeg", "image/png", "image/jpg"];
        if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2])))
        {
            document.getElementById('error').innerHTML = 'The image must be a file of type: jpg, jpeg, png';
            return false;
        } else
        {
            document.getElementById('error').innerHTML = '';
            var reader = new FileReader();
            reader.onload = bannerIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
        $("#txt_changeprofileimage").html(file.name);
    });

    function imageIsLoaded(e)
    {
        var image = new Image();
        image.src = e.target.result;
        image.onload = function () {
            $(".roundedimg").attr('src', e.target.result);
        }
    }

    function faviconIsLoaded(e)
    {
        var image = new Image();
        image.src = e.target.result;
        image.onload = function () {
            $(".faviconimg").attr('src', e.target.result);
        }
    }

    function logoIsLoaded(e)
    {
        var image = new Image();
        image.src = e.target.result;
        image.onload = function () {
            $(".logoimg").attr('src', e.target.result);
        }
    }

    function bannerIsLoaded(e)
    {
        var image = new Image();
        image.src = e.target.result;
        image.onload = function () {
            $(".bannerimg").attr('src', e.target.result);
        }
    }
</script>
@endsection
