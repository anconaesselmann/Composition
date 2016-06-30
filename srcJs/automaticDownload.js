
// @author Axel Ancona Esselmann

function remove(id) {
   return (elem=document.getElementById(id)).parentNode.removeChild(elem);
}

function download(url) {
	window.location.href = url;
}
function timedDownload(url, delay) {
	dlStatus = document.getElementById('dlStatus');
	if	(delay > 0) {
		setTimeout(function() { timedDownload(url, --delay); }, 1000);
		if (dlStatus != null) {
			dlStatus.innerHTML = delay;
		};
	} else {
		dlStatus.innerHTML = delay;
		download(url);
		remove('dlStatusMessage');

	}
}

window.addEventListener('load', timedDownload("/gpx/download", 5), false);