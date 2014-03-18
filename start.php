<?php
/**
 * Elgg plugin to allow using the web cam to capture profile icons
 * 
 * Author Gerard Kanters https://www.centillien.com
 * 
 */
elgg_register_event_handler('init', 'system', 'webcam_init');

/**
 * Init
 */

function webcam_init() {
	// routing of urls
	elgg_register_page_handler('webcam', 'webcam_page_handler');

	//register actions
	$action_path = elgg_get_plugins_path() . 'webcam/actions';
	elgg_register_action('webcam/save', "$action_path/save.php");
	elgg_register_action('avatar/upload', "$action_path/avatar/upload.php");

	//register js
	$url = elgg_get_simplecache_url('js', 'webcam');
	elgg_register_simplecache_view('js/webcam');
	elgg_register_js('webcam', $url);

	elgg_extend_view('css/elgg', 'css/better_avatars');
}
