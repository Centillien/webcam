<?php
/**
 * WEbcam plugin
 * (c) Centillien
 * Website: https://www.centillien.com
 *
 */
?>
<style>
video {
  width: 260px;
  height: 200px;
  background: rgba(255,255,255,0.5);
  border: 1px solid #ccc;
}
canvas {
  width: 260px;
  height: 200px;
  background: rgba(255,255,255,0.5);
}
</style>

<video id="video" autoplay controls loop></video>
<canvas id="canvas"></canvas><br><br>
<button id="snap" class="elgg-button elgg-button-submit">Take Picture</button>


<script>
// Put event listeners into place
window.addEventListener("DOMContentLoaded", function() {
	// Grab elements, create settings, etc.
		var canvas = document.getElementById("canvas"),
		context = canvas.getContext("2d"),
		video = document.getElementById("video"),
		videoObj = { "video": true },
		errBack = function(error) {
			console.log("Video capture error: ", error.code); 
		};

	// Put video listeners into place
	if(navigator.getUserMedia) { // Standard
		navigator.getUserMedia(videoObj, function(stream) {
			video.src = stream;
			video.play();
		}, errBack);
	} else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
		navigator.webkitGetUserMedia(videoObj, function(stream){
			video.src = window.webkitURL.createObjectURL(stream);
			video.play();
		}, errBack);
	}
	else if(navigator.mozGetUserMedia) { // Firefox-prefixed
		navigator.mozGetUserMedia(videoObj, function(stream){
			video.src = window.URL.createObjectURL(stream);
			video.play();
		}, errBack);
	}
}, false);

// Trigger photo take
document.getElementById("snap").addEventListener("click", function() {
        var image = new Image();
        image.src = canvas.toDataURL("image/png");
	$.post( "mod/webcam/actions/save.php");
});

</script>
