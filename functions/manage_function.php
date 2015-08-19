<?php
/*
 * This file use for to call/include plugin related php file and frontend related js or css file and manage comman function * 
 */
if(is_admin()){
	include_once(TEVOLUTION_DIRECTORY_DIR.'functions/manage_category_customfields.php'); 
}
include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_functions.php');
if(!is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX){
	include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_filters.php');
	include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_page_templates.php');
	include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_listing_functions.php');
	include_once(TEVOLUTION_DIRECTORY_DIR.'functions/directory_single_functions.php');	
}

/*call plug-in js and css file on admin_head and wp_head action*/
add_action('admin_head','manage_function_script');
add_action('wp_enqueue_scripts','manage_function_script',4);
add_action('init','directory_init_function',99);
function manage_function_script(){
	global $pagenow,$post,$wp_query;
	if(is_admin()){
		wp_enqueue_script('function_script',TEVOLUTION_DIRECTORY_URL.'js/function_script.js',array( 'jquery' ),'',false);
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
	}
	
}


/* Hook to pass the plugin css in tevolution main css - to merge all css in one file */
add_action('tevolution_css','tmpl_direcoty_addon_css',12); 

/* it will return the css name */
function tmpl_direcoty_addon_css(){
	global $tev_css;
	if (function_exists('tmpl_wp_is_mobile') &&  !tmpl_wp_is_mobile()) {
		if(!empty($tev_css)){
			$tev_css = array_merge($tev_css,array(TEVOLUTION_DIRECTORY_URL.'css/directory.css'));
		}else{
			$tev_css = array(TEVOLUTION_DIRECTORY_URL.'css/directory.css');
		}
	}
}
/*
 * add the image sizes for addon
 */
function directory_init_function(){
	add_image_size( 'directory-listing-image', 250, 165, true );
	add_image_size( 'directory_listing-image', 250, 165, true );
	add_image_size( 'directory-single-image', 300, 200, true );
	/* Register widgetized areas*/
	if ( function_exists('register_sidebar') )
	{
		register_sidebars(1,array('id' => 'after_directory_header', 'name' => __('Listing Category Pages - Below Header','templatic-admin'), 'description' => __('Use this area to show widgets between the secondary navigation bar and main content area on Listing category pages.','templatic-admin'),'before_widget' => '<div class="widget">','after_widget' => '</div>','before_title' => '<h3><span>','after_title' => '</span></h3>'));
	}
	remove_filter('the_content','view_sharing_buttons');
	remove_filter( 'the_content', 'view_count' );
	remove_action('tmpl_before_comments','single_post_categories_tags');
}
add_action('admin_init','tables_creatation');
function tables_creatation(){
	global $wpdb,$country_table,$zones_table,$multicity_table,$city_log_table,$pagenow;
	/* DOING_AJAX is define then return false for admin ajax*/
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {		
		return ;	
	}
	/*
	 * Create postcodes table and save the sorting option in templatic setting on plugin page or tevolution system menu page
	 */
	if($pagenow=='plugins.php' || $pagenow=='themes.php' || (isset($_REQUEST['page']) && ($_REQUEST['page']=='templatic_system_menu' || $_REQUEST['page']=='templatic_settings'))){
	
		/*MultiCity Table Creation BOF */
		$postcodes_table = $wpdb->prefix . "postcodes";	
		if($wpdb->get_var("SHOW TABLES LIKE \"$postcodes_table\"") != $postcodes_table) {
			$postcodes_table = "CREATE TABLE IF NOT EXISTS $postcodes_table (
			  pcid bigint(20) NOT NULL AUTO_INCREMENT,
			  post_id bigint(20) NOT NULL,
			  post_type varchar(100) NOT NULL,
			  address varchar(255) NOT NULL,
			  latitude varchar(255) NOT NULL,
			  longitude varchar(255) NOT NULL,
			  PRIMARY KEY (pcid)
			)DEFAULT CHARSET=utf8";
			$wpdb->query($postcodes_table);
		}
		/*directory Setting option */
		$templatic_settings=get_option('templatic_settings');
		if($templatic_settings=='' || empty($templatic_settings)){
			$templatic_settings=array('sorting_type'   => 'select',
								 'category_map'   => 'yes',
								 'sorting_option' => array('title_asc','title_desc','date_asc','date_desc','random','stdate_low_high','stdate_high_low'),
								 );
			
			update_option('templatic_settings',$templatic_settings);
		}/*finish directory setting option */
	}
}
/*
 * Function Name:directory_multisity_custom_field_save
 * Save the multisite id, country id, zone id when admin user update or new create listing.
 */
add_action('save_post','directory_multisity_custom_field_save',12);
function directory_multisity_custom_field_save($post_id){
	global $wpdb;
	$post_type= @$_POST['post_type'];
	if(isset($_REQUEST['page']) && $_REQUEST['page'] == 'bulk_upload' && is_admin())
		return;
	if(isset($_POST['country_id']) && $_POST['country_id']!="")
		update_post_meta($_POST['post_ID'],'country_id',$_POST['country_id']);
	if(isset($_POST['zones_id']) && $_POST['zones_id']!="")
		update_post_meta($_POST['post_ID'],'zones_id',$_POST['zones_id']);
	if(isset($_POST['post_city_id']) && $_POST['post_city_id']!=""){
		$post_city_id=$_POST['post_city_id'];
		if(is_array($post_city_id)){
			$post_city_id	=implode(',',$post_city_id);
		}
		update_post_meta($_POST['post_ID'],'post_city_id',$post_city_id);
	}
	
	$post_address = (isset($_POST['address']))? @$_POST['address']:@$_SESSION['custom_fields']['address'];
	$latitude = (isset($_POST['geo_latitude']))? @$_POST['geo_latitude']:@$_SESSION['custom_fields']['geo_latitude'];
	$longitude = (isset($_POST['geo_longitude']))? @$_POST['geo_longitude']:@$_SESSION['custom_fields']['geo_longitude'];
	$pID = (isset($_POST['post_ID']))?$_POST['post_ID'] : $post_id;
	$post_type=get_post_type( $pID );
	if($post_address && $latitude && $longitude){
		$postcodes_table = $wpdb->prefix . "postcodes";
		$pcid = $wpdb->get_var("select pcid from $postcodes_table where post_id = '".$pID."'");
		if($pcid){
			$postcodes_update = "UPDATE $postcodes_table set 
				post_type='".$post_type."',
				address = '".$post_address."',
				latitude ='".$latitude."',
				longitude='".$longitude."' where pcid = '".$pcid."' and post_id = '".$pID."'";
				$wpdb->query($postcodes_update);
		}else{
			$postcodes_insert = 'INSERT INTO '.$postcodes_table.' set 
					pcid="",
					post_id="'.$pID.'",
					post_type="'.$post_type.'",
					address = "'.$post_address.'",
					latitude ="'.$latitude.'",
					longitude="'.$longitude.'"';
					$wpdb->query($postcodes_insert);
		}
	}
}

/* 
 * Function Name: directory_import_insert_post
 * Return: insert postcodes table when import xml data by wordpress import plugin
 */
add_action('wp_import_insert_post','directory_import_insert_post',10,4);
function directory_import_insert_post($post_id, $original_post_ID, $postdata, $post){
	global $wpdb;
	foreach($post['postmeta'] as $key=>$val){
		if($val['key']=='address'){
			$post_address=$val['value'];
		}
		if($val['key']=='geo_latitude'){
			$latitude=$val['value'];
		}
		if($val['key']=='geo_longitude'){
			$longitude=$val['value'];
		}
	}
	/*check post address, latitude and longitude  */
	if($post_address && $latitude && $longitude){
		$postcodes_table = $wpdb->prefix . "postcodes";	
		$pcid = $wpdb->get_results($wpdb->prepare("select pcid from $postcodes_table where post_id = %d",$post_id));
		/* import post already import then update existing listing informationin wp_postcodes table */
		if(count($pcid)!=0){
			$wpdb->update($postcodes_table , array('post_type' => $post['post_type'],'address'=>$post_address,'latitude'=> $latitude,'longitude'=> $longitude), array('pcid' => $pcid,'post_id'=>$post_id) );
		}else{
			$wpdb->query( $wpdb->prepare("INSERT INTO $postcodes_table ( post_id,post_type,address,latitude,longitude) VALUES ( %d, %s, %s, %s, %s)", $post_id,$post['post_type'],$post_address,$latitude,$longitude ) );
		}
	}
}
?>