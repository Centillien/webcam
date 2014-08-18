<?php
/**
 * Plugin settings
 */
$input_options = array(
	"html5" => elgg_echo("html5"),
	"flash" => elgg_echo("flash")
);

$noyes_options = array(
	"no" => elgg_echo("option:no"),
	"yes" => elgg_echo("option:yes")
);



$webcam_input = $vars['entity']->webcam_input;
$webcam_input_registration = $vars['entity']->webcam_registration;

echo elgg_echo('webcam:webcam_input');
echo '<br>';
echo elgg_view("input/dropdown", array("name" => "params[webcam_input]", "value" => $webcam_input, "options_values" => $input_options));
echo '<br>';
echo '<br>';

echo elgg_echo('webcam:webcam_registration');
echo '<br>';
echo elgg_view("input/dropdown", array("name" => "params[webcam_registration]", "value" => $webcam_input_registration, "options_values" => $noyes_options));
echo '<br>';
echo '<br>';

