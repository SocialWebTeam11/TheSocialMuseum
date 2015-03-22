// JavaScript Document

function eqfeed_callback(data){
	  data.features.forEach(function(tweet){
		  tweet.properties.end = moment(tweet.properties.start).add(100, 's');
	  });
	  timeline = L.timeline(data, {
		formatDate: function(date){
		  return moment.unix(date).format("YYYY-MM-DD HH:MM:SS");
		},
		steps: 1000,
		duration:5000,
		pointToLayer: function(data, latlng){
		  var hue = 0;
		  return L.circleMarker(latlng, {
			radius: 25,
			color: "hsl("+hue+", 100%, 50%)",
			fillColor: "hsl("+hue+", 100%, 50%)"
		  }).bindPopup("<table class=\"table table-striped\"><tr><th>User: </th><td>"+data.properties.username+"</td></tr><tr><th>Tweet: </th><td>"+data.properties.text+"</td></tr></table>");
		}
	  });
}