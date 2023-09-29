<?php 

/**
 * Get Easy Posts instance and return rendered HTML
 * Editing something here also edit it in ajax.php!
 * 
 * @since 2.0
 * @author Ilya K.
 */

oxygen_vsb_ajax_request_header_check();

$component_json = file_get_contents('php://input');
$component 		= json_decode( $component_json, true );
$options 		= $component['options']['original'];

$options['preview'] = true;
// add selector to proper CSS generation
$options['selector'] = $component['options']['selector'];

global $oxygen_vsb_components;
echo $oxygen_vsb_components['gallery']->shortcode($options);