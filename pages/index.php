<?php
/**
 * Elgg plugin to allow using the web cam to capture profile icons
 * 
 */

//load GET value
$display = get_input('display', '');

//display proper content for page
if ($display == "capture") {
	include(dirname(dirname(__FILE__)) . "/views/default/profile/captureicon.php");
	//echo elgg_view('profile/captureicon');
} else {
	include(dirname(dirname(__FILE__)) . "/views/default/profile/uploadicon.php");
}