<?php 
ini_set('mongo.long_as_object', 1);
error_reporting(0);

include('cache.php');
$cache = new FileCache();
$usecache = $_GET["cache"];

$m = new MongoClient();
$db = $m->museums;

$museum_hashtags = $db->museum_hashtags;
$museum_locations = $db->museum_locations;
$museum_tweets = $db->museum_tweets;

$key = 'getGeoJSON';

if($usecache != 1){
	$cache->delete($key);
}

if (!$data = $cache->fetch($key)) {
	$geojson = 'eqfeed_callback({"type": "FeatureCollection", "features": [';
	$hashtags = $museum_hashtags->find();
	foreach ($hashtags as $hashtag) {
		$hashtagtext = $hashtag["hashtag"];
		$museumid = $hashtag["id"];
		$hashtagtext = substr($hashtagtext, 1);
		$tweets = $museum_tweets->find(array('entities.hashtags.text' => array('$regex' => new MongoRegex("/^$hashtagtext/i"))));
	
		foreach ($tweets as $tweet) {
			$time = strtotime($tweet["created_at"]);
			$location = $museum_locations->findone(array('id' => array('$regex' => new MongoRegex("/^$museumid/i"))));
			$geojson = $geojson . '{
				"type": "Feature",
				"properties": {
					"museumid": '.json_encode($museumid).',
					"username": '.json_encode($tweet["user"]["name"]).',
					"text": '.json_encode($tweet["text"]).',
					"start": '.$time.',
					"end": '.$time.'
				},
				"geometry": { "type": "Point",
				"coordinates": [
					'.$location["location"]["geometry"]["location"]["lng"].',
					'.$location["location"]["geometry"]["location"]["lat"].'
				] },
				"id": "'.$tweet["id_str"].'"
			},';
		}
	}
	
	$geojson = substr($geojson, 0, -1);
	$geojson = $geojson . ']})';
	
	$cache->store($key,$geojson,3600);
	echo $geojson;
}else{
	echo $data;
}

?>