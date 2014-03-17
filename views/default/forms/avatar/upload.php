<?php
/**
 * Avatar upload form
 * 
 * @uses $vars['entity']
 */

?>
<div>
	<label><?php echo elgg_echo("avatar:upload"); ?></label><br />
	<?php echo elgg_view("input/file",array('name' => 'avatar')); ?>
</div>
<div class="elgg-foot">
	<?php echo elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['entity']->guid)); ?>
	<?php echo elgg_view('input/submit', array('value' => elgg_echo('upload'))); ?>
</div>
<div>
        <?php 
	$webcam_input = elgg_get_plugin_setting("webcam_input","webcam");
        if($webcam_input == "flash") {
		echo elgg_view('profile/captureicon'); 
	}else{
		echo elgg_view('profile/webcam'); 
	}
	?>

</div>

