<?php 

define("OXTK_PATH", plugin_dir_path( __FILE__ ) );
define("OXTK_URI", 	plugin_dir_url( __FILE__ ) );

Class OxygenVSBTypekit {

	function __construct() {

		// add scripts and styles
		add_action( 'oxygen_enqueue_scripts', 	array( $this, 'enqueue_script' ) );
		add_action( 'admin_menu', 				array( $this, 'add_typekit_page' ) );
		add_action( 'ct_builder_ng_init', 		array( $this, 'init_typekit' ) );
	}

	
	/**
	 * Add scripts
	 *
	 * @since 1.0
	 * @author Ilya K.
	 */

	function enqueue_script() {

		if ( !get_option("ct_typekit_token", "") ) {
			return;
		}

		if ( $kit_id = get_option( 'ct_typekit_kit_id', '') ) {
			
			wp_enqueue_script   ( 'oxygen-adobe-typekit', 'https://use.typekit.net/'.$kit_id.'.js');
			wp_add_inline_script( 'oxygen-adobe-typekit', 'try{Typekit.load({ async: true });}catch(e){}');
		}
	}


	/**
	 * Add Typekit sub-menu
	 * 
	 * @since 1.2
	 */

	function add_typekit_page() {

		// if(!oxygen_vsb_current_user_can_access()) {
		// 	return;
		// }
		
		// add_submenu_page( 	'ct_dashboard_page', 
		// 					'Typekit', 
		// 					'Typekit', 
		// 					'manage_options', 
		// 					'ct_typekit', 
		// 					array( $this, 'typekit_page_callback' ) );
	}


	/**
	 * Callback to show Typekit settings page
	 *
	 * @since 1.2
	 */

	function typekit_page_callback() {
		require_once 'includes/typekit-page.view.php';
	}


	/**
	 * Output Typekit fonts if user set the Typekit kit
	 *
	 * @since 1.0
	 */

	function init_typekit() {

		$token  = get_option("ct_typekit_token", "");
		$kit_id = get_option("ct_typekit_kit_id", "");

		if(empty($kit_id) || empty($token)) {
			echo "typeKitFonts=[];";
		}
		else {
			// Get Typekit fonts
			$response = wp_remote_get( 'https://typekit.com/api/v1/json/kits/'.$kit_id.'?token='.$token, 
				array( 	'timeout' => 120, 
						'httpversion' => '1.1' ) );

			$response = json_decode( $response["body"], true );

			//var_dump( $response );

			if ( isset( $response["kit"] ) && $response["kit"] && is_array( $response["kit"]["families"] ) ) {

				$fonts = [];

				foreach ( $response["kit"]["families"] as $family ) {
					$fonts[] = array(
							//"slug" => $family["slug"],
							"slug" => $family["css_names"][0],
							"name" => $family["name"]
						);	
				}

				$output = json_encode( $fonts );
				$output = htmlspecialchars( $output, ENT_QUOTES );

				echo "typeKitFonts=$output;";
			}
			else {
				echo "typeKitFonts=[];";	
			}
		}
	}

}

/**
 * Init Typekit
 */

function oxygen_typekit_init() {
	// Instantiate the plugin
	global $oxygenTypekitInstance;
	$oxygenTypekitInstance = new OxygenVSBTypekit();
}
add_action( 'plugins_loaded', 'oxygen_typekit_init' );