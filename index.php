<html>
<head>
<meta name = "viewport" content = "width = device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;">		
<script src="http://code.google.com/apis/gears/gears_init.js" type="text/javascript" charset="utf-8"></script>
<script src="js/geo.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script>
function initialize_map()
{
    var myOptions = {
	      zoom: 4,
	      mapTypeControl: true,
	      mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
	      navigationControl: true,
	      navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
	      mapTypeId: google.maps.MapTypeId.ROADMAP      
	    }	
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}
function initialize()
{
	if(geo_position_js.init())
	{
		document.getElementById('current').innerHTML="Receiving...";
		geo_position_js.getCurrentPosition(show_position,function(){document.getElementById('current').innerHTML="Couldn't get location"},{enableHighAccuracy:true});
	}
	else
	{
		document.getElementById('current').innerHTML="Functionality not available";
	}
}

function show_position(p)
{
	document.getElementById('current').innerHTML="latitude="+p.coords.latitude.toFixed(2)+" longitude="+p.coords.longitude.toFixed(2);
	var pos=new google.maps.LatLng(p.coords.latitude,p.coords.longitude);
	map.setCenter(pos);
	map.setZoom(14);

	var infowindow = new google.maps.InfoWindow({
	    content: "<strong>yes</strong>"
	});

	var marker1 = new google.maps.Marker({
	    position: pos,
	    map: map,
	    title:"You are here"
	});

	marker2 = new google.maps.Marker({
	    map: map,
            draggable: true,
            position: new google.maps.LatLng(<?php $_REQUEST['lat']; ?>, <?php $_REQUEST['long'];?>)
	});

	var polyOptions = {
	     strokeColor: '#FF0000',
	     strokeOpacity: 1.0,
	     strokeWeight: 3,
	     map: map,
	};
	var poly = new google.maps.Polyline(polyOptions);

	var geodesicOptions = {
	     strokeColor: '#CC0099',
	     strokeOpacity: 1.0,
	     strokeWeight: 3,
	     geodesic: true,
	     map: map
	};
	var geodesicPoly = new google.maps.Polyline(geodesicOptions);

	var path = [marker1.getPosition(), marker2.getPosition()];
	poly.setPath(path);
	geodesicPoly.setPath(path);

	google.maps.event.addListener(marker, 'click', function() {
	  infowindow.open(map,marker);
	});
	
}
</script >
<style>
	body {font-family: Helvetica;font-size:11pt;padding:0px;margin:0px}
	#current {font-size:10pt;padding:5px;}	
</style>
</head>
<body onload="initialize_map();initialize()">
	<div id="current">Initializing...</div>
	<div id="map_canvas" style="width:320px; height:350px"></div>
</body>
</html>
