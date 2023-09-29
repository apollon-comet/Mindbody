<?php
/*
Plugin Name: Oxy Transfer Pro
Author: Robin Singh
Author URI: https://oxytransfer.com
Description: Use Oxy Transfer Pro to export and import Complete Oxygen installations including the design sets. This plugin allows you to zip up a clone of your Oxygen site to be easily imported using even the free version of the Oxy Transfer plugin.
Version: 1.2.6
*/

class OxyTransferColorsCallback {
	
	public static $global_colors_map;

	public static function action($matches) {
		if(is_array(self::$global_colors_map) && sizeof(self::$global_colors_map) > 0) {
			if(isset(self::$global_colors_map[intval($matches[1])])) {
				return 'color('.self::$global_colors_map[intval($matches[1])].')';
			}
		}

		return $matches[0];
	}
	
}
require_once( 'edd.php' );
class C_Oxy_Transfer {

	public $version = '1.2.6';
	public $title = 'Oxy Transfer Pro';
	public $prefix = 'oxy_transfer_pro_';
	public $store_url = 'https://oxytransfer.com';
	public $item_id =13;

	function __construct() {

		add_action('admin_menu', array($this, 'oxyTransferMenu'));
		OxyTransferLicense::init($this->prefix, $this->title, $this->store_url, $this->item_id );
		add_action('wp_ajax_oxyTransferRemote', array($this, 'oxyTransferRemote'));

		add_action('admin_init', array($this, 'oxyTransferExport'));

		add_action('admin_init', array($this, 'oxyTransferImport'));

		add_action('admin_init', array($this, 'oxyTransferOptions'));

		add_action('add_meta_boxes', array($this, 'oxyTransferAddMetabox'), 10, 2);
		
		add_action( 'admin_init', array($this, 'updater'), 0 );

		add_action( 'upload_mimes', array($this, 'mime_types'), 0 );
		
		$unrestrictedUploads = get_option($this->prefix.'disablefilter', true);
		if($unrestrictedUploads === "1") {
			add_filter('map_meta_cap', array($this, 'unrestricted_upload_filter'), 0, 2);
		}
	}


	public function oxyTransferOptions() {
		add_option( $this->prefix.'disablefilter', 0 );
		register_setting($this->prefix.'settings', $this->prefix.'disablefilter', array($this, 'sanitize_disablefilter') );
	}

	public function sanitize_disablefilter($val) {
		
		if(is_numeric($val)) {
			return intval($val);
		}
		return 0;
	}

	public function settings_page() {
		?>
		<h2><?php echo $this->title.' '.__('Settings'); ?></h2>
		<form method="post" action="options.php">

			<?php settings_fields($this->prefix.'settings'); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th>
							<?php _e('Disable Uploads extensions filter'); ?>
						</th>
						<td>
							<input id="<?php echo $this->prefix;?>disablefilter" name="<?php echo $this->prefix;?>disablefilter" type="checkbox" value="1" <?php checked(get_option($this->prefix.'disablefilter'), "1"); ?> />
						</td>
						<td><small>Warning: This setting is meant to be checked as a temporary measure, while you are importing data containing .svg images. Once your task is complete, this setting should be turned back off.</small></td>
					</tr>
					
				</tbody>
			</table>
		<?php submit_button(); ?>
		</form>
		<?php
	}


	public function unrestricted_upload_filter($caps, $cap) {
	  


	  if ($cap == 'unfiltered_upload') {
	    $caps = array();
	    $caps[] = $cap;
	  }

	  return $caps;
	}

	

	public function mime_types($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}


	public function updater() {
		// retrieve our license key from the DB.
		$license_key = trim( get_option( $this->prefix . 'license_key' ) );

		// setup the updater.
		$edd_updater = new OxyTransferUpdater( $this->store_url, __FILE__,
			array(
				'version' => $this->version,    // current version number
				'license' => $license_key,             // license key (used get_option above to retrieve from DB)
				'item_id' => $this->item_id,    // ID of the product
				'item_name' => $this->title,
				'author'  => 'Oxy Transfer', // author of this plugin
				'url'     => home_url(),
				'beta'    => false,
			)
		);
	}

	function oxyTransferAddMetabox($post_type, $post) {

		if(!in_array(
			$post_type, 
			array('post', 'page', 'oxy_user_library', 'ct_template')
		 )) {
			return;
		}

		add_meta_box(
			'oxy-transfer-metabox',
			'Oxy Transfer Import/Export',
			array($this, 'oxyTransferMetabox'),
			null,
			'normal',
			'default',
			array($post)
		);
	}

	function oxyTransferMetabox($post) {
		?>
		<a href='<?php echo add_query_arg(array('page' => 'c_oxy_transfer', 'post' => $post->ID), get_admin_url().'admin.php'); ?>'>Click to Import/Export</a>
		<?php
	}

	function oxyTransferMenu() {

		add_menu_page( 	
			'Oxy Transfer', 
			'Oxy Transfer', 
			'read', 
			'c_oxy_transfer', 
			array($this, 'oxyTransferPageCallback' )
		);

		add_submenu_page( 'c_oxy_transfer', 'License', 'License', 'manage_options', $this->prefix.'menu', 'OxyTransferLicense::license_page');
		add_submenu_page( 'c_oxy_transfer', 'Settings', 'Settings', 'manage_options', $this->prefix.'settingsmenu', array($this, 'settings_page'));

	}

	function oxyTransferPageCallback() {

		$post_id = isset($_GET['post']) ? intval($_GET['post']) : false;
		
		$post = false;
		
		if($post_id) {
			$post = get_post($post_id);
		}

		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		
		$upload_mb = min($max_upload, $max_post);
		?>
		<style type="text/css">
			.oxy-transfer-container > div {
				width: auto;
				padding: 20px;
				margin: 20px;
				text-align: center;
				background: #fcfcfc;
			}

			.oxy-transfer-container > div input[type=file] {

			}

			.oxy-transfer-container > div  .button.button-primary {
				font-size: 24px;
				padding: 10px;
				margin: 20px;
				width: 300px;
				display: inline-block;
				line-height: 24px;
				height: auto;
			}

		</style>
		

		<div class="oxy-transfer-container">
			<?php
				if($post) {
			
			?>
			<h3>Post Specific Data Transfer</h3>
			<p><strong>Post ID:</strong> <?php echo $post->ID;?></p>
			<p><strong>Post Title:</strong> <?php echo $post->post_title;?></p>
			<p><strong>Post type:</strong> <?php echo $post->post_type;?></p>
			<?php
				}
			?>
			<div>
				<form id="importForm" action="" method="post" enctype="multipart/form-data">

					<table style="margin:auto">
						<tr style="text-align: right; vertical-align: top; padding-bottom: 40px">
							<td>Upload the transfer zip file:<br>
								<small>Max allowed file size for upload is <?php echo $upload_mb;?>mb</small>
							</td>
							<td>
								<input type="file" name="oxy-transfer-file" id="oxy-transfer-file" onsubmit="return on_submit()" />
							</td>
						</tr>
						<!-- <tr>
							<td style="padding: 20px; 0">Or</td>
						</tr>
						<tr style="text-align: left">
							<td>Enter a URL to the transfer zip file</td>
							<td><input type="url" name="oxy-transfer-url" id="oxy-transfer-url" /></td>
						</tr> -->
					</table>
					<?php
						echo $post?'<input type="hidden" value="'.$post->ID.'" name="post" />':'';
					?>
					<input type="submit" class="button button-primary" name="upload" value="Import<?php echo $post?' to Post':'';?>"/>
				</form>
				
				<div id="remoteprocess" style="background-color:#fcc"></div>
			</div>

			<div>
				<form id="exportForm" action="" method="get">
					<input type="hidden" value="c_oxy_transfer" name="page" />
					<input type="hidden" value="export" name="action" />
					<?php
						echo $post?'<input type="hidden" value="'.$post->ID.'" name="post" />':'';
					?>
					<div>
						<label>Pack Images: <input type="checkbox" value="1" name="packimages" /></label><br>
						<small>This will include the images in the zip file. If this option is not checked, the images will be downloaded automatically at the time of import, from the source site if it is accessible on the internet.</small>
					</div>
					<input type="submit" class="button button-primary" name="upload" value="Export<?php echo $post?' from Post':'';?>"/>
				</form>
			</div>
			
		</div>

		<script type="text/javascript">
			var oxyTransferUrl = "<?php echo add_query_arg(array('page' => 'c_oxy_transfer'), get_admin_url().'admin.php');?>";
			jQuery('document').ready(function($) {
				$('form#importForm').submit(function(e) {
					
					if (document.getElementById("oxy-transfer-file").files[0].size > <?php echo $upload_mb*1000000;?>) {
					    alert("The file being uploaded "+(document.getElementById("oxy-transfer-file").files[0].size/1000000)+"mb is greater than the allowed limit of <?php echo $upload_mb;?>mb");
					    return false;
					}
					
					return true; 
					
				})

				function getUrlParameter(name) {
				    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
				    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
				    var results = regex.exec(location.search);
				    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
				};

				function processRemote(index, list) {
					$.post( window.ajaxurl, {action:'oxyTransferRemote',index: index},  function( data ) {
					  if(data['index']) {
					  	if(data['index'] === -1) {
					  		list.children('li').text('All remote images loaded');
					  		// redirect to loading the data;
					  		
					  		var params = {action:'oxyTransferRemote', data:true};
					  		var post = parseInt(getUrlParameter('post'));
					  		
					  		if(post) {
					  			params['post'] = post;
					  		}

					  		$.post( window.ajaxurl, params,  function( data ) {
					  			if(data['datadone']) {
					  				list.children('li').text('Import Complete.');
					  			}
					  		});
					  	} else {
					  		list.children('li').text('Loaded remote image '+index+ '  processing...');
					  		processRemote(data['index'], list);
					  	}
					  } else if(data['error']) {
					  	list.children('li').text('Error:'+data['error']);
					  } else if(data['datadone']) {
					  	list.children('li').text('Congratulations: Import process is complete');
					  }
					  
					});
				}

				if(getUrlParameter('action') == 'remote') {
					let list = $('<ul>');
					$('#remoteprocess').append(list);
					list.append('<li>Processing...</li>');
					processRemote(0, list);
				}
			}) 

			
		</script>

		<?php
	}

	function oxyTransferImages($items) {
		$images = array();
		if(is_array($items)) {
			foreach($items as $index => $item) {
				if($index === 'src' || $index === 'background-image') {
					// do the thing
					$images[] = $item;
				} elseif(is_array($item)) {
					$images = array_merge($images, $this->oxyTransferImages($item));
				}
			}
		}

		return $images;
	}

	function oxyTransferBlocks($post_id = false) {

		$posts = get_posts(array("post_type" => "oxy_user_library", 'numberposts' => -1));
		$templates = array(); $images = array(); $classes = array();
		foreach ($posts as $post) {
			if($post_id !== false && intval($post_id) !== $post->ID ) {
				continue;
			}
			$template['ID'] = $post->ID;
			$template['post_title'] = $post->post_title;


			foreach(array('builder_shortcodes') as $property) {
				$template[$property] = get_post_meta($post->ID, 'ct_'.$property, true);
			}

			$template['ct_connection_page_category'] = get_post_meta($post->ID, '_ct_connection_page_category', true);
			

			// collect the images
			$shortcodes = parse_shortcodes($template['builder_shortcodes'], false);
			
			$images = array_merge($images, $this->oxyTransferImages($shortcodes['content']));

			if(isset($shortcodes['content'])) {
				$classes = array_merge($classes, $this->oxyTransferExtractClasses($shortcodes['content']));
			}
		
			$templates[] = $template;
		}

		
		return array( 'blocks' => $templates, 'images' => $images, 'classes' => $classes);
	}


	function oxyTransferTemplates($post_id = false) {
		$posts = array();

		if($post_id !== false) {

			$collect = array();
			
			$parent_template = get_post_meta($post_id, 'ct_parent_template', true);
			if($parent_template && intval($parent_template) > 0) {
				$collect[] = intval($parent_template);
			}

			$other_template = get_post_meta($post_id, 'ct_other_template', true);
			if($other_template && intval($other_template) > 0) {
				$collect[] = intval($other_template);
			}

			$postCodes = get_post_meta($post_id, 'ct_builder_shortcodes', true);

			$postCodes = parse_shortcodes($postCodes, false, false);

			if(is_array($postCodes['content'])) {
				$collect = array_merge($collect, $this->oxyTransferGetReusableIDs( $postCodes['content']));
			}

			foreach($collect as $id) {
				$posts[] = get_post($id);
			}

			$selfPost = get_post(intval($post_id));
			if($selfPost->post_type === 'ct_template') {
				$posts[] = $selfPost;
			}

		}
		else {
			$posts = get_posts(array("post_type" => "ct_template", 'numberposts' => -1));
		}

		$templates = array(); $images = array(); $classes = array();
		foreach ($posts as $post) {

			$template['ID'] = $post->ID;
			$template['post_title'] = $post->post_title;
			foreach(array('template_type', 
				'template_order', 
				'parent_template', 
				'template_single_all', 
				'template_post_types', 
				'use_template_taxonomies', 
				'template_taxonomies',
				'template_apply_if_post_of_parents', 
				'template_post_of_parents', 
				'template_all_archives', 
				'template_apply_if_archive_among_taxonomies',
				'template_archive_among_taxonomies', 
				'template_apply_if_archive_among_cpt', 
				'template_archive_post_types', 
				'template_date_archive', 
				'template_front_page', 
				'template_blog_posts', 
				'template_search_page', 
				'template_404_page', 
				'template_index', 
				'builder_shortcodes',
				'template_inner_content') as $property) {
				$template[$property] = get_post_meta($post->ID, 'ct_'.$property, true);
			}

			// todo, there is a glitch with the above statement, there was a ct, which will be used while import, so take care
			if(is_array($template['template_taxonomies']) && 
				$template['use_template_taxonomies']) {
				foreach($template['template_taxonomies']['values'] as $index => $value) {
					$term = get_term($value);
					if( $term !== false) {
						$template['template_taxonomies']['values'][$index] = $term->slug;
					}
				}
			}
			if(is_array($template['template_archive_among_taxonomies']) && 
				$template['template_apply_if_archive_among_taxonomies']) {
				foreach($template['template_archive_among_taxonomies'] as $index => $value) {
					$term = get_term($value);
					if($term !== false) {
						$template['template_archive_among_taxonomies'][$index] = array('taxonomy' => $term->taxonomy, 'slug' => $term->slug);
					}
				}
			}

			// collect the images
			$shortcodes = parse_shortcodes($template['builder_shortcodes'], false);
			
			$images = array_merge($images, $this->oxyTransferImages($shortcodes['content']));

			
			if(isset($shortcodes['content'])) {
				$classes = array_merge($classes, $this->oxyTransferExtractClasses($shortcodes['content']));
			}
		
			$templates[] = $template;
		}

		return array( 'templates' => $templates, 'images' => $images, 'classes' => $classes);
	}

	function oxyTransferPages($post_id = false) {
		$posts = get_posts(array("post_type" => "page", 'numberposts' => -1));
		$pages = array(); $images = array(); $classes = array();

		foreach ($posts as $post) {
			if($post_id !== false && intval($post_id) !== $post->ID ) {
				continue;
			}
			$page = json_decode(json_encode($post), true);

			foreach(array('builder_shortcodes', 'other_template') as $property) {
				$page[$property] = get_post_meta($post->ID, 'ct_'.$property, true);
			}
			
			$shortcodes = parse_shortcodes($page['builder_shortcodes'], false);

			$images = array_merge($images, $this->oxyTransferImages($shortcodes['content']));

			if(isset($shortcodes['content'])) {
				$classes = array_merge($classes, $this->oxyTransferExtractClasses($shortcodes['content']));
			}

			$pages[] = $page;

		}

		return array( 'pages' => $pages, 'images' => $images, 'classes' => $classes);
	}

	function oxyTransferRemote() {

		$data = get_transient('oxyTransferImportData');
		$imageMap = get_transient('oxyTransferImages');

		if(!is_array($imageMap)) {
			$imageMap = array();
		}

		if(empty($data)) {
			header('Content-Type: application/json');
			echo json_encode(array('error'=>'No data found. Start the Import process again'));
			die();
		}

		$data = base64_decode($data);

		$data = json_decode($data, true);


		$processData = isset($_REQUEST['data']) ? true : false;

		if($processData) {
			
			$post_id = isset($_REQUEST['post']) ? intval($_REQUEST['post']) : false;

			$result = $this->oxyTransferImportData($data, $imageMap, $post_id);

			header('Content-Type: application/json');
			echo json_encode(array('datadone'=>true));
			die();
		}

		$remoteImages = $data['remoteImages'];
		
		$index = isset($_REQUEST['index']) && is_numeric($_REQUEST['index']) ? intval($_REQUEST['index']) : false;

		if($index === false) {
			
			header('Content-Type: application/json');
			echo json_encode(array('error'=>'Bad Index.'));
			die();
		}
		$url = '';
		if(isset($remoteImages[$index])) {
			
			$src = $remoteImages[$index];

			$get = wp_remote_get( $src );

			$response_code = wp_remote_retrieve_response_code($get);

			if($response_code !== 200) {
				$imageMap[$src] = $src;
				
			} else {

				$result = wp_upload_bits( basename($src), '', wp_remote_retrieve_body( $get ) );

				if (!$result['error']) {
					$filename = basename($src);
					$wp_filetype = wp_check_filetype($filename, null );
					$attachment = array(
						'post_mime_type' => $wp_filetype['type'],
						'post_parent' => $parent_post_id,
						'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
						'post_content' => '',
						'post_status' => 'inherit'
					);
					$attachment_id = wp_insert_attachment( $attachment, $result['file'], $parent_post_id );
					if (!is_wp_error($attachment_id)) {
						require_once(ABSPATH . "wp-admin" . '/includes/image.php');
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $result['file'] );
						wp_update_attachment_metadata( $attachment_id,  $attachment_data );
					}

					$imageMap[$src] = $result['url'];
					$url = $result['url'];
				}
				else {
					$imageMap[$src] = $src;	
				}
			}

		}

		set_transient('oxyTransferImages', $imageMap);
		$index++;
		
		header('Content-Type: application/json');
		echo json_encode(array('index'=>(($index > (sizeof($remoteImages)-1)) ? -1 : $index)));
		die();

	}

	function oxyTransferImport() {

		if(isset($_REQUEST['page']) && 
			sanitize_text_field($_REQUEST['page']) == 'c_oxy_transfer' && 
			isset($_REQUEST['upload'])) {
			
			$post_id = isset($_REQUEST['post']) ? intval($_REQUEST['post']) : false;

			delete_transient('oxyTransferDataFile');
			delete_transient('oxyTransferImages');

			if($_FILES["oxy-transfer-file"]["name"]) {
				$filename = $_FILES["oxy-transfer-file"]["name"];
				$source = $_FILES["oxy-transfer-file"]["tmp_name"];
				$type = $_FILES["oxy-transfer-file"]["type"];
				
				$name = explode(".", $filename);
				$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
				foreach($accepted_types as $mime_type) {
					if($mime_type == $type) {
						$okay = true;
						break;
					} 
				}
				
				$continue = strtolower($name[1]) == 'zip' ? true : false;
				if(!$continue) {
					$message = "The file you are trying to upload is not a .zip file. Please try again.";
				}

				
				$zip = new ZipArchive();
				$za = $zip->open($_FILES["oxy-transfer-file"]["tmp_name"]);

				$dataFile = null;
				$localFiles = [];
				
				if ($za ) {

					$numFiles = $zip->numFiles;

					if($numFiles > 1) { 
						// look for fileLog.json
						$stat = $zip->statIndex( $numFiles-2 ); 
						if(basename( $stat['name'] ) == 'fileLog.json') {
							// load files from the zip
							$filesList = $zip->getFromIndex($numFiles-2);
							if(!empty($filesList)) {
								$filesList = json_decode($filesList, true);
								$count = 0;
								
								foreach($filesList as $oldFile => $index) {
									
									$count++;

									if($count > $numFiles-2) {
										break;
									}

									$tmpfname = tempnam('.', '');

									$handle = fopen($tmpfname, "w");

									$content = $zip->getFromIndex(intval($index));

									fwrite($handle, $content);
									fclose($handle);

									// do here something
									$img_name = basename( $oldFile );
									$title    = explode( '.', $img_name );
									array_pop( $title );
									$title    = implode( '.', $title );

									//$img_url = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $img );

									$file    = array(
										'file'     => $tmpfname,
										'tmp_name' => $tmpfname,
										'name'     => $img_name
									);
									
									$img_id  = media_handle_sideload( $file, 0, $title );
									if(isset($img_id->errors)) {
										$localFiles[$oldFile] = "";
									} else {
										$uploaded_image = wp_get_attachment_url($img_id);
										if($uploaded_image) {
											$localFiles[$oldFile] = $uploaded_image;
										}
									}

								}
							}
						}
						$stat = $zip->statIndex( $numFiles-1 ); 
						if(basename( $stat['name'] ) == 'data.json') {
							$dataFile = $zip->getFromIndex($numFiles-1);
						}
					} elseif ($numFiles == 1 ) {
						$stat = $zip->statIndex(0);
						if(basename( $stat['name'] ) == 'data.json') {
							$dataFile = $zip->getFromIndex(0);
						}
					}

				    $zip->close();

				    
				}
				
				
				unlink($_FILES["oxy-transfer-file"]["tmp_name"]);	

				if($dataFile) {

					set_transient('oxyTransferImportData', base64_encode($dataFile));

					$data = json_decode($dataFile, true);

					if(sizeof($localFiles) > 0) {
						set_transient('oxyTransferImages', $localFiles);
					}

					$queryParams = array('page' => 'c_oxy_transfer', 'action' => 'remote');
					
					if($post_id) {
						$queryParams['post'] = $post_id;
					}									

					wp_redirect(add_query_arg($queryParams, get_admin_url().'admin.php'));
					exit();	
					
				} else {
					// display an error message
				}
				
				
				
			}
			
			$queryParams = array('page' => 'c_oxy_transfer');
			if($post_id) {
				$queryParams['post'] = $post_id;
			}	
			wp_redirect(add_query_arg($queryParams, get_admin_url().'admin.php'));
			exit();
 
		}
	}

	

	function oxyTransferSwapColors($settings) {

		foreach($settings as $key => $item) {
			if(is_string($item)) {
				$settings[$key] = preg_replace_callback('/color\((\d*)\)/', array('OxyTransferColorsCallback', 'action'), $item); // replaced value
			}
			else if(is_array($item)) {
				$settings[$key] = $this->oxyTransferSwapColors($settings[$key]);
			}
		}

		return $settings;

	}

	function oxyTransferImportTemplates($templates, $imageMap, $singularID = false, $global_colors_map = false) {
		$new_id_map = array();
		$current_user = wp_get_current_user();
		foreach($templates as $template) {
			
			if(intval($template['ID']) === intval($singularID)) {
				continue;
			}

			$post_data = array(
				'ID' => 0,
				'post_title' => $template['post_title'],
				'post_type' => 'ct_template',
				'post_status' => 'publish'
			);
		
			if($current_user && isset($current_user->ID)) {
				$post_data['post_author'] = $current_user->ID;
			}

			$new_id_map[$template['ID']] = wp_insert_post($post_data);
			
			foreach($template['applied_classes'] as $key => $val) {
				$selectiveClasses[$key] = $val;			
			}		

		}
		set_transient('oxygen-vsb-templates-id-map', $new_id_map);

		foreach($templates as $template) {
			
			if(intval($template['ID']) === intval($singularID)) {
				continue;
			}

			$shortcodes = parse_shortcodes($template['builder_shortcodes'], false, false);

			if(is_array($shortcodes['content'])) {
				$shortcodes['content'] = $this->oxyTransferReplaceImages($shortcodes['content'], $imageMap);
				$shortcodes['content'] = $this->oxyTransferSwapIDs( $shortcodes['content'], $new_id_map );

				if($global_colors_map) {
					$shortcodes['content'] = $this->oxyTransferSwapColors( $shortcodes['content']);
				}
			}

			$wrap_shortcodes = array();

			$wrap_shortcodes['children'] = $shortcodes['content'];
			// code tree back to JSON to pass into old function
			$components_tree_json = json_encode( $wrap_shortcodes );
			
			ob_start();
		
			// transform JSON to shortcodes
			$shortcodes = components_json_to_shortcodes( $components_tree_json );
		
			// we don't need anything to be output by custom shortcodes
			ob_clean();

			update_post_meta($new_id_map[$template['ID']], 'ct_builder_shortcodes', $shortcodes);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_type', $template['template_type']);

			// cache styles
			oxygen_vsb_cache_page_css($new_id_map[$template['ID']], $shortcodes);

			if(isset($template['template_type']) && $template['template_type'] == 'reusable_part') { // store the source parameters to check for redundancy while importing re-usables again
				update_post_meta($new_id_map[$template['ID']], 'ct_source_site', $site);
				update_post_meta($new_id_map[$template['ID']], 'ct_source_post', $template['ID']);
			}

			update_post_meta($new_id_map[$template['ID']], 'ct_template_order', $template['template_order']);
			update_post_meta($new_id_map[$template['ID']], 'ct_parent_template', $new_id_map[$template['parent_template']]);

			update_post_meta($new_id_map[$template['ID']], 'ct_template_single_all', $template['template_single_all']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_post_types', $template['template_post_types']);
			update_post_meta($new_id_map[$template['ID']], 'ct_use_template_taxonomies', $template['use_template_taxonomies']);
			
			// match id to slug for each taxonomy
			if(is_array($template['template_taxonomies'])) {
				foreach($template['template_taxonomies']['values'] as $key => $val) {
					// get id for the slug
					$term = get_term_by('slug', $val, $template['template_taxonomies']['names'][$key]);
					
					if($term) {
						$template['template_taxonomies']['values'][$key] = $term->term_id;
					}
					else {
						if(isset($template['template_taxonomies'])) {
							unset($template['template_taxonomies']['names'][$key]);
							unset($template['template_taxonomies']['values'][$key]);
						}
					}

				}
			}

			update_post_meta($new_id_map[$template['ID']], 'ct_template_taxonomies', $template['template_taxonomies']);

			

			update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_post_of_parents', $template['template_apply_if_post_of_parents']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_post_of_parents', $template['template_post_of_parents']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_all_archives', $template['template_all_archives']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_archive_among_taxonomies', $template['template_apply_if_archive_among_taxonomies']);

			// match id to slug for each taxonomy
			if(isset($template['template_archive_among_taxonomies']) && is_array($template['template_archive_among_taxonomies'])) {
				foreach($template['template_archive_among_taxonomies'] as $key => $val) {
					// get id for the slug
					if(is_array($val)) {
						$term = get_term_by('slug', $val['slug'], $val['taxonomy']);	
						if($term) {
							$template['template_archive_among_taxonomies'][$key] = $term->term_id;
						}
						else {
							unset($template['template_archive_among_taxonomies'][$key]);
						}
					}

				}
			}

			update_post_meta($new_id_map[$template['ID']], 'ct_template_archive_among_taxonomies', $template['template_archive_among_taxonomies']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_archive_among_cpt', $template['template_apply_if_archive_among_cpt']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_archive_post_types', $template['template_archive_post_types']);
			// update_post_meta($new_id_map[$template['ID']], 'ct_template_apply_if_archive_among_authors', $template['template_apply_if_archive_among_authors']);
			// update_post_meta($new_id_map[$template['ID']], 'ct_template_authors_archives', $template['template_authors_archives']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_date_archive', $template['template_date_archive']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_front_page', $template['template_front_page']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_blog_posts', $template['template_blog_posts']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_search_page', $template['template_search_page']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_404_page', $template['template_404_page']);
			update_post_meta($new_id_map[$template['ID']], 'ct_template_index', $template['template_index']);

			update_post_meta($new_id_map[$template['ID']], 'ct_template_inner_content', $template['ct_template_inner_content']);
			
		}
	}

	function oxyTransferImportBlocks($pages, $imageMap) {
		$new_id_map = array();

		$current_user = wp_get_current_user();

		$templates_id_map = get_transient('oxygen-vsb-templates-id-map');

		// insert posts
		foreach($pages as $page) {

			$post_data = $page;
			
			unset($post_data['ID']);

			$post_data['post_type'] = 'oxy_user_library';
			$post_data['post_status'] = 'publish';
			
			if($current_user && isset($current_user->ID)) {
				$post_data['post_author'] = $current_user->ID;
			}

			$new_id_map[$page['ID']] = wp_insert_post($post_data);
			
		}

		foreach($pages as $page) {
			
			// update parent status
			$post_data = array(
				'ID' => $new_id_map[$page['ID']],
				'post_parent' => $new_id_map[$page['post_parent']],
			);

			wp_update_post($post_data);

			// TODO run through the shortcodes, if some re-usable in place, replace its id from the $new_id_map
			$shortcodes = parse_shortcodes($page['builder_shortcodes'], false, false);
			$wrap_shortcodes = array();

			if(is_array($shortcodes['content'])) {
				$shortcodes['content'] = $this->oxyTransferReplaceImages($shortcodes['content'], $imageMap);
				$shortcodes['content'] = $this->oxyTransferSwapIDs( $shortcodes['content'], $templates_id_map );
			}

			$wrap_shortcodes['children'] = $shortcodes['content'];
			// code tree back to JSON to pass into old function
			$components_tree_json = json_encode( $wrap_shortcodes );
			
			ob_start();
		
			// transform JSON to shortcodes
			$shortcodes = components_json_to_shortcodes( $components_tree_json );
		
			// we don't need anything to be output by custom shortcodes
			ob_clean();

			update_post_meta($new_id_map[$page['ID']], 'ct_builder_shortcodes', $shortcodes);

			update_post_meta($new_id_map[$page['ID']], '_ct_connection_page_category', $page['ct_connection_page_category']);

			// cache styles
			oxygen_vsb_cache_page_css($new_id_map[$page['ID']], $shortcodes);
		}
	}


	function oxyTransferGetReusableIDs($shortcodes) {
		$ids = array();
		if(is_array($shortcodes)) {
			foreach($shortcodes as $key => $shortcode) {

				if($shortcode['name'] == 'ct_reusable') {
					$ids[] = intval($shortcodes[$key]['options']['view_id']);
				}

				if(isset($shortcode['children']) && is_array($shortcode['children'])) {
					$ids = array_merge($ids, $this->oxyTransferGetReusableIDs($shortcodes[$key]['children']));
				}
			}
		}

		return $ids;
	}
	function oxyTransferSwapIDs($shortcodes, $new_id_map) {

		if(is_array($shortcodes)) {
			foreach($shortcodes as $key => $shortcode) {

				if($shortcode['name'] == 'ct_reusable') {
					$shortcodes[$key]['options']['view_id'] = $new_id_map[$shortcode['options']['view_id']];
				}

				if(isset($shortcode['children']) && is_array($shortcode['children'])) {
					$shortcodes[$key]['children'] = $this->oxyTransferSwapIDs($shortcodes[$key]['children'], $new_id_map);
				}
			}
		}

		return $shortcodes;
	}

	function oxyTransferImportSingular($pages, $imageMap, $from_id, $to_id, $post_type, $global_colors_map = false) {
		
		$current_post = get_post(intval($to_id));

		$templates_id_map = get_transient('oxygen-vsb-templates-id-map');

		$current_user = wp_get_current_user();

		$pageData = false;

		foreach($pages as $page) {
			
			if(intval($from_id) === intval($page['ID'])) {
				$pageData = $page;
				break;
			}
		}

		$templates_id_map = get_transient('oxygen-vsb-templates-id-map');


		if($post_type === 'ct_template' && $current_post->post_type ==='ct_template') {
			
			$template = $pageData;
			$template['ID'] = intval($to_id);

			$shortcodes = parse_shortcodes($template['builder_shortcodes'], false, false);

			if(is_array($shortcodes['content'])) {
				$shortcodes['content'] = $this->oxyTransferReplaceImages($shortcodes['content'], $imageMap);
				$shortcodes['content'] = $this->oxyTransferSwapIDs( $shortcodes['content'], $templates_id_map );

				if($global_colors_map) {
					$shortcodes['content'] = $this->oxyTransferSwapColors( $shortcodes['content']);
				}
			}

			$wrap_shortcodes = array();

			$wrap_shortcodes['children'] = $shortcodes['content'];
			// code tree back to JSON to pass into old function
			$components_tree_json = json_encode( $wrap_shortcodes );
			
			ob_start();
		
			// transform JSON to shortcodes
			$shortcodes = components_json_to_shortcodes( $components_tree_json );
		
			// we don't need anything to be output by custom shortcodes
			ob_clean();

			update_post_meta($to_id, 'ct_builder_shortcodes', $shortcodes);
			update_post_meta($to_id, 'ct_template_type', $template['template_type']);

			// cache styles
			oxygen_vsb_cache_page_css($to_id, $shortcodes);

			if(isset($template['template_type']) && $template['template_type'] == 'reusable_part') { // store the source parameters to check for redundancy while importing re-usables again
				update_post_meta($to_id, 'ct_source_site', $site);
				update_post_meta($to_id, 'ct_source_post', $template['ID']);
			}

			update_post_meta($to_id, 'ct_template_order', $template['template_order']);
			update_post_meta($to_id, 'ct_parent_template', $new_id_map[$template['parent_template']]);

			update_post_meta($to_id, 'ct_template_single_all', $template['template_single_all']);
			update_post_meta($to_id, 'ct_template_post_types', $template['template_post_types']);
			update_post_meta($to_id, 'ct_use_template_taxonomies', $template['use_template_taxonomies']);
			
			// match id to slug for each taxonomy
			if(is_array($template['template_taxonomies'])) {
				foreach($template['template_taxonomies']['values'] as $key => $val) {
					// get id for the slug
					$term = get_term_by('slug', $val, $template['template_taxonomies']['names'][$key]);
					
					if($term) {
						$template['template_taxonomies']['values'][$key] = $term->term_id;
					}
					else {
						if(isset($template['template_taxonomies'])) {
							unset($template['template_taxonomies']['names'][$key]);
							unset($template['template_taxonomies']['values'][$key]);
						}
					}

				}
			}

			update_post_meta($to_id, 'ct_template_taxonomies', $template['template_taxonomies']);

			

			update_post_meta($to_id, 'ct_template_apply_if_post_of_parents', $template['template_apply_if_post_of_parents']);
			update_post_meta($to_id, 'ct_template_post_of_parents', $template['template_post_of_parents']);
			update_post_meta($to_id, 'ct_template_all_archives', $template['template_all_archives']);
			update_post_meta($to_id, 'ct_template_apply_if_archive_among_taxonomies', $template['template_apply_if_archive_among_taxonomies']);

			// match id to slug for each taxonomy
			if(isset($template['template_archive_among_taxonomies']) && is_array($template['template_archive_among_taxonomies'])) {
				foreach($template['template_archive_among_taxonomies'] as $key => $val) {
					// get id for the slug
					if(is_array($val)) {
						$term = get_term_by('slug', $val['slug'], $val['taxonomy']);	
						if($term) {
							$template['template_archive_among_taxonomies'][$key] = $term->term_id;
						}
						else {
							unset($template['template_archive_among_taxonomies'][$key]);
						}
					}

				}
			}

			update_post_meta($to_id, 'ct_template_archive_among_taxonomies', $template['template_archive_among_taxonomies']);
			update_post_meta($to_id, 'ct_template_apply_if_archive_among_cpt', $template['template_apply_if_archive_among_cpt']);
			update_post_meta($to_id, 'ct_template_archive_post_types', $template['template_archive_post_types']);
			// update_post_meta($to_id, 'ct_template_apply_if_archive_among_authors', $template['template_apply_if_archive_among_authors']);
			// update_post_meta($to_id, 'ct_template_authors_archives', $template['template_authors_archives']);
			update_post_meta($to_id, 'ct_template_date_archive', $template['template_date_archive']);
			update_post_meta($to_id, 'ct_template_front_page', $template['template_front_page']);
			update_post_meta($to_id, 'ct_template_blog_posts', $template['template_blog_posts']);
			update_post_meta($to_id, 'ct_template_search_page', $template['template_search_page']);
			update_post_meta($to_id, 'ct_template_404_page', $template['template_404_page']);
			update_post_meta($to_id, 'ct_template_index', $template['template_index']);

			update_post_meta($to_id, 'ct_template_inner_content', $template['ct_template_inner_content']);
			
		
		} else {
			
			$page = $pageData;
			$page['ID'] = intval($to_id);

			$shortcodes = parse_shortcodes($page['builder_shortcodes'], false, false);
			
			$wrap_shortcodes = array();

			if(is_array($shortcodes['content'])) {
				$shortcodes['content'] = $this->oxyTransferReplaceImages($shortcodes['content'], $imageMap);
				$shortcodes['content'] = $this->oxyTransferSwapIDs( $shortcodes['content'], $templates_id_map );

				if($global_colors_map) {
					$shortcodes['content'] = $this->oxyTransferSwapColors( $shortcodes['content']);
				}
			}

			$wrap_shortcodes['children'] = $shortcodes['content'];
			// code tree back to JSON to pass into old function
			$components_tree_json = json_encode( $wrap_shortcodes );
			
			ob_start();
		
			// transform JSON to shortcodes
			$shortcodes = components_json_to_shortcodes( $components_tree_json );
		
			// we don't need anything to be output by custom shortcodes
			ob_clean();

			update_post_meta($to_id, 'ct_builder_shortcodes', $shortcodes);
			
			if(isset($page['ct_connection_page_category'])) {
				update_post_meta($to_id, '_ct_connection_page_category', $page['ct_connection_page_category']);	
			}

			if(isset($page['other_template'])) {
				update_post_meta($to_id, 'ct_other_template', (isset($templates_id_map[$page['other_template']])?$templates_id_map[$page['other_template']]:$page['other_template']));
			}

			// cache styles
			oxygen_vsb_cache_page_css($to_id, $shortcodes);
		}



	}

	function oxyTransferImportPages($pages, $imageMap) {
		$new_id_map = array();

		$templates_id_map = get_transient('oxygen-vsb-templates-id-map');

		$current_user = wp_get_current_user();

		// insert posts
		foreach($pages as $page) {

			$post_data = $page;
			
			unset($post_data['ID']);

			$post_data['post_type'] = 'page';
			$post_data['post_status'] = 'publish';
			
			if($current_user && isset($current_user->ID)) {
				$post_data['post_author'] = $current_user->ID;
			}

			$new_id_map[$page['ID']] = wp_insert_post($post_data);
			
		}
		

		foreach($pages as $page) {
			
			// update parent status
			$post_data = array(
				'ID' => $new_id_map[$page['ID']],
				'post_parent' => $new_id_map[$page['post_parent']],
			);

			wp_update_post($post_data);

			// TODO run through the shortcodes, if some re-usable in place, replace its id from the $new_id_map
			$shortcodes = parse_shortcodes($page['builder_shortcodes'], false, false);
			
			$wrap_shortcodes = array();

			if(is_array($shortcodes['content'])) {
				$shortcodes['content'] = $this->oxyTransferReplaceImages($shortcodes['content'], $imageMap);
				$shortcodes['content'] = $this->oxyTransferSwapIDs( $shortcodes['content'], $templates_id_map );
			}

			$wrap_shortcodes['children'] = $shortcodes['content'];
			// code tree back to JSON to pass into old function
			$components_tree_json = json_encode( $wrap_shortcodes );
			
			ob_start();
		
			// transform JSON to shortcodes
			$shortcodes = components_json_to_shortcodes( $components_tree_json );
		
			// we don't need anything to be output by custom shortcodes
			ob_clean();

			update_post_meta($new_id_map[$page['ID']], 'ct_builder_shortcodes', $shortcodes);
			update_post_meta($new_id_map[$page['ID']], 'ct_other_template', (isset($templates_id_map[$page['other_template']])?$templates_id_map[$page['other_template']]:$page['other_template']));

			// cache styles
			oxygen_vsb_cache_page_css($new_id_map[$page['ID']], $shortcodes);
		}
	}

	function oxyTransferReplaceImages($items, $imageMap) {
		
		foreach ($items as $index => $item) {
			if(is_array($item)) {
				$items[$index] = $this->oxyTransferReplaceImages($item, $imageMap);
			} elseif($index === 'src' || $index === 'background-image') {
				$items[$index] = $imageMap[$item];
			}
		}

		return $items;

	}

	function oxyTransferImportData($data, $imageMap, $post_id = false) {
		// delete all the transients

		// generate the CSS styles
		global $oxygen_vsb_global_colors, $oxygen_vsb_css_classes;
		
		$dataExistingColors = get_option('oxygen_vsb_global_colors', array());
		$dataColors = $data['colors'];
		
		$global_colors_map = false;

		if(is_array($dataExistingColors) && $post_id) {
			$global_colors_map = array();
			foreach($dataColors['colors'] as $color) {
				$dataExistingColors['colors'][] = array(
					'id' => ++$dataExistingColors['colorsIncrement'],
					'name' => $color['name'],
					'value' => $color['value'],
					'sourceVal' => $color['sourceVal'],
					'set' => 0
				);

				$global_colors_map[$color['id']] = $dataExistingColors['colorsIncrement'];
			}

			$dataColors = $dataExistingColors;
		}

		if($global_colors_map) {
			OxyTransferColorsCallback::$global_colors_map = $global_colors_map;
		}

		update_option('oxygen_vsb_global_colors', $dataColors);
		
		$oxygen_vsb_global_colors = oxy_get_global_colors();
		
		update_option("oxygen_global_colors_cache_update_required", true);

		//$dataExistingStyleSheets = get_option('ct_style_sheets', array());
		$dataStylesheets = $data['stylesheets'];
		// if(is_array($dataExistingStyleSheets) && $post_id) {
		// 	$dataStylesheets = array_merge($dataExistingStyleSheets, $dataStylesheets);
		// }
		// 
		if(!$post_id)
			update_option('ct_style_sheets', $dataStylesheets);
		
		if(!$post_id) {
			update_option('ct_global_settings', $data['settings']);
		}
		
		$dataExistingStyleSets = get_option('ct_style_sets', array());
		$dataStyleSets = $data['stylesets'];
		if(is_array($dataExistingStyleSets) && $post_id) {
			$dataStyleSets = array_merge($dataExistingStyleSets, $dataStyleSets);
		}
		update_option('ct_style_sets', $dataStyleSets);

		
		$dataExistingSelectors = get_option('ct_custom_selectors', array());
		$dataSelectors =  $this->oxyTransferReplaceImages($data['selectors'], $imageMap);

		$dataSelectors = $this->oxyTransferSwapColors($dataSelectors);

		if(is_array($dataExistingSelectors) && $post_id) {
			$dataSelectors = array_merge($dataExistingSelectors, $dataSelectors);
		}
		update_option('ct_custom_selectors', $dataSelectors);
		
		$dataExistingClasses = get_option('ct_components_classes', array());
		$dataClasses = $this->oxyTransferReplaceImages($data['classes'], $imageMap);
		
		$dataClasses = $this->oxyTransferSwapColors($dataClasses);
		
		if(is_array($dataExistingClasses) && $post_id) {
			$dataClasses = array_merge($dataExistingClasses, $dataClasses);
		}
		update_option('ct_components_classes', $dataClasses);
		
		$oxygen_vsb_css_classes = $dataClasses;

		$singularData = $data['singularData'];

		$singularID = false;

		if($post_id && $singularData) {
			$singularID = intval($singularData['id']);
		}

		$this->oxyTransferImportTemplates($data['templates'], $imageMap, $singularID, $global_colors_map);

		if($singularID) {
			$singularPosts = array();
			
			switch($singularData['type']) {
				case 'page':
					$singularPosts = $data['pages'];
				break;
				case 'ct_template':
					$singularPosts = $data['templates'];
				break;
				case 'oxy_user_library':
					$singularPosts = $data['blocks'];
				break;
			}

			$this->oxyTransferImportSingular($singularPosts, $imageMap, $singularID, $post_id, $singularData['type'], $global_colors_map);
		}
		else {
			$this->oxyTransferImportPages($data['pages'], $imageMap);

			$this->oxyTransferImportBlocks($data['blocks'], $imageMap);
		}

		oxygen_vsb_cache_universal_css();
	}

	function oxyTransferExtractClasses($children) {

		$classes = array();
		if(is_array($children)) {
			foreach($children as $child) {

				if(isset($child['options']['classes'])) {
					foreach($child['options']['classes'] as $item) {
						if(is_string($item)) {
							$classes[$item] = false;
						}
					}
				}

				if(isset($child['children'])) {
					$classes = array_merge($classes, $this->oxyTransferExtractClasses($child['children']));
				}
			}
		}

		return $classes;
	}

	// converts relative url to absolute, credit goes to 'mys5droid at gmail dot com' https://www.php.net/manual/en/function.parse-url.php
	function relativeToAbsolute($inurl, $absolute) {
	    // Get all parts so not getting them multiple times :)
	    $absolute_parts = parse_url($absolute);   

	    // Test if URL is already absolute (contains host, or begins with '/')
	    if ( strpos($inurl, '://') === false) {

	        // Define $tmpurlprefix to prevent errors below
	        $tmpurlprefix = "";
	        // Formulate URL prefix    (SCHEME)                   
	        if (!(empty($absolute_parts['scheme']))) {
	            // Add scheme to tmpurlprefix
	            $tmpurlprefix .= $absolute_parts['scheme'] . "://";
	        }
	        // Formulate URL prefix (USER, PASS)   
	        if ((!(empty($absolute_parts['user']))) and (!(empty($absolute_parts['pass'])))) {
	            // Add user:port to tmpurlprefix
	            $tmpurlprefix .= $absolute_parts['user'] . ":" . $absolute_parts['pass'] . "@";   
	        }
	        // Formulate URL prefix    (HOST, PORT)   
	        if (!(empty($absolute_parts['host']))) {
	            // Add host to tmpurlprefix
	            $tmpurlprefix .= $absolute_parts['host'];
	            // Check for a port, add if exists
	            if (!(empty($absolute_parts['port']))) {
	                // Add port to tmpurlprefix
	                $tmpurlprefix .= ":" . $absolute_parts['port'];
	            }
	        }
	        // Formulate URL prefix    (PATH) and only add it if the path to image does not include ./   
	        if ( (!(empty($absolute_parts['path']))) and (substr($inurl, 0, 1) != '/') ) {
	            // Get path parts
	            $path_parts = pathinfo($absolute_parts['path']);
	            // Add path to tmpurlprefix
	            $tmpurlprefix .= $path_parts['dirname'];
	            $tmpurlprefix .= "/";
	        }
	        else {   
	            $tmpurlprefix .= "/";   
	        }   
	        // Lets remove the '/'
	        if (substr($inurl, 0, 1) == '/') { $inurl = substr($inurl, 1); }   
	        // Lets remove the './'
	        if (substr($inurl, 0, 2) == './') { $inurl = substr($inurl, 2); }   
	        return $tmpurlprefix . $inurl;
	    }   
	    else {
	        // Path is already absolute. Return it :)
	        return $inurl;
	    }
	}

	function oxyTransferExport() {
		error_reporting(E_ERROR);
		if(isset($_REQUEST['page']) && sanitize_text_field($_REQUEST['page']) == 'c_oxy_transfer' &&
			isset($_REQUEST['action']) && sanitize_text_field($_REQUEST['action']) == 'export') {

			$post_id = isset($_REQUEST['post'])? intval($_REQUEST['post']) : false;
			$post = false;

			if($post_id) {
				$post = get_post($post_id);
			}

			$packimages = isset($_REQUEST['packimages'])?true:false;

			$images = array();
			
			$colors = get_option('oxygen_vsb_global_colors');
			$stylesheets = get_option('ct_style_sheets');
			
			
			$settings = get_option('ct_global_settings');
			

			$stylesets = get_option('ct_style_sets');

			$classes = get_option('ct_components_classes');
			$selectors = get_option('ct_custom_selectors');

			


			// takes care of reusables as well
			$gotTemplates = $this->oxyTransferTemplates($post_id);

			$singularData = false;
			if($post) {
				$singularData = array('id' => $post->ID, 'type' => 'ct_template');
			}
			
			$gotPages = array();
			if(!$post || $post->post_type === 'page') {
				$gotPages = $this->oxyTransferPages($post_id);
				if($singularData) {
					$singularData['type'] = 'page';
				}
			}

			$gotBlocks = array();
			if(!$post || $post->post_type === 'oxy_user_library') {
				$gotBlocks = $this->oxyTransferBlocks($post_id);
				if($singularData) {
					$singularData['type'] = 'oxy_user_library';
				}
			}


			$templates = isset($gotTemplates['templates']) ? $gotTemplates['templates'] : array();
			$pages = isset($gotPages['pages']) ? $gotPages['pages'] : array();
			$blocks = isset($gotBlocks['blocks']) ? $gotBlocks['blocks'] : array();

			$applied_classes = array();

			if(isset($gotTemplates['classes'])) {
				$applied_classes = array_merge($applied_classes, $gotTemplates['classes']);
			}

			if(isset($gotPages['classes'])) {
				$applied_classes = array_merge($applied_classes, $gotPages['classes']);
			}
				
			if(isset($gotBlocks['classes'])) {
				$applied_classes = array_merge($applied_classes, $gotBlocks['classes']);
			}


			if($singularData) {
				// only use applied_classes
				$classes = array_intersect_key($classes, $applied_classes);
			}


			if(is_array($classes)) {
				$images = $this->oxyTransferImages($classes);
			}

			if(is_array($selectors)) {
				$images = array_merge($images, $this->oxyTransferImages($selectors));
			}

			if(isset($gotTemplates['images'])) {
				$images = array_merge($images, $gotTemplates['images']);
			}

			if(isset($gotPages['images'])) {
				$images = array_merge($images, $gotPages['images']);
			}

			if(isset($gotBlocks['images'])) {
				$images = array_merge($images, $gotBlocks['images']);
			}
			
			$images = array_unique($images);

			$localImages = array();

			$remoteImages = array();

			$siteURL = get_site_url();

			foreach($images as $image) {
				
				if($packimages === true) {
					
					$localImages[] = $image;

				} else {
					$remoteImages[] = $image;
				}
			}

			$output = array(
				'colors' => $colors,
				'stylesheets' => $stylesheets,
				'settings' => $settings,
				'stylesets' => $stylesets,
				'classes' => $classes,
				'selectors' => $selectors,
				'templates' => $templates,
				'pages' => $pages,
				'blocks' => $blocks,
				'localImages' => $localImages,
				'remoteImages' => $remoteImages
			);

			if($singularData) {
				$output['singularData'] = $singularData;
			}


			$zip = new ZipArchive();
			$tmp_file = tempnam('.', '');
			$zip->open($tmp_file, ZipArchive::CREATE);
			$output['packImageRelAbs'] = array();
			if(sizeof($localImages) > 0) {
				
				$index = 0;
				$fileLog = array();	

				foreach($localImages as $image) {
					
					// avoid some internal encoded images
					if(strpos($image, '[oxygen') === 0) {
						continue;
					}

					$absimage = $this->relativeToAbsolute($image, $siteURL);

					$download_file = file_get_contents($absimage);
					
					$zip->addFromString(basename($image), $download_file);
					
					$fileLog[$image] = $index;
					$output['packImageRelAbs'][$image] = $absimage;
					$index++;
				
				}

				$listFile = json_encode($fileLog);
				$zip->addFromString('fileLog.json', $listFile);

			}
			
			$zip->addFromString('data.json', json_encode($output));

			$zip->close();

				
			header('Content-disposition: attachment; filename="oxyTransferExportData.zip"');
			header('Content-type: application/zip');
			readfile($tmp_file);
			unlink($tmp_file);

			die();
		}

	}
}

$COxyTransfer = new C_Oxy_Transfer();

?>