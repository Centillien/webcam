<?php
/**
 * Elgg plugin to allow using the web cam to capture profile icons
 * 
 * @author  Lenny Urbanowski
 * @link	http://www.itslennysfault.com
 * @package photobooth
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 *
 * Created for Gabriel Media Group (http://www.gabrielmediagroupinc.com
 *
 * 	save.php - Called from the swf file to save the captured picture
 */

global $CONFIG;

$user = elgg_get_logged_in_user_entity();

//Load data from flash object
$jpg = file_get_contents('php://input');
if ($jpg) {
	$img = get_input('img');
	
	//save master file using Elgg file system
	$filehandler = new ElggFile();
	$filehandler->owner_guid = $user->getGUID();
	$filehandler->setFilename("profile/" . $user->guid . "master.jpg");
	$filehandler->open("write");
	$filehandler->write($jpg);
	$filename = $filehandler->getFilenameOnFilestore();
	$filehandler->close();
} else {
	register_error(elgg_echo("photobooth:saveerror"));
	forward(REFERRER);
}


//create resized versions
$topbar = get_resized_image_from_existing_file($filename, 16, 16, true);
$tiny = get_resized_image_from_existing_file($filename, 25, 25, true);
$small = get_resized_image_from_existing_file($filename, 40, 40, true);
$medium = get_resized_image_from_existing_file($filename, 100, 100, true);
$large = get_resized_image_from_existing_file($filename, 200, 200);

//save other versions if this worked.
if ($small !== false && $medium !== false && $large !== false && $tiny !== false) {
	$filehandler->setFilename("profile/" . $user->guid . "large.jpg");
	$filehandler->open("write");
	$filehandler->write($large);
	$filehandler->close();
	$filehandler->setFilename("profile/" . $user->guid . "medium.jpg");
	$filehandler->open("write");
	$filehandler->write($medium);
	$filehandler->close();
	$filehandler->setFilename("profile/" . $user->guid . "small.jpg");
	$filehandler->open("write");
	$filehandler->write($small);
	$filehandler->close();
	$filehandler->setFilename("profile/" . $user->guid . "tiny.jpg");
	$filehandler->open("write");
	$filehandler->write($tiny);
	$filehandler->close();
	$filehandler->setFilename("profile/" . $user->guid . "topbar.jpg");
	$filehandler->open("write");
	$filehandler->write($topbar);
	$filehandler->close();

	$user->icontime = time();
	if (elgg_trigger_event('profileiconupdate', $user->type, $user)) {
		system_message(elgg_echo("avatar:upload:success"));

		$view = 'river/user/default/profileiconupdate';
		elgg_delete_river(array('subject_guid' => $user->guid, 'view' => $view));
		add_to_river($view, 'update', $user->guid, $user->guid);
	}
} else {
	system_message(elgg_echo("profile:icon:notfound"));
}
forward(REFERRER);
