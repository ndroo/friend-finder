<?php
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

$me = generateRandomString(20);

//allows overriding the me value
if(isset($_REQUEST['me']))
{
	$me = $_REQUEST['me'];
}
$friend = "";
if(isset($_REQUEST['friend']))
{
	$friend = $_REQUEST['friend'];
}
?>

<html>
<head>
<meta charset="UTF-8"> 
<meta name = "viewport" content = "width = device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;">		
<script src="http://okv.mouseofdoom.com/statics/openkeyval.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.12&sensor=false"></script>

<script type="text/javascript">

var map = null;
var zoom = 18;
var id = null; //watch id so we can cancel the watch when we're done

var marker1 = null;
var marker2 = null;
var poly = null;

//keys for 'me' and the friend
var friend = "<?php echo $friend; ?>";
var me = "<?php echo $me;?>"; 

//friend location
var friendLat = null;
var friendLng = null;
var lastPos = null;

function initialize_map()
{
    var myOptions = {
		zoom: zoom,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}	
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
}
function initialize()
{
	console.log("Location tracking starting...");
	id = navigator.geolocation.watchPosition(show_position,error,{enableHighAccuracy:true,frequency:3000});
	console.log("Location tracking started!");

	//start the refreshing...
	setTimeout(RefreshFriendData,5000);

	//if we have a friend, lets connect
	ConnectToFriend();
}

function error()
{
	console.log("Something went wrong while updating the users location");
}

function show_position(p)
{
	lastPos = p;

	console.log("Updating location...");
	var pos=new google.maps.LatLng(p.coords.latitude,p.coords.longitude);
	UpdateMyPos(pos);

	//your location
	if(marker1 == null)
	{
		//set the zoom and center the map to get started
		map.setCenter(pos);

		var pinColor = "0000FF";
		var myPinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
				new google.maps.Size(21, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(10, 34));

		//create the marker
		marker1 = new google.maps.Marker({
			icon: myPinImage,
			position: pos,
			map: map,
			draggable: true,
			title:"You are here"
		});
    		map.panTo(pos);
	}
	else //update the position of the marker and pan to that location
	{
		marker1.setPosition(pos);
	}

	//if we have the friends lat && lng, then lets map it out
	if(friendLat != null && friendLng != null)
	{
		console.log("Plotting friend...");
		var friendPos=GetFriendsPos();

		document.getElementById("last_update").innerHTML = "<strong>Friend</strong><br>lat: " + friendLat.substring(0,6) + " | lng: " + friendLng.substring(0,6) + "<br><strong>You</strong><br>lat: " +pos.lat().toString().substring(0,6) + " | lng: " +pos.lng().toString().substring(0,6) + "<br>Last update: " + new Date().getTime() + "<br>Friend: " + friend + "<br>me: " + me;

		//friends location
		if(marker2 == null)
		{
                        var pinColor = "00FF00";
                        var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
                                        new google.maps.Size(21, 34),
                                        new google.maps.Point(0,0),
                                        new google.maps.Point(10, 34));

			//create the friends position
			marker2 = new google.maps.Marker({
				icon: pinImage,
				map: map,
		        	draggable: false,
				map: map,
				title:"Your friend is here",
	            		position: friendPos
			});
		}
		else //update the position of the marker
		{
			marker2.setPosition(friendPos);
		}

		//draw a line to your friend
		if(poly == null)
		{
			var polyOptions = {
			     strokeColor: '#000000',
			     strokeOpacity: 1.0,
			     strokeWeight: 3,
			     map: map,
			};
			
			poly = new google.maps.Polyline(polyOptions);
		}
	
		//update the path for the line between the two people
		var path = [marker1.getPosition(), marker2.getPosition()];
		poly.setPath(path);
	}

	console.log("Location updated completed!");
	
}

//sent the pos back to the central server
function UpdateMyPos(pos)
{
	//update remote server with my location
	window.remoteStorage.setItem(me + "-lat", pos.lat());
	console.log("'me-lat' updated (" + me + "-lat," + pos.lat() + ")");
	window.remoteStorage.setItem(me + "-lng", pos.lng());
	console.log("'me-lat' updated (" + me + "-lng," + pos.lng() + ")");
}

function refreshLat(value,key)
{
	friendLat = value;
	console.log("'friend-lat' updated (" + friend + "-lat," + value + ")");
}

function refreshLng(value,key)
{
	friendLng = value;	
	console.log("'friend-lng' updated (" + friend + "-lng," + value + "|" + key + ")");
}

function ConnectToFriend()
{
	if(friend != "")
	{
		console.log("Connecting to friend");
		window.remoteStorage.setItem(friend + "-friend", me);
	}
}

function setFriend(value,key)
{
	if(value != null)
	{
		friend = value;
		console.log("Found a friend: " + value);
	}
}

function RefreshFriendData()
{
	if(friend == "")
	{
		//see if we can find the friend?
		console.log("Looking for your friend");
		window.remoteStorage.getItem(me + "-friend", setFriend);
	}
	else
	{
        	//refresh friends location from the cache
		console.log("Refreshing your friends location");
        	window.remoteStorage.getItem(friend + "-lat", refreshLat);
        	window.remoteStorage.getItem(friend + "-lng", refreshLng);
	}
	show_position(lastPos);
	//do this every 15 seconds
	setTimeout(RefreshFriendData,1000);
}

//requests the location of the friend from the central server
function GetFriendsPos()
{
	var fp =new google.maps.LatLng(friendLat,friendLng);
	console.log("Friend coords ("+fp.lat()+","+fp.lng()+")");
	return fp;
}
</script>

<style>
	body {font-family: Helvetica;font-size:11pt;padding:0px;margin:0px}
</style>
</head>
<body onload="initialize();initialize_map();">
	<div id="map_canvas" style="width:320px; height:350px"></div>
Link: http://ff.mouseofdoom.com/?friend=<?php echo $me; ?>
<div id="last_update"></div>
</body>
</html>
