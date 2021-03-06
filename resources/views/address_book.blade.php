@extends('header')

@section('content')
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
                <li><a href="{!! URL::to('/edit_profile') !!}"><?php echo trans('frontend.Edit Profile'); ?></a></li>
                <li class="active"><a href="{!! URL::to('/address_book') !!}"><?php echo trans('frontend.Address Book'); ?></a></li>
                </li> 
                
                </li>             
            </ul>
        </div>
    </div>

    <div class="col-md-9 ">
        <div class="col-md-12">
            <p style="font-size:24px;color:#3e3e3e;"><?php echo trans('frontend.Address Book'); ?></p>
            @if(Session::has('success')) <p class="error_msg"> <?php echo Session::get('success'); ?> </p> @endif
            <hr>

            <?php
                if(count($address)) 
                {
                foreach($address as $address_details)
                {
                    $latitude       = $address_details->latitude;
                    $longitude      = $address_details->longitude;
                    $address_key    = $address_details->address_key;
                    ?>
                     <div class="col-md-10">
                        <p class="add_wdth"><?= $address_details->address ?></p>                    
                    </div>
                    <div class="col-md-2 add_del_sec">
                        <a id="edit_popup" href="javascript:void(0);" onclick="addaddress_popup('<?php echo $address_details->address_key; ?>','<?php echo $address_details->id; ?>');"><img src="{!! URL::to('assets/images/edit_icn.png') !!}" class="ed_icn"></a>
                        <button id="del" onclick="del_address('<?php echo $address_details->address_key; ?>','<?php echo $address_details->customer_id; ?>')"></button>
                        <input type="hidden" name="latitude1" id="latitude2" value="<?= $address_details->latitude ?>">
                        <input type="hidden" name="longitude1" id="longitude2" value="<?= $address_details->longitude ?>">    
                    </div>
                    <div class="clr"></div>
                    <hr>
                    <?php
                } }
            ?>  
        </div>
        <div class="col-md-12">
            <a href="#" class="add_plus" data-toggle="modal" data-target="#add-modal"><?php echo trans('frontend.Add'); ?></a>
        </div>
    </div>
    <div class="clr"></div>
</div>

<!--Edit Address Modal Dialog-->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content add_picker">
            
        </div>
    </div>
</div> 


<!--Address Modal Dialog-->
<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="loginmodal-container">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h1><?php echo trans('frontend.Address Book'); ?></h1>
            <br>
            {!! Form::open(array('url' => '/addaddress', 'files' => 1)) !!}
                <textarea class="fg" cols="50" rows="4" name="address" id="address" placeholder="User Address"></textarea>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">               
                <div style='overflow:hidden;height:440px;width:100%;'>
                    <div id="map-canvas" style='height:440px;width:700px;'></div>
                    <style>
                        #map-canvas img {
                            max-width: none!important;
                            background: none!important
                        }
                    </style>
                </div>
                <div class="checkbox" style="margin-bottom:15px;margin-top:15px;">
                  <input type="checkbox" name="default" id="default" class="css-checkbox" />
                  <label for="default" class="css-label-chekup"><?php echo trans('frontend.Set This as Default Location'); ?>
                  </label>
               </div>
               <script type='text/javascript'>
                    $(document).ready(function()
                    {                      
                         if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(GetAddress);
    }
function GetAddress(position) {
	$("#latitude").val(position.coords.latitude);
    $("#longitude").val(position.coords.longitude);
    var lat = parseFloat(position.coords.latitude);
    var lng = parseFloat(position.coords.longitude);
    var latlng = new google.maps.LatLng(lat, lng);
    $("#latitude").val(lat);
    $("#longitude").val(lng);
    var geocoder = geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'latLng': latlng }, function (results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
        if (results[1]) {
            $("#address").val(results[1].formatted_address);
        }
    }
    });

	var latitude = $("#latitude").val();
	var longitude = $("#longitude").val();
    $('#map-canvas').locationpicker({
		location: {
        latitude: latitude, longitude: longitude}
      ,
      radius: 300,
      inputBinding: {
		latitudeInput: $('#latitude'),
        longitudeInput: $('#longitude'),  
        locationNameInput: $('#address')       
      }
});

}
                        $('#add-modal').on('shown.bs.modal', function () {
                                 $('#map-canvas').locationpicker('autosize');
                        });
         
                    });
                </script>
                <input type="submit" name="login" class="deliver-btn" value="<?php echo trans('frontend.Save'); ?>" style="margin-top:15px;">
            {!! Form::close() !!}   


        </div>
    </div>
</div>
    
<!--End of Address Modal Dialog-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script>
        $('#myTab a').click(function(e) {
            e.preventDefault()
            $(this).tab('show')
        })
    </script>

    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
   <!--  <script src="js/bootstrap.min.js"></script> -->
    <script type="text/javascript">   


      /* Delete Address */
      function del_address(key,id)
      {
        var customer_id   = id;
        var address_key   = key;
        if (confirm('<?php echo trans("frontend.Are you sure you want to delete this?"); ?>')) {
            $.ajax({
                beforeSend : function() {
                    $("body").addClass("loading");
                },
                type : "GET",
                dataType : "html",
                url  : "<?php echo URL::to('/delete_address'); ?>",
                data : {'customer_id':customer_id,'address_key':address_key},
                success: function(result) {
                            if(result=='success')
                            {
                                $("body").removeClass("loading");
                                location.reload();
                            }
                        }
                });
        }
      }

      /* Add Address Popup */
      function addaddress_popup(key,id)
      {
        $.ajax({
                beforeSend: function () {
                    $("body").addClass("loading");
                },
                type: "GET",
                url: "<?php echo URL::to('/addaddress_popup'); ?>",
                data: {'id': id,'address_key':key},
                async: true,
                success: function (result) {
						$("body").removeClass("loading");
						$('.add_picker').html(result);
						$("#edit-modal").modal('show');
                }
            }); 
      }

    $(document).ready(function()
    {
        $('#edit-modal').on('shown.bs.modal', function () {
            $('#edit-canvas').locationpicker('autosize');
        });
   });
    </script>
@endsection
