<?php
global $wp_query,$wpdb;
/**-- condition for activate plugin --**/
update_option('no_alive_days',1);
if(@$_REQUEST['activated'] == 'tevolution_directory' && @$_REQUEST['true']==1)
{
	if(!is_plugin_active('Tevolution-Directory/directory.php'))
	{		
		$current = get_option( 'active_plugins' );
	     $plugin = plugin_basename( trim( 'Tevolution-Directory/directory.php') );	
	     if ( !in_array( $plugin, $current ) ) {
		   $current[] = $plugin;
		   sort( $current );		  
		   update_option( 'active_plugins', $current );		  
	     }
	}
	
	update_option('tevolution_directory','Active');
	update_option('monetization','Active');
	update_option('currency_symbol','$');
	update_option('currency_code','USD');
	update_option('currency_pos','1');
	update_option('templatic-login','Active');		
}else if(isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'tevolution_directory' && isset($_REQUEST['true']) && $_REQUEST['true']==0)
{
	delete_option('tevolution_directory');
}
?>