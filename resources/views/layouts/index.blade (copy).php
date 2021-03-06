@if(Session::has('signup_error')) <?php $error = Session::get('signup_error') ?> @endif
@if(Session::has('login_error')) <?php $login_error = Session::get('login_error') ?> @endif
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      <title>Shuneez</title>
      {!! Html::style('assets/css/bootstrap.min.css') !!}
      {!! Html::style('assets/css/font-awesome.min.css') !!}
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
      <div class="banner-section">
         <div class="container">
            <div class="hme-header">
               <div class="col-lg-7 col-md-5 col-sm-4 col-xs-12">
                  <a href="{!! URL::to('') !!}"><img src="{!! URL::to('assets/images/Shuneez-logo.png') !!}" class="img-responsive hme-logo"></a>
               </div>
               <div class="col-lg-5 col-md-7 col-sm-8 col-xs-12">
                  <div class="hme-menu">
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
                        <?php if(!isset(Auth::user()->id)) { ?>
                        <li><a href="#" data-toggle="modal" data-target="#signup-modal"><?php echo trans('frontend.Signup'); ?></a></li>
                        <li><a href="#" data-toggle="modal" data-target="#login-modal"><?php echo trans('frontend.Login'); ?></a></li>
                        <?php } else { ?>
                        <li class="show_hide">
                           <a><?php echo trans('frontend.My Account'); ?></a>
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
            </div>
            <div class="col-md-12 col-xs-12">
               <h3 class="text-center" style="color:#fff;padding-bottom:50px;font-size:26px;">Lorem Ipsum is simply dummy text of the <br>printing and typesetting industry. </h3>
            </div>
            <div class="col-md-3"></div>
            {!! Form::open(array('url' => 'getitems', 'id' => 'listing_form')) !!}
            <div class="col-md-6">
               <input type="radio" value="address" name="search_type" id="address" class="css-checkbox" checked="checked" /><label for="address"  class="css-label-homechk radGroup1" ><?php echo trans('frontend.Location'); ?></label>
               <input type="radio" value="branch" name="search_type" id="branch" class="css-checkbox" <?php echo (Input::old('search_type') == 'branch') ? 'checked' : ''; ?>/><label for="branch"  class="css-label-homechk radGroup1" ><?php echo trans('frontend.Branch'); ?></label>
               <input type="radio" value="keyword" name="search_type" id="keyword" class="css-checkbox" <?php echo (Input::old('search_type') == 'keyword') ? 'checked' : ''; ?>/><label for="keyword" class="css-label-homechk radGroup1" ><?php echo trans('frontend.Keywords'); ?> </label>
               @if(Session::has('error'))
               <p class="error_msg" style="color:red"><?php echo trans('messages.Delivery area is required'); ?></p>
               @endif
               <div class="input-group custom_group col-md-12 col-xs-12">
                  <input type="text"  class="cc" id="delivery_area" name="delivery_area" value="<?php echo Input::old('delivery_area'); ?>" placeholder="<?php echo trans('frontend.Enter your Area / locality'); ?>">
                  <input type="hidden" name="delivery_type" id="delivery_type" value="<?php echo Input::old('delivery_type'); ?>">
                  <select name="branch" class="selectpicker n_sel" data-live-search="true" style="width:100%;height:50px;display:none;-webkit-appearance: none;" id="bran">
                     <option value=""><?php echo trans('frontend.Select Branch'); ?></option>
                     <?php if(count($branches))
                     {
						 foreach($branches as $branch)
						 {
					?>
					<option value="<?php echo $branch->id; ?>"><?php echo $branch->branch; ?></option>
					<?php } } ?>
                  </select>
                  <input type="text" style="display:none;" class="hme_cc" id="delivery_area1" name="keyword" value="<?php echo Input::old('delivery_area'); ?>" placeholder="<?php echo trans('frontend.Enter your keyword'); ?>">
               </div>
               <div class="col-md-6 agn-btn"><button type="button" class="deliver-btn" onclick="getlistings('d')"><?php echo trans('frontend.Delivery'); ?></button></div>
               <div class="col-md-6 agn-btn"><button type="button" class="pickup-btn"  onclick="getlistings('p')"><?php echo trans('frontend.Pickup'); ?></button></div>
               {!! Form::close() !!}
            </div>
            <div class="col-md-3"></div>
         </div>
      </div>
   </header>
   <div class="mobile-section">
      <?php if(Session::has('success')) { ?>
      <h4 align="center" style="text-alig:center;color:green" class="success_msg"><?php echo Session('success'); ?></h4>
      <?php } ?>
      <div class="container">
         <div class="col-md-5 col-xs-12">
            <img src="{!! URL::to('assets/images/mobile-banner.png') !!}" class="img-responsive" style="margin:0 auto;">
         </div>
         <div class="col-md-7 col-xs-12">
            <div class="down-mobile">
               <h2 style="color:#3e3e3e;margin-bottom:30px;">Download our Mobile Application</h2>
               <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. the industry's standard dummy text ever. </p>
               <div class="agn-mob">
                  <ul class="mob_a1">
                     <li class="mob_a2"><a href="#"><img src="{!! URL::to('assets/images/app-store.png') !!}" class="img-responsive"></a></li>
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
               <img class="foot_img" src="{!! URL::to('assets/images/payfort.png') !!}">
            </div>
            <div class="col-md-8 col-xs-12">
               <ul class="foot-mnu text-center">
                  <li><a href="#"><?php echo trans('frontend.About Shuneez'); ?></a></li>
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
            <div class="col-md-12 col-xs-12">
               <p class="text-center" style="color:#fff;">© 2016 Shuneez. All rights reserved</p>
            </div>
         </div>
      </div>
   </footer>
    @include('includes/login')
   <body>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
   </body>
    
   <script>
      $(document).ready(function()
         {
      	$('#listing_form').on('keyup keypress', function(e) {
      	  var keyCode = e.keyCode || e.which;
      	  if (keyCode === 13) { 
      		e.preventDefault();
      		return false;
      	  }
      	});
       });
       
       function getlistings(delivery_type)
       {
         $("#delivery_type").val(delivery_type);
         $( "#listing_form" ).submit();
       }
       
      window.onload=function(){
      var place;
      var autocomplete = new google.maps.places.Autocomplete(delivery_area);
      
      google.maps.event.addListener(autocomplete, 'place_changed', function () {
             place = autocomplete.getPlace();
             console.log(place);
      });
      }
      
      //Signup validation function
      function Registration(){
       $error = 0;
       
       if($('#firstname').val()==''){
         $("#firsterror").html("Please enter first name");
             $error = 1;
       }
       else{
         $("#firsterror").html("");
       }
       if($('#lastname').val()==''){
         $("#lasterror").html("Please enter last name");
             $error = 1;
       }
       else{
         $("#lasterror").html("");
       }
       if($('#email').val()==''){
         $("#emailiderror").html("Please enter Email address");
             $error = 1;
       }
       else{
          if(!validateEmail($("#email").val()))
                 {
                     $("#emailiderror").html("Invalid email address");
                     $error = 1;
                 }
           else{
              $("#emailiderror").html("");
           }
       } 
       if($('#mobno').val()==''){
         $("#mobnoerror").html("Please enter Mobile Number");
             $error = 1;
       }
       else{
            if(!$.isNumeric($('#mobno').val()))
                         {
                             $("#mobnoerror").html("accept numbers only");
                             $error = 1;
                         }
                         else if($('#mobno').val().length > 10 || $('#mobno').val().length < 10)
                         {
                             $("#mobnoerror").html("accept 10 digit numbers only");
                             $error = 1;
                         }
         else{
         $("#mobnoerror").html("");
         }
       }
       if($('#passwordfield').val()==''){
         $("#paswderror").html("Please enter Password");
             $error = 1;
       }
       else{
         $("#paswderror").html("");
       }
       var firstname = $('#firstname').val();
       var lastname = $('#lastname').val();
       var emailid = $('#email').val();
       var mobileno = $('#mobno').val();
       var passwd = $('#passwordfield').val();
        if (!$error) {
        $.ajax({
                     
             url: '/register',
                     type: 'post',
                     data: { 
             firstname: firstname,
             lastname: lastname,
             emailid: emailid,
             mobileno: mobileno,
             passwd: passwd, 
           },
                     success: function(e)
                     {
                         if(e==1){
                  window.location="{{URL::to('/login')}}";
                  $('#signup-modal').modal('hide');
               }
                     },
             headers:
             {
               'X-CSRF-Token': $('input[name="_token"]').val()
             },
                 });
       
        }
      }
      
      //Email Validation
         function validateEmail(email)
         {
             var emailReg = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
             var valid = emailReg.test(email);
             if(!valid) 
                 return false;
             else 
                 return true;
         }
      
   </script>
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
      
      function select_language()
      {
      	var url = "<?php echo URL::to(''); ?>";
      	var language = $( "#language option:selected" ).val();
      	if(language != '')
      	window.location.href = url+'/changelanguage/'+language;
      }
      
      /* Special Character Validation */
      jQuery('#firstname').keyup(function () {     
        this.value = this.value.replace(/[^a-zA-Z0-9]/g,'');
      });   
      
      jQuery('#lastname').keyup(function () {     
        this.value = this.value.replace(/[^a-zA-Z0-9]/g,'');
      }); 
      
      jQuery('#mobno').keyup(function () {     
        this.value = this.value.replace(/[^1-9\.]/g,'');
      });                
      
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
      
   </script>
   <script>
      $('input[name=search_type]').click(function () {
        if ($("#address").prop("checked")) {
        $("#delivery_area").show();
        $("#delivery_area1").hide();
        $("#bran").hide();
      } 
      else {
        $("#delivery_area").hide();
      }
      if ($("#branch").prop("checked")) {
        $("#delivery_area1").hide();
        $("#delivery_area").hide();
        $("#bran").show();
      } 
      else {
        $("#bran").hide();
      }
      if ($("#keyword").prop("checked")) {
        $("#delivery_area1").show();
        $("#delivery_area").hide();
        $("#bran").hide();
      } 
      else {
        $("#delivery_area1").hide();
      }
      });
   </script>
   {!! Html::script('assets/js/bootstrap.min.js') !!}
   {!! Html::script('assets/js/modernizr.min.js') !!}
   {!! Html::script('assets/js/datetimepicker.js') !!}
   {!! Html::script('assets/admin/js/jquery-clockpicker.min.js') !!}
   {!! Html::script('assets/js/custom-script.js') !!}
   <!--{!! Html::script('assets/js/bootstrap-select.js') !!}-->
   <div class="modal_load"></div>
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
      url('http://i.stack.imgur.com/FhHRx.gif') 
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
</html>
