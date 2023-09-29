<?php
/*
Plugin Name: Loading Page
Plugin URI: http://wordpress.dwbooster.com/content-tools/loading-page
Description: Loading Page plugin performs a pre-loading of images on your website and displays a loading progress screen with percentage of completion. Once everything is loaded, the screen disappears.
Version: 1.0.73
Author: CodePeople
Author URI: http://wordpress.dwbooster.com/content-tools/loading-page
License: GPLv2
Text Domain: loading-page
*/

require_once 'banner.php';
$codepeople_promote_banner_plugins[ 'codepeople-loading-page' ] = array(
	'plugin_name' => 'Loading Page',
	'plugin_url'  => 'https://wordpress.org/support/plugin/loading-page/reviews/#new-post'
);

// CONST
define('LOADING_PAGE_PLUGIN_DIR', dirname(__FILE__));
define('LOADING_PAGE_PLUGIN_URL', plugins_url('', __FILE__));
define('LOADING_PAGE_TD', 'loading-page');

// Feedback system
require_once 'feedback/cp-feedback.php';
new CP_FEEDBACK(plugin_basename( dirname(__FILE__) ), __FILE__, 'https://wordpress.dwbooster.com/contact-us');

include LOADING_PAGE_PLUGIN_DIR.'/includes/admin_functions.php';

add_filter('option_sbp_settings', 'loading_page_troubleshoot');
if(!function_exists('loading_page_troubleshoot'))
{
	function loading_page_troubleshoot($option)
	{
		if(!is_admin())
		{
			// Solves a conflict caused by the "Speed Booster Pack" plugin
			if(is_array($option) && isset($option['jquery_to_footer'])) unset($option['jquery_to_footer']);
			if(is_array($option) && isset($option['defer_parsing'])) unset($option['defer_parsing']);
		}
		return $option;
	} // End loading_page_troubleshoot
}

/**
* Plugin activation
*/
register_activation_hook( __FILE__, 'loading_page_install' );
if(!function_exists('loading_page_install')){
	function _loading_page_options()
	{
		$loading_page_options = get_option('loading_page_options', array());
		if( !empty($loading_page_options) ) return;

		// Set the default options here
        $loading_page_options = array(
            'foregroundColor'           => '#000000',
            'backgroundColor'           => '#ffffff',
            'enabled_loading_screen'    => true,
            'close_btn'    				=> true,
            'remove_in_on_load'    		=> false,
            'loading_screen'            => 'logo',
            'lp_loading_screen_display_in'  => 'all',
			'once_per_session'  		=> 'always',
			'lp_loading_screen_display_in_pages' => '',
			'lp_loading_screen_exclude_from_pages' => '',
			'lp_loading_screen_exclude_from_post_types' => '',
			'lp_loading_screen_exclude_from_urls' => array(),
            'displayPercent'            => true,
            'backgroundImage'           => '',
            'backgroundImageRepeat'     => 'repeat',
            'fullscreen'                => 0,
            'pageEffect'                => 'none',
			'deepSearch'				=> false,
			'modifyDisplayRule'			=> false,
			// For the logo screen
			'lp_ls'						=> array( 'logo' => array( 'image' => plugins_url( 'loading-screens/logo/images/05.svg', __FILE__ )))
        );

        update_option('loading_page_options', $loading_page_options);
    }
	function loading_page_install( $networkwide ) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			if ($networkwide) {
	            $old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					_loading_page_options();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		_loading_page_options();
	} // End loading_page_install
} // End plugin activation

/**
 * Patch to solve the conflict with the previous version that was using animated gifs.
 */
function loading_page_gifs_replacement_patch()
{
	$opt = get_option('loading_page_options', array());
	if(
		!empty($opt) &&
		!empty($opt['lp_ls']) &&
		!empty($opt['lp_ls']['logo']) &&
		!empty($opt['lp_ls']['logo']['image'])
	)
	{
		$path = $opt['lp_ls']['logo']['image'];
		// Only to solve an issue with the previous version of the plugin when where used .gif
		$path = preg_replace('/\/loading\-screens\/logo\/gifs\/(\d+)\.gif/i', '/loading-screens/logo/images/$1.svg', $path);
		$opt['lp_ls']['logo']['image'] = $path;
		update_option('loading_page_options', $opt);
	}
}
function loading_page_upgrade_completed_gifs_patch( $upgrader_object, $options ) {
	$our_plugin = plugin_basename( __FILE__ );
	if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
		foreach( $options['plugins'] as $plugin ) {
			if( $plugin == $our_plugin ) {
				loading_page_gifs_replacement_patch();
			}
		}
	}
}
add_action( 'upgrader_process_complete', 'loading_page_upgrade_completed_gifs_patch', 10, 2 );
function loading_page_activated_gifs_patch($plugin, $network_activation)
{
	if($plugin == plugin_basename( __FILE__ ))
	{
		loading_page_gifs_replacement_patch();
	}
}
add_action( 'activated_plugin', 'loading_page_activated_gifs_patch', 10, 2 );

/**
*	A new blog has been created in a multisite WordPress
*/
add_action( 'wpmu_new_blog', 'loading_page_new_blog', 10, 6);
function loading_page_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;
	$file = basename(__FILE__);
	$dir = basename(dirname(__FILE__));
	if ( is_plugin_active_for_network($dir.'/'.$file) )
	{
        $current_blog = $wpdb->blogid;
        switch_to_blog( $blog_id );
		_loading_page_options();
        switch_to_blog( $current_blog );
    }
} // End loading_page_new_blog

// Redirecting the user to the settings page of the plugin
add_action( 'activated_plugin', 'loading_page_redirect_to_settings', 10, 2 );
if(!function_exists('loading_page_redirect_to_settings'))
{
	function loading_page_redirect_to_settings($plugin, $network_activation)
	{
		if(
			$plugin == plugin_basename( __FILE__ ) &&
			(!isset($_POST["action"]) || $_POST["action"] != 'activate-selected') &&
			(!isset($_POST["action2"]) || $_POST["action2"] != 'activate-selected')
		)
		{
			exit( wp_redirect( admin_url( 'options-general.php?page=loading-page.php' ) ) );
		}
	}
}

/*
*   Plugin initializing
*/
add_action( 'init', 'loading_page_init');
if(!function_exists('loading_page_init')){
    function loading_page_init(){
        if(!is_admin() && empty($_REQUEST['elementor-preview'])){
            $op = get_option('loading_page_options');
            if($op &&  $op['enabled_loading_screen'])
			{
                add_action('wp_enqueue_scripts', 'loading_page_enqueue_scripts', 1);
			}
        }
    } // End loading_page_init
}

/*
*   Admin initionalizing
*/
add_action('admin_init', 'loading_page_admin_init');
if(!function_exists('loading_page_admin_init')){
    function loading_page_admin_init(){
        // Load the associated text domain
        load_plugin_textdomain( LOADING_PAGE_TD, false, LOADING_PAGE_PLUGIN_DIR . '/languages/' );

        // Set plugin links
        $plugin = plugin_basename(__FILE__);
        add_filter('plugin_action_links_'.$plugin, 'loading_page_links');

        // Load resources
        add_action('admin_enqueue_scripts', 'loading_page_admin_resources');

    } // End loading_page_admin_init
}

if(!function_exists('loading_page_links')){
    function loading_page_links($links){
        // Custom link
        $custom_link = '<a href="http://wordpress.dwbooster.com/contact-us" target="_blank">'.__('Request custom changes', LOADING_PAGE_TD).'</a>';
		array_unshift($links, $custom_link);

        // Settings link
        $settings_link = '<a href="options-general.php?page=loading-page.php">'.__('Settings', LOADING_PAGE_TD).'</a>';
		array_unshift($links, $settings_link);

		return $links;
    } // End loading_page_customization_link
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'loading_page_customizationLink');
if(!function_exists('loading_page_customizationLink'))
{
	function loading_page_customizationLink($links)
	{
		array_unshift($links, '<a href="https://wordpress.org/support/plugin/loading-page/#new-post" target="_blank">'.__('Help', LOADING_PAGE_TD).'</a>');
		return $links;
	}
}

// Set the settings menu option
add_action('admin_menu', 'loading_page_settings_menu');
if(!function_exists('loading_page_settings_menu')){
    function loading_page_settings_menu(){
        /**
		 *	Add to admin_menu
		 *	- The capability has been modified, from "edit_posts" to "manage_options"
		 * 	- Now only the administrators can now edit the "Loading Page" settings.
		*/
		add_options_page('Loading Page', 'Loading Page', 'manage_options', basename(__FILE__), 'loading_page_settings_page');
    } // End loading_page_settings_menu
}

if(!function_exists('loading_page_add_codeBlock')){
	function loading_page_add_codeBlock()
	{
		$codeblock = '';
		$op = get_option('loading_page_options');
		if( !empty($op) && !empty($op['codeBlock']) )
		{
			$codeblock = '<div id="loading_page_codeBlock">'.$op['codeBlock'].'</div>';
		}
		return $codeblock;
	}
}

if(!function_exists('loading_page_admin_resources')){
    function loading_page_admin_resources($hook){
        if(strpos($hook, "loading-page") !== false){
			wp_enqueue_media();
            wp_enqueue_style( 'farbtastic' );
            wp_enqueue_script( 'farbtastic' );
		    wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'thickbox' );

            wp_enqueue_script('lp-admin-script', LOADING_PAGE_PLUGIN_URL.'/js/loading-page-admin.js', array('jquery', 'thickbox', 'farbtastic'), 'free-1.0.73');
        }
    } // End loading_page_admin_resources
}

if( !function_exists('loading_page_loading_screen') ){
    function loading_page_loading_screen(){
		if( !defined('DOING_AJAX') && session_id() == "" ) @session_start();
        global $post;
		$op = get_option('loading_page_options');
        $loadingScreen = 0;
        if(
			!isset( $_SERVER['HTTP_USER_AGENT'] ) ||
			preg_match( '/bot|crawl|slurp|spider/i', $_SERVER[ 'HTTP_USER_AGENT' ] )
		)
		{
			return $loadingScreen;
		}

        if(
			!empty( $op['enabled_loading_screen'] )
		)
        {
			global $wp;
			$current_url = home_url( add_query_arg( array(), $wp->request ) );
			$_permalink = md5($current_url);
			if(
				empty( $op[ 'once_per_session' ] ) ||
				$op[ 'once_per_session' ] == 'always' ||
				empty( $_SESSION[ 'loading_page_once_per_session' ] ) ||
				(
					$op[ 'once_per_session' ] == 'page' &&
					is_array($_SESSION[ 'loading_page_once_per_session' ]) &&
					empty($_SESSION[ 'loading_page_once_per_session' ][$_permalink])
				)
			)
			{
				if(empty($_SESSION[ 'loading_page_once_per_session' ]) || !is_array($_SESSION[ 'loading_page_once_per_session' ])) $_SESSION[ 'loading_page_once_per_session' ] = array();
				$_SESSION[ 'loading_page_once_per_session' ][$_permalink] = 1;

				$pages = ( !empty( $op[ 'lp_loading_screen_display_in_pages' ] ) ) ? $op[ 'lp_loading_screen_display_in_pages' ] : '';
				$pages = str_replace( ' ', '', $pages );
				$pages = explode( ',', $pages );

				$exclude_pages = ( !empty( $op[ 'lp_loading_screen_exclude_from_pages' ] ) ) ? $op[ 'lp_loading_screen_exclude_from_pages' ] : '';
				$exclude_pages = str_replace( ' ', '', $exclude_pages );
				$exclude_pages = explode( ',', $exclude_pages );

				$exclude_post_types = ( !empty( $op[ 'lp_loading_screen_exclude_from_post_types' ] ) ) ? $op[ 'lp_loading_screen_exclude_from_post_types' ] : '';
				$exclude_post_types = str_replace( ' ', '', $exclude_post_types );
				$exclude_post_types = explode( ',', $exclude_post_types );

				$exclude_pages_urls = ( !empty( $op[ 'lp_loading_screen_exclude_from_urls' ] ) ) ? $op[ 'lp_loading_screen_exclude_from_urls' ] : array();

				$exclude_elementor_maintenance = (
					defined('ELEMENTOR_VERSION') &&
					!empty($op['lp_loading_screen_exclude_from_elementor_maintenance']) &&
					get_option('elementor_maintenance_mode_mode', '') != ''
				) ? true : false;

				if(
					!$exclude_elementor_maintenance &&
					(
						empty( $op[ 'lp_loading_screen_display_in' ] ) ||
						$op[ 'lp_loading_screen_display_in' ] == 'all' ||
						( $op[ 'lp_loading_screen_display_in' ] == 'home' && ( is_home() || is_front_page() ) ) ||
						( $op[ 'lp_loading_screen_display_in' ] == 'pages' && is_singular() && isset( $post ) && in_array( $post->ID, $pages )  )
					) &&
					(
						empty( $post ) ||
						empty( $post->ID ) ||
						(
							(
								empty( $exclude_pages ) ||
								!in_array( $post->ID, $exclude_pages )
							)
							&&
							(
								empty( $exclude_post_types ) ||
								!in_array( $post->post_type, $exclude_post_types )
							)
						)
					)
				)
				{
					if(!empty($exclude_pages_urls))
					{
						$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
						$current_url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
						foreach($exclude_pages_urls as $exclude_page_url)
						{
							$exclude_page_url = preg_quote($exclude_page_url, '/');
							$exclude_page_url = str_replace('\*', '.*', $exclude_page_url);
							if(preg_match('/'.$exclude_page_url.'/i', $current_url)) return;
						}
					}

					$loadingScreen = 1;
					add_action( 'wp_head',  'loading_page_replace_the_header', 99 );
					add_filter( 'autoptimize_filter_js_dontmove','loading_page_autoptimize_exclude',10,1);
				}
			}
        }
        return $loadingScreen;
    }
}

if(!function_exists('loading_page_autoptimize_exclude'))
{
	function loading_page_autoptimize_exclude($to_exclude)
	{
		$to_exclude[] = 'loading-page.js';
		$to_exclude[] = '/loading-screens/';
		return $to_exclude;
	}
}

if(!function_exists('loading_page_replace_the_header'))
{
	function loading_page_replace_the_header()
	{
		$opt = get_option('loading_page_options', array());
		if(empty($opt) || empty($opt['modifyDisplayRule']))
			echo '<style>body{visibility:hidden;}</style><noscript><style>body{visibility:visible;}</style></noscript>';
		else
			echo '<style>body{display:none;}</style><noscript><style>body{display:block;}</style></noscript>';

		if(
			!empty($opt) &&
			!empty($opt['lp_ls']) &&
			!empty($opt['lp_ls']['logo']) &&
			!empty($opt['lp_ls']['logo']['image'])
		)
		{
			$path = $opt['lp_ls']['logo']['image'];
			echo '<link rel="preload" href="'.esc_attr($path).'" as="image" type="image/svg+xml">';
		}
	} // End loading_page_replace_the_header
}

if(!function_exists('loading_page_hex2rgb'))
{
	function loading_page_hex2rgb( $colour ) {
		if($colour[0]=='#') $colour = substr($colour, 1);
		if(strlen($colour) == 6) list($r, $g, $b) = array($colour[0].$colour[1], $colour[2].$colour[3], $colour[4].$colour[5]);
		elseif(strlen($colour) == 3) list($r, $g, $b) = array($colour[0].$colour[0], $colour[1].$colour[1], $colour[2].$colour[2]);
		else return $colour;
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );
		return 'rgba('.$r.','.$g.','.$b.',.8)';
	}
}

if(!function_exists('loading_page_enqueue_scripts')){
    function loading_page_enqueue_scripts()
	{
		global $post;

        $op = get_option('loading_page_options');
        $loadingScreen = loading_page_loading_screen();

        if( $loadingScreen )
		{
			$loading_page_settings = array(
				'loadingScreen'   => $loadingScreen,
				'closeBtn'		  => (!isset($op['close_btn']) || $op['close_btn']) ? true : false,
				'removeInOnLoad'  => (!empty($op['remove_in_on_load'])) ? $op['remove_in_on_load'] : false,
				'codeblock'		  => loading_page_add_codeBlock(),
                'backgroundColor' => (!isset($op['transparency']) || $op['transparency']) ? loading_page_hex2rgb($op['backgroundColor']) : $op['backgroundColor'],
                'foregroundColor' => $op['foregroundColor'],
                'backgroundImage' => $op['backgroundImage'],
				'additionalSeconds' => (!empty($op['additionalSeconds'])) ? $op['additionalSeconds'] : 0,
                'pageEffect'      => $op['pageEffect'],
                'backgroundRepeat'=> $op['backgroundImageRepeat'],
                'fullscreen'      => (( !empty( $op[ 'fullscreen' ] ) ) ? 1 : 0),
                'graphic'         => $op['loading_screen'],
                'text'            => ((!empty($op['displayPercent'])) ? $op['displayPercent'] : 0),
				'lp_ls' 		  => ((!empty($op['lp_ls'])) ? $op[ 'lp_ls' ] : 0),
				'screen_size'	  => ((!empty($op['screen_size']))  ? $op[ 'screen_size' ]  : 'all'),
				'screen_width'	  => ((!empty($op['screen_width'])) ? $op[ 'screen_width' ] : 0),
				'deepSearch'	  	=> ((empty($op['deepSearch' ])) ? 0 : 1),
				'modifyDisplayRule'	=> ((empty($op['modifyDisplayRule' ])) ? 0 : 1)
			);

			$required = array('jquery');
			wp_enqueue_script('jquery');
			 wp_enqueue_style('codepeople-loading-page-style', LOADING_PAGE_PLUGIN_URL.'/css/loading-page.css', array(), 'free-1.0.73', false);
			wp_enqueue_style('codepeople-loading-page-style-effect', LOADING_PAGE_PLUGIN_URL.'/css/loading-page'.(($op['pageEffect'] != 'none') ? '-'.$op['pageEffect'] : '').'.css', array(), 'free-1.0.73', false);

            $s = loading_page_get_screen($op['loading_screen']);
            if($s)
			{
                if(!empty($s['style']))
				{
                    wp_enqueue_style('codepeople-loading-page-style-'.$s['id'], $s['style'], array(), 'free-1.0.73', false);
                }

                if(!empty($s['script']))
				{
                    wp_enqueue_script('codepeople-loading-page-script-'.$s['id'], $s['script'], array('jquery'), 'free-1.0.73', false);
                    $required[] = 'codepeople-loading-page-script-'.$s['id'];
                }
            }
            wp_enqueue_script('codepeople-loading-page-script', LOADING_PAGE_PLUGIN_URL.'/js/loading-page.js', $required, 'free-1.0.73', false);
			if(function_exists('wp_add_inline_script'))
			{
				wp_add_inline_script('codepeople-loading-page-script', 'loading_page_settings='.json_encode($loading_page_settings).';', 'before');
			}
			else
			{
				wp_localize_script('codepeople-loading-page-script', 'loading_page_settings', $loading_page_settings);
			}
        }
    } // End loading_page_enqueue_scripts
}

if(!function_exists('loading_page_clean_and_sanitize'))
{
	function loading_page_clean_and_sanitize($str)
	{
		if(is_object($str) || is_array($str)) return '';

		$str = (string) $str;
		$str = stripcslashes(trim($str));

		$filtered = wp_check_invalid_utf8( $str );
		while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) )
			$filtered = str_replace( $match[0], '', $filtered );
		return trim($filtered);
	} // End loading_page_clean_and_sanitize

}

if(!function_exists('loading_page_sanitize_array'))
{
	function loading_page_sanitize_array($array)
	{
		if(is_array($array))
		{
			foreach($array as $key => $value)
			{
				if(is_array($value)) $array[$key] = loading_page_sanitize_array($value);
				else $array[$key] = sanitize_text_field($value);
			}
			return $array;
		}
		else return sanitize_text_field($array);
	} // End loading_page_sanitize_array
}

if(!function_exists('loading_page_settings_page')){
    function loading_page_settings_page(){
        if(isset($_POST['loading_page_nonce']) && wp_verify_nonce($_POST['loading_page_nonce'], __FILE__)){
			$additionalSeconds = @intval($_POST['lp_additionalSeconds']);
			$codeBlock = loading_page_clean_and_sanitize($_POST['lp_codeBlock']);

			$lp_loading_screen_exclude_from_urls = sanitize_textarea_field($_POST['lp_loading_screen_exclude_from_urls']);
			$lp_loading_screen_exclude_from_urls  = explode("\n", $lp_loading_screen_exclude_from_urls);
			foreach ($lp_loading_screen_exclude_from_urls as $key => $url)
			{
				$url = trim($url);
				if($url == '') unset($lp_loading_screen_exclude_from_urls[$key]);
				else $lp_loading_screen_exclude_from_urls[$key]=$url;
			}

            // Set the default options here
            $loading_page_options = array(
                'foregroundColor'           => (!empty($_POST['lp_foregroundColor'])) ? sanitize_text_field($_POST['lp_foregroundColor']) : '#FFFFFF',
                'backgroundColor'           => (!empty($_POST['lp_backgroundColor'])) ? sanitize_text_field($_POST['lp_backgroundColor']) : '#000000',
                'transparency'           	=> (!empty($_POST['lp_backgroundTransparency'])) ? true : false,
                'backgroundImage'           => esc_url_raw($_POST['lp_backgroundImage']),
                'backgroundImageRepeat'     => (isset($_POST['lp_backgroundRepeat']) && in_array($_POST['lp_backgroundRepeat'], array('repeat', 'no-repeat'))) ? $_POST['lp_backgroundRepeat'] : 'repeat',
                'additionalSeconds'     	=> $additionalSeconds,
                'codeBlock'     			=> $codeBlock,
                'fullscreen'                => ( isset( $_POST['lp_fullscreen'] ) ) ? 1 : 0,
                'enabled_loading_screen'    => (isset($_POST['lp_enabled_loading_screen'])) ? true : false,
                'close_btn'    				=> (isset($_POST['lp_close_btn'])) ? true : false,
                'remove_in_on_load'    		=> (isset($_POST['lp_remove_in_on_load'])) ? true : false,
				'screen_size'				=> (isset($_POST['lp_screen_size']) && in_array($_POST['lp_screen_size'], array('all', 'greater', 'lesser'))) ? $_POST['lp_screen_size'] : 'all',
				'screen_width'				=> (!empty($_POST['lp_screen_width']) && is_numeric( ($lp_screen_width = preg_replace('/[^\\d\\.]/', '', $_POST['lp_screen_width']) ) ) ) ? $lp_screen_width : '',
                'lp_loading_screen_display_in'  	 => ( isset( $_POST[ 'lp_loading_screen_display_in' ] ) ) ? $_POST[ 'lp_loading_screen_display_in' ] : 'all',
				'once_per_session'			=> (
													!isset( $_POST[ 'once_per_session' ] ) ||
													!in_array($_POST[ 'once_per_session' ], array('always','site','page'))
												) ? 'always' : $_POST[ 'once_per_session' ],
				'lp_loading_screen_display_in_pages'   => sanitize_text_field($_POST[ 'lp_loading_screen_display_in_pages' ]),
				'lp_loading_screen_exclude_from_pages' => sanitize_text_field($_POST[ 'lp_loading_screen_exclude_from_pages' ]),
				'lp_loading_screen_exclude_from_post_types' => sanitize_text_field($_POST[ 'lp_loading_screen_exclude_from_post_types' ]),
				'lp_loading_screen_exclude_from_urls'  => $lp_loading_screen_exclude_from_urls,
				'lp_loading_screen_exclude_from_elementor_maintenance'  => (isset($_POST['lp_loading_screen_exclude_from_elementor_maintenance'])) ? true : false,
				'deepSearch'				=> (isset($_POST['lp_deactivateDeepSearch'])) ? false : true,
				'modifyDisplayRule'			=> (isset($_POST['lp_modifyDisplayRule'])) ? true : false,
                'loading_screen'            => sanitize_text_field($_POST['lp_loading_screen']),
                'displayPercent'            => (isset($_POST['lp_displayPercent'])) ? true : false,
                'pageEffect'                => sanitize_text_field($_POST['lp_pageEffect'])
            );

			if( isset( $_POST[ 'lp_ls' ] ) )
			{
				$loading_page_options[ 'lp_ls' ] = loading_page_sanitize_array($_POST[ 'lp_ls' ]);
			}

			update_option('loading_page_video_tutorial', (!empty($_POST['loading_page_video_tutorial']) && $_POST['loading_page_video_tutorial'] == 'collapsed') ? 'collapsed': 'expanded');

            if(update_option('loading_page_options', $loading_page_options)){
                print '<div class="updated">'.__('The Loading Page has been stored successfully', LOADING_PAGE_TD).'</div>';
            }else{
                print '<div class="error">'.__('The Loading Page settings could not be stored', LOADING_PAGE_TD).'</div>';
            }
        }

        $loading_page_options = get_option('loading_page_options');
		$loading_page_video_tutorial = get_option('loading_page_video_tutorial', 'expanded');
		$loading_page_video_tutorial_open_close = ($loading_page_video_tutorial == 'expanded') ? 'X' : '+';
		$loading_page_video_tutorial_status = ($loading_page_video_tutorial == 'collapsed') ? 'lp-video-collapsed' : '';

?>
		<style>.lp-video-container {overflow: hidden;position: relative;width:100%;} .lp-video-container::after {padding-top: 56.25%;display: block;content: '';} .lp-video-container iframe {position: absolute;top: 0;left: 0;width: 100%;height: 100%;} .lp-video-collapsed{display:none;}</style>
        <div class="wrap">
            <form method="post">
                <input type="hidden" name="loading_page_nonce" value="<?php print(wp_create_nonce(__FILE__)); ?>" />
				<h2><?php _e('Loading Page Settings', LOADING_PAGE_TD); ?></h2>
				<div class="postbox">
					<h3 class='hndle' style="padding:5px;"><span><?php _e('Video Tutorial', LOADING_PAGE_TD); ?></span><a href="javascript:void(0);" onclick="loading_page_collapse_expand_video_tutorial(this);" style="float:right;font-size:1.3em;text-decoration:none;"><?php print $loading_page_video_tutorial_open_close; ?></a></h3>
					<div class="inside lp-video-tutorial <?php echo $loading_page_video_tutorial_status; ?>" style="position:relative;">
						<input type="hidden" name="loading_page_video_tutorial" value="<?php echo esc_attr($loading_page_video_tutorial); ?>" />
						<div class="lp-video-container">
						<iframe title="<?php print esc_attr(__('Video tutorial', LOADING_PAGE_TD)); ?>" src="https://www.youtube.com/embed/5x_LtjoCFUY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>
					</div>
				</div>
                <div class="postbox">
                    <h3 class='hndle' style="padding:5px;"><span><?php _e('Loading Screen', LOADING_PAGE_TD); ?></span></h3>
                    <div class="inside">
                        <p  style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">If you want test the premium version of Loading Page go to the following links:<br/> <a href="https://demos.dwbooster.com/loading-page/wp-login.php" target="_blank">Administration area: Click to access the administration area demo</a><br/>
						<a href="https://demos.dwbooster.com/loading-page/" target="_blank">Public page: Click to access the Loading Page</a><br />
						<a href="https://wordpress.org/support/plugin/loading-page/#new-post" target="_blank">If you need additional help</a>
						</p>
                        <p><?php
                            print(
                                _e("Displays a loading screen until the webpage is ready, the screen shows the loading percent.",
                                LOADING_PAGE_TD)
                            );
                        ?></p>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Enable loading screen', LOADING_PAGE_TD); ?></th>
                                <td><input aria-label="<?php print esc_attr(__('Enable loading screen', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_enabled_loading_screen" <?php echo((!empty($loading_page_options['enabled_loading_screen'])) ? 'CHECKED' : '' ); ?> /></td>
                            </tr>
							<tr>
                                <th><?php _e('Display a close screen button', LOADING_PAGE_TD); ?></th>
                                <td><input aria-label="<?php print esc_attr(__('Display a close screen button', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_close_btn" <?php echo(( !isset( $loading_page_options['close_btn'] ) || $loading_page_options['close_btn']) ? 'CHECKED' : '' ); ?> /></td>
                            </tr>
							<tr>
                                <th><?php _e('Hide the loading screen in the window onload event', LOADING_PAGE_TD); ?></th>
                                <td><input aria-label="<?php print esc_attr(__('Hide the loading screen in the onload event', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_remove_in_on_load" <?php echo((!empty($loading_page_options['remove_in_on_load'])) ? 'CHECKED' : '' ); ?> /> <i><?php _e('Hides the loading screen in the onload event of window - or - as soon as possible',LOADING_PAGE_TD); ?></i></td>
                            </tr>
							<tr>
								<th><?php _e('Display the loading screen on', LOADING_PAGE_TD); ?></th>
								<td>
									<select aria-label="<?php print esc_attr(__('Screen size', LOADING_PAGE_TD)); ?>" name="lp_screen_size" style="float:left;">
										<option value="all" <?php
										if(
											isset($loading_page_options['screen_size']) &&
											$loading_page_options['screen_size'] == 'all'
										) print "SELECTED";
										?> ><?php _e( 'All Screens', LOADING_PAGE_TD ) ?></option>
										<option value="greater" <?php
										if(
											isset($loading_page_options['screen_size']) &&
											$loading_page_options['screen_size'] == 'greater'
										) print "SELECTED";
										?> ><?php _e( 'Greater Than', LOADING_PAGE_TD ) ?></option>
										<option value="lesser" <?php
										if(
											isset($loading_page_options['screen_size']) &&
											$loading_page_options['screen_size'] == 'lesser'
										) print "SELECTED";
										?> ><?php _e( 'Lesser Than', LOADING_PAGE_TD ) ?></option>
									</select>
									<div id="lp_width_container" style="float:left; padding-left:10px;<?php
										if( !isset($loading_page_options['screen_size']) || $loading_page_options['screen_size'] == 'all') echo 'display:none;';
										else echo 'display:block;';
									?>">
										<?php _e( 'Width', LOADING_PAGE_TD ); ?>:
										<input aria-label="<?php print esc_attr(__('Screen width', LOADING_PAGE_TD)); ?>" type="text" name="lp_screen_width" value="<?php
											if(isset( $loading_page_options['screen_width'] )) echo esc_attr($loading_page_options['screen_width']);
										?>" /> px
									</div>
									<script>
										jQuery('[name="lp_screen_size"]').change(function(){
											jQuery( '#lp_width_container' )[ ( this.value == 'all' ) ? 'hide' : 'show' ]();
										})
									</script>
								</td>
							</tr>
							<tr>
								<th><?php _e('Display the loading screen', LOADING_PAGE_TD); ?></th>
								<td>
									<?php
										if(
											!isset( $loading_page_options['once_per_session'] ) ||
											$loading_page_options['once_per_session'] === false
										)
										{
											$loading_page_options['once_per_session'] = 'always';
										}
										elseif( $loading_page_options['once_per_session'] === true )
										{
											$loading_page_options['once_per_session'] = 'site';
										}
									?>
									<input aria-label="<?php print esc_attr(__('Always', LOADING_PAGE_TD)); ?>" type="radio" value="always" name="once_per_session" <?php echo(($loading_page_options['once_per_session'] == 'always') ? 'CHECKED' : '' ); ?> /> <span><?php _e( 'always', LOADING_PAGE_TD ); ?></span><br />
									<input aria-label="<?php print esc_attr(__('Once per session', LOADING_PAGE_TD)); ?>" type="radio" value="site" name="once_per_session" <?php echo(($loading_page_options['once_per_session'] == 'site') ? 'CHECKED' : '' ); ?> /> <span><?php _e( 'once per session', LOADING_PAGE_TD ); ?></span><br />
									<input aria-label="<?php print esc_attr(__('Once per page', LOADING_PAGE_TD)); ?>" type="radio" value="page" name="once_per_session" <?php echo(($loading_page_options['once_per_session'] == 'page') ? 'CHECKED' : '' ); ?> /> <span><?php _e( 'once per page', LOADING_PAGE_TD ); ?></span>

								</td>
							</tr>
                            <tr>
                                <th><?php _e('Display loading screen in', LOADING_PAGE_TD); ?></th>
                                <td>
									<div><input aria-label="<?php print esc_attr(__('Homepage', LOADING_PAGE_TD)); ?>" type="radio" name="lp_loading_screen_display_in" value="home" <?php echo(( isset( $loading_page_options['lp_loading_screen_display_in'] ) && $loading_page_options['lp_loading_screen_display_in'] == 'home' ) ? 'CHECKED' : '' ); ?> /> homepage only</div>
									<div><input aria-label="<?php print esc_attr(__('All pages', LOADING_PAGE_TD)); ?>" type="radio" name="lp_loading_screen_display_in" value="all" <?php echo(( isset( $loading_page_options['lp_loading_screen_display_in'] ) && $loading_page_options['lp_loading_screen_display_in'] == 'all' ) ? 'CHECKED' : '' ); ?> /> all pages</div>
									<div><input aria-label="<?php print esc_attr(__('Specific pages', LOADING_PAGE_TD)); ?>" type="radio" name="lp_loading_screen_display_in" value="pages" <?php echo(( isset( $loading_page_options['lp_loading_screen_display_in'] ) && $loading_page_options['lp_loading_screen_display_in'] == 'pages' ) ? 'CHECKED' : '' ); ?> /> the specific pages
									<input aria-label="<?php print esc_attr(__('Pages/posts ids', LOADING_PAGE_TD)); ?>" type="text" name="lp_loading_screen_display_in_pages" value="<?php if( !empty( $loading_page_options['lp_loading_screen_display_in_pages'] ) ) print $loading_page_options['lp_loading_screen_display_in_pages']; ?>"> <i><?php _e('Type one, or more post/pages IDs, comma separated ","', LOADING_PAGE_TD); ?></i></div>

								</td>
                            </tr>
							<tr><td colspan="2"><hr /></td></tr>
							<tr>
								<th><?php _e( 'Exclude loading screen from', LOADING_PAGE_TD ); ?></th>
								<td><input aria-label="<?php print esc_attr(__('Pages/posts ids separated by comma', LOADING_PAGE_TD)); ?>" type="text" name="lp_loading_screen_exclude_from_pages" value="<?php if( !empty( $loading_page_options['lp_loading_screen_exclude_from_pages'] ) ) print esc_attr($loading_page_options['lp_loading_screen_exclude_from_pages']); ?>"> <i><?php _e('Type one, or more post/pages IDs, comma separated ","', LOADING_PAGE_TD); ?></i></td>
							</tr>
							<tr>
								<th><?php _e( 'Exclude loading screen from post types', LOADING_PAGE_TD ); ?></th>
								<td><input aria-label="<?php print esc_attr(__('Post types separated by comma', LOADING_PAGE_TD)); ?>" type="text" name="lp_loading_screen_exclude_from_post_types" value="<?php if( !empty( $loading_page_options['lp_loading_screen_exclude_from_post_types'] ) ) print esc_attr($loading_page_options['lp_loading_screen_exclude_from_post_types']); ?>"> <i><?php _e('Type one, or more post/pages types, comma separated ","', LOADING_PAGE_TD); ?></i></td>
							</tr>
							<tr>
								<th><?php _e( 'Exclude loading screen from pages with the URL', LOADING_PAGE_TD ); ?></th>
								<td><textarea aria-label="<?php print esc_attr(__('URLs of the pages to exclude', LOADING_PAGE_TD)); ?>" name="lp_loading_screen_exclude_from_urls" style="width:100%" rows="6"><?php
									print esc_textarea(
										(!empty($loading_page_options['lp_loading_screen_exclude_from_urls']))
										? implode("\n", $loading_page_options['lp_loading_screen_exclude_from_urls']) : ""
									);
								?></textarea>
								<i>Enter an URL per row (use the * symbol as wildcard)</i></td>
							</tr>
							<tr>
								<th colspan="2">
									<?php _e( 'Exclude the loading screen if Elementor Maintenance or Coming Soon modes are enabled', LOADING_PAGE_TD ); ?>
									<input aria-label="<?php print esc_attr(__('Disable the loading screen from Coming Soon or Maintenance modes', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_loading_screen_exclude_from_elementor_maintenance" <?php echo((!empty($loading_page_options['lp_loading_screen_exclude_from_elementor_maintenance'])) ? 'CHECKED' : '' ); ?> />
								</th>
							</tr>
							<tr><td colspan="2"><hr /></td></tr>
                            <tr>
                                <?php $loading_screens = loading_page_get_screen_list();?>
                                <th><?php _e('Select the loading screen', LOADING_PAGE_TD); ?></th>
                                <td>
                                    <select aria-label="<?php print esc_attr(__('Loading screen', LOADING_PAGE_TD)); ?>" name="lp_loading_screen">
                                        <?php
                                            foreach($loading_screens as $screen){
                                                print '<option value="'.esc_attr($screen['id']).'" '.((isset($loading_page_options['loading_screen']) && $loading_page_options['loading_screen'] == $screen['id']) ? 'SELECTED' : '').' title="'.((!empty($screen['tips'])) ? esc_attr($screen['tips']) : '' ).'" >'.$screen['name'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span style="color:#FF0000;">
										The free version of the plugin includes the "Bar Screen" and "Logo Screen", however, if they are not sufficient to your website, the commercial version of the plugin includes other loading screens: <a href="http://wordpress.dwbooster.com/content-tools/loading-page" target="_blank">CLICK HERE</a>
                                    </span>
                                </td>
                            </tr>
							<?php
							foreach( $loading_screens as $screen )
							{
								if( !empty( $screen[ 'adminsection' ] ) ) include_once $screen[ 'adminsection' ];
								if( !empty( $screen[ 'adminscript' ] ) ) print '<script src="'.$screen[ 'adminscript' ].'"></script>';
							}
							?>
                            <tr>
                                <th><?php _e('Select background color', LOADING_PAGE_TD); ?></th>
                                <td>
									<input aria-label="<?php print esc_attr(__('Background color', LOADING_PAGE_TD)); ?>" type="text" name="lp_backgroundColor" id="lp_backgroundColor" value="<?php if(isset($loading_page_options['backgroundColor'])) print(esc_attr($loading_page_options['backgroundColor'])); ?>" />
									<input aria-label="<?php print esc_attr(__('Apply transparency', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_backgroundTransparency" <?php print (!isset($loading_page_options['transparency']) || $loading_page_options['transparency']) ? 'CHECKED' : ''; ?> /><?php _e('Apply transparency', LOADING_PAGE_TD); ?>
									<div id="lp_backgroundColor_picker"></div>
								</td>
                            </tr>
                            <tr>
                                <th><?php _e('Select image as background', LOADING_PAGE_TD); ?></th>
                                <td>
                                    <input aria-label="<?php print esc_attr(__('Background image', LOADING_PAGE_TD)); ?>" type="text" name="lp_backgroundImage" id="lp_backgroundImage" value="<?php if(isset($loading_page_options['backgroundImage'])) print(esc_attr($loading_page_options['backgroundImage'])); ?>" />
                                    <input type="button" value="Browse" onclick="loading_page_selected_image('lp_backgroundImage');" />
                                    <select aria-label="<?php print esc_attr(__('Background position', LOADING_PAGE_TD)); ?>" id="lp_backgroundRepeat" name="lp_backgroundRepeat">
                                        <option value="repeat" <?php if( isset($loading_page_options[ 'backgroundImageRepeat' ]) && $loading_page_options[ 'backgroundImageRepeat' ] == 'repeat' ) echo "SELECTED"; ?> >Tile</option>
                                        <option value="no-repeat" <?php if( isset($loading_page_options[ 'backgroundImageRepeat' ]) && $loading_page_options[ 'backgroundImageRepeat' ] == 'no-repeat' ) echo "SELECTED"; ?> >Center</option>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Display image in fullscreen', LOADING_PAGE_TD); ?></th>
                                <td>
                                    <input aria-label="<?php print esc_attr(__('Fullscreen', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_fullscreen" id="lp_fullscreen" <?php echo (( isset( $loading_page_options[ 'fullscreen' ] ) && $loading_page_options[ 'fullscreen' ] )   ? 'CHECKED' : '' );?> />
                                    <i><?php _e('(The fullscreen attribute can fail in some browsers)', LOADING_PAGE_TD); ?></i>
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Select foreground color', LOADING_PAGE_TD); ?></th>
                                <td><input aria-label="<?php print esc_attr(__('Foreground color', LOADING_PAGE_TD)); ?>" type="text" name="lp_foregroundColor" id="lp_foregroundColor" value="<?php if(isset($loading_page_options['foregroundColor'])) print(esc_attr($loading_page_options['foregroundColor'])); ?>" /><div id="lp_foregroundColor_picker"></div></td>
                            </tr>
                            <tr>
                                <th><?php _e('Additional seconds', LOADING_PAGE_TD); ?></th>
                                <td>
									<input aria-label="<?php print esc_attr(__('Additional seconds', LOADING_PAGE_TD)); ?>" type="text" name="lp_additionalSeconds" id="lp_additionalSeconds" value="<?php if(isset($loading_page_options['additionalSeconds'])) print(esc_attr($loading_page_options['additionalSeconds'])); ?>" />
									<i><?php _e( 'Show the loading screen some few seconds after loading the page', LOADING_PAGE_TD ); ?></i>
								</td>
                            </tr>
                            <tr>
                                <th><?php _e('Include an ad, or your own block of code', LOADING_PAGE_TD); ?></th>
                                <td>
									<textarea aria-label="<?php print esc_attr(__('Ad or block of code', LOADING_PAGE_TD)); ?>" name="lp_codeBlock" id="lp_codeBlock" rows="6" style="width:80%;"><?php if(isset($loading_page_options['codeBlock'])) print(esc_textarea($loading_page_options['codeBlock'])); ?></textarea>
									<p><i><b><?php _e('Trick', LOADING_PAGE_TD);?></b>:
									<?php _e('As the block of code you can insert a pair of &lt;style&gt;&lt;/style&gt; tags to customize the appearance of loading screen. For example: for changing the font-family of the loading text:  <b>&lt;style&gt;.lp-screen-text{font-family:Georgia !important;}&lt;/style&gt;</b>', LOADING_PAGE_TD); ?></i></p>
								</td>
                            </tr>
                            <tr>
                                <th><?php _e('Apply the effect on page', LOADING_PAGE_TD); ?></th>
                                <td>
                                <select aria-label="<?php print esc_attr(__('Animation effect', LOADING_PAGE_TD)); ?>" name="lp_pageEffect">
                                <?php
                                    $pageEffects = array('none', 'rotateInLeft');

                                    foreach($pageEffects as $value){
                                        print '<option value="'.$value.'" '.((isset($loading_page_options['pageEffect']) && $loading_page_options['pageEffect'] == $value) ? 'SELECTED' : '').'>'.$value.'</option>';
                                    }
                                ?>

                                </select>
                                <div style="color:#FF0000;">The premium version of plugin add the following effects: collapseIn, risingFromBottom, expandIn, fadeIn, fallFromTop, rotateInLeft, rotateInRight, rotateInRightWithoutToKeyframe, slideInSkew, tumbleIn, whirlIn</div>
                                </td>
                            </tr>
                            <tr>
                                <th><?php _e('Display loading percent', LOADING_PAGE_TD); ?></th>
                                <td><input aria-label="<?php print esc_attr(__('Display loading percent', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_displayPercent" <?php echo((!empty($loading_page_options['displayPercent'])) ? 'CHECKED' : '' ); ?> /></td>
                            </tr>
                        </table>
						<div style="border: 1px solid #DADADA; padding:10px;">
							<h3><?php _e( 'Troubleshoot Area - Loading Screen', LOADING_PAGE_TD ); ?></h3>
							<table class="form-table">
								<tr>
									<th><?php _e( 'Disable the search in deep', LOADING_PAGE_TD ); ?></th>
									<td>
										<input aria-label="<?php print esc_attr(__('Disable search in deep', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_deactivateDeepSearch" <?php echo ((empty($loading_page_options['deepSearch'])) ? 'CHECKED' : ''); ?>/> <i><?php _e( 'If the loading screen stops in some percentage, tick the checkbox', LOADING_PAGE_TD ); ?></i>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'The pages\' background is visible', LOADING_PAGE_TD ); ?></th>
									<td>
										<input aria-label="<?php print esc_attr(__('Tick if page visible', LOADING_PAGE_TD)); ?>" type="checkbox" name="lp_modifyDisplayRule" <?php echo ((!empty($loading_page_options['modifyDisplayRule'])) ? 'CHECKED' : ''); ?>/> <i><?php _e( 'If website\'s background is visible before the loading screen, tick the checkbox', LOADING_PAGE_TD ); ?></i>
									</td>
								</tr>

							</table>
						</div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class='hndle' style="padding:5px;"><span><?php _e('Lazy Loading', LOADING_PAGE_TD); ?></span></h3>
                    <div class="inside">
                        <p><?php
                            print(
                                _e("To load only the images visible in the viewport to improve the loading rate of your website and reduce the bandwidth consumption.",
                                LOADING_PAGE_TD)
                            );
                        ?></p>
                        <p>
                            <span style="color:#FF0000;">
                                The lazy loading of images is available only in the commercial version of plugin <a href="http://wordpress.dwbooster.com/content-tools/loading-page" target="_blank">CLICK HERE</a>
                            </span>
                        </p>
                        <p><img alt="<?php print esc_attr(__('Lazy loading chart', LOADING_PAGE_TD)); ?>" src="<?php print(LOADING_PAGE_PLUGIN_URL.'/images/consumption_graph.png'); ?>" style="max-width:100%;" /></p>
                        <table class="form-table">
                            <tr>
                                <th><?php _e('Enable lazy loading', LOADING_PAGE_TD); ?></th>
                                <td><input aria-label="<?php print esc_attr(__('Enable lazy loading', LOADING_PAGE_TD)); ?>" type="checkbox" DISABLED /></td>
                            </tr>
                            <tr>
                                <th><?php _e('Select the image to load by default', LOADING_PAGE_TD); ?></th>
                                <td>
                                    <input aria-label="<?php print esc_attr(__('Image to load by default', LOADING_PAGE_TD)); ?>" type="text" DISABLED /><input type="button" value="Browse" DISABLED />
                                </td>
                            </tr>
							<tr>
								<th><?php _e( 'Exclude lazy loading from', LOADING_PAGE_TD ); ?></th>
								<td><input aria-label="<?php print esc_attr(__('Pages/posts ids separated by comma', LOADING_PAGE_TD)); ?>" type="text" DISABLED /> <i><?php _e('Type one, or more post/pages IDs, comma separated ","', LOADING_PAGE_TD); ?></i></td>
							</tr>
                        </table>
						<div style="border: 1px solid #DADADA; padding:10px;">
							<h3><?php _e( 'Troubleshoot Area - Lazy Loading', LOADING_PAGE_TD ); ?></h3>
							<table class="form-table">
								<tr>
									<th><?php _e( 'Exclude images whose tag includes the class or attribute', LOADING_PAGE_TD ); ?></th>
									<td>
										<input aria-label="<?php print esc_attr(__('Class or attributes names', LOADING_PAGE_TD)); ?>" type="text" style="width:100%;" disabled /><br />
										<p><i><?php _e( 'Don\'t apply the lazy loading to the images with the classes or attributes (comma separated ",")', LOADING_PAGE_TD ); ?></i></p>
									</td>
								</tr>
							</table>
						</div>
                    </div>
                </div>
                <div><input type="submit" value="Update Settings" class="button-primary" /></div>
            </form>
        </div>
<?php
    } // End loading_page_settings_page
}
?>