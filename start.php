<?php

	/**
	 * Elgg plugin to allow using the web cam to capture profile icons
	 * 
	 * Author Gerard Kanters https://www.centillien.com
	 * 
	 */
	 
function webcam_init() {
	// routing of urls
        elgg_register_page_handler('webcam', 'webcam_page_handler');

	//register actions
	$action_path = elgg_get_plugins_path() . 'webcam/actions';
	elgg_register_action('webcam/save', "$action_path/save.php");


	//elgg_extend_view("forms/profile/edit", "webcam/forms/profile/edit");

	}

/**
 * Handle requests to /webcam/
 *
 * @param array $page Page segments
 * @return boolean
 */
function webcam_page_handler($page) {
        $base = elgg_get_plugins_path() . 'webcam/pages/';

        if (!isset($page[0])) {
                $page[0] = '?display=capture';
        }

	$page_type = $page[0];
        switch ($page_type) {
                default:
                        include $base . 'index.php';
                        break;
        }
}

