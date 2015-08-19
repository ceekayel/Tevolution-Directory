<?php
/*
WPDirectoryUpdates Plugin Updater Class
display plugin auto update notification in plugin section on backend
new WPUpdatesPluginUpdater( 'http://wp-updates.com/api/1/plugin', 1, plugin_basename(__FILE__) );
*/
if( !class_exists('WPDirectoryUpdates') ) {
    class WPDirectoryUpdates {
		
    	var $api_url;
    	var $plugin_path;
    	var $plugin_slug=TEVOLUTION_DIRECTORY_SLUG;
    	var $plugin_version=TEVOLUTION_DIRECTORY_VERSION;
    	function __construct( $api_url, $plugin_path ) {
			$plugin_path = plugin_dir_path( __FILE__ );
			$plugin_file = $plugin_path ."directory.php";
			$plugin_data = get_plugin_data( $plugin_file, $markup = true, $translate = true );
			$plugin_version = $plugin_data['Version'];
    		$this->api_url = $api_url;
    		$this->plugin_path = $plugin_path;
    		if(strstr($plugin_path, '/')) list ($t1, $t2) = explode('/', $plugin_path); 
    		else $t2 = $plugin_path;    		
    	
    		add_filter( 'pre_set_site_transient_update_plugins', array(&$this, 'directory_check_for_update') );
    		add_filter( 'plugins_api', array(&$this, 'directory_plugin_api_call'), 10, 3 );
		
			if ( is_network_admin() || !is_multisite() ) {			
				add_action('after_plugin_row_'.TEVOLUTION_DIRECTORY_SLUG, array(&$this, 'directory_plugin_row'),10,3);	  
			}
    	}
	
	
	/*
	 * add action for set the auto update for tevolution plugin
	 * Function Name: tevolution_plugin_row
	 * Return : Display the plugin new version update message
	 */
	function directory_plugin_row($plugin_file, $plugin_data,$status)
	{		
		/*check the remote version*/
		 global $plugin_response;	 
			$current = get_site_transient( 'update_plugins' );

			$r = @$current->response[ $plugin_file ];
			$remote_version = @$r->new_version;	  
		 if (version_compare($this->plugin_version , $remote_version, '<'))
		 {	
			$new_version = version_compare($this->plugin_version , $remote_version, '<') ? __('There is a new version of Tevolution-Directory plugin available ', 'templatic')  : '';
			  
			  $ajax_url = esc_url( add_query_arg( array( 'slug' => 'directory', 'action' => 'directory' , '_ajax_nonce' => wp_create_nonce( 'directory' ), 'TB_iframe' => true ,'width'=>500,'height'=>400), admin_url( 'admin-ajax.php' ) ) );
			  $file='DigitalDownloads/directory.php';
			  $download= wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=').$file, 'upgrade-plugin_' . $file);
			echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">' . $new_version .  ' <a href="'.$ajax_url.'" class="thickbox" title="Templatic directory Update">'.__('update now','templatic').'</a></div></td></tr>';
		
		 }
	}
    
    	function directory_check_for_update( $transient ) {
			
    		global $plugin_response,$wp_version;
			/* Transient checked email then return transient */
			if (empty($transient->checked)) return $transient;						
			$request_args = array(
				'slug' => $this->plugin_slug,
				'version' => $transient->checked[$this->plugin_slug]
				);
			$request_string = $this->directory_prepare_request( 'templatic_plugin_update', $request_args );			
			/*get the plugin auto update notification request */
			$raw_response = wp_remote_post( $this->api_url, $request_string );				
			$response = null;
			if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
				$response = json_decode($raw_response['body']);
		
			if( !empty($response) ) {/* Feed the update data into WP updater*/
				/* PLugin Auto Update request response set in transient*/
				$transient->response[$this->plugin_slug] = $response; 
				$plugin_response[$this->plugin_slug] = (array)$response; 
				update_option($this->plugin_slug.'_theme_version',$plugin_response);
			}
			return $transient;
    	}
    	
		/**
		 * Allows a plugin to override the WordPress.org Plugin Install API entirely.
		 * This function call for get the plugin update notification if they get the error from there then display plugins api failed and its correct then return request object
		 * Please ensure that an object is returned.
		 */
    	function directory_plugin_api_call( $def, $action, $args ) {			
    		if( !isset($args->slug) || $args->slug != $this->plugin_slug ) return $def;
    		
    		$plugin_info = get_site_transient('update_plugins');
    		$request_args = array(
    		    'slug' => $this->plugin_slug,
    			'version' => (isset($plugin_info->checked)) ? $plugin_info->checked[$this->plugin_path] : 0 /* Current version*/
    		);
    		
    		$request_string = $this->directory_prepare_request( $action, $request_args );
    		$raw_response = wp_remote_post( $this->api_url, $request_string );
    		if( is_wp_error($raw_response) ){
    			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'templatic-admin'), $raw_response->get_error_message());
    		} else {
    			$res = json_decode($raw_response['body']);
    			if ($res === false)
    				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred', 'templatic-admin'), $raw_response['body']);
    		}    		
    		return $res;
    	}
    	/* Create auto update request array */
    	function directory_prepare_request( $action, $args ) {
    		global $wp_version;
    		
    		return array(
    			'body' => array(
    				'action' => $action, 
    				'request' => serialize($args),
    				'api-key' => md5(home_url())
    			),
    			'user-agent' => 'WordPress/'. $wp_version .'; '. home_url()
    		);	
    	}
    
    
    }
}
?>