<?php
$dbhost = 'localhost';
$dbname = 'museums';

// Connect to test database
$m = new MongoClient();
$db = $m->$dbname;
$tweets = [];  

// select the collection
$dbpedia = $db->museum_dbpedia;

$query = array('id' => $_POST['id']);

// pull a cursor query
$cursor = $dbpedia->find($query); 
?>

<div>
	<h2>Information <small>source: dbpedia</small></h2>
	<?php 
	foreach ($cursor as $document) { 		
		if ($document['dbpedia'] != "" || !isset($document['dbpedia'])) {
			for($i=0; $i<count($document['dbpedia']); $i++) {
				if($document['id'] == $document['id']) {
				?>
					<div class="media">
						<div class="media-body">
							<div class="panel panel-default">
								<div class="panel-heading">
									<span><?php echo $document['dbpedia'][$i]['property']['value']; ?></span>
								</div>
								<div class="panel-body">
									<?php echo "<p>".$document['dbpedia'][$i]['entity']['value']."</p>"; ?>
								</div>
							</div>
						</div>
					</div>
				<?php
				}
			}
		}
	} 
	?>
</div>