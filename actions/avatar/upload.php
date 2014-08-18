<?php
/**
 * Avatar upload action
 *
 * Modified to allow flash and html5 webcam uploads
 */

$guid = get_input('guid');
$owner = get_entity($guid);

if (!$owner || !($owner instanceof ElggUser) || !$owner->canEdit()) {
	register_error(elgg_echo('avatar:upload:fail'));
	forward(REFERER);
}

// check for html5, and finally file upload
$img_data = false;
$html5 = get_input('webcam-image-base64');
$url = get_input('avatar_url');
$upload = isset($_FILES['avatar']['name']) && !empty($_FILES['avatar']['name']);

if ($html5) {
	$img_data = base64_decode($html5);
	
	if (!$img_data){
		register_error(elgg_echo("avatar:upload:fail"));
		forward(REFERRER);
	}
} elseif ($url) {
	$url = elgg_normalize_url($url);
	$img_data = file_get_contents($url);
	if (!$img_data) {
		// try curl
		if (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$img_data = curl_exec($ch);
			curl_close($ch);
		}

		if (!$img_data) {
			register_error(elgg_echo('avatar:upload:fail'));
			forward(REFERRER);
		}
	}
} elseif ($upload) {
		if ($_FILES['avatar']['error'] !== 0) {
				 register_error(elgg_echo('avatar:upload:fail'));
				 return $return;
		} elseif(!in_array(strtolower(substr($_FILES['avatar']['name'], -3)), array('jpg','png','gif'))) {
				  register_error(elgg_echo('avatar:upload:fail'));
				 return $return;
		}
} else {
	// nothing was submitted
	register_error(elgg_echo('webcam:no_avatar_selected'));
	forward(REFERER);
}

// if we have img data, save it
if ($img_data) {
	$filehandler = new ElggFile();
	$filehandler->owner_guid = $owner->getGUID();
	$filehandler->setFilename("profile/" . $owner->guid . "master.jpg");
	$filehandler->open("write");
	if (!$filehandler->write($img_data)) {
		register_error(elgg_echo("avatar:upload:fail"));
		forward(REFERRER);
	}
	$filename = $filehandler->getFilenameOnFilestore();
	$filehandler->close();
}

$icon_sizes = elgg_get_config('icon_sizes');

// get the images and save their file handlers into an array
// so we can do clean up if one fails.
$files = array();
foreach ($icon_sizes as $name => $size_info) {
	if ($upload) {
		$resized = get_resized_image_from_uploaded_file('avatar', $size_info['w'], $size_info['h'], $size_info['square'], $size_info['upscale']);
	} else {
		$resized = get_resized_image_from_existing_file(
				$filename,
				$size_info['w'],
				$size_info['h'],
				$size_info['square']
		);
	}

	if ($resized) {
		//@todo Make these actual entities.  See exts #348.
		$file = new ElggFile();
		$file->owner_guid = $guid;
		$file->setFilename("profile/{$guid}{$name}.jpg");
		$file->open('write');
		$file->write($resized);
		$file->close();
		$files[] = $file;
	} else {
		// cleanup on fail
		foreach ($files as $file) {
			$file->delete();
		}

		register_error(elgg_echo('avatar:resize:fail'));
		forward(REFERER);
	}
}

// reset crop coordinates
$owner->x1 = 0;
$owner->x2 = 0;
$owner->y1 = 0;
$owner->y2 = 0;

$owner->icontime = time();
if (elgg_trigger_event('profileiconupdate', $owner->type, $owner)) {
	system_message(elgg_echo("avatar:upload:success"));

	$view = 'river/user/default/profileiconupdate';
	elgg_delete_river(array('subject_guid' => $owner->guid, 'view' => $view));
	add_to_river($view, 'update', $owner->guid, $owner->guid);
}

forward(REFERER);
