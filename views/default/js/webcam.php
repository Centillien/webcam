<?php
if (false) {
	?><script><?php
}
?>

elgg.provide('elgg.avatar');

elgg.avatar.options = {
	width: 480,
	height: 0
};

/**
 * Normalize the vendor-prefixed media objects
 *
 * @type @exp;navigator@pro;mozGetUserMedia|@exp;navigator@pro;webkitGetUserMedia|@exp;navigator@pro;msGetUserMedia|@exp;navigator@pro;getUserMedia
 */
elgg.avatar.getMedia = (
	navigator.getUserMedia ||
	navigator.webkitGetUserMedia ||
	navigator.mozGetUserMedia ||
	navigator.msGetUserMedia
);

elgg.avatar.init = function() {
	$('.avatar-tabs a').live('click', elgg.avatar.changeTab);
	$('.elgg-form-avatar-upload').live('submit', elgg.avatar.submit);

	$('#webcam-video').bind('canplay', elgg.avatar.setStream);
	$('#webcam-video').live('click', elgg.avatar.capturePicture);

	// must be called in the context of navigator or window, depending on browser
	elgg.avatar.getMedia.call(navigator || window,
		{
			video: true,
			audio: false
		},

		function(stream) {
			var video = $('#webcam-video').get(0);

			if (navigator.mozGetUserMedia) {
				video.mozSrcObject = stream;
			} else {
				var vendorURL = window.URL || window.webkitURL;
				video.src = vendorURL.createObjectURL(stream);
			}

			video.play();
		},

		function(err) {
			elgg.register_error(elgg.echo('better_avatars:webcam_error'));
		}
	);
};

/**
 * Normalize resolutions
 */
elgg.avatar.setStream = function(ev) {
	var $this = $(this);
	$streaming = $this.data('streaming');
	if (!$streaming) {
		// normalize the resolutions
		var canvas = $('#webcam-canvas');

		elgg.avatar.options.height = this.videoHeight / (this.videoWidth / elgg.avatar.options.width);
		$this.attr('width', elgg.avatar.options.width);
		$this.attr('height', elgg.avatar.options.height);
		canvas.attr('width', elgg.avatar.options.width);
		canvas.attr('height', elgg.avatar.options.height);
		
		$this.data('streaming', true);
	}
};

/**
 * Capture a picture and save as base64 input data
 * 
 * @param Obj ev Event
 * @returns {undefined}
 */
elgg.avatar.capturePicture = function(ev) {
	ev.preventDefault();

	var canvas = $('#webcam-canvas').get(0),
		video = this,
		width = elgg.avatar.options.width,
		height = elgg.avatar.options.height;

	// if clicked on while paused, unpause
	if (video.paused) {
		video.play();
		$(video).removeClass('has-photo');
		$('#webcam-image-base64').remove();
		return;
	}
	
	video.pause();
	$(video).addClass('has-photo');
	canvas.width = width;
    canvas.height = height;
    canvas.getContext('2d').drawImage(video, 0, 0, width, height);

	var data = canvas.toDataURL();

	var html = "<input id='webcam-image-base64' type='hidden' name='webcam-image-base64'>";
	$(this).prepend(html);
	$('#webcam-image-base64').attr('value', data);
};

elgg.avatar.changeTab = function(ev) {
	ev.preventDefault();
	var $this = $(this);
	var $li = $this.closest('li');
	var $ul = $this.closest('ul');

	// change tab
	$ul.find('li').removeClass('elgg-state-selected');
	$li.addClass('elgg-state-selected');
	
	// change content
	$("#avatar-options > div").hide();
	$('#' + $li.attr('id').replace('-tab', '')).show();
};

elgg.avatar.submit = function(ev) {
	// prevent if no data at all
	if (!$('#webcam-image-base64').val()
		&& !$('input[name=avatar]').val()
		&& !$('input[name=avatar_url]').val()
	) {
		elgg.register_error(elgg.echo('better_avatars:no_avatar_selected'));
		ev.preventDefault();
	}
};

elgg.register_hook_handler('init', 'system', elgg.avatar.init);