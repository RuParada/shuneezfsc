
<!--Address Modal Dialog-->  
<!--
        <div class="loginmodal-container">
-->
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h1><?php echo trans('frontend.Address Book'); ?></h1>
            <br>
              {!! Form::open(array('url' => '/updateaddress')) !!}
                <textarea class="fg" cols="50" rows="4" name="address" id="address" placeholder="User Address"></textarea>
                <input type="hidden" name="latitude" id="latitude" value="<?php echo $address_display->latitude; ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?php echo $address_display->longitude; ?>">   
                <input type='hidden' name='address_key' value="<?php echo $address_display->address_key; ?>">  
                <input type='hidden' name='id' value="<?php echo $address_display->id; ?>">           
                <div style='overflow:hidden;height:440px;width:100%;'>
                    <div id="edit-canvas" style='height:440px;width:100%;'></div>
                </div>
              
<!--
                </div>
-->
                <script type='text/javascript'>
                    $(document).ready(function()
                    {                       
                        var lat = $("#latitude").val();
                        var lon = $("#longitude").val();
                        $('#edit-canvas').locationpicker({
                            location: {latitude: lat, longitude: lon},
                            radius: 300,
                            inputBinding: {
                            latitudeInput: $('#latitude'),
                            longitudeInput: $('#longitude'),
                            locationNameInput: $('#address')              
                            }
                        });
         
                    });
                </script>
                <input type="submit" class="deliver-btn" value="<?php echo trans('frontend.Update'); ?>" style="margin-top:15px;">
            {!! Form::close() !!}   


 
<!--End of Address Modal Dialog-->

<style>

div#edit-modal {
width: 50%;
background: white;
max-height: 500px;
margin: 7% auto;
}
.modal-content
{
	width:100%;
}
</style>
