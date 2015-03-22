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
			//General info
			?><h2>Information <small>source: Yelp</small></h2><?php
			$museum_info = array();
			foreach($document['yelp'] as $key => $value) {
				if($document['id'] == $document['id'] && !is_array($value) && $value) {
					$museum_info[$key] = $value;
				}
			}

			?>
				<div class="media">
					<div class="media-body">
						<div class="panel panel-default">
							<div class="panel-heading">
								<span><?php echo $museum_info['name'] ?></span>
							</div>
							<div class="panel-body">
								<p>Yelp: <a href="<?php echo $museum_info['url'] ?>"><?php echo $museum_info['url'] ?></a></p>
								<p>Phone: <?php echo $museum_info['display_phone'] ?></p>
								<p>Rating: <?php echo isset($museum_info['rating']) ? $museum_info['rating'].'/5' : '' ?></p>
							</div>
						</div>
					</div>
				</div>
			<?php

			// Ratings
			if (isset($document['yelp']['reviews']) && $document['yelp']['reviews'] != "") {
				?><h2>Reviews <small>source: Yelp</small></h2><?php
				foreach($document['yelp']['reviews'] as $key => $value) {
					?>
					<div class="media" itemprop="media" itemscope itemtype="http://schema.org/Review">
						<div class="media-body">
							<div class="panel panel-default">
								<div class="panel-heading" itemprop="author" itemscope itemtype="http://schema.org/Person">
									<span itemprop="name"><?php echo $value['user']['name']; ?></span>
								</div>
								<div class="panel-body" itemprop="reviewBody">
									<p><?php echo $value['excerpt'] ?></p>
								</div>
								<div class="panel-footer" itemprop="rating" itemscope itemtype="http://schema.org/Rating">
									<img itemprop="image" src="<?php echo $value['rating_image_url'] ?>" />
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}
		}
		else {
			?>
			<h2>Reviews <small>source: Yelp</small></h2>
			<i>Unfortunately, no reviews are available for this museum at this moment</i>
			<?php
		}
	} 
	?>
</div>