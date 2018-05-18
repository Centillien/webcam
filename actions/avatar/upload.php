<?php
/**
 * Avatar upload action
 */

$guid = get_input('guid');
$owner = get_entity($guid);

// check for html5, and finally file upload
$img_data = false;
$html5 = get_input('webcam-image-base64');

if ($html5) {
    $img_data = base64_decode($html5);

    if (!$img_data) {
        register_error(elgg_echo("avatar:upload:fail"));
        forward(REFERRER);
    }
}

// if we have img data, save it
$filehandler = null;
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

if (!$owner || !($owner instanceof ElggUser) || !$owner->canEdit()) {
    register_error(elgg_echo('avatar:upload:fail'));
    forward(REFERER);
}

if ($filehandler != null) {
    if (!$owner->saveIconFromElggFile($filehandler)) {
        register_error(elgg_echo('avatar:resize:fail'));
        forward(REFERER);
    }
} else {
    if (!$owner->saveIconFromUploadedFile('avatar')) {
        register_error(elgg_echo('avatar:resize:fail'));
        forward(REFERER);
    }
}

$error = elgg_get_friendly_upload_error($_FILES['avatar']['error']);
if ($error) {
    //register_error($error);
    forward(REFERER);
}

if (elgg_trigger_event('profileiconupdate', $owner->type, $owner)) {
    system_message(elgg_echo("avatar:upload:success"));

    $view = 'river/user/default/profileiconupdate';
    _elgg_delete_river(array('subject_guid' => $owner->guid, 'view' => $view));
    elgg_create_river_item(array(
        'view' => $view,
        'action_type' => 'update',
        'subject_guid' => $owner->guid,
        'object_guid' => $owner->guid,
    ));
}

forward(REFERER);
