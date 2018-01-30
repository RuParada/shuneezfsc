<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SHUNEEZ | Log in</title>    
    <link rel="icon" href="{!! URL::to('assets/images/favicon.ico') !!}" type="image/x-icon" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    {!! Html::style('assets/admin/css/bootstrap.min.css') !!}
    {!! Html::style('assets/admin/css/font-awesome.min.css') !!}
    {!! Html::style('assets/admin/css/AdminLTE.min.css') !!}	
    <!--[if lt IE 9]>
		<script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
  @if(Session::has('login_error')) <?php $error = Session::get('login_error') ?> @endif
  @if(Session::has('login_check')) <?php $login_check = Session::get('login_check') ?> @endif
  <body class="hold-transition login-page">
 
    <div class="login-box">
      
	  <div class="login-logo"> Login in to <b>SHUNEEZ</b> </div>
	 
      <div class="login-box-body">
        @if(Session::has('login_check'))<p class="error_msg"><?php echo $login_check; ?></p>@endif  
        @if(Session::has('succ_msg'))<p class="error_msg"><?php echo Session('succ_msg'); ?></p>@endif     
        {!! Form::open(array('url' => 'branch/login')) !!}
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Email" name="email" value="<?php echo Input::old('email'); ?>">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @if(Session::has('login_error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password" name="password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            @if(Session::has('login_error'))<p class="error_msg"><?php echo ($error->first('password') != '') ? $error->first('password') : ''; ?></p>@endif
          </div>
          <div class="row">
            <div class="col-xs-12">
              <span class="login_btn"><button type="submit" class="btn btn-primary btn-block btn-flat">Log in</button>
            </div><!-- /.col -->
          </div>
        {!! Form::close() !!}		
      </div><!-- /.login-box-body -->
	  <a href="#" class="forgot" data-toggle="modal" data-target="#myModal">I forgot my password</a>
    </div><!-- /.login-box -->
	
<!-- Forgot Password -->
<div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="close_btn" aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Forgot Password</h4>
      </div>
		  <div class="modal-body"> 
        <div class="msg"></div>       
			  <div class="form-group has-feedback">
				<input type="text" name='mail' value='<?php echo Input::old('mail'); ?>' id='forgot_field' class="form-control" placeholder="Email">
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			  </div>         
		  </div>
		  <div class="modal-footer forgot_btn">
			<button type="button" class="btn btn-default close_btn" data-dismiss="modal" id="close_btn">Close</button>
			<button type="button" class="btn btn-primary" id="forgot_btn">Sent Email</button>	
		  </div>
    </div>
  </div>
</div>	
<div class="modal_load"></div>	
    {!! Html::script('assets/admin/js/jQuery-2.1.4.min.js') !!}
    {!! Html::script('assets/admin/js/bootstrap.min.js') !!}
	
	<style>
  .modal_load {
  display:    none;
  position:   fixed;
  z-index:    1000;
  top:        0;
  left:       0;
  height:     100%;
  width:      100%;
  background: rgba( 255, 255, 255, .8 ) 
  url('<?php echo URL::to('assets/images/loading.gif'); ?>') 
      50% 50% 
      no-repeat;
  }
      
body.loading {
    overflow: hidden;   
}

body.loading .modal_load {
    display: block;
}
</style>

    <script>
      $('#forgot_btn').click(function(){
        var email = $("#forgot_field").val();
        $.ajax({
          beforeSend : function() {
          $("body").addClass("loading");
          },
         type: "GET",
         url: "branch/forgotpassword",
         dataType:"json",
         data: {'email' : email},
         async: true,
         success:  function(result){
            $('.msg').html(result.msg)
            $("body").removeClass("loading");
          }
        });
      });

      $(".close_btn").click(function()
      {
        location.reload();
      });
    </script>
  </body>
</html>
