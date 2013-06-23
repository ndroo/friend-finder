<?php function make_short_url($link)
{
	//create the URL
	$url = "https://www.googleapis.com/urlshortener/v1/url";
	$fields = array("longUrl" => $link);
	
	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_MUTE, 1);

	//execute post
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result)->id;

}
?>
