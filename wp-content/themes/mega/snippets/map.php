<?php 
//   http://webdesignledger.com/tutorials/how-to-add-a-color-tinted-full-width-google-map-in-wordpress
?>
<style media="screen" scoped>
#googleMap iframe {
   width: 100%;
}
#googleMap {
   height: 350px;
}
#googleMap img { max-width: none; }
</style>
<div id="googleMap"></div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
//<![CDATA[
var geocoder = new google.maps.Geocoder();
var address = "12317 Technology, Blvd., Ste. 100, Austin, TX 78727"; //Add your address here, all on one line.
var latitude;
var longitude;
var color = "#cccccc"; //Set your tint color. Needs to be a hex value.

function getGeocode() {
	geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
    		latitude = results[0].geometry.location.lat();
			longitude = results[0].geometry.location.lng(); 
			initGoogleMap();   
    	} 
	});
}

function initGoogleMap() {
	var styles = [
	    {
	      stylers: [
	        { saturation: -100 }
	      ]
	    }
	];
	
	var options = {
		mapTypeControlOptions: {
			mapTypeIds: ['Styled']
		},
		center: new google.maps.LatLng(latitude, longitude),
		zoom: 13,
		scrollwheel: false,
		navigationControl: true,
		mapTypeControl: false,
		zoomControl: true,
		disableDefaultUI: true,	
		mapTypeId: 'Styled'
	};
	var div = document.getElementById('googleMap');
	var map = new google.maps.Map(div, options);
	marker = new google.maps.Marker({
	    map:map,
	    draggable:false,
	    animation: google.maps.Animation.DROP,
	    position: new google.maps.LatLng(latitude,longitude)
	});
	var styledMapType = new google.maps.StyledMapType(styles, { name: 'Styled' });
	map.mapTypes.set('Styled', styledMapType);
	
	var infowindow = new google.maps.InfoWindow({
	      content: "<div class='iwContent'>"+address+"<br /><a href='https://www.google.com/maps/place/425+W+Guadalupe+Rd+%23101/@33.3638003,-111.7994789,17z/data=!3m1!4b1!4m2!3m1!1s0x872ba902e3352613:0x55edcdaaf767f226'><i class='fa fa-map-marker'></i> Show on Google Maps</a></div>"
	});
	google.maps.event.addListener(marker, 'click', function() {
	    infowindow.open(map,marker);
	  });
	
	
	bounds = new google.maps.LatLngBounds(
	  new google.maps.LatLng(-84.999999, -179.999999), 
	  new google.maps.LatLng(84.999999, 179.999999));

	rect = new google.maps.Rectangle({
	    bounds: bounds,
	    fillColor: color,
	    fillOpacity: 0.2,
	    strokeWeight: 0,
	    map: map
	});
}
google.maps.event.addDomListener(window, 'load', getGeocode);
//]]>
</script>