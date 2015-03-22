<?php
$dbhost = 'localhost';
$dbname = 'museums';

// Connect to test database
$m = new MongoClient();
$db = $m->$dbname;
$tweets = [];  

// select the collection
$collection = $db->museum_tweets;

$query = array('entities.hashtags.text' => strtolower(str_replace('_', '', $_POST['id'])));

// pull a cursor query
$cursor = $collection->find($query)->limit(10);
// iterate cursor to display title of documents
?>
<div>
<h2>Guestbook <small>source: Twitter</small></h2>
<?php 
if ($cursor->count() != 0) {
	foreach ($cursor as $document) { 
		if ($document['text'] != "" || !isset($document['text'])) {
		?>
			<div class="media">
				<div class="media-body">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="media-heading"><?php echo $document['user']['screen_name']; ?>
								<a href="#">
							 		<img class="pull-right" style="width: 28px; height: 28px;" src="<?php echo $document['user']['profile_image_url_https']; ?>" alt="">
							    </a>
							</h4>
						</div>
						<div class="panel-body">
							<?php echo $document['text']; ?>
						</div>
						<div class="panel-footer text-center">
							<small class=""><?php echo $document['created_at']; ?></small>
						</div>
					</div>
				</div>
			</div>
		<?php 
		}
	}
}
else {
	?><i>Unfortunately, no tweets are available for this museum at this moment</i><?php
} 
?>
</div>