<?php
/**
 * Uses HTML5 user media interface to get a picture for the avatar.
 */

elgg_register_event_handler('init', 'system', 'better_avatars_init');

/**
 * Init
 */
function better_avatars_init() {
	//register actions
	$action_path = dirname(__FILE__)  . '/actions';
	elgg_register_action('avatar/upload', "$action_path/avatar/upload.php");

	$url = elgg_get_simplecache_url('js', 'better_avatars');
	elgg_register_simplecache_view('js/better_avatars');
	elgg_register_js('better_avatars', $url);

	elgg_extend_view('css/elgg', 'css/better_avatars');
}