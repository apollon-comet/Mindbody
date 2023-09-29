<?php
/**
 * Add Dashboard pages/subpages for different settings
 *
 */


/**
 * Main Page
 * 
 * @since 0.2.0
 */

add_action('admin_menu', 'ct_dashboard_main_page');

function ct_dashboard_main_page(){

	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}

	$homePageView = add_menu_page( 	'Oxygen', // page <title>
					'Oxygen', // menu item name
					'read', // capability
					'ct_dashboard_page', // get param
					'ct_oxygen_home_page_view',
					'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIzODFweCIgaGVpZ2h0PSIzODVweCIgdmlld0JveD0iMCAwIDM4MSAzODUiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+ICAgICAgICA8dGl0bGU+VW50aXRsZWQgMzwvdGl0bGU+ICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPiAgICA8ZGVmcz4gICAgICAgIDxwb2x5Z29uIGlkPSJwYXRoLTEiIHBvaW50cz0iMC4wNiAzODQuOTQgMzgwLjgwNSAzODQuOTQgMzgwLjgwNSAwLjYyOCAwLjA2IDAuNjI4Ij48L3BvbHlnb24+ICAgIDwvZGVmcz4gICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+ICAgICAgICA8ZyBpZD0iT3h5Z2VuLUljb24tQ01ZSyI+ICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0id2hpdGUiPiAgICAgICAgICAgICAgICA8dXNlIHhsaW5rOmhyZWY9IiNwYXRoLTEiPjwvdXNlPiAgICAgICAgICAgIDwvbWFzaz4gICAgICAgICAgICA8ZyBpZD0iQ2xpcC0yIj48L2c+ICAgICAgICAgICAgPHBhdGggZD0iTTI5Ny41MDgsMzQ5Ljc0OCBDMjc1LjQ0MywzNDkuNzQ4IDI1Ny41NTYsMzMxLjg2IDI1Ny41NTYsMzA5Ljc5NiBDMjU3LjU1NiwyODcuNzMxIDI3NS40NDMsMjY5Ljg0NCAyOTcuNTA4LDI2OS44NDQgQzMxOS41NzMsMjY5Ljg0NCAzMzcuNDYsMjg3LjczMSAzMzcuNDYsMzA5Ljc5NiBDMzM3LjQ2LDMzMS44NiAzMTkuNTczLDM0OS43NDggMjk3LjUwOCwzNDkuNzQ4IEwyOTcuNTA4LDM0OS43NDggWiBNMjIyLjMwNCwzMDkuNzk2IEMyMjIuMzA0LDMxMi4wMzkgMjIyLjQ0NywzMTQuMjQ3IDIyMi42MzksMzE2LjQ0MSBDMjEyLjMzLDMxOS4wOTIgMjAxLjUyOCwzMjAuNTA1IDE5MC40MDMsMzIwLjUwNSBDMTE5LjAxLDMyMC41MDUgNjAuOTI5LDI2Mi40MjMgNjAuOTI5LDE5MS4wMzEgQzYwLjkyOSwxMTkuNjM4IDExOS4wMSw2MS41NTcgMTkwLjQwMyw2MS41NTcgQzI2MS43OTQsNjEuNTU3IDMxOS44NzcsMTE5LjYzOCAzMTkuODc3LDE5MS4wMzEgQzMxOS44NzcsMjA2LjgzMyAzMTcuMDIsMjIxLjk3OCAzMTEuODE1LDIzNS45OSBDMzA3LjE3OSwyMzUuMDk3IDMwMi40MDQsMjM0LjU5MiAyOTcuNTA4LDIzNC41OTIgQzI1NS45NzQsMjM0LjU5MiAyMjIuMzA0LDI2OC4yNjIgMjIyLjMwNCwzMDkuNzk2IEwyMjIuMzA0LDMwOS43OTYgWiBNMzgwLjgwNSwxOTEuMDMxIEMzODAuODA1LDg2LjA0MiAyOTUuMzkyLDAuNjI4IDE5MC40MDMsMC42MjggQzg1LjQxNCwwLjYyOCAwLDg2LjA0MiAwLDE5MS4wMzEgQzAsMjk2LjAyIDg1LjQxNCwzODEuNDMzIDE5MC40MDMsMzgxLjQzMyBDMjEyLjQ5OCwzODEuNDMzIDIzMy43MDgsMzc3LjYwOSAyNTMuNDU2LDM3MC42NTcgQzI2NS44NDUsMzc5LjY0MSAyODEuMDM0LDM4NSAyOTcuNTA4LDM4NSBDMzM5LjA0MiwzODUgMzcyLjcxMiwzNTEuMzMgMzcyLjcxMiwzMDkuNzk2IEMzNzIuNzEyLDI5Ni4wOTIgMzY4Ljk4OCwyODMuMjgzIDM2Mi41ODQsMjcyLjIxOSBDMzc0LjI1MSwyNDcuNTc1IDM4MC44MDUsMjIwLjA1OCAzODAuODA1LDE5MS4wMzEgTDM4MC44MDUsMTkxLjAzMSBaIiBpZD0iRmlsbC0xIiBmaWxsPSIjMDBCM0MxIiBtYXNrPSJ1cmwoI21hc2stMikiPjwvcGF0aD4gICAgICAgIDwvZz4gICAgPC9nPjwvc3ZnPg==' ); 

	add_action( 'load-' . $homePageView, 'ct_oxygen_admin_home_page_css' );

	add_submenu_page( 	'ct_dashboard_page', 
						'Home', 
						'Home', 
						'read', 
						'ct_dashboard_page');
}

function ct_oxygen_admin_home_page_css() {
	add_action( 'admin_enqueue_scripts', 'ct_oxygen_enqueue_admin_home_page_css' );
}

function ct_oxygen_enqueue_admin_home_page_css() {
	wp_enqueue_style("oxy-admin-screen-home", CT_FW_URI."/admin/oxy-admin-screen-home.css");
}

function ct_oxygen_home_page_view() {
	if ( !oxygen_vsb_current_user_can_access() )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    include(plugin_dir_path(__FILE__)."oxy-admin-screen-home.php");
    
}

add_action('admin_menu', 'ct_install_wiz_page');

function ct_install_wiz_page() {

	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	$ctInstallWizCallback = add_submenu_page( 	'', //ct_dashboard_page to show it in the sub-menu
						'Install Wizard', 
						'Install Wizard', 
						'read', 
						'ct_install_wiz', 
						'ct_install_wiz_callback' );

	add_action( 'load-' . $ctInstallWizCallback, 'ct_oxygen_install_wiz_page_css' );


}

function ct_oxygen_install_wiz_page_css() {
	add_action( 'admin_enqueue_scripts', 'ct_oxygen_enqueue_install_wiz_page_css' );
}

function ct_oxygen_enqueue_install_wiz_page_css() {
	wp_enqueue_style("oxy-admin-screen-install-wiz", CT_FW_URI."/admin/oxy-admin-screen-install-wiz.css");
}

function ct_install_wiz_callback() {
	if ( !oxygen_vsb_current_user_can_access() )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    include(plugin_dir_path(__FILE__)."oxy-admin-screen-install-wiz.php");
    
}

add_action('admin_menu', 'ct_templates_page');

function ct_templates_page() {

	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	if(oxygen_vsb_get_user_edit_mode() == "edit_only") {
		
		add_menu_page( 	'Oxygen', // page <title>
					'Templates', // menu item name
					'read', // capability
					'edit.php?post_type=ct_template', // get param
					'',
					'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIzODFweCIgaGVpZ2h0PSIzODVweCIgdmlld0JveD0iMCAwIDM4MSAzODUiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+ICAgICAgICA8dGl0bGU+VW50aXRsZWQgMzwvdGl0bGU+ICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPiAgICA8ZGVmcz4gICAgICAgIDxwb2x5Z29uIGlkPSJwYXRoLTEiIHBvaW50cz0iMC4wNiAzODQuOTQgMzgwLjgwNSAzODQuOTQgMzgwLjgwNSAwLjYyOCAwLjA2IDAuNjI4Ij48L3BvbHlnb24+ICAgIDwvZGVmcz4gICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+ICAgICAgICA8ZyBpZD0iT3h5Z2VuLUljb24tQ01ZSyI+ICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0id2hpdGUiPiAgICAgICAgICAgICAgICA8dXNlIHhsaW5rOmhyZWY9IiNwYXRoLTEiPjwvdXNlPiAgICAgICAgICAgIDwvbWFzaz4gICAgICAgICAgICA8ZyBpZD0iQ2xpcC0yIj48L2c+ICAgICAgICAgICAgPHBhdGggZD0iTTI5Ny41MDgsMzQ5Ljc0OCBDMjc1LjQ0MywzNDkuNzQ4IDI1Ny41NTYsMzMxLjg2IDI1Ny41NTYsMzA5Ljc5NiBDMjU3LjU1NiwyODcuNzMxIDI3NS40NDMsMjY5Ljg0NCAyOTcuNTA4LDI2OS44NDQgQzMxOS41NzMsMjY5Ljg0NCAzMzcuNDYsMjg3LjczMSAzMzcuNDYsMzA5Ljc5NiBDMzM3LjQ2LDMzMS44NiAzMTkuNTczLDM0OS43NDggMjk3LjUwOCwzNDkuNzQ4IEwyOTcuNTA4LDM0OS43NDggWiBNMjIyLjMwNCwzMDkuNzk2IEMyMjIuMzA0LDMxMi4wMzkgMjIyLjQ0NywzMTQuMjQ3IDIyMi42MzksMzE2LjQ0MSBDMjEyLjMzLDMxOS4wOTIgMjAxLjUyOCwzMjAuNTA1IDE5MC40MDMsMzIwLjUwNSBDMTE5LjAxLDMyMC41MDUgNjAuOTI5LDI2Mi40MjMgNjAuOTI5LDE5MS4wMzEgQzYwLjkyOSwxMTkuNjM4IDExOS4wMSw2MS41NTcgMTkwLjQwMyw2MS41NTcgQzI2MS43OTQsNjEuNTU3IDMxOS44NzcsMTE5LjYzOCAzMTkuODc3LDE5MS4wMzEgQzMxOS44NzcsMjA2LjgzMyAzMTcuMDIsMjIxLjk3OCAzMTEuODE1LDIzNS45OSBDMzA3LjE3OSwyMzUuMDk3IDMwMi40MDQsMjM0LjU5MiAyOTcuNTA4LDIzNC41OTIgQzI1NS45NzQsMjM0LjU5MiAyMjIuMzA0LDI2OC4yNjIgMjIyLjMwNCwzMDkuNzk2IEwyMjIuMzA0LDMwOS43OTYgWiBNMzgwLjgwNSwxOTEuMDMxIEMzODAuODA1LDg2LjA0MiAyOTUuMzkyLDAuNjI4IDE5MC40MDMsMC42MjggQzg1LjQxNCwwLjYyOCAwLDg2LjA0MiAwLDE5MS4wMzEgQzAsMjk2LjAyIDg1LjQxNCwzODEuNDMzIDE5MC40MDMsMzgxLjQzMyBDMjEyLjQ5OCwzODEuNDMzIDIzMy43MDgsMzc3LjYwOSAyNTMuNDU2LDM3MC42NTcgQzI2NS44NDUsMzc5LjY0MSAyODEuMDM0LDM4NSAyOTcuNTA4LDM4NSBDMzM5LjA0MiwzODUgMzcyLjcxMiwzNTEuMzMgMzcyLjcxMiwzMDkuNzk2IEMzNzIuNzEyLDI5Ni4wOTIgMzY4Ljk4OCwyODMuMjgzIDM2Mi41ODQsMjcyLjIxOSBDMzc0LjI1MSwyNDcuNTc1IDM4MC44MDUsMjIwLjA1OCAzODAuODA1LDE5MS4wMzEgTDM4MC44MDUsMTkxLjAzMSBaIiBpZD0iRmlsbC0xIiBmaWxsPSIjMDBCM0MxIiBtYXNrPSJ1cmwoI21hc2stMikiPjwvcGF0aD4gICAgICAgIDwvZz4gICAgPC9nPjwvc3ZnPg==' ); 
		
		return;
	}

	add_submenu_page( 	'ct_dashboard_page', 
						'Templates', 
						'Templates', 
						'read', 
						'edit.php?post_type=ct_template');

}

add_action( 'admin_enqueue_scripts', 'ct_templates_admin_scripts' );

function ct_templates_admin_scripts($hook) {

	if (!oxygen_vsb_current_user_can_full_access()) {
		return;
	}

	global $post;

	if(!is_object($post) || !property_exists($post, 'post_type')) {
		return;
	}

    if ( $hook == 'post.php' || $hook == 'edit.php' ) {
        if ( 'ct_template' === $post->post_type ) {
        	wp_register_script('ct_template_edit_add', CT_FW_URI.'/admin/ct_template_edit_add.js');
        	wp_localize_script( 'ct_template_edit_add', 'ct_template_add_reusable_link', add_query_arg(array('post_type'=>'ct_template', 'is_reusable'=>'true'),admin_url('post-new.php')) ) ;
            wp_enqueue_script(  'ct_template_edit_add' );
        }
    }
	
}


/**
 * Export/Import
 * 
 * @since 0.2.1
 */

add_action('admin_menu', 'ct_export_import_page', 11);

function ct_export_import_page() {

	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}
	
	add_submenu_page( 	'ct_dashboard_page', 
						'Export & Import', 
						'Export & Import', 
						'read', 
						'ct_export_import', 
						'ct_export_import_callback' );
}

add_action('admin_menu', 'ct_admin_settings', 12);


function oxygen_vsb_process_signature_validation_toggle($val) {
	if(get_option('oxygen_vsb_enable_signature_validation') !== 'true' && $val === 'true') {
		set_transient('oxygen-vsb-enabled-shortcode-signing', true);
	}

	return $val;
}

/**
 * Settings page
 *
 * @since 2.0
 */ 

function oxygen_vsb_register_settings() {
   add_option( 'oxygen_vsb_history_limit', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_history_limit', array(
       'type' => "number",
       'sanitize_callback' => function ($limit) {
           if ($limit && $limit < 50) {
               $limit = 50;
           }
           return $limit;
       },
       'default' => false
   ));
   
   add_option( 'oxygen_vsb_preview_dropdown_limit', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_preview_dropdown_limit' );

   add_option( 'oxygen_vsb_preview_dropdown_exclude_non_public', "true" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_preview_dropdown_exclude_non_public' );
   
   add_option( 'oxygen_vsb_enable_selector_detector', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_enable_selector_detector' );


   add_option( 'oxygen_vsb_enable_default_designsets', 'true' );
   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_enable_default_designsets' );

   add_option( 'oxygen_vsb_enable_3rdp_designsets', false );
   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_enable_3rdp_designsets' ); 

   add_option( 'oxygen_vsb_enable_connection', false );
   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_enable_connection' );    

   add_option( 'oxygen_vsb_google_maps_api_key', "" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_google_maps_api_key' );

   // added with "register_activation_hook"
   register_setting( 'oxygen_vsb_options_group_cache', 'oxygen_vsb_universal_css_cache' );

   add_option( 'oxygen_vsb_show_all_acf_fields', 'true' );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_show_all_acf_fields' );

   add_option( 'oxygen_vsb_enable_google_fonts_cache', "true" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_enable_google_fonts_cache' );

   add_option( 'oxygen_vsb_enable_ie_layout_improvements', "true" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_enable_ie_layout_improvements' );

   add_option( 'oxygen_vsb_enable_signature_validation', false );
   register_setting( 'oxygen_vsb_options_group_security', 'oxygen_vsb_enable_signature_validation', 'oxygen_vsb_process_signature_validation_toggle' );

   add_option( 'oxygen_vsb_enable_signature_frontend_errors', 'false' );
   register_setting( 'oxygen_vsb_options_group_security', 'oxygen_vsb_enable_signature_frontend_errors' );

   add_option( 'oxygen_vsb_disable_emojis', "false" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_emojis' );

   add_option( 'oxygen_vsb_disable_jquery_migrate', "false" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_jquery_migrate' );

   add_option( 'oxygen_vsb_disable_embeds', "false" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_embeds' );

   add_option( 'oxygen_vsb_disable_google_fonts', "" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_google_fonts' );

   add_option( 'oxygen_vsb_use_css_for_google_fonts', "" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_use_css_for_google_fonts' );

   add_option( 'oxygen_vsb_block_category_label', "" );
   register_setting( 'oxygen_vsb_options_group_gutenberg', 'oxygen_vsb_block_category_label' );

   add_option( 'oxygen_vsb_full_page_block_category_label', "" );
   register_setting( 'oxygen_vsb_options_group_gutenberg', 'oxygen_vsb_full_page_block_category_label' );

   add_option( 'oxygen_vsb_options_users_access_list', array() );
   add_option( 'oxygen_vsb_options_users_access_enable_elements', array() );
   add_option( 'oxygen_vsb_options_users_access_advanced_tab', array() );
   add_option( 'oxygen_vsb_options_users_access_drag_n_drop', array() );
   add_option( 'oxygen_vsb_options_users_access_enabled_elements', array() );
   add_option( 'oxygen_vsb_options_users_access_disable_classes', array() );
   add_option( 'oxygen_vsb_options_users_access_disable_ids', array() );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_list' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_enable_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_advanced_tab' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_drag_n_drop' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_enabled_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_reusable_parts' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_design_library' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_disable_classes' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_disable_ids' );

   add_option( 'oxygen_vsb_options_role_access_enable_elements', array() );
   add_option( 'oxygen_vsb_options_role_access_advanced_tab', array() );
   add_option( 'oxygen_vsb_options_role_access_drag_n_drop', array() );
   add_option( 'oxygen_vsb_options_role_access_enabled_elements', array() );
   add_option( 'oxygen_vsb_options_role_access_disable_classes', array() );
   add_option( 'oxygen_vsb_options_role_access_disable_ids', array() );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_enable_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_advanced_tab' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_drag_n_drop' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_enabled_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_reusable_parts' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_design_library' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_disable_classes' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_disable_ids' );

   // Access related settings
   if(!defined('CT_FREE')) {
	    add_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
		$roles = get_editable_roles();
		remove_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
		foreach($roles as $role => $item) {
			add_option( "oxygen_vsb_access_role_$role", false);
	   		register_setting( 'oxygen_vsb_options_group_client_control', "oxygen_vsb_access_role_$role");
		}
	}

	// related to post type settings

	global $ct_ignore_post_types;
	$postTypes = get_post_types();
	
	if(is_array($ct_ignore_post_types) && is_array($postTypes)) {
		$postTypes = array_diff($postTypes, $ct_ignore_post_types);
	}
	
	foreach($postTypes as $key => $item) {
		add_option( "oxygen_vsb_ignore_post_type_$key", false);
   		register_setting( 'oxygen_vsb_options_group_client_control', "oxygen_vsb_ignore_post_type_$key");
	}

}
add_action( 'admin_init', 'oxygen_vsb_register_settings' );

function oxygen_vsb_process_source_site() {
	
	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	$oxygen_vsb_source_sites = get_option('oxygen_vsb_source_sites');


	if(	isset($_GET['page']) && $_GET['page'] == 'oxygen_vsb_settings' &&
		isset($_GET['tab']) && $_GET['tab'] == 'library_manager' &&
		isset($_GET['action']) && $_GET['action'] == 'add_source_site') {

		if(!isset($_REQUEST['add_3rdp_designset']) || !wp_verify_nonce($_REQUEST['add_3rdp_designset'])) {
			return;
		}

		$source_key = isset($_REQUEST['oxygen_vsb_source_key'])?$_REQUEST['oxygen_vsb_source_key']:false;
		$source_key = base64_decode($source_key);
		$exploded = explode("\n", $source_key);
		$valid = true;
		$source_site_label = isset($exploded[1])?$exploded[1]:false;
		$source_site_url = isset($exploded[0])?$exploded[0]:false;
		$source_site_access = isset($exploded[2])?$exploded[2]:false;

		$valid = $valid && $source_site_label && $source_site_url;

		if(!$valid) {
			set_transient('oxygen-vsb-admin-error-transient', 'invalid Design set key');
		}

		if($valid && sizeof($oxygen_vsb_source_sites) > 0) {
			// check if source site label or url already exists.
			if(isset($oxygen_vsb_source_sites[sanitize_title($source_site_label)])) {
				// TODO: add some notice here
				$valid = false;
				set_transient('oxygen-vsb-admin-error-transient', 'Design set with the same title already exists');
			}

			// check if sourcesite url already exists
			if(array_search($source_site_url, $oxygen_vsb_source_sites)) {
				// TODO: add some notice here
				$valid = false;
				set_transient('oxygen-vsb-admin-error-transient', 'Design set with the same url already exists');
			}
		}

		if($valid) {

			// attempt to connect to the source site and check if the access is valid

			$url = $source_site_url.'/wp-json/oxygen-vsb-connection/v1/addrequest/';


			$args = array(
			  'headers' => array(
			    'oxygenclientversion' => '3.7rc1',
			    'auth' => md5($source_site_access)
			  ),
			  'timeout' => 15,
			);

			$result = wp_remote_request($url, $args);

			$status = wp_remote_retrieve_response_code($result);
			
			if ( is_wp_error( $result ) ) {
			    set_transient('oxygen-vsb-admin-error-transient', $result->get_error_message());
			} 
			elseif($status !== 200) {

				set_transient('oxygen-vsb-admin-error-transient', wp_remote_retrieve_response_message($result));
				
			} 
			else {

				$result = json_decode($result['body'], true);
			

				if(is_array($result) && isset($result['access']) && intval($result['access']) === 1) {
					$oxygen_vsb_source_sites[sanitize_title($source_site_label)] = array('label' => sanitize_text_field($source_site_label), 'url' => sanitize_url($source_site_url), 'accesskey' => ($source_site_access === false ? '' : sanitize_text_field($source_site_access)));
					
					update_option('oxygen_vsb_source_sites', $oxygen_vsb_source_sites);

				}
				else {
					// put some notice;
					set_transient('oxygen-vsb-admin-error-transient', 'Access to the design set is denied');
				}
			}
		}

		wp_redirect(add_query_arg(array('page' => 'oxygen_vsb_settings', 'tab' => 'library_manager'), get_admin_url().'admin.php'));
		exit();
	}

	if(	isset($_GET['page']) && $_GET['page'] == 'oxygen_vsb_settings' &&
		isset($_GET['tab']) && $_GET['tab'] == 'library_manager' &&
		isset($_GET['delete']) && isset($oxygen_vsb_source_sites[$_GET['delete']])) {

		if(isset($_GET['delete_3rdp_designset']) && wp_verify_nonce($_GET['delete_3rdp_designset'])) {

			unset($oxygen_vsb_source_sites[$_GET['delete']]);

			update_option('oxygen_vsb_source_sites', $oxygen_vsb_source_sites);

			wp_redirect(add_query_arg(array('page' => 'oxygen_vsb_settings', 'tab' => 'library_manager'), get_admin_url().'admin.php'));
			exit();
		}
	}
}


add_action( 'admin_init', 'oxygen_vsb_process_source_site');

function oxygen_vsb_remove_admin_role($all_roles) {
	
	if(isset($all_roles['administrator'])) {
		unset($all_roles['administrator']);
	}

	return $all_roles;
}

function oxygen_vsb_options_page() {

	$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : false;
?>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
	    <a href="?page=oxygen_vsb_settings&tab=general" class="nav-tab<?php echo ($tab === false || $tab == 'general') ? ' nav-tab-active':'';?>">General</a>
	    <a href="?page=oxygen_vsb_settings&tab=client_control" class="nav-tab<?php echo $tab == 'client_control'?' nav-tab-active':'';?>">Client Control</a>
	    <a href="?page=oxygen_vsb_settings&tab=security_manager" class="nav-tab<?php echo $tab == 'security_manager'?' nav-tab-active':'';?>">Security</a>
	    <a href="?page=oxygen_vsb_settings&tab=svg_manager" class="nav-tab<?php echo $tab == 'svg_manager'?' nav-tab-active':'';?>">SVG Sets</a>
	    <a href="?page=oxygen_vsb_settings&tab=typekit_manager" class="nav-tab<?php echo $tab == 'typekit_manager'?' nav-tab-active':'';?>">Typekit</a>
	   	<?php
	    	if(!defined('CT_FREE')) {
	    ?>
	    <a href="?page=oxygen_vsb_settings&tab=license_manager" class="nav-tab<?php echo $tab == 'license_manager'?' nav-tab-active':'';?>">License</a>
	    <?php
	    	}
	    ?>
	    <a href="?page=oxygen_vsb_settings&tab=cache" class="nav-tab<?php echo $tab == 'cache'?' nav-tab-active':'';?>">CSS Cache</a>
        <a href="?page=oxygen_vsb_settings&tab=bloat" class="nav-tab<?php echo $tab == 'bloat'?' nav-tab-active':'';?>">Bloat Eliminator</a>

        <a href="?page=oxygen_vsb_settings&tab=library_manager" class="nav-tab<?php echo $tab == 'library_manager'?' nav-tab-active':'';?>">Library</a>

        <?php if(class_exists('Oxygen_Gutenberg')): ?>
            <a href="?page=oxygen_vsb_settings&tab=gutenberg" class="nav-tab<?php echo $tab == 'gutenberg'?' nav-tab-active':'';?>">Gutenberg</a>
        <?php endif;?>
	</h2>
	<?php

		switch($tab) {
			case false:
			case 'general':
				oxygen_vsb_options_general_page();
			break;

			case 'library_manager':
				oxygen_vsb_options_library_manager();
			break;

			case 'client_control':
				oxygen_vsb_options_client_control_page();
			break;

			case 'security_manager':
				oxygen_vsb_options_security_manager_page();
			break;

			case 'svg_manager':
				ct_svg_sets_callback();
			break;

			case 'typekit_manager':
				global $oxygenTypekitInstance;
				$oxygenTypekitInstance->typekit_page_callback();
			break;

			case 'license_manager':
				ct_license_page_callback();
			break;

			case 'cache':
				ct_cache_page_callback();
			break;
			case 'bloat':
				oxygen_vsb_options_bloat_eliminator_page();
			break;
			case 'gutenberg':
				oxygen_vsb_options_gutenberg();
			break;

		}

	?>

	  
</div>

<?php

}

function add_3rdp_designset_callback() {
	?>
	<h2>Add Source Sites</h2>
	<form method="post" action="?page=oxygen_vsb_settings&tab=library_manager&action=add_source_site">
		<div>
			<?php wp_nonce_field(-1, 'add_3rdp_designset');?>
			<label for="oxygen_vsb_source_key">Site Key</label>
			<input type="text" value="" name="oxygen_vsb_source_key" id="oxygen_vsb_source_key" />
		</div>
		<?php submit_button('Add Source Site'); ?>
	</form>
	<?php
}

function oxygen_vsb_options_library_manager() {
	?>
	<h2>Library</h2>
		
	<form method="post" action="options.php">
	<?php settings_fields( 'oxygen_vsb_options_group_library' ); ?>
    <?php do_settings_sections( 'oxygen_vsb_options_group_library' ); ?>
      	<div>
      		<input type="checkbox" id="oxygen_vsb_enable_default_designsets" name="oxygen_vsb_enable_default_designsets" value="true" <?php checked(get_option('oxygen_vsb_enable_default_designsets'), "true"); ?>>
      		<label for="oxygen_vsb_enable_default_designsets"><?php _e("Enable Default Design Sets","oxygen"); ?></label>
      	</div>
      	<?php
      		$oxygen_vsb_enable_3rdp_designsets = get_option('oxygen_vsb_enable_3rdp_designsets');
      	?>
      	<div>
      		<input type="checkbox" id="oxygen_vsb_enable_3rdp_designsets" name="oxygen_vsb_enable_3rdp_designsets" value="true" <?php checked($oxygen_vsb_enable_3rdp_designsets, "true"); ?>>
      		<label for="oxygen_vsb_enable_3rdp_designsets"><?php _e("Enable 3rd Party Design Sets","oxygen"); ?></label>
      		

  		<?php 
		if($oxygen_vsb_enable_3rdp_designsets == 'true') {
			$oxygen_vsb_source_sites = get_option('oxygen_vsb_source_sites');

	  		?>
		  	<div id="oxygen_vsb_3rdp_designsets_container">
		  		<ul>
					<?php
					if(is_array($oxygen_vsb_source_sites))
						foreach($oxygen_vsb_source_sites as $key=>$item) {
							if(isset($item['system'])) {
								continue;
							}
						?>
							<li><?php echo sanitize_text_field($item['label']);?> <a href="<?php 
								echo wp_nonce_url(
									add_query_arg(
										array(
											'page'	=>	'oxygen_vsb_settings',
											'tab'	=>	'library_manager',
											'delete'=>	sanitize_text_field($key)
										),
										get_admin_url().'admin.php'
									), 
									-1, 
									'delete_3rdp_designset'
								);

								?>">Remove</a>
							</li>
						<?php		
						}
					?>
				</ul>
				<a href="<?php echo add_query_arg('page', 'add_3rdp_designset', get_admin_url().'admin.php');?>">+ Add Design Set</a>
		  	</div>
		<?php
		}
		?>
      	</div>
      	<div>
      		<input type="checkbox" id="oxygen_vsb_enable_connection" name="oxygen_vsb_enable_connection" value="true" <?php checked(get_option('oxygen_vsb_enable_connection'), "true"); ?>><label for="oxygen_vsb_enable_connection"> <?php _e("Make this WordPress Install a Design Set","oxygen"); ?></label>
      		<div id="oxygen_vsb_connection_panel">
      			<?php do_action('oxygen_vsb_connection_panel'); ?>
      		</div>
      	</div>
			

			<?php submit_button('Update'); ?>
	</form>


	<?php 
	
}

function oxygen_vsb_options_general_page() {
	?>
	<h2>Oxygen Settings</h2>
		
	  <form method="post" action="options.php">
	  <?php settings_fields( 'oxygen_vsb_options_group' ); ?>
      <?php do_settings_sections( 'oxygen_vsb_options_group' ); ?>
		  <table>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_history_limit">History Limit (min: 50)</label></th>
				  <td><input type="number" id="oxygen_vsb_history_limit" name="oxygen_vsb_history_limit" min="50" value="<?php echo esc_attr(get_option('oxygen_vsb_history_limit')); ?>"></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_preview_dropdown_limit">Preview Dropdown Limit</label></th>
				  <td><input type="number" id="oxygen_vsb_preview_dropdown_limit" name="oxygen_vsb_preview_dropdown_limit" value="<?php echo esc_attr(get_option('oxygen_vsb_preview_dropdown_limit')); ?>"></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_preview_dropdown_exclude_non_public"><?php _e("Exclude Non-Public Post Types From Preview","oxygen"); ?></label></th>
				  <td><input type="checkbox" id="oxygen_vsb_preview_dropdown_exclude_non_public" name="oxygen_vsb_preview_dropdown_exclude_non_public" value="true" <?php checked(get_option('oxygen_vsb_preview_dropdown_exclude_non_public'), "true"); ?>></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_enable_selector_detector"><?php _e("Enable Selector Detector","oxygen"); ?></label></th>
				  <td><input type="checkbox" id="oxygen_vsb_enable_selector_detector" name="oxygen_vsb_enable_selector_detector" value="true" <?php checked(get_option('oxygen_vsb_enable_selector_detector'), "true"); ?>></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_google_maps_api_key"><?php _e("Google Maps API key","oxygen"); ?></label></th>
				  <td><input type="text" id="oxygen_vsb_google_maps_api_key" name="oxygen_vsb_google_maps_api_key" value="<?php echo esc_attr(get_option('oxygen_vsb_google_maps_api_key')); ?>"></td>
			  </tr>
              <tr valign="top">
                  <th scope="row"><label for="oxygen_vsb_show_all_acf_fields"><?php _e("Show all ACF fields in the Dynamic Data Dialog","oxygen"); ?></label></th>
                  <td><input type="checkbox" id="oxygen_vsb_show_all_acf_fields" name="oxygen_vsb_show_all_acf_fields" value="true" <?php checked(get_option('oxygen_vsb_show_all_acf_fields'), "true"); ?>></td>
              </tr>
              <tr valign="top">
                  <th scope="row"><label for="oxygen_vsb_enable_google_fonts_cache"><?php _e("Cache list of Google Fonts","oxygen"); ?></label></th>
                  <td><input type="checkbox" id="oxygen_vsb_enable_google_fonts_cache" name="oxygen_vsb_enable_google_fonts_cache" value="true" <?php checked(get_option('oxygen_vsb_enable_google_fonts_cache'), "true"); ?>></td>
              </tr>
               <tr valign="top">
                  <th scope="row"><label for="oxygen_vsb_enable_ie_layout_improvements"><?php _e("Enable Layout Improvements for IE 10+","oxygen"); ?></label></th>
                  <td><input type="checkbox" id="oxygen_vsb_enable_ie_layout_improvements" name="oxygen_vsb_enable_ie_layout_improvements" value="true" <?php checked(get_option('oxygen_vsb_enable_ie_layout_improvements'), "true"); ?>></td>
              </tr>
		  </table>

		  <?php submit_button(); ?>
	  </form>

	<?php
}

function oxygen_vsb_options_gutenberg() {
    ?>
    <h2>Gutenberg</h2>

    <form method="post" action="options.php">
		<?php settings_fields( 'oxygen_vsb_options_group_gutenberg' ); ?>
		<?php do_settings_sections( 'oxygen_vsb_options_group_gutenberg' ); ?>
        <table>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_block_category_label">Label For Block Category in Gutenberg</label></th>
                <td><input type="text" id="oxygen_vsb_block_category_label" name="oxygen_vsb_block_category_label" value="<?php echo esc_attr(get_option('oxygen_vsb_block_category_label')); ?>" placeholder="Oxygen Blocks"></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_full_page_block_category_label">Label For Full Page Block Category in Gutenberg</label></th>
                <td><input type="text" id="oxygen_vsb_full_page_block_category_label" name="oxygen_vsb_full_page_block_category_label" value="<?php echo esc_attr(get_option('oxygen_vsb_full_page_block_category_label')); ?>" placeholder="Oxygen Full Page Blocks"></td>
            </tr>
        </table>

		<?php submit_button(); ?>
    </form>
    <?php
}

function oxygen_vsb_options_bloat_eliminator_page() {
	?>
    <h2>Bloat Eliminator</h2>

    <form method="post" action="options.php">
		<?php settings_fields( 'oxygen_vsb_options_group_bloat_eliminator' ); ?>
		<?php do_settings_sections( 'oxygen_vsb_options_group_bloat_eliminator' ); ?>
        <table>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_disable_emojis"><?php _e("Disable WP Emojis","oxygen"); ?></label></th>
                <td><input type="checkbox" id="oxygen_vsb_disable_emojis" name="oxygen_vsb_disable_emojis" value="true" <?php checked(get_option('oxygen_vsb_disable_emojis'), "true"); ?>></td>
                <td><label for="oxygen_vsb_disable_emojis"><?php _e("Disables built-in WordPress JavaScript for rendering Emojis."); ?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_disable_jquery_migrate"><?php _e("Disable jQuery Migrate","oxygen"); ?></label></th>
                <td><input type="checkbox" id="oxygen_vsb_disable_jquery_migrate" name="oxygen_vsb_disable_jquery_migrate" value="true" <?php checked(get_option('oxygen_vsb_disable_jquery_migrate'), "true"); ?>></td>
                <td><label for="oxygen_vsb_disable_jquery_migrate"><?php _e("Disables the ability to run deprecated jQuery code on the current jQuery version."); ?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_disable_embeds"><?php _e("Disable Embeds","oxygen"); ?></label></th>
                <td><input type="checkbox" id="oxygen_vsb_disable_embeds" name="oxygen_vsb_disable_embeds" value="true" <?php checked(get_option('oxygen_vsb_disable_embeds'), "true"); ?>></td>
                <td><label for="oxygen_vsb_disable_embeds"><?php _e("Disables the automatic embedding of some content (YouTube videos, Tweets, etc.,) when pasting the URL into your blog posts."); ?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_disable_google_fonts"><?php _e("Disable Google Fonts","oxygen"); ?></label></th>
                <td><input type="checkbox" id="oxygen_vsb_disable_google_fonts" name="oxygen_vsb_disable_google_fonts" value="true" <?php checked(get_option('oxygen_vsb_disable_google_fonts'), "true"); ?>></td>
                <td><label for="oxygen_vsb_disable_google_fonts"><?php _e("Disables Google Fonts for your entire site."); ?></label></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_use_css_for_google_fonts"><?php _e("Disable Webfont.js","oxygen"); ?></label></th>
                <td><input type="checkbox" id="oxygen_vsb_use_css_for_google_fonts" name="oxygen_vsb_use_css_for_google_fonts" value="true" <?php checked(get_option('oxygen_vsb_use_css_for_google_fonts'), "true"); ?>></td>
                <td><label for="oxygen_vsb_use_css_for_google_fonts"><?php _e("Use CSS for Google Fonts instead of Webfont.js"); ?></label></td>
            </tr>
        </table>

		<?php submit_button(); ?>
    </form>

	<?php
}

function oxygen_vsb_options_client_control_page() {

	$users_access_list = get_option("oxygen_vsb_options_users_access_list", array());
	if (!is_array($users_access_list)) $users_access_list = array();
	$users_access_enable_elements = get_option("oxygen_vsb_options_users_access_enable_elements", array());
	$users_access_advanced_tab = get_option("oxygen_vsb_options_users_access_advanced_tab", array());
	$users_access_drag_n_drop = get_option("oxygen_vsb_options_users_access_drag_n_drop", array());
	$users_access_enabled_elements = get_option("oxygen_vsb_options_users_access_enabled_elements", array());
	$users_access_reusable_parts = get_option("oxygen_vsb_options_users_access_reusable_parts", array());
	$users_access_design_library = get_option("oxygen_vsb_options_users_access_design_library", array());
	$users_access_disable_classes = get_option("oxygen_vsb_options_users_access_disable_classes", array());
	$users_access_disable_ids = get_option("oxygen_vsb_options_users_access_disable_ids", array());

	$role_access_enable_elements = get_option("oxygen_vsb_options_role_access_enable_elements", array());
	$role_access_advanced_tab = get_option("oxygen_vsb_options_role_access_advanced_tab", array());
	$role_access_drag_n_drop = get_option("oxygen_vsb_options_role_access_drag_n_drop", array());
	$role_access_enabled_elements = get_option("oxygen_vsb_options_role_access_enabled_elements", array());
	$role_access_reusable_parts = get_option("oxygen_vsb_options_role_access_reusable_parts", array());
	$role_access_design_library = get_option("oxygen_vsb_options_role_access_design_library", array());
	$role_access_disable_classes = get_option("oxygen_vsb_options_role_access_disable_classes", array());
	$role_access_disable_ids = get_option("oxygen_vsb_options_role_access_disable_ids", array());

	if ( !is_array($role_access_enable_elements)) {
		$role_access_enable_elements = array();
	}
	if ( !is_array($role_access_advanced_tab)) {
		$role_access_advanced_tab = array();
	}
	if ( !is_array($role_access_drag_n_drop)) {
		$role_access_drag_n_drop = array();
	}
	if ( !is_array($role_access_enabled_elements)) {
		$role_access_enabled_elements = array();
	}
	if ( !is_array($role_access_reusable_parts)) {
		$role_access_reusable_parts = array();
	}
	if ( !is_array($role_access_design_library)) {
		$role_access_design_library = array();
	}
	if ( !is_array($role_access_disable_classes)) {
		$role_access_disable_classes = array();
	}
	if ( !is_array($role_access_disable_ids)) {
		$role_access_disable_ids = array();
	}
	if ( !is_array($users_access_enabled_elements)) {
		$users_access_enabled_elements = array();
	}

	global $oxygen_vsb_components;

	$all_oxygen_components = array();
	foreach ($oxygen_vsb_components as $component) {
		
		if (!isset($component->options) || !is_array($component->options)) continue;
		if (!isset($component->options['tag']) || !isset($component->options['name'])) continue;
		if (in_array($component->options['tag'], array('ct_widget','ct_sidebar','ct_li','ct_svg_icon'))) continue;
		
		$all_oxygen_components[$component->options['tag']] = $component->options['name'];
	}

	// Composite Elements
	$OxygenCompositeElements = new OxygenCompositeElements();
	
	if (is_array($OxygenCompositeElements->composite_elements->components)) {
		foreach ($OxygenCompositeElements->composite_elements->components as $component) {
			if ( !isset($component->min_version) || version_compare(CT_VERSION, $component->min_version) === -1 ) {
				continue;
			}
			if ( !isset($component->location) ) {
				continue;
			}
			if (isset($component->id) && isset($component->name)) {
				$all_oxygen_components[$component->id."-".$component->page] = $component->name;
			}
		}
	}
	?>

	<h2>Role Manager</h2>
	<?php do_action('oxygen_vsb_before_settings_page');?>
	<p>
		<strong>Important Security Warning:</strong> Oxygen's Code Block element can execute any PHP code. A malicious user could use this to do literally anything to the site. Therefore, do not grant Oxygen access to untrusted users. 
	</p>

	<form method="post" action="options.php">
		<?php settings_fields( 'oxygen_vsb_options_group_client_control' ); ?>
	    <?php do_settings_sections( 'oxygen_vsb_options_group_client_control' ); ?>
		<table id="oxygen_vsb_access_role_settings">
		<?php 
			
			add_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
			$roles = get_editable_roles();
			remove_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
			
			foreach($roles as $role => $item) {
				?>
				<tr valign="top" class="oxygen_role_access_settings_row">
					<th scope="row"><label for="oxygen_vsb_access_role_<?php echo esc_attr($role);?>"><?php echo esc_html($item['name']); ?></label></th>
					<td>
						<select name="oxygen_vsb_access_role_<?php echo esc_attr($role);?>" id="oxygen_vsb_access_role_<?php echo esc_attr($role);?>" class="oxygen_vsb_access_role_select">
							<option value="false" >No Access</option>
							<?php if (oxygen_vsb_is_agency_bundle()) : ?>
							<option value="edit_only" <?php selected(get_option("oxygen_vsb_access_role_$role"), "edit_only"); ?>>Edit Only</option>
							<?php endif; ?>
							<option value="true" <?php selected(get_option("oxygen_vsb_access_role_$role"), "true"); ?>>Full Access</option>
						</select>
						<div class="oxygen_role_access_edit_only_sub_options">
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_enable_elements" name="oxygen_vsb_options_role_access_enable_elements[<?php echo esc_attr($role);?>][]" <?php if (isset($role_access_enable_elements[$role])) checked($role_access_enable_elements[$role][0], "true"); ?> value="true"/>Enable Elements<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Allows user to access a specific element in the +Add Pane</span></label>
							<div>
								<select name="oxygen_vsb_options_role_access_enabled_elements[<?php echo esc_attr($role); ?>][]" class="oxygen_user_access_enabled_elements" multiple="multiple">
									<?php foreach ($all_oxygen_components as $tag => $name) : ?>
									<option value="<?php echo $tag; ?>" <?php if(is_array($role_access_enabled_elements) && isset($role_access_enabled_elements[$role]) && is_array($role_access_enabled_elements[$role])) selected(in_array($tag, $role_access_enabled_elements[$role])); ?>><?php echo $name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_advanced_tab" name="oxygen_vsb_options_role_access_advanced_tab[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_advanced_tab[$role])) checked($role_access_advanced_tab[$role][0], "true"); ?> value="true"/>Enable Advanced Tab</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_drag_n_drop" name="oxygen_vsb_options_role_access_drag_n_drop[<?php echo esc_attr($role);?>][]" <?php if (isset($role_access_drag_n_drop[$role])) checked($role_access_drag_n_drop[$role][0], "true"); ?> value="true"/>Enable Drag & Drop</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_reusable_parts" name="oxygen_vsb_options_role_access_reusable_parts[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_reusable_parts[$role])) checked($role_access_reusable_parts[$role][0], "true"); ?> value="true"/>Enable Reusable Parts</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_design_library" name="oxygen_vsb_options_role_access_design_library[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_design_library[$role])) checked($role_access_design_library[$role][0], "true"); ?> value="true"/>Enable Design Library</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_disable_classes" name="oxygen_vsb_options_role_access_disable_classes[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_disable_classes[$role])) checked($role_access_disable_classes[$role][0], "true"); ?> value="true"/>Disable Classes</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_disable_ids" name="oxygen_vsb_options_role_access_disable_ids[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_disable_ids[$role])) checked($role_access_disable_ids[$role][0], "true"); ?> value="true"/>Disable IDs</label>
						</div>
					</td>
				</tr>
				<?php
			}
		 ?>
		</table>

		<h2>Per User Access</h2>
		<?php do_action('oxygen_vsb_before_settings_page');?>
		<p>
			This override the above role-based restrictions. 
		</p>

		<div id="oxygen_user_access_table">
			<div id="oxygen_user_access_placeholder">
					<a href="#" class="oxygen_user_access_remove_user" title="<?php _e("Remove Access Rule","oxygen"); ?>"></a>
					Grant
					<select name="oxygen_user_access_user_select" class="oxygen_user_access_user_select">
					<option value=""></option>
					<?php foreach(get_users() as $registered_user) : 
						if ($registered_user->ID==get_current_user_id()){
							continue;
						}
						?>
						<option value="<?php echo $registered_user->ID; ?>"><?php echo $registered_user->data->user_login; ?></option>
					<?php endforeach; ?>
					</select>
					access level
					<select class="oxygen_user_access_level_select">
						<option value="false">No Access</option>
						<?php if (oxygen_vsb_is_agency_bundle()) : ?>
						<option value="edit_only">Edit Only</option>
						<?php endif; ?>
						<option value="full">Full Access</option>
					</select>
				<div class="oxygen_user_access_edit_only_sub_options">
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_enable_elements" value="true"/>Enable Elements<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Allows user to access a specific element in the +Add Pane</span></label>
					<div>
						<select name="" class="oxygen_user_access_enabled_elements" multiple="multiple">
							<?php foreach ($all_oxygen_components as $tag => $name) : ?>
								<option value="<?php echo $tag; ?>"><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_advanced_tab" value="true"/>Enable Advanced Tab</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_drag_n_drop" value="true"/>Enable Drag & Drop</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_reusable_parts" value="true"/>Enable Reusable Parts</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_design_library" value="true"/>Enable Design Library</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_classes" value="true"/>Disable Classes</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_ids" value="true"/>Disable IDs</label>
				</div>
			</div>
			<?php foreach ($users_access_list as $user_id => $user_with_access) : ?>
			<div class="oxygen-user-access-settings-row">
				<a href="#" class="oxygen_user_access_remove_user" title="<?php _e("Remove Access Rule","oxygen"); ?>"></a>
				Grant
				<select name="oxygen_user_access_user_select" class="oxygen_user_access_user_select">
				<?php foreach(get_users() as $registered_user) : 
					if ($registered_user->ID==get_current_user_id()){
						continue;
					}
					?>
					<option value="<?php echo $registered_user->ID; ?>" <?php selected($registered_user->ID, $user_id); ?>">
						<?php echo $registered_user->data->user_login; ?>
					</option>
				<?php endforeach; ?>
				</select>
				access level
				<select name="oxygen_vsb_options_users_access_list[<?php echo esc_attr($user_id); ?>][]" class="oxygen_user_access_level_select">
					<option value="false">No Access</option>
					<?php if (oxygen_vsb_is_agency_bundle()) : ?>
					<option value="edit_only" <?php selected($users_access_list[$user_id][0], "edit_only"); ?>>Edit Only</option>
					<?php endif; ?>
					<option value="true" <?php selected($users_access_list[$user_id][0], "true"); ?>>Full Access</option>
				</select></br>
				<div class="oxygen_user_access_edit_only_sub_options">
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_enable_elements" name="oxygen_vsb_options_users_access_enable_elements[<?php echo esc_attr($user_id); ?>][]" <?php if (isset($users_access_enable_elements[$user_id])) checked($users_access_enable_elements[$user_id][0], "true"); ?> value="true"/>Enable Elements<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Allows user to access a specific element in the +Add Pane</span></label>
					<div>
						<select name="oxygen_vsb_options_users_access_enabled_elements[<?php echo esc_attr($user_id); ?>][]" class="oxygen_user_access_enabled_elements" multiple="multiple">
							<?php foreach ($all_oxygen_components as $tag => $name) : ?>
							<option value="<?php echo $tag; ?>" <?php if(isset($users_access_enabled_elements[$user_id]) && is_array($users_access_enabled_elements[$user_id])) selected(in_array($tag, $users_access_enabled_elements[$user_id])); ?>><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_advanced_tab" name="oxygen_vsb_options_users_access_advanced_tab[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_advanced_tab[$user_id])) checked($users_access_advanced_tab[$user_id][0], "true"); ?> value="true"/>Enable Advanced Tab</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_drag_n_drop" name="oxygen_vsb_options_users_access_drag_n_drop[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_drag_n_drop[$user_id])) checked($users_access_drag_n_drop[$user_id][0], "true"); ?> value="true"/>Enable Drag & Drop</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_reusable_parts" name="oxygen_vsb_options_users_access_reusable_parts[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_reusable_parts[$user_id])) checked($users_access_reusable_parts[$user_id][0], "true"); ?> value="true"/>Enable Reusable Parts</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_design_library" name="oxygen_vsb_options_users_access_design_library[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_design_library[$user_id])) checked($users_access_design_library[$user_id][0], "true"); ?> value="true"/>Enable Design Library</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_classes" name="oxygen_vsb_options_users_access_disable_classes[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_disable_classes[$user_id])) checked($users_access_disable_classes[$user_id][0], "true"); ?> value="true"/>Disable Classes</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_ids" name="oxygen_vsb_options_users_access_disable_ids[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_disable_ids[$user_id])) checked($users_access_disable_ids[$user_id][0], "true"); ?> value="true"/>Disable IDs</label>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<table>
			<tr>
				<a id="oxygen_user_access_add_user" href="#"><?php _e("Add User", "oxygen"); ?></a>
			</tr>
		</table>

		<h2>Post Type Manager</h2>
	
		<p>Hide Oxygen metabox on the following post types:</p>
		<table>
		<?php 
			
			global $ct_ignore_post_types;
			$postTypes = get_post_types();
			
			if(is_array($ct_ignore_post_types) && is_array($postTypes)) {
				$postTypes = array_diff($postTypes, $ct_ignore_post_types);
			}
			
			foreach($postTypes as $key => $item) {
				?>
				<tr valign="top">
					<td><input type="checkbox" id="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>" name="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>" value="true" <?php checked(get_option("oxygen_vsb_ignore_post_type_$key"), "true"); ?>></td>
					<td><label for="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>"><?php echo esc_html($item); ?></label></td>
				</tr>
				<?php
			}
	
		 ?>
		</table>
		 <?php submit_button(); ?>
	  </form>

	<?php
}

function oxygen_vsb_options_security_manager_page() {
	?>
	<h2>Oxygen Settings</h2>
		
	  <form method="post" action="options.php">
	  <?php settings_fields( 'oxygen_vsb_options_group_security' ); ?>
      <?php do_settings_sections( 'oxygen_vsb_options_group_security' ); ?>
		  <table>
			 
			  <tr valign="top">
				  <td><input type="checkbox" id="oxygen_vsb_enable_signature_validation" name="oxygen_vsb_enable_signature_validation" value="true" <?php checked(get_option('oxygen_vsb_enable_signature_validation'), "true"); ?>></td>
				  <td><label for="oxygen_vsb_enable_signature_validation"><?php _e("Check Oxygen's shortcodes for a valid signature before executing.","oxygen"); ?> </label></td>
			  </tr>
			  <tr valign="top">
				  <td><input type="checkbox" id="oxygen_vsb_enable_signature_frontend_errors" name="oxygen_vsb_enable_signature_frontend_errors" value="true" <?php checked(get_option('oxygen_vsb_enable_signature_frontend_errors'), "true"); ?>></td>
				  <td><label for="oxygen_vsb_enable_signature_frontend_errors"><?php _e("Show invalid shortcode signature warnings on the front end.","oxygen"); ?> </label></td>
			  </tr>
			  <tr>
			  	<td>
			  		
			  	</td>
			  	<td>
			  		<p><a href="https://oxygenbuilder.com/documentation/other/security/" target="_blnak"><?php _e("More Information.", "oxygen");?></a></p>
			  	</td>
			  </tr>
		  </table>

		  <?php submit_button(); ?>
	  </form>
			<p><a href="<?php echo add_query_arg('page', 'oxygen_vsb_sign_shortcodes', get_admin_url().'admin.php');?>">Sign All Shortcodes</a></p>
	<?php
}



function ct_cache_page_callback() {
	?>
	<h2>Oxygen Cache</h2>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'oxygen_vsb_options_group_cache' ); ?>
    	<?php do_settings_sections( 'oxygen_vsb_options_group_cache' ); ?>
		<table>
			<tr valign="top">
				<th scope="row"><label for="oxygen_vsb_universal_css_cache"><?php _e("Enable CSS Caching","oxygen"); ?></label></th>
				<td>
					<input type="checkbox" id="oxygen_vsb_universal_css_cache" name="oxygen_vsb_universal_css_cache" value="true" <?php checked(get_option('oxygen_vsb_universal_css_cache'), "true"); ?>>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
	
	<h2>Regenerate CSS Cache</h2>
	
	<p class="submit">
		<input type="button" id="oxy-cache-generate" class="button button-primary" value="Regenerate"/>
	</p>
	<div id="oxy-cache-result"></div>
	<?php

}


function ct_admin_settings() {
	
	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}

	$oxygen_vsb_settings = add_submenu_page(
			'ct_dashboard_page',
			'Settings',
			'Settings',
			'read',
			'oxygen_vsb_settings',
			'oxygen_vsb_options_page');

	add_submenu_page(null, 'Add 3rd Party Design Set', 'Add 3rd Party Design Set', 'manage_options', 'add_3rdp_designset', 'add_3rdp_designset_callback');
		
	add_action( 'load-' . $oxygen_vsb_settings, 'oxygen_vsb_settings_page_onload' );
}



function oxygen_vsb_settings_page_onload() {
	add_action( 'admin_enqueue_scripts', 'oxygen_vsb_settings_page_css' );
}

function oxygen_vsb_settings_page_css() {
	wp_enqueue_style("oxy-admin-settings-page", CT_FW_URI."/admin/oxy-settings-page.css");
}

function ct_license_page_callback() { 

	if(defined('CT_FREE')) {
		return;
	}

	?>
	
	<div class="wrap">

		<h2><?php _e("Oxygen License", "component-theme"); ?></h2>
	
		<?php 
		
		/**
		 * Hook for addons to show things in Oxygen admin
		 *
		 * 10 - Oxygen
		 * 20 - Selector Detector
		 * 30 - Typekit
		 *
		 * @since 1.4
		 */
		
		do_action("oxygen_license_admin_screen");
		
		?>

	</div>

	
<?php }

function oxygen_vsb_register_signing_page() {
	
	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	add_submenu_page(null, 'Oxygen Sign Shortcodes', 'Oxygen Sign Shortcodes', 'read', 'oxygen_vsb_sign_shortcodes', 'oxygen_vsb_sign_shortcodes_page');

}

add_action('admin_menu', 'oxygen_vsb_register_signing_page', 15);

function oxygen_vsb_sign_shortcodes_page() {
	wp_nonce_field( 'oxygen_vsb_sign_shortcodes', 'oxygen_vsb_sign_shortcodes_nonce' );

	?>
	
	<p>
		<strong><?php _e('Please backup your site before using this tool.', 'oxygen');?></strong>
	</p>
	<p>
		<label for="site_backup_confirmation"><input type="checkbox" value="1" id="site_backup_confirmation"> <?php _e('I have made a complete backup of my site.', 'oxygen');?></label>
	</p>

	<p>
		<strong><?php _e('Select the post types.', 'oxygen');?></strong>
	</p>
	<table>
	<?php 
		
		global $ct_ignore_post_types;
		$postTypes = get_post_types();
		
		$ignore_post_types = $ct_ignore_post_types;

		$ct_template_key = array_search('ct_template', $ignore_post_types);

		if($ct_template_key !== false) {
			unset($ignore_post_types[$ct_template_key]);
		}

		if(is_array($ignore_post_types) && is_array($postTypes)) {
			$postTypes = array_diff($postTypes, $ignore_post_types);
		}
		
		foreach($postTypes as $key => $item) {
			?>
			<tr valign="top">
				<td><input type="checkbox" class="oxygen_vsb_ignore_post_type" id="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>" name="oxygen_vsb_ignore_post_type[]" value="<?php echo esc_attr($key);?>" <?php checked($key == 'page' || $key == 'ct_template'); ?>></td>
				<td><label for="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>"><?php echo esc_html($item); ?></label></td>
			</tr>
			<?php
		}

	 ?>
	</table>

	<p>
		<button id="start-signing-process">Start shortcodes signing process</button>
	</p>
	<div id="upgrade-output">

	</div>
	<script>
	
		jQuery(document).ready(function($) {
			var stepCount = 0;
			var parent = $('#upgrade-output');
			function processMessages(response, step) {


				if(response['messages']) {
			

					response['messages'].forEach(function(message, index) {
						
						var msgBlock = $('<div>').html(message);
						
						parent.append(msgBlock);	

					});

	
				}



			}

			function processSigning(step, pageindex) {

				if(step > 1000) {
					var msgBlock = $('<div>').html('Completed');
					
					parent.append(msgBlock);	
					return;
				}

				var postTypes = [];

				jQuery('.oxygen_vsb_ignore_post_type').each(
					function(item) { 
						if(jQuery(this).prop('checked')) { 
							postTypes.push(jQuery(this).val());
						}
					}
				);

				var data = {
					'action': 'oxygen_vsb_signing_process',
					'nonce': jQuery('#oxygen_vsb_sign_shortcodes_nonce').val(),
					'postTypes': postTypes
				};

				if(typeof(step) !== 'undefined') {
					data['step'] = step;
				}

				if(typeof(pageindex) !== 'undefined') {
					data['index'] = pageindex;
				}


				jQuery.post(ajaxurl, data, function(response) {
					
					if(response['messages']) {
						processMessages(response, step);
					}

					if(typeof(response['step']) !== 'undefined') {
						
						if(typeof(response['index']) !== 'undefined') {
							processSigning(parseInt(response['step']), parseInt(response['index']));
						}
						else {
							processSigning(parseInt(response['step']));
						}
					}

				});
			}

			$('#start-signing-process').on('click', function() {
				if(!$('#site_backup_confirmation').prop('checked')) {
					alert('<?php _e('Please back up your site and then check the box.', 'oxygen');?>');
					return;
				}

				$('#upgrade-output').html('');
				processSigning();

			});

		});

	</script>

	<?php
}

add_action('admin_footer', 'edit_with_oxygen_buttons_for_edit_mode');
function edit_with_oxygen_buttons_for_edit_mode() {

	if (oxygen_vsb_current_user_can_full_access()) {
		return;
	}
	
	global $post;
	$screen = get_current_screen();

	// Post edit mode locked
	$post_locked = get_post_meta( $post->ID, 'oxygen_lock_post_edit_mode', true );
	if ($post_locked=="true") { ?>
		
		<a class="oxygen-edit-mode-button oxygen-edit-mode-button-disabled" href="#" title="<?php _e("Editing is locked for this post.", "oxygen"); ?>">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/Oxygen-O.png'/>
		</a>
		<?php if (isset($screen->id)&&($screen->id=="post"||$screen->id=="page"||$screen->id=="ct_template")) {?>
		<a class="oxygen-edit-mode-button-non-gutenberg page-title-action oxygen-edit-mode-button-disabled" href="#" title="<?php _e("Editing is locked for this post.", "oxygen"); ?>">
			<?php _e("Edit with Oxygen","oxygen"); ?>
		</a>
		<?php }

		return;
	}

	$edit_with_oxygen_link = oxygen_add_posts_quick_action_link(array(), $post, "array");

	$template_locked = false;
	if (isset($edit_with_oxygen_link["template"])) {
		$template_locked = get_post_meta( $edit_with_oxygen_link["template"], 'oxygen_lock_post_edit_mode', true );
	}

	if (is_oxygen_edit_post_locked()||$template_locked) { 
		
		if ($template_locked) {
			$text = __("Editing is locked for template.", "oxygen");
		} else {
			$text = __("Oxygen is open in another tab or by another user.", "oxygen");
		}
		
		?>
		
		<a class="oxygen-edit-mode-button oxygen-edit-mode-button-disabled" href="#" title="<?php echo $text; ?>">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/Oxygen-O.png'/>
		</a>
		<?php if (isset($screen->id)&&($screen->id=="post"||$screen->id=="page"||$screen->id=="ct_template")) {?>
		<a class="oxygen-edit-mode-button-non-gutenberg page-title-action oxygen-edit-mode-button-disabled" href="#" title="<?php echo $text; ?>">
			<?php _e("Edit with Oxygen","oxygen"); ?>
		</a>
		<?php }
		
		return;
	}

	if (!isset($edit_with_oxygen_link["url"])||!isset($edit_with_oxygen_link["text"])) {
		return;
	}	

	$extra_class = "";
	if ($edit_with_oxygen_link["text"]=="Edit Template") {
		$extra_class = "oxygen-edit-mode-button-template";
	}

	?>
	<a class="oxygen-edit-mode-button <?php echo $extra_class; ?>" href="<?php echo $edit_with_oxygen_link["url"] ?>" title="<?php echo $edit_with_oxygen_link["text"] ?>">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/Oxygen-O.png'/>
	</a>
	<?php if (isset($screen->id)&&($screen->id=="post"||$screen->id=="page"||$screen->id=="ct_template")) {?>
	<a class="oxygen-edit-mode-button-non-gutenberg page-title-action <?php echo $extra_class; ?>" href="<?php echo $edit_with_oxygen_link["url"] ?>" title="<?php echo $edit_with_oxygen_link["text"] ?>">
		<?php echo $edit_with_oxygen_link["text"]; ?>
	</a>
	<?php }
}
