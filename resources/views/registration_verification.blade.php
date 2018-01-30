@extends('header')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error') ?> @endif
<div class="container">
          <div class="verfiy-page">
          <div class="bred1">
            <a href="{!! URL::to('/') !!}" class="bred2"><?php echo trans('frontend.Home'); ?></a><img src="{!! URL::to('assets/images/breadcrumb-arrow.png') !!}" class="bred"><a href="#"><?php echo trans('frontend.Registration Confirmation'); ?></a>
          </div>
          </div>

          <div class="row" style="margin-bottom:110px;">
            <div class="col-md-2"></div>
            <div class="col-md-8">
              <div class="verify-box">
                <img src="{!! URL::to('assets/images/mobile-icon.png') !!}" class="verify-mob">
                <p class="text-center" style="font-size:18px;"><?php echo trans('frontend.Confirm your mobile number'); ?></p>
                <hr>
                <p class="text-center" style="font-size:16px;"><?php echo trans("frontend.We've sent an SMS containing your verification code to"); ?> <?php echo Session('mobile'); ?><br>
				        <?php echo trans('frontend.Please, enter the code in the form below to confirm your mobile number'); ?></p>
				        <p class="text-center success_msg" style="color:green" id="msg"></p>
                <p class="error_msg text-center" id="error_msg"></p>
                @if(Session::has('error'))<p class="error_msg text-center"> <?php echo $error; ?></p>@endif
                <div class="ver-code">
                <ul class="listing_align">
                  <li><p><?php echo trans('frontend.Verification Code'); ?></p></li>
                  {!! Form::open(array('url' => 'registration_otpverification', 'id' => 'otp_form')) !!}
                  <li><input name="otp" id="otp" type="text" value="<?php echo Input::old('otp'); ?>" style="width:100%;height:30px;border-radius:1px;border:1px solid #d4d4d4;"></li>
                 <input type="hidden" name="user_id" id="user_id" value="<?php echo Session('user_id'); ?>">
                  <li><button type="button" onclick="validate_form();" class="verify-btn"><?php echo trans('frontend.Verify'); ?></button></li>
                  {!! Form::close() !!}
                </ul>
                <hr>
                <p class="text-center" style="font-size:16px;"><?php echo trans('frontend.You did not receive a verification code?'); ?> <a href="#" onclick="resend_otp();"><?php echo trans('frontend.Resend code'); ?></a></p>
                  </div>
                  <p class="text-center" style="margin-top:10px;font-size:16px;margin-bottom:40px;"><?php echo trans('frontend.Your order will be finalized once you confirm your code'); ?></p>
                  <div class="code-once">
                  <p class="text-center" style="font-size:16px;"><?php echo trans('frontend.You need to confirm your code only once'); ?></p>
                </div>
                </div>
                
              </div>
            </div>
            <div class="col-md-2"></div>
          </div>
    <div class="modal_load"></div>	
<script>

function resend_otp(id)
{
  $('.error_msg').text('');
	var user_id = $("#user_id").val();
	var mobile = '<?php echo Session('mobile'); ?>';
	$.ajax({
		beforeSend: function () {
			$("body").addClass("loading");
		},
		type: "GET",
		url: "<?php echo URL::to('registration_resendotp'); ?>",
		data: {'user_id': user_id, 'mobile': mobile},
		async: true,
		success: function (result) {
			 $("body").removeClass("loading");
			 $("#msg").text('Otp send successfully...');
		}
	}); 
}

function validate_form()
{
	var otp = $("#otp").val();
	if(otp == '')
	{
		$("#error_msg").text('Please Enter otp');
	}
	else
	{
		$("#otp_form").submit();
	}
}
</script>
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
                url(<?php echo URL::to('assets/images/loading.gif'); ?>) 
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
@endsection
