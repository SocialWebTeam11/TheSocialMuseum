<!DOCTYPE html>
<html>
	<head>
	    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
	    <link rel="stylesheet" href="css/leaflet-sidebar.css" />
	    <link href="css/leaflet.timeline.css" rel="stylesheet">
	    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" />
	    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" />
	    
	    <title>The Social Museum</title>  

	    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	    <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
	    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>   
		<script src="js/objects.js"></script>
		<script src="js/prov.js"></script>
		<script src="js/leaflet-sidebar.js"></script>
		<script src="js/moment.js"></script>
		<script src="js/leaflet.timeline.js"></script>
		<script src="js/tweet.timeline.js"></script>
		<script src="tweet-geojson.php?cache=1"></script>

		<style>
		html, body, #map {
			height: 100%;
			width: 100%;
			padding: 0px;
			margin: 0px;
		} 
		.leaflet-control-container .leaflet-timeline-controls {
		    box-sizing: border-box;
		    width: 100%;
		}
		.leaflet-bottom.leaflet-left {
		    width: 90%;
		    display: table;
    		margin: 0 auto;
    		right:0;
		}
		input[type="range"] {
			display:inline;
		}
		</style>
	</head>
	<body>
		<div id="sidebar" class="sidebar collapsed">
	        <!-- Nav tabs -->
	        <ul class="sidebar-tabs" role="tablist">
	            <li><a href="#home" role="tab"><i class="fa fa-twitter"></i></a></li>
	            <li><a href="#info" role="tab"><i class="fa fa-info"></i></a></li>
	            <li><a href="#yelp" role="tab"><i class="fa fa-star"></i></a></li>
	        </ul>

	        <!-- Tab panes -->
	        <div class="sidebar-content active" itemscope itemtype="http://schema.org/Museum">
	            <div class="sidebar-pane" id="home">
	                <h1>Tweets</h1>
	                <p>No tweets found</p>
	            </div>
	            <div class="sidebar-pane" id="info">
	            	<h1>DBPedia</h1>
	            	<p>No information found</p>
	            </div>
	            <div class="sidebar-pane" id="yelp">
	            	<h1>Yelp</h1>
	            	<p>No information found</p>
	            </div>
	        </div>
	    </div>
		
		<script>
		    $(document).ready(function() {}); //document.ready
		    museumArray = new Array();
		</script>
		
		<div id="map" class="sidebar-map"></div>

		<?php
		
		// Config
		$dbhost = 'localhost';
		$dbname = 'museums';
		
		// Connect to test database
		$m = new MongoClient();
		$db = $m->$dbname;  

		// select the collection
		$locations = $db->museum_locations;
		$dbpedia = $db->museum_dbpedia;
		$hastags = $db->museum_hashtags;
		
		// pull a cursor query
		$cursor = $locations->find();

		// iterate cursor to display title of documents
		foreach ($cursor as $document) {
			$name = json_encode($document['id']);
			$lat = json_encode($document["location"]["geometry"]["location"]["lat"]);
			$lon = json_encode($document["location"]["geometry"]["location"]["lng"]);

			$addressComponents =  $document["location"]["address_components"];

			$arrayLength = count($addressComponents) - 3;

			if (!isset($document["location"]["address_components"][$arrayLength])) continue;

			$county = json_encode($document["location"]["address_components"][$arrayLength]['long_name']); 

			$address = json_encode($document["location"]["formatted_address"]);
			$description = "n/a";
			$website = "n/a";

			$cursor2 = $dbpedia->find(array("id" => $document['id'])); 

			foreach ($cursor2 as $value) {
		
				$abstract = $value['dbpedia'];
				$len = count($abstract);
				
				for($i=0;$i<$len;$i++){
					if($value['id'] == $document['id']){
						if($value['dbpedia'][$i]['property']['value'] == "http://dbpedia.org/ontology/abstract"){
							$description = $value['dbpedia'][$i]['entity']['value'];
						}	
						if($value['dbpedia'][$i]['property']['value'] == "http://nl.dbpedia.org/property/website"){
							$website = $value['dbpedia'][$i]['entity']['value'];
						}
					}
				}	
			} 
			?>
			<script> 
			museumArray.push(
				new Museum(
					<?php echo $name;?>, 
					<?php echo json_encode($description);?>, 
					<?php echo $address;?>, <?php echo $county;?>, 
					<?php echo json_encode($website);?>, 
					<?php echo $lat;?>, <?php echo $lon;?>
				)
			);
			</script>
			<?php
		}
		?>
	    
	    <script>
			tileLayer = L.tileLayer('http://{s}.tiles.mapbox.com/v3/examples.map-i875mjb7/{z}/{x}/{y}.png', {
			    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
			    maxZoom: 18
			});

			var markers = new L.FeatureGroup();
			
			var geojson;
			
			var map = L.map('map', {
				center: [52.3388, 5.2842],
				zoom: 8,
				minZoom: 8,
				layers: [tileLayer]
			});

			var museumIcon = L.icon({
				iconUrl: 'img/icon.png',
				iconSize:     [50, 64], // size of the icon
				iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
				popupAnchor:  [7, -85] // point from which the popup should open relative to the iconAnchor
			});

			var popup = L.popup();

			var sidebar = L.control.sidebar('sidebar').addTo(map);
			
			L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
				maxZoom: 18,
				attribution: 'TEAM 11 - The Social Museum',
				id: 'examples.map-i875mjb7'
			}).addTo(map);

			function style(feature) {
				return {
					weight: 2,
					opacity: 1,
					color: 'white',
					dashArray: '',
					fillOpacity: 0.1,
					fillColor: 'gray'
				};
			}

			function getRelatedMuseums(museum){
				// The Python script 'Related_Museums.py' generates a new JSON file with new relations among the museums, 
				// due to a security restriction it is not possible to load local JSON files in javascript. 
				// For know this is the quick fix (the python script is working in the repository).
				var jsonRelatedMuseums = JSON.parse('[{"Museum": "Allard_Pierson_Museum", "Related_Museum": "HortusBotanicusAmsterdam"}, {"Museum": "Amsterdam_Museum", "Related_Museum": "FriesMuseum"}, {"Museum": "Hash_Marihuana_&_Hemp_Museum", "Related_Museum": "PaleisopdeDam"}, {"Museum": "Hash_Marihuana_&_Hemp_Museum", "Related_Museum": "RijksmuseumAmsterdam"}, {"Museum": "Hortus_Botanicus_Amsterdam", "Related_Museum": "AllardPiersonMuseum"}, {"Museum": "Paleis_op_de_Dam", "Related_Museum": "HashMarihuana&HempMuseum"}, {"Museum": "Paleis_op_de_Dam", "Related_Museum": "RijksmuseumAmsterdam"}, {"Museum": "Rijksmuseum_Amsterdam", "Related_Museum": "HashMarihuana&HempMuseum"}, {"Museum": "Rijksmuseum_Amsterdam", "Related_Museum": "PaleisopdeDam"}, {"Museum": "Fries_Museum", "Related_Museum": "AmsterdamMuseum"}]');
				var relatedMuseums = "";
				
				for (var i = 0; i < jsonRelatedMuseums.length; i++){
					if (jsonRelatedMuseums[i].Museum == museum){
						relatedMuseums += jsonRelatedMuseums[i].Related_Museum + "; ";
					}
				}
				if(relatedMuseums == ""){
					relatedMuseums = "None";
				}
				return relatedMuseums;
			}

			function clickOnFeature(e) {
				map.fitBounds(e.target.getBounds());
				markers.clearLayers();	

				for (i = 0; i < museumArray.length; i++) {
				
					var latitude = museumArray[i].latitude;
					var longitute = museumArray[i].longitute;
					var county = museumArray[i].county;
					
					if(e.target.feature.properties.OMSCHRIJVI == county) {
						var marker = L.marker([latitude, longitute])
										.addTo(map)
										.bindPopup("<table class=\"table table-striped\">" +
														"<tr>" +
															"<th>Museum: </th>" +
															"<td>" + museumArray[i].name + "</td>" +
														"</tr>" +
														"<tr>" +
															"<th>Address: </th>" +
															"<td>" + museumArray[i].address + "</td>" +
														"</tr>" +
														"<tr>" +
															"<th>Description: </th>" +
															"<td><div style=\"height: 200px; overflow-y:scroll;\"><i>"+museumArray[i].description+"</i></div></td>" +
														"</tr>" +
														"<tr>" +
															"<th>Website: </th>" +
															"<td><a href='"+museumArray[i].website+"' target='_blank'>"+museumArray[i].website+"</a></td>" +
														"</tr>" +
														"<tr>" +
															"<th>Related museums: </th>" +
															"<td>" + getRelatedMuseums(museumArray[i].name) + "</td>" +
														"</tr>" +
													"</table>");
										
						marker.mycustom_id  = museumArray[i].name;

						marker.on('click', function (d) {
							sidebar.open('home');
							$("home").active;

							$.ajax({
						        url: "get_tweets.php",
						        type: "post",
						        data: {id: d.target.mycustom_id},
						        success: function(html){
						            $("#home").html(html);
						        },
						        error:function(){

						        }
						    });

						    $.ajax({
						        url: "get_dbpedia.php",
						        type: "post",
						        data: {id: d.target.mycustom_id},
						        success: function(html){
						            $("#info").html(html);
						        },
						        error:function(){

						        }
						    });

						    $.ajax({
						        url: "get_yelp.php",
						        type: "post",
						        data: {id: d.target.mycustom_id},
						        success: function(html){
						            $("#yelp").html(html);
						        },
						        error:function(){

						        }
						    });
						});

						markers.addLayer(marker);
					}
				}
			}
			
			map.addLayer(markers);		
				
			function onEachFeature(feature, layer) {
				layer.on({
					click: clickOnFeature
				});
			}
			
			L.geoJson(provincies, {
				style: style,
				onEachFeature: onEachFeature
			}).addTo(map);

			var baseMaps = {
			    "Map": tileLayer
			};

			var overlayMaps = {
			    "Timeline": timeline,
			    "Markers": markers
			};

			L.control.layers(baseMaps, overlayMaps).addTo(map);

			map.on('overlayremove', function (eventLayer) {
			    if (eventLayer.name === 'Timeline') {
			    	$("#sidebar").removeClass('hidden');
					document.getElementsByClassName("leaflet-timeline-controls")[0].style.display = 'none';
			    }
			});

			map.on('overlayadd', function (eventLayer) {
			    if (eventLayer.name === 'Timeline') {
					$("#sidebar").addClass('collapsed').addClass('hidden');
			    }
			});

		</script>
	</body>
</html>
