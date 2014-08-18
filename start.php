<?php
/**
 * Uses HTML5 user media interface or flash to get a picture for the avatar.
 */

require_once(dirname(__FILE__) . "/lib/register.php");

elgg_register_event_handler('init', 'system', 'webcam_init');

/**
 * Init
 */
function webcam_init() {
	//register actions
	$action_path = elgg_get_plugins_path() . 'webcam/actions';
	elgg_register_action('webcam/save', "$action_path/save.php");
	elgg_register_action('avatar/upload', "$action_path/avatar/upload.php");
	elgg_register_action('avatar/register', "$action_path/avatar/register.php");

	//Incorporate webcam in registration form
	elgg_extend_view('register/extend', 'forms/avatar/register' );
	elgg_register_event_handler('create', 'user', 'webcam_registration_event');
	

	//register js
	$url = elgg_get_simplecache_url('js', 'webcam');
	elgg_register_simplecache_view('js/webcam');
	elgg_register_js('webcam', $url);

	//js for profile, @todo make one js 
        $url = elgg_get_simplecache_url('js', 'webcam_profile');
        elgg_register_simplecache_view('js/webcam_profile');
        elgg_register_js('webcam_profile', $url);


	elgg_extend_view('css/elgg', 'css/webcam');
}
