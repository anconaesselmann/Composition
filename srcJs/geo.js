/*** include std/EventUtil.js ***/

// @author Axel Ancona Esselmann

function inverseMercator(x, y) {
	var R = 6371000.0;
	var longitude = (Math.PI / 2.0 - 2.0 * Math.atan(Math.pow(Math.E, -y / R))) * (180.0 / Math.PI);
	var latitude  = (x / R) * (180.0 / Math.PI);
	return {lat:latitude,lon:longitude};
}
function formatDegMinSec(coord) {
	var degree = Math.floor(coord);
	var remainder = coord - degree;
	var minutes = Math.floor(remainder * 60.0);
	var seconds = (remainder * 60 - minutes) * 60;
	return degree + "&deg;" + minutes + "'" + seconds.toFixed(2) + "\"";
}
function formatLat(lat) {
	if (lat < 0) {
		var direction = "W";
		lat = -lat;
	} else {
		var direction = "E";
	}
	return formatDegMinSec(lat) + " " + direction;
}
function formatLon(lon) {
	if (lon < 0) {
		var direction = "S";
		lon = -lon;
	} else {
		var direction = "N";
	}
	return formatDegMinSec(lon) + " " + direction;
}
function displayLatLong(e, svg, div) {
	var viewBox = svg.viewBox;
	var xFract = e.offsetX / viewBox.svgWidth;
	var partialViewWidth = xFract * viewBox.width;
	var adjustedXPos = viewBox.x + partialViewWidth;
	var yFract = e.offsetY / viewBox.svgHeight;
	var partialViewHeight = yFract * viewBox.height;
	var adjustedYPos = viewBox.y + partialViewHeight;

	var latLon = inverseMercator(adjustedXPos, -adjustedYPos);
	var lat = formatLat(latLon.lat);
	var lon = formatLon(latLon.lon);
	div.innerHTML = lon + " " + lat;
}
function registerMap(svg, info) {
	svg.svgNode.addEventListener('mousemove', function(event){displayLatLong(event, svg, info);}, false);
}