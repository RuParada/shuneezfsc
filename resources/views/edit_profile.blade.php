@extends('header')
@section('content')
@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif

<div class="container">
        <div class="verfiy-page">
              <div class="bred1">
                <a href="{!! URL::to('/') !!}" class="bred2"><?php echo trans('frontend.Home'); ?></a><img src="assets/img/breadcrumb-arrow.png" class="bred"><a href="#"><?php echo trans('frontend.My Order'); ?></a>
              </div>
        </div>
        <div class="col-md-3 pad0">
            <div class="inn_mnu">
              <ul class="in_mnu">     
              <li><a href="{!! URL::to('/myorder') !!}"><?php echo trans('frontend.My Order'); ?></a></li>           
                <li class="active"><a href="{!! URL::to('/edit_profile') !!}"><?php echo trans('frontend.Edit Profile'); ?></a></li>
                <li><a href="{!! URL::to('/address_book') !!}"><?php echo trans('frontend.Address Book'); ?></a></li>
                
              </ul>
            </div>
          </div>
          <div class="col-md-9 ">
          <div class="col-md-12">
          @if(Session::has('success')) <p class="error_msg1"> <?php echo Session::get('success'); ?> </p> @endif
            <p style="font-size:24px;color:#3e3e3e;"><?php echo trans('frontend.Account Settings'); ?></p>
            <hr>
            <p style="font-size:16px;color:#3e3e3e;"><?php echo trans('frontend.Hi! Please keep your information up to date to enable us to serve you as best as possible'); ?>.</p>
            <p style="font-size:16px;color:#662d91;font-weight:bold;"><?php echo trans('frontend.Personal Information'); ?></p>

            {!! Form::open(array('url' => '/updateuser', 'class' => 'form-horizontal', 'files' => 1)) !!}
            <div class="col-md-6 pad0" style="margin-bottom:20px;">
                <label for="first_name"><?php echo trans('messages.First Name'); ?> <span class="req">*</span></label>     
                    <input type="text" maxlength='30' name="first_name" class="form-control" id="name" placeholder="<?php echo trans('messages.First Name'); ?>" value="<?php echo (Input::old('first_name')) ? Input::old('first_name') : $user->first_name; ?>">
                     @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('first_name') != '') ? $error->first('first_name') : ''; ?></p>@endif                             
            </div>

            <div class="clr"></div>

            <div class="col-md-6 pad0" style="margin-bottom:20px;">
                <label for="last_name"><?php echo trans('messages.Last Name'); ?> <span class="req">*</span></label>                          
                <input type="text" maxlength='30' name="last_name" class="form-control" id="name" placeholder="<?php echo trans('messages.Last Name'); ?>" value="<?php echo (Input::old('last_name')) ? Input::old('last_name') : $user->last_name; ?>">
                 @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('last_name') != '') ? $error->first('last_name') : ''; ?></p>@endif 
                            
            </div>

            <div class="clr"></div>

            <p style="font-size:24px;color:#3e3e3e;"><?php echo trans('frontend.My Contact Details'); ?></p>
            <div class="col-md-6 pad0" style="margin-bottom:20px;">           
                <label for="mobile" style="width:100%;"><?php echo trans('messages.Mobile'); ?> <span class="req">*</span></label>
                <input type="text" class="form-control mob_fld" placeholder="+966">
                <input type="text" maxlength='15' name="mobile" class="form-control mob_fld1"  id="mobile" placeholder="<?php echo trans('messages.Mobile'); ?>" value="<?php echo (Input::old('mobile')) ? Input::old('mobile') : $user->mobile; ?>">
                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('mobile') != '') ? $error->first('mobile') : ''; ?></p>@endif
            </div>

            <div class="col-md-6"><p style="margin-top:30px;color:#cdcdcd"><?php echo trans('frontend.You will receive an sms with regarding your orders'); ?></p></div>
            <div class="clr"></div>

            <div class="clr"></div>
            <div class="col-md-6 pad0" style="margin-bottom:20px;">
                <label for="email" style="width:100%;"><?php echo trans('messages.Email'); ?> <span class="req">*</span></label>                         
                <input type="text" maxlength='75' name="email" class="form-control" id="email" placeholder="<?php echo trans('messages.Email'); ?>" value="<?php echo (Input::old('email')) ? Input::old('email') : $user->email; ?>">
                                @if(Session::has('error'))<p class="error_msg"><?php echo ($error->first('email') != '') ? $error->first('email') : ''; ?></p>@endif
                            
            </div>

            <div class="col-md-6"><p style="margin-top:30px;color:#cdcdcd"><?php echo trans('frontend.You will receive an email with your order details'); ?></p></div>
            <div class="clr"></div>
            <div class="col-md-6 pad0" style="margin-bottom:20px;">
                <p style="font-size:24px;color:#3e3e3e;"><?php echo trans('frontend.Newsletter'); ?></p>
                <div class="checkbox" style="margin-bottom:20px;margin-top:0px;">
                    <?php 
                    if($subscribe>=1)
                    {
                        echo '<input type="checkbox" name="subscribe" id="checkboxG2" class="css-checkbox" checked />';
                    }
                    else
                    {
                        echo '<input type="checkbox" name="subscribe" id="checkboxG2" class="css-checkbox" />';
                    }
                    ?>                 
                    <label for="checkboxG2" class="css-label-chekup"><?php echo trans('frontend.Subscribe to newsletter'); ?>
                  </label>
               </div>
                <input type="hidden" name="id" value="<?php echo $user->id; ?>" >
                <button type="submit" class="view_ord"><?php echo trans('messages.Submit'); ?></button>
            </div> 
            <div class="clr"></div> 
            {!! Form::close() !!}


          </div>
          
          </div>
          <div class="clr"></div>         
      </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script>
      $('#myTab a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})

    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
@endsection
