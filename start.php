<?php
/**
 * Uses HTML5 user media interface or flash to get a picture for the avatar.
 */
elgg_register_event_handler('init', 'system', 'webcam_init');

/**
 * Init
 */
function webcam_init() {
	//register actions
	$action_path = elgg_get_plugins_path() . 'webcam/actions';
	elgg_register_action('webcam/save', "$action_path/save.php");
	elgg_register_action('avatar/upload', "$action_path/avatar/upload.php");

	//register js
	$url = elgg_get_simplecache_url('js', 'webcam');
	elgg_register_simplecache_view('js/webcam');
	elgg_register_js('webcam', $url);

	elgg_extend_view('css/elgg', 'css/webcam');
}