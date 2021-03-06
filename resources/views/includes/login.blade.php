<!--Login Modal dialog-->
   <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
      <div class="modal-dialog">
         <div class="loginmodal-container">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h1><?php echo trans('frontend.LOGIN'); ?></h1>
            <br>
            @if(Session::has('login_check'))<p class="lerror" style='color:red;'>  <?php echo Session::get('login_check'); ?></p> @endif
            {!! Form::open(array('url' => 'login')) !!}
            <input type="text" name="username" placeholder="<?php echo trans('frontend.Email'); ?>" value="<?php echo Input::old('username'); ?>">
            @if(Session::has('login_error'))<span class="error_msg"> <?php echo ($login_error->first('username') != '') ? $login_error->first('username') : ''; ?></span>@endif
            <input type="password" name="password" placeholder="<?php echo trans('frontend.Password'); ?>">
            @if(Session::has('login_error'))<span class="error_msg"> <?php echo ($login_error->first('password') != '') ? $login_error->first('password') : ''; ?></span>@endif
            <input type="submit" name="login" class="deliver-btn" value="<?php echo trans('frontend.Login'); ?>" style="margin-top:15px;">
            {!! Form::close() !!}
            <div class="login-help">
               <p style="text-align: center;"><a href="#" id="forgot_pwd" class="forgot" data-toggle="modal" data-target="#myModal"><?php echo trans('frontend.I forgot my password'); ?></a></p>
               <h3 style="text-align: center;font-size:18px;color:#3e3e3e;"><?php echo trans('frontend.Don’t have an account?'); ?> <a id="close_login_up" href="#" data-toggle="modal" data-target="#signup-modal" ><?php echo trans('frontend.Signup now for free'); ?></a></h3>
            </div>
         </div>
      </div>
   </div>
   <!--End of Modal Dialog-->
   <!--SignUp Modal dialog-->
   <div class="modal fade" id="signup-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
      <div class="modal-dialog">
         <div class="loginmodal-container">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h1>SIGNUP</h1>
            <br>
            @if(Session::has('signup_success'))<p class="lerror" style='color:red;'> <?php echo Session::get('signup_success'); ?> </p> @endif
            {!! Form::open(array('url' => 'register')) !!}
            <!--<meta name="csrf-token" content="{{ csrf_token() }}">-->
            <input class="validation required" type="text" id="firstname" name="first_name" placeholder="<?php echo trans('frontend.First Name'); ?>" value="<?php echo Input::old('first_name'); ?>">
            <span class="ferror" style='color:red;'></span>
            @if(Session::has('signup_error'))<span class="error_msg"> <?php echo ($error->first('first_name') != '') ? $error->first('first_name') : ''; ?></span>@endif
            <input type="text" class="validation required" id="lastname" name="last_name" placeholder="<?php echo trans('frontend.Last Name'); ?>" value="<?php echo Input::old('last_name'); ?>">
            <span class="lerror" style='color:red;'></span>
            @if(Session::has('signup_error'))<span class="error_msg"> <?php echo ($error->first('last_name') != '') ? $error->first('last_name') : ''; ?></span>@endif
            <input type="text" class="validation required" id="email" name="email" placeholder="<?php echo trans('frontend.Email'); ?>" value="<?php echo Input::old('email'); ?>">
            @if(Session::has('signup_error'))<span class="error_msg"> <?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></span><br>@endif
            <label style="width:100%;float:left;"><?php echo trans('frontend.Mobile'); ?><span class="req">*</span></label>
             
             <input type="text" value="+966" style=" text-align:center;   width: 15%;float: left;border-left:0;border-top:0;display: inline-block;height: 44px;border: 1px solid #ddd;border-right: none;" readonly>
             
            <input type="text" class="validation required" style="width:85%;float:left;display:inline-block;"name="mobile" maxlength=10 placeholder="<?php echo trans('frontend.Enter mobile number without country code'); ?>" value="<?php echo Input::old('mobile'); ?>">
            
            @if(Session::has('signup_error'))<span class="error_msg"> <?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></span>@endif
            <input id="passwordfield" class="validation required" type="password" name="password" placeholder="<?php echo trans('frontend.Password'); ?>" value="<?php echo Input::old('password'); ?>">
            @if(Session::has('signup_error'))<span class="error_msg"> <?php echo ($error->first('password') != '') ? $error->first('password') : ''; ?></span>@endif
            <input type="submit" name="Signup" class="deliver-btn" value="Signup" style="margin-top:15px;"> 
            {!! Form::close() !!}
            <div class="login-help">
               <h3 style="text-align: center;font-size:18px;color:#3e3e3e;"><?php echo trans('frontend.Already on Shuneez?'); ?><a  id="close_sign_up" href="#" data-toggle="modal" data-target="#login-modal"><?php echo trans('frontend.Login'); ?></a></h3>
            </div>
         </div>
      </div>
   </div>
   <!--End of Sign Up Modal Dialog-->
   <!-- Forgot Password -->
   <div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-sm" role="document">
         <div class="modal-content1">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
               <h4 class="modal-title" id="myModalLabel"><?php echo trans('frontend.Forgot Password'); ?></h4>
            </div>
            <div class="modal-body">
               <div class="msg"></div>
               <div class="form-group has-feedback">
                  <input id="forgot_field" type="text" name="email" class="form-control" placeholder="<?php echo trans('messages.Email'); ?>">
                  <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
               </div>
            </div>
            <div class="modal-footer forgot_btn">
               <button type="button" class="deliver-btn" id="forgot_btn"><?php echo trans('frontend.Sent Email'); ?></button> 
            </div>
         </div>
      </div>
   </div>
   <?php if(Session::has('signup_error') || Session::has('signup_success')) { ?>
   <script>
      $(document).ready(function()
      {
        $('#signup-modal').modal('show');
      });
   </script>
   <?php } ?>  
   <?php if(Session::has('login_error') || Session::has('login_check')) { ?>
   <script>
      $(document).ready(function()
      {
        $('#login-modal').modal('show');
      });
   </script>
   <?php } ?> 
