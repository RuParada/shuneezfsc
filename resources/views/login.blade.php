<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Shuneez</title>

    {!! Html::style('assets/css/bootstrap.min.css') !!}
	{!! Html::style('assets/css/style.css') !!}
	{!! Html::style('assets/css/font-awesome.min.css') !!}

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
            
              <div class="col-md-3 col-xs-12">
                <a href="{!! URL::to('/') !!}"><img src="{!! URL::to('assets/images/Shuneez-logo-white.png') !!}" class="img-responsive inner-logo"></a>
              </div>
              <div class="col-md-6 col-xs-12">
                
              </div>

              <div class="col-md-3 col-xs-3">
                <div class="inner-menu">
                <ul>
                  <li><a href="{!! URL::to('/') !!}">Home</a></li>
                  <li><a href="#">My Account</a></li>
                </ul>
                </div>
              </div>
           <div class="col-md-12"><h3 class="text-center" style="font-size:26px;color:#fff;">Hello, Abdullah! Welcome to Shuneez!!</h3></div>
           </div>
          
        </div>
        </header>
       @yield('content') 


      <div class="mobile-section">
        <div class="container">
          <div class="col-md-5 col-xs-12">
            <img src="{!! URL::to('assets/images/mobile-banner.png') !!}" class="img-responsive" style="margin:0 auto;">
          </div>
          <div class="col-md-7 col-xs-12">
            <div class="down-mobile">
            <h2 style="color:#3e3e3e;margin-bottom:30px;">Download our Mobile Application</h2>
            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. the industry's standard dummy text ever. </p>
            
            <div class="agn-mob">
            <ul style="padding-left:0px;margin-top:40px;">
              <li style="margin-right:10px;"><a href="#"><img src="{!! URL::to('assets/images/app-store.png') !!}" class="img-responsive"></a></li>
              <li><a href="#"><img src="{!! URL::to('assets/images/google-play.png') !!}" class="img-responsive"></a></li>
            </ul>
            </div>
            </div>
          </div>
        </div>
      </div>
      <footer>
        <div class="foot-sec">
        <div class="container">
          <div class="col-md-2 col-xs-12">
            <img src="{!! URL::to('assets/images/payfort.png') !!}">
          </div>
          <div class="col-md-8 col-xs-12">
            <ul class="foot-mnu text-center">
              <li><a href="#">About Suneez</a></li>
              <li><a href="#">Terms of Use</a></li>
              <li><a href="#">Terms & Conditions</a></li>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Contact Us</a></li>
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
          <div class="col-md-12 col-xs-12"><p class="text-center" style="color:#fff;">© 2016 Shuneez. All rights reserved</p></div>
        </div>
        </div>
      </footer>

      <!--Login Modal dialog-->
      <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
        <div class="loginmodal-container">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h1>LOGIN</h1><br>
          <form>
          <input type="text" name="user" placeholder="Username">
          <input type="password" name="pass" placeholder="Password">
          <input type="submit" name="login" class="deliver-btn" value="Login" style="margin-top:15px;">
          </form>
          
          <div class="login-help">
          <p style="text-align: center;"><a href="#" style="font-size:18px;">Forgot your password</a></p>
          <h3 style="text-align: center;font-size:18px;color:#3e3e3e;">Don’t have an account? <a href="#">Signup now for free</a></h3>
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
          <h1>SIGNUP</h1><br>
		  <form>
		  <meta name="csrf-token" content="{{ csrf_token() }}">
          <input class="validation required" type="text" id="firstname" name="firstname" placeholder="First Name">
		  <span class="error_txt" id="firsterror"></span>
          <input type="text" class="validation required" id="lastname" name="lastname" placeholder="Last Name">
		  <span class="error_txt" id="lasterror"></span>
          <input type="text" class="validation required" id="email" name="email" placeholder="Email Address">
		   <span class="error_txt" id="emailiderror"></span>
          <input type="text" class="validation required" id="mobno" name="mobno" placeholder="Mobile Number">
		  <span class="error_txt" id="mobnoerror"></span>
           <input id="passwordfield" class="validation required" type="password" name="pass" placeholder="Password">
		   <span class="error_txt" id="paswderror"></span>
          <input type="button" onclick="Registration();" name="Signup" class="deliver-btn" value="Signup" style="margin-top:15px;"> 
    	 </form>
          
          <div class="login-help">
          <h3 style="text-align: center;font-size:18px;color:#3e3e3e;">Already on Shuneez?<a href="#" data-toggle="modal" data-target="#login-modal">Login</a></h3>
          </div>
        </div>
      </div>
      </div>
      <!--End of Sign Up Modal Dialog-->


      <style>
        
      </style>

  <body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    {!! Html::script('assets/js/bootstrap.min.js') !!}
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places"></script>
  </body>

</html>
