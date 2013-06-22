<html>
<head>
<meta charset="UTF-8"> 
<meta name = "viewport" content = "width = device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;">		
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script type="text/javascript">

var id = null; //watch id so we can cancel the watch when we're done

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
	console.log("Location tracking starting...");

	id = navigator.geolocation.watchPosition(show_position,error,{enableHighAccuracy:true,frequency:3000});

	console.log("Location tracking started!");
}

function error()
{
	console.log("Something went wrong while updating the users location");
}

function show_position(p)
{
	console.log("Updating location...");

	var pos=new google.maps.LatLng(p.coords.latitude,p.coords.longitude);
	map.setCenter(pos);
	map.setZoom(14);

	//your location
	var marker1 = new google.maps.Marker({
	    position: pos,
	    map: map,
	    title:"You are here"
	});

	//your friends location
	marker2 = new google.maps.Marker({
	    map: map,
            draggable: false,
			map: map,
			title:"Your friend is here",
            position: new google.maps.LatLng(<?php echo $_REQUEST['lat']; ?>, <?php echo $_REQUEST['long'];?>)
	});

	//draw a line to your friend
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

	console.log("Location updated completed!");
}
</script>

<style>
	body {font-family: Helvetica;font-size:11pt;padding:0px;margin:0px}
</style>
</head>
<body onload="initialize_map();initialize()">
	<div id="map_canvas" style="width:320px; height:350px"></div>
</body>
</html>
