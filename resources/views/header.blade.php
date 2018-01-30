@if(Session::has('signup_error')) <?php $error = Session::get('signup_error') ?> @endif
@if(Session::has('login_error')) <?php $login_error = Session::get('login_error') ?> @endif
<?php $segment = Request::segment(1); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Shuneez</title>
  <link rel="icon" href="{!! URL::to('assets/images/favicon.ico') !!}" type="image/x-icon" />
  
  {!! Html::style('assets/css/bootstrap.min.css') !!}
  {!! Html::style('assets/css/font-awesome.min.css') !!}
  {!! Html::style('assets/css/datetimepicker.css') !!}
  {!! Html::style('assets/admin/css/jquery-clockpicker.min.css') !!}
  
  
   <?php if(isset($_SESSION['language']) && $_SESSION['language'] == 'ar') { ?>
    {!! Html::style('assets/css/rtl.css') !!}
    {!! Html::style('assets/css/media_style_rtl.css') !!}
   <?php } else { ?>
	{!! Html::style('assets/css/style.css') !!}
  {!! Html::style('assets/css/media_style.css') !!}
	<?php } ?>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    
    {!! Html::script('assets/admin/js/jQuery-2.1.4.min.js') !!}

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
    <header>
      <div class="innerbanner-section">
      
          <div class="container">
            
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                <a href="{!! URL::to('/') !!}"><img src="{!! URL::to('assets/images/Shuneez-logo-white.png') !!}" class="img-responsive inner-logo"></a>
              </div>
              <div class="col-lg-4 col-md-3 col-sm-1 col-xs-12">
                
              </div>

              <div class="col-lg-5 col-md-6 col-sm-8 col-xs-12 tex_cen">
                <div class="inner-menu">
                <ul class="my_acc_pos_rel">
                  <li>
					  <select name="language" id="language" onchange="select_language();" class="hme-sel">
						  <option value=""><?php echo trans('messages.Select Language'); ?></option>
							<?php
							if(count($languages) > 0)
							{
								foreach($languages as $language) { 
									$select = (isset($_SESSION['language']) && $_SESSION['language'] == $language->code) ? 'selected' : '';   
							?>
							<option <?php echo $select; ?> value="<?php echo $language->code; ?>"><?php echo $language->language; ?></option>
							<?php } } ?>
					  </select>
				  </li>
                  <?php if(!isset(Auth::user()->id) && $segment != 'checkout') { ?>
                  <li><a href="#" data-toggle="modal" data-target="#signup-modal"><?php echo trans('frontend.Signup'); ?></a></li>
                  <li><a href="#" data-toggle="modal" data-target="#login-modal"><?php echo trans('frontend.Login'); ?></a></li>
                  <?php } elseif(isset(Auth::user()->id) && $segment != 'checkout') { ?>
					    <li class="show_hide"><a><?php echo trans('frontend.My Account'); ?></a>
						<ul id='content' class="slidingDiv" style="display: none;">                        
							<li><a href="{!! URL::to('edit_profile') !!}"><?php echo trans('frontend.Edit Profile'); ?></a></li>
							<li><a href="{!! URL::to('address_book') !!}"><?php echo trans('frontend.Address Book'); ?></a></li>
							<li><a href="{!! URL::to('myorder') !!}"><?php echo trans('frontend.My Order'); ?></a></li>
						</ul>
                    </li>
                    <li><a href="{!! URL::to('logout') !!}" ><?php echo trans('frontend.Logout'); ?></a></li>
                    <?php } ?>
                </ul>
                </div>
              </div>
           <div class="col-md-12 col-sm-12"><h3 class="text-center" style="font-size:26px;color:#fff;">Hello! Welcome to Shuneez!!</h3></div>
           </div>
          
        </div>
        </header>
        
         @yield('content') 
        
        <div class="clr"></div>
    <footer>
        <div class="foot-sec">
        <div class="container">
          <div class="col-md-2 col-xs-12">
            <!--<img class="foot_img" src="{!! URL::to('assets/images/payfort.png') !!}">-->
          </div>
          <div class="col-md-8 col-xs-12">
            <ul class="foot-mnu text-center">
              <li><a href="#"><?php echo trans('frontend.About').' '.trans('frontend.Shuneez'); ?></a></li>
              <li><a href="#"><?php echo trans('frontend.Terms of Use'); ?></a></li>
              <li><a href="#"><?php echo trans('frontend.Terms & Conditions'); ?></a></li>
              <li><a href="#"><?php echo trans('frontend.Privacy Policy'); ?></a></li>
              <li><a href="#"><?php echo trans('frontend.Contact Us'); ?></a></li>
            </ul>
            
          </div>
          <div class="col-md-2 col-xs-12">
            <div class="social-share">
              <ul>
                <li><a href="#"><img src="{!! URL::to('assets/images/footer-fb.png') !!}" class="sicn"></a></li>
                <li><a href="#"><img src="{!! URL::to('assets/images/footer-twitter.png') !!}" class="sicn"></a></li>
                <li><a href="#"><img src="{!! URL::to('assets/images/footer-gplus.png') !!}" class="sicn"></a></li>
                <li><a href="#"><img src="{!! URL::to('assets/images/footer-linkedin.png') !!}" class="sicn"></a></li>
              </ul>
            </div>
          </div>
          <div class="col-md-12 col-xs-12"><p class="text-center" style="color:#fff;">© <?php echo date('Y'); ?> Shuneez. All rights reserved</p></div>
        </div>
        </div>
      </footer>
<!-- Button trigger modal -->
    @include('includes/login')

<script>
  $('#forgot_btn').click(function(){
  var email = $("#forgot_field").val();
  $.ajax({
    beforeSend : function() {
    $("body").addClass("loading");
    },
   type: "GET",
   url: "forgotpassword",
   dataType:"json",
   data: {'email' : email},
   async: true,
   success:  function(result){
    $('.msg').html(result.msg)
    $("body").removeClass("loading");
    }
  });
  });
</script>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script>
function select_language()
{
	var url = "<?php echo URL::to(''); ?>";
	var language = $( "#language option:selected" ).val();
	if(language != '')
	window.location.href = url+'/changelanguage/'+language;
}
</script>
 <script type="text/javascript">   
      $('.slidingDiv').hide();
      $('.show_hide').click(function(e){ // <----you missed the '.' here in your selector.
          e.stopPropagation();
          $('.slidingDiv').slideToggle();
      });
      $('.slidingDiv').click(function(e){
          e.stopPropagation();
      });

      $(document).click(function(){
           $('.slidingDiv').slideUp();
      });
      /*$(document).ready(function()
      {
        var isMobile = {
          Android: function() {
              return navigator.userAgent.match(/Android/i);
          },
          iOS: function() {
              return navigator.userAgent.match(/iPhone|iPad|iPod/i);
          }
        };
        if( isMobile.Android() )
        {
          alert('Android');
        }
        else if( isMobile.iOS() )
        {
          alert('iOS');
        } 
      });*/
    </script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
{!! Html::script('assets/js/locationpicker.jquery.js') !!}
{!! Html::script('assets/js/bootstrap.min.js') !!}
{!! Html::script('assets/js/modernizr.min.js') !!}
{!! Html::script('assets/js/datetimepicker.js') !!}
{!! Html::script('assets/admin/js/jquery-clockpicker.min.js') !!}
{!! Html::script('assets/js/rating.js') !!}
{!! Html::script('assets/js/custom-script.js') !!}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5S43ovL2gYBhNreg2EeVzTPSAZARKqrY&sensor=false&amp;libraries=places"></script>
</body>
</html>
