<?php
/**
 * Avatar upload form
 *
 * @uses $vars['entity']
 */
elgg_require_js('webcam');

$selected_tab = get_input('tab', 'acquire');

$options = array(
	'tabs' => array(),
	'class' => 'avatar-tabs'
);
$tabs = array('acquire', 'upload', 'url');

foreach ($tabs as $tab) {
	$options['tabs'][] = array(
		'text' => elgg_echo("webcam:tab:$tab"),
		'id' => "avatar-$tab-tab",
		'selected' => $selected_tab == $tab,
		'href' => '#'
	);
}

$tab_nav = elgg_view('navigation/tabs', $options);

echo $tab_nav;
?>

<div id="avatar-options">
	<div id="avatar-upload" class="hidden">
		<label><?php echo elgg_echo("avatar:upload"); ?></label><br />
		<?php echo elgg_view("input/file", array('name' => 'avatar')); ?>
	</div>

	<div id="avatar-acquire">
		<label><?php echo elgg_echo("webcam:acquire:info"); ?></label><br />
		<div id="webcam">
			<canvas id="webcam-canvas" class="hidden"></canvas>
			<video id="webcam-video"></video>
		</div>
	</div>

	<div id="avatar-url" class="hidden">
		<label><?php echo elgg_echo("webcam:url:info"); ?></label><br />
		<?php echo elgg_view("input/text", array('name' => 'avatar_url')); ?>
	</div>
</div>


<div class="elgg-foot">
<?php
	echo elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['entity']->guid));
	echo elgg_view('input/submit', array(
		'value' => elgg_echo('upload'),
		'id' => 'avatar-upload'
	));
?>
</div>
