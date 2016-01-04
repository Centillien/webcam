define(function(require) {
	var $ = require('jquery');
	var elgg = require('elgg');

	var options = {
		width: 480,
		height: 0
	};

	/**
	 * Normalize the vendor-prefixed media objects
	 *
	 * @type @exp;navigator@pro;mozGetUserMedia|@exp;navigator@pro;webkitGetUserMedia|@exp;navigator@pro;msGetUserMedia|@exp;navigator@pro;getUserMedia
	 */
	var getMedia = (
		navigator.getUserMedia ||
		navigator.webkitGetUserMedia ||
		navigator.mozGetUserMedia ||
		navigator.msGetUserMedia
	);

	var init = function() {
		// Make sure that registration form is able to send the image
		$(".elgg-form-register")
			.attr("enctype", "multipart/form-data")
			.attr("encoding", "multipart/form-data");

		$(document).on('click', '.avatar-tabs a', changeTab);
		$(document).on('submit', '.elgg-form-avatar-upload', submit);
		$(document).on('submit', '.elgg-form-register', submit);

		if (getMedia) {
			initHtml5();
		} else if (hasFlash()) {
			initFlash();
		} else {
			$('#avatar-upload-tab > a').click();
			$('#avatar-acquire-tab').hide();
		}
	};

	var hasFlash = function() {
		try {
			var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
			if (fo) {
				return true;
			}
		} catch (e) {
			if (navigator.mimeTypes
				&& navigator.mimeTypes['application/x-shockwave-flash'] != undefined
				&& navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin) {
				return true;
			}
		}

		return false;
	};

	var initHtml5 = function() {
		$(document).on('click', '#webcam-video', capturePicture);

		// must be called in the context of navigator or window, depending on browser
		getMedia.call(navigator || window,
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
				elgg.register_error(elgg.echo('webcam:webcam_error'));
			}
		);
	};

	var shutterSound = function() {
		var audio = document.createElement('audio');
		audio.src = elgg.get_site_url() + 'mod/webcam/haxe/shutter.mp3';

		return {
			play: function() {
				audio.play();
			}
		};
	};

	var initFlash = function() {
		var html = '<div id="flashContent">'
			+ '<object type="application/x-shockwave-flash" data="' + elgg.get_site_url()
				// @todo make these dynamic
				+ 'mod/webcam/haxe/take_picture.swf" width="480" height="360">'
			+ '<param name="movie" value="take_picture.swf" />'
			+ '<param name="quality" value="high" />'
			+ '<param name="bgcolor" value="#ffffff" />'
			+ '<param name="play" value="true" />'
			+ '<param name="loop" value="true" />'
			+ '<param name="wmode" value="transparent" />'
			+ '<param name="scale" value="noscale" />'
			+ '<param name="menu" value="true" />'
			+ '<param name="devicefont" value="false" />'
			+ '<param name="salign" value="" />'
			+ '<param name="allowScriptAccess" value="sameDomain" />'
			+ '<a href="http://www.adobe.com/go/getflash">'
			+ '	<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />'
			+ '</a>'
			+ '</object>'
			+ '</div>';

		$('#webcam').html(html);
	};

	/**
	 * Normalize resolutions
	 */
	var setRes = function(ev) {
		var $video = $('video');
		video = $video.get(0);

		// normalize the resolutions
		var canvas = $('#webcam-canvas');

		options.height = video.videoHeight / (video.videoWidth / options.width);
		$video.attr('width', options.width);
		$video.attr('height', options.height);
		canvas.attr('width', options.width);
		canvas.attr('height', options.height);

		$video.data('streaming', true);
	};

	/**
	 * Capture a picture and save as base64 input data
	 *
	 * @param Obj ev Event
	 * @returns {undefined}
	 */
	var capturePicture = function(ev) {
		ev.preventDefault();

		var canvas = $('#webcam-canvas').get(0);
		var video = this;

		// if clicked on while paused, unpause
		if (video.paused) {
			video.play();
			$(video).removeClass('has-photo');
			removeBase64Input();
			return;
		}

		// @todo just make this return the resolution
		setRes();

		var width = options.width,
			height = options.height;

		shutterSound().play();
		video.pause();
		$(video).addClass('has-photo');
		canvas.width = width;
		canvas.height = height;
		canvas.getContext('2d').drawImage(video, 0, 0, width, height);

		// the data url format is data:<mime_type>;base64,<data>
		var data = canvas.toDataURL().split(',')[1];
		saveBase64Input(data, $(this).closest('form'));
	};

	var saveBase64Input = function(data, formElement) {
		var html = "<input id='webcam-image-base64' type='hidden' name='webcam-image-base64'>";
		$(formElement).prepend(html);
		$('#webcam-image-base64').attr('value', data);
	};

	var removeBase64Input = function() {
		$('#webcam-image-base64').remove();
	};

	var changeTab = function(ev) {
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

	var submit = function(ev) {
		// prevent if no data at all
		if (!$('#webcam-image-base64').val()
			&& !$('input[name=avatar]').val()
			&& !$('input[name=avatar_url]').val()
		) {
			elgg.register_error(elgg.echo('webcam:no_avatar_selected'));
			ev.preventDefault();
		}
	};

	elgg.register_hook_handler('init', 'system', init);
});