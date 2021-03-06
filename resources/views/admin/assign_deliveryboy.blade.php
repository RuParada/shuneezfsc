@extends('adminheader')

@section('content')

@if(Session::has('error')) <?php $error = Session::get('error'); ?> @endif

<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo trans('messages.Assign Deliveryboy'); ?> </h1>
        @if ( !count($deliveryboys) )<p class="error_msg">{!! trans('messages.No deliveryboy found') !!}</p>@endif
    </section>
    <section class="content">
    
        <div class="row">
            <div class="col-md-12">

                <div class="box">
				<div id="map-canvas" style="height:500px"></div>
				
                </div><!--box-info-->
              <div class="col-md-3" id="deliveryboy_details" style="border:1px solid #fff; padding:10px; background-color:#fff;">
				
				</div>
            </div></div>	  

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
  <script>
$('document').ready(function(){
var map;
var infoWindow;

var image = '{!! URL::to('assets/images/CarLocation.png') !!}';

// markersData variable stores the information necessary to each marker
var markersData = [
<?php $i=0; foreach($deliveryboys as $deliveryboy) { 
  if( $deliveryboy->latitude != '' ) {
?>
   {
      lat: <?php echo $deliveryboy->latitude; ?>,
      lng: <?php echo $deliveryboy->longitude; ?>,
      name: "<?php echo $deliveryboy->deliveryboy; ?>",
      address1:"<?php echo $deliveryboy->address; ?>",
      mobile:"<?php echo $deliveryboy->mobile; ?>",
      id: <?php echo $deliveryboy->id; ?>
   }<?php echo($i != count($deliveryboys) ? ',' : ''); ?>
<?php $i++; } } ?>
];


function initialize() {
   var mapOptions = {
      center: new google.maps.LatLng(24.7136,46.6753),
      zoom: 8,
      mapTypeId: 'roadmap',
   };
  

   map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

   // a new Info Window is created
   infoWindow = new google.maps.InfoWindow();

   // Event that closes the Info Window with a click on the map
   google.maps.event.addListener(map, 'click', function() {
      infoWindow.close();
   });

   // Finally displayMarkers() function is called to begin the markers creation
   displayMarkers();
}
google.maps.event.addDomListener(window, 'load', initialize);


// This function will iterate over markersData array
// creating markers with createMarker function
function displayMarkers(){

   // this variable sets the map bounds according to markers position
   var bounds = new google.maps.LatLngBounds();
   
   // for loop traverses markersData array calling createMarker function for each marker 
   for (var i = 0; i < markersData.length; i++){

      var latlng = new google.maps.LatLng(markersData[i].lat, markersData[i].lng);
      var name = markersData[i].name;
      var address1 = markersData[i].address1;
      var mobile = markersData[i].mobile;
      var id = markersData[i].id;
      
    createMarker(latlng, name, address1, mobile, id);

      // marker position is added to bounds variable
      bounds.extend(latlng);  
   }

   // Finally the bounds variable is used to set the map bounds
   // with fitBounds() function   
}

// This function creates each marker and it sets their Info Window content
function createMarker(latlng, name, address1, mobile, id){
   var marker = new google.maps.Marker({
      map: map,
      position: latlng,
      title: name,
      icon: image,
      center: new google.maps.LatLng(latlng),
   });

   // This event expects a click on a marker
   // When this event is fired the Info Window content is created
   // and the Info Window is opened.
   google.maps.event.addListener(marker, 'click', function() {
      // Creating the content to be inserted in the infowindow
      var iwContent = '<div id="iw_container">' +
            '<div class="iw_title">' + name + '</div>' +
         '<div class="iw_content">' + address1 + '<br />' +
          mobile + '<br />' +
        '</div></div>';
      $("#deliveryboy_details").html('<p>Name : '+name+'</p><p>Mobile : '+mobile+'</p><p>Address : '+address1+'</p><button class="btn btn-primary" onclick="assign_deliveryboy('+id+');">Assign</button>');
      // including content to the Info Window.
      infoWindow.setContent(iwContent);
      
      

      // opening the Info Window in the current map and at the current marker location.
      infoWindow.open(map, marker);
   });
}
});
</script>
<script type="text/javascript">
function assign_deliveryboy(id)
{
	var order_id = <?php echo $order_id; ?>; 
	var url = '<?php echo URL::to(''); ?>';
	window.location.href = url+'/admin/assign_deliveryboy/'+id+'/'+order_id;
}
</script>
<style>
html {
	height: 100%;
}
body {
	height: 100%;
	margin: 0;
	padding: 0;
}
#map-canvas {
	height: 100%;
}
#iw_container .iw_title {
	font-size: 16px;
	font-weight: bold;
}
.iw_content {
	padding: 15px 15px 15px 0;
}
</style>
@endsection
