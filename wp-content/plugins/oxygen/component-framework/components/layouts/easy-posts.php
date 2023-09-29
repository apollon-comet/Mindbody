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

global $oxygen_signature;

$shortcode_atts = array(
	'preview' => 'true',
	'ct_options' => "{\"selector\":\"{$component['options']['selector']}\",\"original\":{\"code-php\":\"".base64_encode($options['code-php'])."\",\"code-css\":\"".base64_encode($options['code-css'])."\",\"posts_per_page\":\"{$options['posts_per_page']}\",\"query_post_ids\":\"{$options['query_post_ids']}\",\"wp_query\":\"{$options['wp_query']}\",\"query_order_by\":\"{$options['query_order_by']}\",\"query_count\":\"{$options['query_count']}\",\"query_all_posts\":\"{$options['query_all_posts']}\",\"query_ignore_sticky_posts\":\"{$options['query_ignore_sticky_posts']}\",\"query_order\":\"{$options['query_order']}\",\"query_args\":\"{$options['query_args']}\",\"query_post_types\":".json_encode($options['query_post_types'], JSON_FORCE_OBJECT).",\"query_taxonomies_any\":".json_encode($options['query_taxonomies_any'], JSON_FORCE_OBJECT).",\"query_taxonomies_all\":".json_encode($options['query_taxonomies_all'], JSON_FORCE_OBJECT).",\"query_authors\":".json_encode($options['query_authors'], JSON_FORCE_OBJECT)."}}",
);

// Generate signature
$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_posts_grid', $shortcode_atts, '');
// Generate output
$shortcode = "[oxy_posts_grid {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

echo do_shortcode($shortcode);
