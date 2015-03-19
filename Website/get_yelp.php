<?php
$dbhost = 'localhost';
$dbname = 'museums';

// Connect to test database
$m = new MongoClient();
$db = $m->$dbname;
$tweets = [];  

// select the collection
$yelp = $db->museum_yelp;

$query = array('id' => $_POST['id']);

// pull a cursor query
$cursor = $yelp->find($query); 
?>

<div>
	<?php 
	foreach ($cursor as $document) { 
		if ($document['yelp'] != "" && isset($document['yelp'])) {
			// Ratings
			if (isset($document['yelp']['reviews']) && $document['yelp']['reviews'] != "") {
				?><h2>Reviews <small>source: Yelp</small></h2><?php
				foreach($document['yelp']['reviews'] as $key => $value) {
					?>
					<div class="media">
						<div class="media-body">
							<div class="panel panel-default">
								<div class="panel-heading">
									<span><?php echo $value['user']['name']; ?></span>
								</div>
								<div class="panel-body">
									<p><?php echo $value['excerpt'] ?></p>
								</div>
								<div class="panel-footer">
									<img src="<?php echo $value['rating_image_url'] ?>" />
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}

			//General info
			?><h2>Information <small>source: Yelp</small></h2><?php
			foreach($document['yelp'] as $key => $value) {
				if($document['id'] == $document['id'] && !is_array($value) && $value) {
				?>
					<div class="media">
						<div class="media-body">
							<div class="panel panel-default">
								<div class="panel-heading">
									<span><?php echo $key; ?></span>
								</div>
								<div class="panel-body">
									<p><?php echo $value ?></p>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
			}
		}
		else {
			?><h2>No info <small>source: Yelp</small></h2><?php
		}
	} 
	?>
</div>