<?php

function create_block($title, $content, $url = false) {
	if ($url) {
		$c = "<a href=\"$content\">$content</a>";
	} else {
		$c = "<p>$content</p>";
	}

	return
	"<div class=\"media\">
		<div class=\"media-body\">
			<div class=\"panel panel-default\">
				<div class=\"panel-heading\">
					<span>$title</span>
				</div>
				<div class=\"panel-body\">$c</div>
			</div>
		</div>
	</div>";
}

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
	if ($cursor->count() != 0) {
		foreach ($cursor as $document) { 		
			if ($document['dbpedia'] != "" || !isset($document['dbpedia'])) {
				$dbpedia_array = array();
				for($i=0; $i<count($document['dbpedia']); $i++) {
					if($document['id'] == $document['id']) {
						$count = count(explode('#', $document['dbpedia'][$i]['property']['value']));
						if ($count > 1) {
							$url = explode('#', $document['dbpedia'][$i]['property']['value']);
							$key = array_pop($url);
							$dbpedia_array[$key] = $document['dbpedia'][$i]['entity']['value'];
						}
						else {
							$url = explode('/', $document['dbpedia'][$i]['property']['value']);
							$key = array_pop($url);
							$dbpedia_array[$key] = $document['dbpedia'][$i]['entity']['value'];
						}
						
					}
				}
			}
		} 

		if (isset($dbpedia_array['label'])) {
			echo (isset($dbpedia_array['label'])) ? create_block('Name', $dbpedia_array['label']) : '';
			echo (isset($dbpedia_array['type'])) ? create_block('Type', $dbpedia_array['type']) : '';
			echo (isset($dbpedia_array['abstract'])) ? create_block('Description', $dbpedia_array['abstract']) : '';
			echo (isset($dbpedia_array['thema'])) ? create_block('Theme', $dbpedia_array['thema']) : '';
			echo (isset($dbpedia_array['opgericht'])) ? create_block('Founded', $dbpedia_array['opgericht']) : '';
			echo (isset($dbpedia_array['directeur'])) ? create_block('Director', $dbpedia_array['directeur']) : '';
			echo (isset($dbpedia_array['homepage'])) ? create_block('Homepage', $dbpedia_array['homepage'], true) : '';
			echo (isset($dbpedia_array['status'])) ? create_block('Status', $dbpedia_array['status']) : '';
			echo (isset($dbpedia_array['wikiPageExternalLink'])) ? create_block('Wikipedia', $dbpedia_array['isPrimaryTopicOf'], true) : '';	
			echo (isset($dbpedia_array['wikiPageModified'])) ? create_block('Last Modified', $dbpedia_array['wikiPageModified']) : '';
		}
		else {
			?><i>Unfortunately, no dbpedia information is available for this museum at this moment</i><?php
		}
	}
	?>
</div>