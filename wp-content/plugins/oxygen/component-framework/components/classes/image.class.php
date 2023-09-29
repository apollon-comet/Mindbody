<?php


Class CT_Image extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_visual", array( $this, "component_button" ) );

		// Increase default 1600 max width for image sizes in srcset attribute.
		add_filter( 'max_srcset_image_width', array( $this, 'max_srcset_image_width' ) );
	}


	/**
	 * Add a [ct_image] shortcode to WordPress
	 *
	 * @since 0.1
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );
		
		ob_start();

		// Run shortcodes in the 'alt' option, because it is base64 encoded, so the set_options() function above won't detect any shortcode on it.
		$options['alt'] = do_shortcode( base64_decode( $options['alt'] ) );

		if( $options['image_type'] == 1 ) {
			$image_src = $options['src'];
			$image_alt = $options['alt'];
			if( class_exists( 'Oxygen_Gutenberg' ) ) {
                $image_src = Oxygen_Gutenberg::decorate_attribute($options, $image_src, 'image');
                $image_alt = Oxygen_Gutenberg::decorate_attribute($options, $image_alt, 'alt');
            }
            
			echo '<img id="' . esc_attr($options['selector']) . '" alt="' . $image_alt . '" src="' . $image_src . '" class="' . esc_attr($options['classes']) . '"'; do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); echo '/>';
		} else {
            $attachment_id = intval($options['attachment_id']);
            
            if ($attachment_id > 0) {
                $image_alt = $options['alt'];
                $attachment_size = isset($options['attachment_size']) ? $options['attachment_size'] : 'thumbnail';
                $source = wp_get_attachment_image_src($attachment_id, $attachment_size);

                if (is_array($source)) {
                    list($image_src, $image_width, $image_height) = $source;

                    // if (class_exists('Oxygen_Gutenberg')) {
                    //     $image_src = Oxygen_Gutenberg::decorate_attribute($options, $image_src, 'image');
                    // }

                    // If alt text is empty, pull it from image metadata
                    if (empty($image_alt)) {
                        $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                    }

                    // Remove image sources son SRCSET with bigger width than the image size selected
                    add_filter('wp_calculate_image_srcset', array($this, 'remove_bigger_srcset_sources'), 10, 5);

                    $srcset = wp_get_attachment_image_srcset($attachment_id, $attachment_size);

                    // Remove our filter so it doesn't affect 3rd party plugins
                    remove_filter('wp_calculate_image_srcset', array($this, 'remove_bigger_srcset_sources'), 10);

                    echo '<img id="' . esc_attr($options['selector']) . '" alt="' . esc_attr($image_alt) . '" src="' . esc_attr($image_src) . '" class="' . esc_attr($options['classes']) . '" srcset="' . $srcset . '" sizes="(max-width: '.$image_width.'px) 100vw, '.$image_width.'px" '; do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); echo '/>';
                }
            }
        }

		return ob_get_clean();
	}

	function max_srcset_image_width( ) {
		// Set max width to 8K resolution
		return 7680;
	}

	function remove_bigger_srcset_sources( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		foreach ( $sources as $width => $data ) {
			if( $width > $size_array[0] ) {
				unset( $sources[ $width ] );
			}
		}
		return $sources;
	}
}

/**
 * Create Image Component Instance
 * 
 * @since 0.1.2
 */

global $oxygen_vsb_components;
$oxygen_vsb_components['image'] = new CT_Image ( 

		array( 
			'name' 		=> 'Image',
			'tag' 		=> 'ct_image',
			'params' 	=> array(
					array(
						"type" 			=> "radio",
						"heading" 		=> "",
						"param_name" 	=> "image_type",
						"value" 		=> array(
							1 	        => __("Image URL"),
							2   	    => __("Media Library")
						),
						"default"       => 1,
						"css"			=> false,
					),
					array(
						"type" 			=> "mediaurl",
						"heading" 		=> __("Image URL"),
						"param_name" 	=> "src",
						"value" 		=> "http://placehold.it/1600x900",
						"condition"		=> "image_type!=2",
						"css"			=> false
					),
					array(
						"type" 			=> "mediaurl",
						"heading" 		=> __("ID"),
						"param_name" 	=> "attachment_id",
						"value" 		=> "",
						"condition"		=> "image_type=2",
						"attachment"    => true,
						"css"			=> false
					),
					array(
						"type" 			=> "dropdown_dynamic",
						"heading" 		=> "Size",
						"param_name" 	=> "attachment_size",
						"dynamic"       => true,
						"ngrepeat_value"=> "option in iframeScope.component.options[iframeScope.component.active.id].size_labels",
						"ngclick_value" => "iframeScope.setOptionModel('attachment_size', option); iframeScope.setOptionModel('attachment_width', iframeScope.component.options[iframeScope.component.active.id].sizes[option].width); iframeScope.setOptionModel('attachment_height', iframeScope.component.options[iframeScope.component.active.id].sizes[option].height); iframeScope.setOptionModel('attachment_url', iframeScope.component.options[iframeScope.component.active.id].sizes[option].url);",
						"default"       => "medium",
						"css"			=> false,
						"condition"		=> "image_type=2",
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Width"),
						"param_name" 	=> "width",
						"value" 		=> "",
						"hide_wrapper_end" => true,
					),
					array(
						"type" 			=> "measurebox",
						"heading" 		=> __("Height"),
						"param_name" 	=> "height",
						"value" 		=> "",
						"hide_wrapper_start" => true,
					),
					array(
						"param_name" 	=> "attachment_width",
						"value" 		=> "",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "attachment_height",
						"value" 		=> "",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "width-unit",
						"value" 		=> "auto",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "height-unit",
						"value" 		=> "auto",
						"hidden" 		=> true,
					),
					array(
						"param_name" 	=> "attachment_url",
						"value" 		=> "http://placehold.it/1600x900",
						"hidden" 		=> true,
					),
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("Alt Text"),
						"param_name" 	=> "alt",
						"value" 		=> "",
						"css" 			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertShortcodeToImageAlt">data</div>'
					)
			),
			'advanced' => array(
				"size" => array(
						"values" 	=> array (
							'max-width' 	=> '100',
							'max-width-unit' 	=> '%',
							)
					),
				'allowed_html' => 'post',
				'allow_shortcodes' => false,
			)
		)
);

?>
