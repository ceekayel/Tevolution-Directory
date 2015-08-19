<?php
/* Pre get posts action call for call post_where and post_orderby filter on archive,search, category page and detail page */
add_action('pre_get_posts','directory_pre_get_posts',12);
function directory_pre_get_posts($query){	
	global $wp_query, $post,$current_cityinfo;	
	/*Call function for set the current city info variable */	
	$tmpdata = get_option('templatic_settings');
	if(!is_author() && !is_admin() && !is_search() && is_tax()){
		$current_term = $wp_query->get_queried_object();
	}
	if(!is_admin()){
		$custom_post_type = tevolution_get_post_type();
		$custom_taxonomy = tevolution_get_taxonomy();
		$custom_taxonomy_tag = tevolution_get_taxonomy_tags();
								
		add_filter('posts_where', 'directory_sortby_where');	
		if(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] !='' && (!is_search() && ! is_author())&&  (isset($query->query_vars['post_type']) && in_array($query->query_vars['post_type'],$custom_post_type)  && $query->query_vars['post_type']==CUSTOM_POST_TYPE_LISTING && is_archive() || 
			(is_archive() && ((isset($current_term->taxonomy) && in_array($current_term->taxonomy,$custom_taxonomy)  && @$current_term->taxonomy=='listingcategory' || in_array($current_term->taxonomy,$custom_taxonomy_tag)  && @!in_array($current_term->taxonomy,array('etags','ptags'))))))){
			add_filter('posts_orderby', 'directory_category_filter_orderby');
		}elseif(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] !='' && $query->query_vars['post_type']!= CUSTOM_POST_TYPE_LISTING && @$current_term->taxonomy !='listingcategory'){
			add_filter('posts_orderby', 'directory_category_filter_orderby');
		}
		/* Is search condition */
		if(is_search() && (isset($_GET['nearby']) && $_GET['nearby']=='search')){		
			add_filter('posts_where', 'directory_search_filter_nearby',12);
		}
		if(is_search() && (isset($_GET['adv_city']) && $_GET['adv_city']!='')){		
			add_filter('posts_where', 'directory_advancesearch_filter_multicity',20);		
		}
		
		if(is_search() && (isset($_REQUEST['post_type']) && $_REQUEST['post_type']==CUSTOM_POST_TYPE_LISTING)){	
			add_filter('posts_where', 'directory_sortby_where');
			add_filter('posts_orderby', 'directory_category_filter_orderby');
		
		}
	}
}
/*
 * display the category page listing by ordering wise.
 */
function directory_category_filter_orderby($orderby){
	global $wpdb,$wp_query;
	if (isset($_REQUEST['directory_sortby']) && ($_REQUEST['directory_sortby'] == 'title_asc' || $_REQUEST['directory_sortby'] == 'alphabetical'))
	{
		$orderby= "(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC, $wpdb->posts.post_title ASC";
		//$orderby= "$wpdb->posts.post_title ASC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'title_desc' )
	{
		$orderby = "$wpdb->posts.post_title DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'date_asc' )
	{
		$orderby = "$wpdb->posts.post_date ASC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'date_desc' )
	{
		$orderby = "$wpdb->posts.post_date DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'stdate_low_high' )
	{
		$orderby = "(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key like \"st_date\") ASC";
	}
	elseif (isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'stdate_high_low' )
	{
		$orderby = "(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key like \"st_date\") DESC";
	}elseif(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'random' )
	{
		$orderby = "(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_h') ASC,rand()";
	}elseif(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'reviews' )
	{
		$orderby = 'DESC';
		$orderby = " comment_count $orderby,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}elseif(isset($_REQUEST['directory_sortby']) && $_REQUEST['directory_sortby'] == 'rating' )
	{
		$orderby = " (select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id = $wpdb->posts.ID and $wpdb->postmeta.meta_key like \"average_rating\") DESC,(select distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where $wpdb->postmeta.post_id=$wpdb->posts.ID and $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC";
	}else{
		$orderby = " (SELECT distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_c' AND $wpdb->postmeta.meta_value = 'c') DESC, $wpdb->posts.post_date DESC";
	}
	return $orderby;
}
/*
 * search widget for fetch the near by post through address
 */
function directory_search_filter_nearby($where){
	global $wpdb,$wp_query,$current_cityinfo;
	$search = str_replace(' ','',$_REQUEST['location']);
	if($search){
		if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
		$arg=array('method' => 'POST',
			 'timeout' => 45,
			 'redirection' => 5,
			 'httpversion' => '1.0',
			 'blocking' => true,
			 'user-agent' => 'WordPress/'. $wp_version .'; '. home_url(),
			 'cookies' => array()
		);
		/* Get the google map data from search address*/
		$response = wp_remote_get($http.'maps.google.com/maps/api/geocode/json?address='.$search.'&sensor=false',$arg );		
		$output=json_decode($response['body']);
		if(!is_wp_error( $response ) ) {
			if(isset($output->results[0]->geometry->location->lat))
				$lat = $output->results[0]->geometry->location->lat;
			if(isset($output->results[0]->geometry->location->lng))
				$long = $output->results[0]->geometry->location->lng;
		}
		$miles = @$_REQUEST['radius'];
		$saddress = @$_REQUEST['location'];

		if(isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']== strtolower('Kilometer')){
			$miles = @$_REQUEST['radius'] / 0.621;
		}else{
			$miles = @$_REQUEST['radius'];
		}
		$tbl_postcodes = $wpdb->prefix . "postcodes";
		if($saddress &&  !isset($_REQUEST['radius'])){
			$where .= " AND ($wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key like 'address' and pm.meta_value like \"%$saddress%\") )";
		}elseif($saddress &&  (isset($_REQUEST['radius']) && $_REQUEST['radius']=='')){
			$where .= " AND ($wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key like 'address' and pm.meta_value like \"%$saddress%\") )";
		}
	}
	
	if($saddress=='' && !empty($current_cityinfo)){
		$lat=$current_cityinfo['lat'];
		$long=$current_cityinfo['lng'];
	}
	if(!empty($_REQUEST['post_type']) )
	{
		$post_type1='';		
		if(count($_REQUEST['post_type']) >1){
			$post_type = implode(",",$_REQUEST['post_type']);
		}else{
			$post_type = $_REQUEST['post_type'];
		}
		$post_type_array = explode(",",$post_type);
		$sep = ",";
		for($i=0;$i<count($post_type_array);$i++)
		{
			if($i == (count($post_type_array) - 1)){
				$sep = "";
			}
			if(isset($post_type_array[$i]))
			$post_type1 .= "'".$post_type_array[$i]."'".$sep;
		}
	}
	
	if($lat!='' && $long!='' && (isset($_REQUEST['radius']) && $_REQUEST['radius']!='')){
		if (function_exists('icl_register_string')) {
			if($lat !='' && $long !=''){
				$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE $tbl_postcodes.post_type in (".$post_type1.")  AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";
			}
			
		}else{
			
			if($lat !='' && $long !=''){
				$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE $tbl_postcodes.post_type in (".$post_type1.") AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";
			}
		}
	}else{
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php') && $current_cityinfo['city_id']!='')
		{
			$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$current_cityinfo['city_id'].", pm.meta_value ))";
		}
	}

	$tquery = tmpl_get_search_term_query(get_query_var('s'),'c.name');
	if(!empty($tquery)){

		if(isset($_SESSION['post_city_id']) && $_SESSION['post_city_id']!=''){
			if($saddress!=''){
				$cats = $wpdb->get_col("select tr.object_id from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p, $wpdb->postmeta pm,$wpdb->postmeta pm1 where ( {$tquery } ) and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' and p.post_type IN ('".str_replace(",","','",$post_type)."') and p.ID=pm.post_id AND p.ID=pm1.post_id and pm.meta_key ='post_city_id' and FIND_IN_SET( ".$_SESSION['post_city_id'].", pm.meta_value ) AND pm1.meta_key='address' AND pm1.meta_value like \"%$saddress%\" group by  p.ID");
			}else{
				$cats = $wpdb->get_col("select tr.object_id from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p, $wpdb->postmeta pm where ( {$tquery } ) and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' and p.post_type IN ('".str_replace(",","','",$post_type)."') and p.ID=pm.post_id and pm.meta_key ='post_city_id' and FIND_IN_SET( ".$_SESSION['post_city_id'].", pm.meta_value )  group by  p.ID");
			}
		}else{
			if($saddress!=''){
				$cats = $wpdb->get_col("select tr.object_id from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p, $wpdb->postmeta pm where ( {$tquery } ) and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' and p.post_type IN ('".str_replace(",","','",$post_type)."') and p.ID=pm.post_id AND pm.meta_key='address' AND pm.meta_value like \"%$saddress%\" group by  p.ID");
			}else{
				$cats = $wpdb->get_col("select tr.object_id from $wpdb->terms c,$wpdb->term_taxonomy tt,$wpdb->term_relationships tr,$wpdb->posts p where ( {$tquery } ) and c.term_id=tt.term_id and tt.term_taxonomy_id=tr.term_taxonomy_id and tr.object_id=p.ID and p.post_status = 'publish' and p.post_type IN ('".str_replace(",","','",$post_type)."')  group by  p.ID");
			}
		}
	}
	/*Added tagwise search*/
	if(!empty($cats))
		$srch_arr = implode(',',$cats);
	if($srch_arr !='')
	 $where .= " OR  ($wpdb->posts.ID in ($srch_arr))";
	return $where;
}
/*
 *  get the results on city wse search on advance search page
 */
function directory_advancesearch_filter_multicity($where){
	global $wpdb;
	if(isset($_GET['adv_city']) && $_GET['adv_city']!=''){
		$where .= " AND $wpdb->posts.ID in (select pm.post_id from $wpdb->postmeta pm where pm.meta_key ='post_city_id' and FIND_IN_SET( ".$_GET['adv_city'].", pm.meta_value ))";
	}
	return $where;
}
/*
 * search by alphabetical
 */
function directory_sortby_where($where){
	global $wpdb,$wp_query;	
	if(isset($_REQUEST['sortby']) && $_REQUEST['sortby']!=''){
		$where .= "  AND $wpdb->posts.post_title like '".$_REQUEST['sortby']."%'";
	}
	return $where;
}
/*
 * find near my listing from current city
 */
function directory_listing_search_posts_where($where){
	global $wpdb,$wp_query,$post,$current_cityinfo;

	if(isset($_REQUEST['range_address']) && $_REQUEST['range_address']!=''){
		if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
		$search = str_replace(' ','',$_REQUEST['range_address']);
		$response = wp_remote_get($http.'maps.google.com/maps/api/geocode/json?address='.$search.'&sensor=false');
		$output=json_decode($response['body']);
		if(!is_wp_error( $response ) ) {
			if(isset($output->results[0]->geometry->location->lat))
				$lat = $output->results[0]->geometry->location->lat;
			if(isset($output->results[0]->geometry->location->lng))
				$long = $output->results[0]->geometry->location->lng;
		}
	}else{
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php')){
			$ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			$url = "http://freegeoip.net/json/$ip";
			$data=wp_remote_get( $url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );
			if($data){
				$location = json_decode($data['body']);
				$lat = $location->latitude;
				$long = $location->longitude;
			}
		}else{
		
			$city_map_setting=get_option('city_googlemap_setting');
			$lat = ($current_cityinfo['lat'])? $current_cityinfo['lat']:$city_map_setting['map_city_latitude'];
			$long = ($current_cityinfo['lng'])?$current_cityinfo['lng'] :$city_map_setting['map_city_longitude'];
		}
	}
	if ($lat!='' && $long!='') {
		$lat = $lat;
		$long = $long;
	}else{
		$city_map_setting=get_option('city_googlemap_setting');
		$lat = ($current_cityinfo['lat'])? $current_cityinfo['lat']:$city_map_setting['map_city_latitude'];
		$long = ($current_cityinfo['lng'])?$current_cityinfo['lng'] :$city_map_setting['map_city_longitude'];
	}

	$miles_range=explode('-',$_REQUEST['miles_range']);
	$defaul_range=explode('-',$_REQUEST['defaul_range']);

	if(!empty($miles_range)){
		$to_miles = (trim($miles_range[0]))? trim($miles_range[0])  : '0';
		$miles = (trim($miles_range[1])) ? trim($miles_range[1]) : '1000';
	}
	/*  convert miles in to kilometer */
	if(isset($_REQUEST['radius_measure']) && strtolower($_REQUEST['radius_measure'])== strtolower('kilometer')){
		$miles = $miles * 0.621;
		$to_miles = $to_miles * 0.621;
	}
	$tbl_postcodes = $wpdb->prefix . "postcodes";
	if($lat !='' && $long !='' && (trim($miles_range[0])!='1' || $defaul_range[1]!=$miles_range[1])){
		$where .= " AND ($wpdb->posts.ID in (SELECT post_id FROM  $tbl_postcodes WHERE truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) <= ".$miles." AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) >= ".$to_miles." ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('".$lat."')) + cos(radians(`latitude`)) * cos( radians('".$lat."')) * cos( radians(`longitude` - '".$long."') ) ) ) * 69.09),1) ASC))";
	}
	return $where;
}
/*
 * Shows the different orders on home page featured widget
 */
function directory_feature_listing_orderby($orderby){
	global $wpdb,$wp_query,$post,$current_cityinfo;
	$tmpdata = get_option('templatic_settings');
	$order = $tmpdata['tev_front_page_order'];
	if($order =='asc'){
		$orderby=" (SELECT distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_h' AND $wpdb->postmeta.meta_value = 'h') DESC, $wpdb->posts.post_title ASC";
	}elseif($order =='desc'){
		$orderby=" (SELECT distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_h' AND $wpdb->postmeta.meta_value = 'h') DESC, $wpdb->posts.post_title DESC";
	}elseif($order =='dasc'){
		$orderby=" (SELECT distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_h' AND $wpdb->postmeta.meta_value = 'h') DESC, $wpdb->posts.post_date ASC";
	}elseif($order =='random'){
		$orderby=" (SELECT distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_h' AND $wpdb->postmeta.meta_value = 'h') DESC, rand()";
	}else{
		$orderby=" (SELECT distinct $wpdb->postmeta.meta_value from $wpdb->postmeta where ($wpdb->posts.ID = $wpdb->postmeta.post_id) AND $wpdb->postmeta.meta_key = 'featured_h' AND $wpdb->postmeta.meta_value = 'h') DESC, $wpdb->posts.post_date DESC";
	}
	return $orderby;
}

/* language code add in post where filter if wpml plugin activate */
function wpml_listing_milewise_search_language($where)
{
	$language = ICL_LANGUAGE_CODE;
	$where .= " and t.language_code='".$language."'";
	return $where;
}
?>