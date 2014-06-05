<?php
/**
 * Plugin settings
 */
$input_options = array(
	"html5" => "html5",
	"flash" => "flash"
);


$webcam_input = $vars['entity']->webcam_input;

echo elgg_echo('webcam:webcam_input');
echo '<br>';
echo elgg_view("input/dropdown", array("name" => "params[webcam_input]", "value" => $webcam_input, "options_values" => $input_options));
echo '<br>';
