<?php
global $wpdb,$pagenow;
$listing_post_type=0;
$custom_post_type_listing = CUSTOM_POST_TYPE_LISTING;
$custom_cat_type_listing = CUSTOM_CATEGORY_TYPE_LISTING;
$custom_tag_type_listing = CUSTOM_TAG_TYPE_LISTING;
$custom_post_types_args = array();
if(function_exists('tevolution_get_post_type'))
{
	$post_type_array = tevolution_get_post_type();
}
else
{
	$post_type_array = get_post_types($custom_post_types_args,'objects');
}
if(isset($_POST['reset_custom_fields']) && (isset($_POST['custom_reset']) && $_POST['custom_reset']==1))
{
	update_option('directory_custom_fields_update','none');
}
/*
 * Function Name: tevolution_event_taxonomy_msg
 *
 */
function tevolution_listing_taxonomy_msg(){
	echo '<div id="message" class="error below-h2">';
	echo '<form action="" method="post">';	
	echo "<p class='tevolution_desc'>".__('You have no listing post type now but your directory is in active status so you can generate listing post type again. ',DIR_DOMAIN);
	echo '<input type="submit" name="listing_post_type" value="'.__('Generate Listing Taxonomy',DIR_DOMAIN).'" class="button-primary">';
	echo '</p>';
	echo '<form>';
	echo '</div>';
}
if((isset($_REQUEST['page']) && $_REQUEST['page']=='custom_setup') && (isset($_POST['listing_post_type']))){	
	$listing_post_type=1;
}

if((isset($_REQUEST['ctab']) && $_REQUEST['ctab']=='custom_fields' && (isset($_POST['reset_custom_fields']) && $_REQUEST['post_type_fields'] == CUSTOM_POST_TYPE_LISTING)) || (@$_REQUEST['page']=='templatic_system_menu' || $pagenow=='themes.php' || $pagenow=='plugins.php') && (!in_array('listing',$post_type_array) && strtolower(get_option('tevolution_directory')) != strtolower('Active')) || $listing_post_type ==1)
{
	
	update_option('tevolution_directory','Active');
	
	/*Register Listing cutom post type */
	$post_arr_merge[$custom_post_type_listing] = array(  'label' 		=> CUSTOM_MENU_SIGULAR_NAME_LISTING,
										'labels' 			=> array( 'name' 			 =>  CUSTOM_MENU_SIGULAR_NAME_LISTING,
																'singular_name'  	 =>  CUSTOM_MENU_SIGULAR_NAME_LISTING,
																'menu_name'          =>  CUSTOM_MENU_NAME_LISTING,
																'all_items'          =>  CUSTOM_MENU_TITLE_LISTING,
																'add_new' 		 =>  CUSTOM_MENU_ADD_NEW_LISTING,
																'add_new_item' 	 =>  CUSTOM_MENU_ADD_NEW_ITEM_LISTING,
																'edit' 			 =>  CUSTOM_MENU_EDIT_LISTING,
																'edit_item' 		 =>  CUSTOM_MENU_EDIT_ITEM_LISTING,
																'new_item' 		 =>  CUSTOM_MENU_NEW_LISTING,
																'view_item'		 =>  CUSTOM_MENU_VIEW_LISTING,
																'search_items' 	 =>  CUSTOM_MENU_SEARCH_LISTING,
																'not_found' 		 =>  CUSTOM_MENU_NOT_FOUND_LISTING,
																'not_found_in_trash' =>  CUSTOM_MENU_NOT_FOUND_TRASH_LISTING	
															    ),
										'public' 			 => true,
										'has_archive'        => true,
										'can_export'		 => true,
										'show_ui' 		 => true, /* SHOW UI IN ADMIN PANEL */
										'_builtin' 		 => false, /* IT IS A CUSTOM POST TYPE NOT BUILT IN */
										'_edit_link' 		 => 'post.php?post=%d',
										'capability_type' 	 => 'post',
										'menu_icon' 		 => TEMPL_PLUGIN_URL.'/images/templatic-logo.png',
										'hierarchical' 	 => false,
										'rewrite' 		 => array("slug" => "$custom_post_type_listing"), /* PERMALINKS TO EVENT POST TYPE */
										'query_var' 		 => "$custom_post_type_listing", /* THIS GOES TO WPQUERY SCHEMA */
										'supports' 		 => array('title', 'author','excerpt','thumbnail','comments','editor','trackbacks','custom-fields','revisions') ,
										'show_in_nav_menus'	 => true ,
										'slugs'			 => array("$custom_cat_type_listing","$custom_tag_type_listing"),
										'taxonomies'		 => array(CUSTOM_MENU_SIGULAR_CAT_LISTING,CUSTOM_MENU_TAG_LABEL_LISTING)
									);
	$original = get_option('templatic_custom_post');
	if($original)	
		$post_arr_merge = array_merge($original,$post_arr_merge);
		
	ksort($post_arr_merge);
	update_option('templatic_custom_post',$post_arr_merge);
	/*END register listing custom post type */
	
	/* REGISTER CUSTOM TAXONOMY FOR POST TYPE EVENT */
	$original = array();
	$taxonomy_arr_merge[$custom_cat_type_listing] = array( "hierarchical" 	=> true, 
											    "label" 		=> CUSTOM_MENU_CAT_LABEL_LISTING, 
											    "post_type"	=> $custom_post_type_listing,
											    "post_slug"	=> $custom_post_type_listing,
											    'labels' 		=> array('name' 	         =>  CUSTOM_MENU_CAT_TITLE_LISTING,
																    'singular_name'     =>  $custom_cat_type_listing,
																    'search_items' 	    =>  CUSTOM_MENU_CAT_SEARCH_LISTING,
																    'popular_items'     =>  CUSTOM_MENU_CAT_SEARCH_LISTING,
																    'all_items' 	    =>  CUSTOM_MENU_CAT_ALL_LISTING,
																    'parent_item' 	    =>  CUSTOM_MENU_CAT_PARENT_LISTING,
																    'parent_item_colon' =>  CUSTOM_MENU_CAT_PARENT_COL_LISTING,
																    'edit_item' 	    =>  CUSTOM_MENU_CAT_EDIT_LISTING,
																    'update_item'	    =>  CUSTOM_MENU_CAT_UPDATE_LISTING,
																    'add_new_item' 	    =>  CUSTOM_MENU_CAT_ADDNEW_LISTING,
																    'new_item_name'     =>  CUSTOM_MENU_CAT_NEW_NAME_LISTING,
																 ), 
											    'public' 		=> true,
											    'show_ui' 		=> true,
											    'rewrite' 		 => array("slug" => "$custom_cat_type_listing"), /* PERMALINKS TO EVENT POST TYPE */
											);
	$original = get_option('templatic_custom_taxonomy');
	$tevolution_taxonomy_marker=get_option('tevolution_taxonomy_marker');
	if(empty($tevolution_taxonomy_marker)){
		update_option('tevolution_taxonomy_marker',array($custom_cat_type_listing=>'enable'));
	}else{
		update_option('tevolution_taxonomy_marker',array_merge($tevolution_taxonomy_marker,array($custom_cat_type_listing=>'enable')));
	}
	if($original)
		$taxonomy_arr_merge = array_merge($original,$taxonomy_arr_merge);
	
	ksort($taxonomy_arr_merge);
	update_option('templatic_custom_taxonomy',$taxonomy_arr_merge);
	/*EOF - REGISTER CUSTOM TAXONOMY FOR POST TYPE LISTING */
	
	
	/* REGISTER TAG FOR POST TYPE LISTING */
	$tag_arr_merge = array();
	$tag_arr_merge[$custom_tag_type_listing] =array("hierarchical" => false, 
									"label" 		=> CUSTOM_MENU_TAG_LABEL_LISTING, 
									"post_type"	=> $custom_post_type_listing,
									"post_slug"	=> $custom_post_type_listing,
									'labels' 		=> array( 'name' 			=>  CUSTOM_MENU_TAG_TITLE_LISTING,
														'singular_name' 	=>  $custom_tag_type_listing,
														'search_items' 	=>  CUSTOM_MENU_TAG_SEARCH_LISTING,
														'popular_items' 	=>  CUSTOM_MENU_TAG_POPULAR_LISTING,
														'all_items' 		=>  CUSTOM_MENU_TAG_ALL_LISTING,
														'parent_item' 		=>  CUSTOM_MENU_TAG_PARENT_LISTING,
														'parent_item_colon' =>  CUSTOM_MENU_TAG_PARENT_COL_LISTING,
														'edit_item' 		=>  CUSTOM_MENU_TAG_EDIT_LISTING,
														'update_item'		=>  CUSTOM_MENU_TAG_UPDATE_LISTING,
														'add_new_item' 	=>  CUSTOM_MENU_TAG_ADD_NEW_LISTING,
														'new_item_name' 	=>  CUSTOM_MENU_TAG_NEW_ADD_LISTING,	
													),  
									'public' 		=> true,
									'show_ui' 	=> true,
									'rewrite' 		 => array("slug" => "$custom_tag_type_listing"), /* PERMALINKS TO EVENT POST TYPE */
									);
	$original = get_option('templatic_custom_tags');
	if($original)	
		$tag_arr_merge = array_merge($original,$tag_arr_merge);
	ksort($tag_arr_merge);
	update_option('templatic_custom_tags',$tag_arr_merge);
	
}
/*
 * display event taxonomy generate when event taxonomy not exists 
 */
$post_type_arra=get_option('templatic_custom_post',@$post_arr_merge);
if(!array_key_exists('listing',$post_type_arra)){	
	add_action('tevolution_custom_taxonomy_msg','tevolution_listing_taxonomy_msg');
}

/* to auto insert default custom fields */

/* Insert Post heading type into posts which user can not delete 
   Note: don't delete this code or change it.
*/

global $wpdb,$pagenow,$table_name;
if(($pagenow=='plugins.php' || $pagenow=='themes.php' ||  (isset($_REQUEST['page']) && ($_REQUEST['page']=='custom_setup' || $_REQUEST['ctab']=='custom_fields' ))))
{
	$taxonomy_name = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_title = '[#taxonomy_name#]' and $wpdb->posts.post_type = 'custom_fields'");
	if(count($taxonomy_name) != 0)
	{
	 $post_type=get_post_meta($taxonomy_name->ID, 'post_type',true );
		if(!strstr($post_type,'listing'))
			update_post_meta($taxonomy_name->ID, 'post_type',$post_type.',listing' );
				
		update_post_meta($taxonomy_name->ID, 'is_submit_field',1);
		update_post_meta($taxonomy_name->ID, 'post_type_listing','listing' );
		update_post_meta($taxonomy_name->ID, 'taxonomy_type_listingcategory','listingcategory' );
		update_post_meta(@$taxonomy_name->ID, 'is_submit_field','1' );
		if(get_post_meta($taxonomy_name->ID,'listing_sort_order',true)){
			update_post_meta($taxonomy_name->ID, 'listing_sort_order',get_post_meta($taxonomy_name->ID,'listing_sort_order',true) );	
		}else{				
			update_post_meta($taxonomy_name->ID, 'listing_sort_order',1 );	
		}
	}

 
 
	/* Insert Post Category into posts */
	 $post_category = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'category' and $wpdb->posts.post_type = 'custom_fields'");
	 if(count($post_category) != 0)
	 {
		 $post_type=get_post_meta($post_category->ID, 'post_type',true );
			if(!strstr($post_type,'listing'))
				update_post_meta($post_category->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_category->ID, 'is_submit_field',1);
			update_post_meta($post_category->ID, 'post_type_listing','listing' );
			update_post_meta($post_category->ID, 'taxonomy_type_listingcategory','listingcategory' );
			update_post_meta($post_category->ID, 'is_submit_field','1' );
			if(get_post_meta($post_category->ID,'listing_sort_order',true)){
				update_post_meta($post_category->ID, 'listing_sort_order',get_post_meta($post_category->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($post_category->ID, 'listing_sort_order',2);
			}
			if(get_post_meta($post_category->ID,'heading_type',true)){
				update_post_meta($post_category->ID, 'listing_heading_type',get_post_meta($post_category->ID,'heading_type',true) );
			}else{
				update_post_meta($post_category->ID, 'listing_heading_type','[#taxonomy_name#]' );
			}
		 
	 }


	/* Insert Post title into posts */
	$post_title = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_title' and $wpdb->posts.post_type = 'custom_fields'");
	if(count($post_title) != 0)
	{
		$post_type=get_post_meta($post_title->ID, 'post_type',true );
		if(!strstr($post_type,'listing'))
			update_post_meta($post_title->ID, 'post_type',$post_type.',listing' );
				
		update_post_meta($post_title->ID, 'post_type_listing','listing' );
		update_post_meta($post_title->ID, 'taxonomy_type_listingcategory','listingcategory' );
		update_post_meta($post_title->ID, 'is_submit_field','1' );
		if(get_post_meta($post_title->ID,'listing_sort_order',true)){
			update_post_meta($post_title->ID, 'listing_sort_order',get_post_meta($post_title->ID,'listing_sort_order',true) );
		}else{
			update_post_meta($post_title->ID, 'listing_sort_order',3);
		}
		update_post_meta($post_title->ID, 'listing_sort_order',3);
		
		if(get_post_meta($post_title->ID,'listing_heading_type',true)){
			update_post_meta($post_title->ID, 'listing_heading_type',get_post_meta($post_title->ID,'listing_heading_type',true) );
		}else{
			update_post_meta($post_title->ID, 'listing_heading_type','[#taxonomy_name#]' );
		}
		update_post_meta($post_title->ID, 'listing_heading_type','[#taxonomy_name#]' );
	}
	/* Insert Post excerpt into posts */
	 $post_excerpt = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_excerpt' and $wpdb->posts.post_type = 'custom_fields'");
	 
 	 if(count($post_excerpt) != 0)
	 {
		 $post_type=get_post_meta($post_excerpt->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_excerpt->ID, 'post_type',$post_type.',listing' );
						
			update_post_meta($post_excerpt->ID, 'post_type_listing','listing' );
			update_post_meta($post_excerpt->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($post_excerpt->ID,'listing_sort_order',true) ){
				update_post_meta($post_excerpt->ID, 'listing_sort_order',get_post_meta($post_excerpt->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($post_excerpt->ID, 'listing_sort_order',10 );
			}
			if(get_post_meta($post_excerpt->ID,'listing_heading_type',true)){
				update_post_meta($post_excerpt->ID, 'listing_heading_type',get_post_meta($post_excerpt->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($post_excerpt->ID, 'listing_heading_type','Listing Information' );
				update_post_meta($post_excerpt->ID, 'is_submit_field','0' ); 
			}
	}
		 
	/* Insert Post content into posts */
	$post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_content' and $wpdb->posts.post_type = 'custom_fields'");
	if(count($post_content) != 0)
	{
	 $post_type=get_post_meta($post_content->ID, 'post_type',true );
		if(!strstr($post_type,'listing'))
			update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
				
		update_post_meta($post_content->ID, 'post_type_listing','listing' );
		update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
		if(get_post_meta($post_content->ID,'listing_sort_order',true) ){
			update_post_meta($post_content->ID, 'listing_sort_order',get_post_meta($post_content->ID,'listing_sort_order',true) );
		}else{
			update_post_meta($post_content->ID, 'listing_sort_order',11);
		}
		
		if(get_post_meta($post_content->ID,'listing_heading_type',true) || get_post_meta($post_content->ID,'listing_heading_type',true) != 'Listing Information'){ 
			update_post_meta($post_content->ID, 'listing_heading_type',get_post_meta($post_content->ID,'listing_heading_type',true) );
		}else{ 
			update_post_meta($post_content->ID, 'listing_heading_type','Listing Information' );
		}

	}
}	


/* Insert listing specific custom fields which user can delete */
	
if((isset($_REQUEST['page']) && $_REQUEST['page']=='custom_setup' && (isset($_POST['reset_custom_fields']) && $_REQUEST['post_type_fields'] == CUSTOM_POST_TYPE_LISTING))  || ((@$_REQUEST['page']=='templatic_system_menu'|| $pagenow=='themes.php' || $pagenow=='plugins.php')  && get_option('directory_custom_fields_update') !='inserted') ) 
{
	update_option('directory_custom_fields_update','inserted');
	/*Reset tevolution Custom Fields */
	if(isset($_POST['reset_custom_fields']) && ((isset($_POST['custom_reset']) && $_POST['custom_reset']==1) || (isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields'] !='')))
	{
		/* Reset the listing custom fields - when click on reset listing custom fields */
		if(isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields'] =='listing'){  
			$args=array('post_type'      => 'custom_fields',
			  'posts_per_page' => -1	,
			  'post_status'    => array('publish'),
			  'meta_key'       => 'post_type_'.$custom_post_type_listing,
			  'meta_value'     => $custom_post_type_listing,
			  'order'          => 'ASC'
			);
		}elseif(isset($_REQUEST['custom_reset']) && $_REQUEST['custom_reset'] !=''){
		/* Reset the listing custom fields - when click on reset ALL custom fields */
			$args=array('post_type'    => 'custom_fields',
			  'posts_per_page' => -1	,
			  'post_status'    => array('publish'),
			  'order'          => 'ASC'
			);
		}
			
		$custom_field = new WP_Query(@$args);

		$array_default_flds = array('post_title','post_content','category','post_images','post_excerpt','basic_inf');
		$array_default_flds = apply_filters('tmpl_default_flds',$array_default_flds);
		
		if($custom_field):
			while ($custom_field->have_posts()) : 
				global $post;
				$custom_field->the_post();
				if(!in_array($post->post_name,$array_default_flds )){
					wp_delete_post( get_the_ID(), true);
				}
			endwhile;
		endif;
	}
	/*Finish the reset all custom fields */
	
	/*Insert listing custom field */
	
	/* Here You have to pass "$exclude_post_types" same variable in other plugins as well.
		*/
		$exclude_post_type = apply_filters('reset_exclude_post_types',array());
		$cus_pos_type = get_option("templatic_custom_post");
		$post_type_arr='post,';
		$heading_post_type_arr='post,';
		if($cus_pos_type && count($cus_pos_type) > 0)
		{
			foreach($cus_pos_type as $key=> $_cus_pos_type)
			{
				if(!empty($exclude_post_type)){
					if(!in_array($key,$exclude_post_type)){
						$post_type_arr .= $key.",";
					}
				}else{
					$post_type_arr .= $key.",";
				}
				$heading_post_type_arr .= $key.",";
			}
		}
		$post_type_arr = substr($post_type_arr,0,-1);
		$heading_post_type_arr = substr($heading_post_type_arr,0,-1);

	
	/* Insert Locations Info heading into posts */
 	 $locations_info = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'locations_info' and $wpdb->posts.post_type = 'custom_fields'"); 	 
	
	 if(count($locations_info) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Locations & Map',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'locations_info',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'post_type'=> $post_type_arr,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'heading_type',
			'htmlvar_name'=>'locations_info',
			'field_category' =>'all',
			'is_active' => '1',
			'is_submit_field' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'sort_order' => '4',
			'listing_sort_order' => 4,
			'post_sort_order' => 4,
			'post_type_post'=>'post',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	 }else{
		$post_type=get_post_meta($locations_info->ID, 'post_type',true );
		
		if(!strstr($post_type,'listing'))
			update_post_meta($locations_info->ID, 'post_type',$post_type.',listing' );
				
		update_post_meta($locations_info->ID, 'is_submit_field',1);					
		update_post_meta($locations_info->ID, 'post_type_listing','listing' );					
		update_post_meta($locations_info->ID, 'post_type_listing','listing' );
		update_post_meta($locations_info->ID, 'taxonomy_type_listingcategory','listingcategory' );
		if(get_post_meta($locations_info->ID,'listing_sort_order',true)){
			update_post_meta($locations_info->ID, 'listing_sort_order',get_post_meta($locations_info->ID,'listing_sort_order',true) );
		}else{
			update_post_meta($locations_info->ID, 'listing_sort_order',4 );
		}
		update_post_meta($locations_info->ID, 'listing_sort_order',4 );
	 }
	/* insert multi city selection */
	$post_city_id = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_city_id' and $wpdb->posts.post_type = 'custom_fields'");
	 if(count($post_city_id) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Multi City',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'post_city_id',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Locations & Map',			
			'listing_heading_type' => 'Locations & Map',			
			'ctype'=>'multicity',
			'post_type'=> $post_type_arr,
			'post_type_property'=> $custom_post_type_listing,
			'htmlvar_name'=>'post_city_id',
			'field_category' =>'all',
			'is_active' => '1',
			'is_submit_field' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'sort_order' => '8',
			'listing_sort_order' => '8',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '0',
			'is_search'=>'1',
			'show_in_email'  =>'1',
			'post_sort_order'=>5,
			'post_heading_type' => 'Locations & Map',
			'is_delete' => '0'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
				global $sitepress;
				$current_lang_code= ICL_LANGUAGE_CODE;
				$default_language = $sitepress->get_default_language();	
				/* Insert wpml  icl_translations table*/				
				$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields', $post_id, $current_lang_code, $default_language );
				if(function_exists('wpml_insert_templ_post'))
					wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
		
		$post_types=get_option('templatic_custom_post');
		$posttype='post,';
		foreach($post_types as $key=>$val){
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $key,'public'   => true, '_builtin' => true ));	
			$posttype.=$key.',';
			$taxonomy = @$taxonomies[0];
			update_post_meta($post_id, 'post_type_'.$key,$key );
			update_post_meta($post_id, 'taxonomy_type_'.$taxonomy,$taxonomy );
			update_post_meta($post_id, $key.'_sort_order',get_post_meta($post_id, $key.'_sort_order',true));
		}
		update_post_meta($post_id, 'post_type_post','post' );
		update_post_meta($post_id, 'taxonomy_type_category','category');
		update_post_meta($post_id, 'post_type',substr($posttype,0,-1));
		update_post_meta($post_id, 'show_on_detail','1' );
		
		update_post_meta($post_id, 'post_type_listing','listing' );
		$post_type=get_post_meta($post_id, 'post_type',true );
			if(!strstr($post_type,'listing'))
				update_post_meta($post_id, 'post_type',$post_type.',listing' );
		update_post_meta($post_id, 'taxonomy_type_listingcategory','listingcategory' );
		update_post_meta($post_id, 'show_on_detail','1' );
		
	}else{
		
		 update_post_meta($post_city_id->ID, 'show_on_detail','1' );
		 
		 $post_type=get_post_meta($post_city_id->ID, 'post_type',true );
			if(!strstr($post_type,'listing'))
				update_post_meta($post_city_id->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_city_id->ID, 'is_submit_field',1);
			update_post_meta($post_city_id->ID, 'post_type_listing','listing' );
			update_post_meta($post_city_id->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($post_city_id->ID,'listing_sort_order',true)){
				update_post_meta($post_city_id->ID, 'listing_sort_order',get_post_meta($post_city_id->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($post_city_id->ID, 'listing_sort_order',5 );
			}
			
			if(get_post_meta($post_city_id->ID,'listing_heading_type',true) && get_post_meta($post_city_id->ID,'listing_heading_type',true) !='[#taxonomy_name#]'){
				update_post_meta($post_city_id->ID, 'listing_heading_type',get_post_meta($post_city_id->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($post_city_id->ID, 'listing_heading_type','Locations & Map' );
			}
			 
		 
	}
	
	/* Insert Post Geo Address into posts */
	 $address = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'address' and $wpdb->posts.post_type = 'custom_fields'"); 	
	 if(count($address) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Address',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'address',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Locations & Map',
			'listing_heading_type'=>'Locations & Map',
			'post_type'=> $post_type_arr,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'geo_map',
			'htmlvar_name'=>'address',
			'field_category' =>'all',
			'is_require' => '1',
			'sort_order' => '6',
			'listing_sort_order' => '6',
			'is_active' => '1',
			'is_submit_field' => '1',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '1',
			'is_edit' => 'false',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_on_success' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'post_sort_order' => 6,
			'post_type_post'=>'post',
			'post_heading_type' => 'Locations & Map',
			'field_require_desc' => __('Please Enter Address',DIR_DOMAIN),
			'validation_type' => 'require',
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	 }else{
			$post_type=get_post_meta($address->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($address->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($address->ID, 'is_submit_field',1 );
			update_post_meta($address->ID, 'post_type_listing','listing' );
			update_post_meta($address->ID, 'taxonomy_type_listingcategory','listingcategory' );		
			if(get_post_meta($address->ID,'listing_sort_order',true)){
				update_post_meta($address->ID, 'listing_sort_order',get_post_meta($address->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($address->ID, 'listing_sort_order',6 );
			}
			
			if(get_post_meta($address->ID,'listing_heading_type',true) && get_post_meta($address->ID,'listing_heading_type',true) !='[#taxonomy_name#]'){
				update_post_meta($address->ID, 'listing_heading_type',get_post_meta($address->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($address->ID, 'listing_heading_type','Locations & Map' );
			}
	 }	
	 /* Insert Post Google Map View into posts */
	 $map_view = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'map_view' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($map_view) == 0)
	 {
		$my_post = array(
			 'post_title' => '',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'map_view',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Locations & Map',
			'listing_heading_type'=>'Locations & Map',
			'post_type'=> $post_type_arr,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'radio',
			'htmlvar_name'=>'map_view',
			'sort_order' => '7',
			'listing_sort_order' => '7',
			'field_category' =>'all',
			'is_active' => '1',
			'is_submit_field' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'0',
			'show_on_success' => '0',
			'option_title'=>'Road Map,Terrain Map,Satellite Map,Street View',
			'option_values' => 'Road Map,Terrain Map,Satellite Map,Street Map',
			'default_value' =>'Road Map',
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
	 }else{
		$post_type=get_post_meta($map_view->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($map_view->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($map_view->ID, 'is_submit_field',1 );
			update_post_meta($map_view->ID, 'post_type_listing','listing' );
			update_post_meta($map_view->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($map_view->ID,'listing_sort_order',true)){
				update_post_meta($map_view->ID, 'listing_sort_order',get_post_meta($map_view->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($map_view->ID, 'listing_sort_order',7 );
			}
			
			if(get_post_meta($map_view->ID,'listing_heading_type',true) && get_post_meta($map_view->ID,'listing_heading_type',true) !='[#taxonomy_name#]'){
				update_post_meta($map_view->ID, 'listing_heading_type',get_post_meta($map_view->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($map_view->ID, 'listing_heading_type','Locations & Map' );
			}
	 }
	 	 
	
	 
	$listing_info = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'listing_info' and $wpdb->posts.post_type = 'custom_fields'"); 	 
	
	 if(count($listing_info) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Listing Information',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'listing_info',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'heading_type',
			'htmlvar_name'=>'listing_info',
			'field_category' =>'all',
			'sort_order' => '8',
			'listing_sort_order' => '8',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		wp_set_post_terms($post_id,'1','category',true);
		$post_id = wp_insert_post( $my_post );
		
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	 }else{
		$post_type=get_post_meta($listing_info->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($listing_info->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($listing_info->ID, 'is_submit_field',1);
			update_post_meta($listing_info->ID, 'post_type_listing','listing' );
			update_post_meta($listing_info->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($listing_info->ID,'listing_sort_order',true) ){
				update_post_meta($listing_info->ID, 'listing_sort_order',get_post_meta($listing_info->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($listing_info->ID, 'listing_sort_order',8);
			}			
	 }	
 
	 /* Insert Website into posts */
	 $listing_logo = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'listing_logo' and $wpdb->posts.post_type = 'custom_fields'");
	 
 	 if(count($listing_logo) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Logo',
			 'post_content' => 'Upload logo from your computer',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'listing_logo',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Listing Information',
			'listing_heading_type'=>'Listing Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'upload',
			'htmlvar_name'=>'listing_logo',
			'field_category' =>'all',
			'sort_order' => '9',
			'listing_sort_order' => '9',
			'is_active' => '1',
			'is_submit_field' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'false',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($listing_logo->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($listing_logo->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($listing_logo->ID, 'is_submit_field',1);
			update_post_meta($listing_logo->ID, 'post_type_listing','listing' );
			update_post_meta($listing_logo->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($listing_logo->ID,'listing_sort_order',true)){
				update_post_meta($listing_logo->ID, 'listing_sort_order',get_post_meta($listing_logo->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($listing_logo->ID, 'listing_sort_order',9);
			}
			if(get_post_meta($listing_logo->ID,'listing_heading_type',true)){
				update_post_meta($listing_logo->ID, 'listing_heading_type',get_post_meta($listing_logo->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($listing_logo->ID, 'listing_heading_type','Listing Information');
			}
	 }
	
	  /* Insert End Time into posts */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'listing_timing' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Time',
			 'post_content' => 'Enter business hours.<br>for example:<b>10.00-18.00 week days - Sunday closed</b>',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'listing_timing',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Listing Information',
			'listing_heading_type'=>'Listing Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'listing_timing',
			'field_category' =>'all',
			'is_require' => '0',
			'sort_order' => '12',
			'listing_sort_order' => '12',
			'is_active' => '1',
			'is_submit_field' => '1',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '1',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_on_success' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'field_require_desc' => '',
			'validation_type' => '',
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
	 }else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'is_submit_field',1);
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($post_content->ID,'listing_sort_order',true) ){
				update_post_meta($post_content->ID, 'listing_sort_order',get_post_meta($post_content->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($post_content->ID, 'listing_sort_order',12);
			}
			if(get_post_meta($post_content->ID,'listing_heading_type',true)){
				update_post_meta($post_content->ID, 'listing_heading_type',get_post_meta($post_content->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($post_content->ID, 'listing_heading_type','Listing Information');
			}
	 }
	 

	  /* Insert Post Contact Info heading into posts */
 	 $contact_info = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'contact_info' and $wpdb->posts.post_type = 'custom_fields'"); 	 
	
	 if(count($contact_info) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Contact Information',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'contact_info',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'heading_type',
			'htmlvar_name'=>'contact_info',
			'field_category' =>'all',
			'sort_order' => '15',
			'listing_sort_order' => '15',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		wp_set_post_terms($post_id,'1','category',true);
		$post_id = wp_insert_post( $my_post );
		
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	 }else{
		$post_type=get_post_meta($contact_info->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($contact_info->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($contact_info->ID, 'post_type_listing','listing' );
			update_post_meta($contact_info->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($contact_info->ID,'listing_sort_order',true) ){
				update_post_meta($contact_info->ID, 'listing_sort_order',get_post_meta($contact_info->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($contact_info->ID, 'listing_sort_order',15);
			}
			
			if(get_post_meta($contact_info->ID,'is_submit_field',true) ){
				update_post_meta($contact_info->ID, 'is_submit_field',get_post_meta($contact_info->ID,'is_submit_field',true) );
			}else{
				update_post_meta($contact_info->ID, 'is_submit_field',0);
			}
			update_post_meta($contact_info->ID, 'listing_heading_type',get_post_meta($contact_info->ID,'heading_type',true) );
	 }	
 
	/* Insert Listing contact information */
	 $phone = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'phone' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($phone) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Phone',
			 'post_content' => 'Enter phone or cell phone number.',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'phone',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'listing_heading_type'=>'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'phone',
			'field_category' =>'all',
			'sort_order' => '14',
			'listing_sort_order' => '14',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '1',
			'is_edit' => 'true',
			'is_search'=>'0',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_in_email'  =>'1',
			'field_require_desc' => __('Please enter phone number',DIR_DOMAIN),
			'validation_type' => 'require'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	
	 }else{
		$post_type=get_post_meta($phone->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($phone->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($phone->ID, 'post_type_listing','listing' );
			update_post_meta($phone->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($phone->ID,'listing_sort_order',true) ){
				update_post_meta($phone->ID, 'listing_sort_order',get_post_meta($phone->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($phone->ID, 'listing_sort_order',14);
			}
			if(get_post_meta($phone->ID,'listing_heading_type',true)){
				update_post_meta($phone->ID, 'listing_heading_type',get_post_meta($phone->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($phone->ID, 'listing_heading_type','Contact Information' );
			}
			
			if(get_post_meta($phone->ID,'is_submit_field',true)){
				update_post_meta($phone->ID, 'is_submit_field',get_post_meta($phone->ID,'is_submit_field',true) );
			}else{
				update_post_meta($phone->ID, 'is_submit_field',0);
			}
	 }
	 
	 /* Insert How to Register into posts */
	 $email = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'email' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($email) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Email',
			 'post_content' => 'Enter your email address.',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'email',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'listing_heading_type'=>'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'email',
			'field_category' =>'all',
			'sort_order' => '15',
			'listing_sort_order' => '15',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '0',
			'validation_type' =>'email',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1',
			'field_reduire_desc' => 'Please provide your email address',			
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 		
	 }else{
		$post_type=get_post_meta($email->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($email->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($email->ID, 'post_type_listing','listing' );
			update_post_meta($email->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($email->ID,'listing_sort_order',true) ){
				update_post_meta($email->ID, 'listing_sort_order',get_post_meta($email->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($email->ID, 'listing_sort_order',15);
			}
			if(get_post_meta($email->ID,'listing_heading_type',true)){
				update_post_meta($email->ID, 'listing_heading_type',get_post_meta($email->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($email->ID, 'listing_heading_type','Contact Information' );
			}
			
			if(get_post_meta($email->ID,'is_submit_field',true)){
				update_post_meta($email->ID, 'is_submit_field',get_post_meta($email->ID,'is_submit_field',true) );
			}else{
				update_post_meta($email->ID, 'is_submit_field',0 );
			}
	 }
	 
	 /* Insert Website into posts */
	 $website = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'website' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($website) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Website',
			 'post_content' => 'Enter website url for example as http://www.yoursite.com',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'website',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'listing_heading_type'=>'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'website',
			'field_category' =>'all',
			'sort_order' => '16',
			'listing_sort_order' => '16',
			'is_active' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_in_email'  =>'1',
			'is_submit_field'  =>'',
			'is_search'=>'0',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );	
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($website->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($website->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($website->ID, 'post_type_listing','listing' );
			update_post_meta($website->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($website->ID,'listing_sort_order',true) ){
				update_post_meta($website->ID, 'listing_sort_order',get_post_meta($website->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($website->ID, 'listing_sort_order',16);
			}
			if(get_post_meta($website->ID,'listing_heading_type',true)){
				update_post_meta($website->ID, 'listing_heading_type',get_post_meta($website->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($website->ID, 'listing_heading_type','Contact Information' );
			}
			
			if(get_post_meta($website->ID,'is_submit_field',true)){
				update_post_meta($website->ID, 'is_submit_field',get_post_meta($website->ID,'is_submit_field',true) );
			}else{
				update_post_meta($website->ID, 'is_submit_field',0);
			}
	 }
	 
	 /* Insert Twitter into posts */
	 $twitter = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'twitter' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($twitter) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Twitter',
			 'post_content' => 'Enter Twitter profile url for example as http://www.twitter.com/profile',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'twitter',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'listing_heading_type'=>'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'twitter',
			'field_category' =>'all',
			'sort_order' => '17',
			'listing_sort_order' => '17',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
	 	}
	 }else{
		$post_type=get_post_meta($twitter->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($twitter->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($twitter->ID, 'post_type_listing','listing' );
			update_post_meta($twitter->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($twitter->ID,'listing_sort_order',true) ){
				update_post_meta($twitter->ID, 'listing_sort_order',get_post_meta($twitter->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($twitter->ID, 'listing_sort_order',17);
			}
			if(get_post_meta($twitter->ID,'listing_heading_type',true)){
				update_post_meta($twitter->ID, 'listing_heading_type',get_post_meta($twitter->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($twitter->ID, 'listing_heading_type','Contact Information' );
			}
			
			if(get_post_meta($twitter->ID,'is_submit_field',true)){
				update_post_meta($twitter->ID, 'is_submit_field',get_post_meta($twitter->ID,'is_submit_field',true) );
			}else{
				update_post_meta($twitter->ID, 'is_submit_field','Contact Information' );
			}
	 }
	 
	 
	 
	 /* Insert Face book into posts */
	 $facebook = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'facebook' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($facebook) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Facebook',
			 'post_content' => 'Enter Facebook profile url for example as https://www.facebook.com/profile',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'facebook',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'listing_heading_type'=>'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'facebook',
			'field_category' =>'all',
			'sort_order' => '18',
			'listing_sort_order' => '18',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($facebook->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($facebook->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($facebook->ID, 'post_type_listing','listing' );
			update_post_meta($facebook->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($facebook->ID,'listing_sort_order',true) ){
				update_post_meta($facebook->ID, 'listing_sort_order',get_post_meta($facebook->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($facebook->ID, 'listing_sort_order',18);
			}
			if(get_post_meta($facebook->ID,'listing_heading_type',true)){
				update_post_meta($facebook->ID, 'listing_heading_type',get_post_meta($facebook->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($facebook->ID, 'listing_heading_type','Contact Information' );
			}
			
			if(get_post_meta($facebook->ID,'is_submit_field',true)){
				update_post_meta($facebook->ID, 'is_submit_field',get_post_meta($facebook->ID,'is_submit_field',true) );
			}else{
				update_post_meta($facebook->ID, 'is_submit_field',0);
			}
	 }
	 
	 
	  /* Insert Google Plus into posts */
	 $google_plus = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'google_plus' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($google_plus) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Google+ ',
			 'post_content' => ' Enter Google+ profile url for example as https://www.plus.google.com/profile',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'google_plus',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Contact Information',
			'listing_heading_type'=>'Contact Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'google_plus',
			'field_category' =>'all',
			'sort_order' => '19',
			'listing_sort_order' => '19',
			'is_active' => '1',
			'is_submit_field' => '0',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'show_on_success' => '1'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 		
	 }else{
		$post_type=get_post_meta($google_plus->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($google_plus->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($google_plus->ID, 'post_type_listing','listing' );
			update_post_meta($google_plus->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($google_plus->ID,'listing_sort_order',true) ){
				update_post_meta($google_plus->ID, 'listing_sort_order',get_post_meta($google_plus->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($google_plus->ID, 'listing_sort_order',19);
			}
			if(get_post_meta($google_plus->ID,'listing_heading_type',true)){
				update_post_meta($google_plus->ID, 'listing_heading_type',get_post_meta($google_plus->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($google_plus->ID, 'listing_heading_type','Contact Information' );
			}		
			
			if(get_post_meta($google_plus->ID,'is_submit_field',true)){
				update_post_meta($google_plus->ID, 'is_submit_field',get_post_meta($post_content->ID,'is_submit_field',true) );
			}else{
				update_post_meta($google_plus->ID, 'is_submit_field',0);
			}
	 }	 
	
	  /* Insert Tag Keyword */
	 $post_content = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_tags' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Tag Keyword',
			 'post_content' => 'Tags are short keywords, with no space within. Up to 40 characters only.',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'post_tags',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Listing Information',
			'listing_heading_type'=>'Listing Information',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'text',
			'htmlvar_name'=>'post_tags',
			'field_category' =>'all',
			'sort_order' => '20',
			'listing_sort_order' => '20',
			'is_active' => '0',
			'is_submit_field' => '0',
			'is_require' => '0',
			'show_on_page' => 'user_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 	}else{
		$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($post_content->ID,'listing_sort_order',true) ){
				update_post_meta($post_content->ID, 'listing_sort_order',get_post_meta($post_content->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($post_content->ID, 'listing_sort_order',19);
			}
			if(get_post_meta($post_content->ID,'listing_heading_type',true)){
				update_post_meta($post_content->ID, 'listing_heading_type',get_post_meta($post_content->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($post_content->ID, 'listing_heading_type','Listing Information' );
			}
			update_post_meta($post_content->ID, 'is_edit','true' );
	 }
	 
	  /* Insert Post Contact Info heading into posts */
 	 $field_label = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'field_label' and $wpdb->posts.post_type = 'custom_fields'"); 	 
	
	 if(count($field_label) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Label of Field',
			 'post_content' => '',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'field_label',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'heading_type',
			'htmlvar_name'=>'field_label',
			'field_category' =>'all',
			'sort_order' => '20',
			'listing_sort_order' => '20',
			'is_active' => '1',
			'is_submit_field' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'is_delete' => '0'
			);
		wp_set_post_terms($post_id,'1','category',true);
		$post_id = wp_insert_post( $my_post );
		
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		
		/*wp_set_post_terms($post_id,'1','category',true);*/
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 	 }else{
		$post_type=get_post_meta($field_label->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($field_label->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($field_label->ID, 'is_submit_field',1);
			update_post_meta($field_label->ID, 'post_type_listing','listing' );
			update_post_meta($field_label->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($field_label->ID,'listing_sort_order',true) ){
				update_post_meta($field_label->ID, 'listing_sort_order',get_post_meta($field_label->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($field_label->ID, 'listing_sort_order',20);
			}
			
	}	
 
	/*Insert post image */
	 $post_images = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'post_images' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($post_images) != 0)
	 {
		 $post_type=get_post_meta($post_images->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_images->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_images->ID, 'is_submit_field',1);
			update_post_meta($post_images->ID, 'post_type_listing','listing' );
			update_post_meta($post_images->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($post_images->ID,'listing_sort_order',true) ){
				update_post_meta($post_images->ID, 'listing_sort_order',get_post_meta($post_images->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($post_images->ID, 'listing_sort_order',18);
			}
			if(get_post_meta($post_images->ID,'listing_heading_type',true)){
				update_post_meta($post_images->ID, 'listing_heading_type',get_post_meta($post_images->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($post_images->ID, 'listing_heading_type','Label of Field');
			}
			update_post_meta($post_images->ID, 'listing_heading_type','Label of Field'); 
		 
	 }
 	  
	 /* Insert Video into posts */
	$post_content = $wpdb->get_row("SELECT ID,post_title,post_content FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'video' and $wpdb->posts.post_type = 'custom_fields'");
 	if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Video',
			 'post_content' => 'Please paste oEmbed video link url.',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'video',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Label of Field',
			'listing_heading_type'=>'Label of Field',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'oembed_video',
			'htmlvar_name'=>'video',
			'field_category' =>'all',
			'sort_order' => '19',
			'listing_sort_order' => '19',
			'is_active' => '1',
			'is_submit_field' => '1',
			'is_require' => '0',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'0',
			'is_delete' => '0'
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		{
			add_post_meta($post_id, $key, $_post_meta);
		}
 	}else{
			$post_type=get_post_meta($post_content->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($post_content->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($post_content->ID, 'is_submit_field',1);
			update_post_meta($post_content->ID, 'post_type_listing','listing' );
			update_post_meta($post_content->ID, 'taxonomy_type_listingcategory','listingcategory' );
			if(get_post_meta($post_content->ID,'listing_sort_order',true) ){
				update_post_meta($post_content->ID, 'listing_sort_order',get_post_meta($post_images->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($post_content->ID, 'listing_sort_order',19);
			} 
			if(get_post_meta($post_content->ID,'listing_heading_type',true) && get_post_meta($post_content->ID,'listing_heading_type',true) != '[#taxonomy_name#]'){
				update_post_meta($post_content->ID, 'listing_heading_type',get_post_meta($post_content->ID,'listing_heading_type',true) );
			}else{
				update_post_meta($post_content->ID, 'listing_heading_type','Label of Field');
			}
			
			$my_post = array(
			  'ID'           => $post_content->ID,
			  'post_title'  => 'Video',
			  'post_content' => 'Please paste oEmbed video link url.'
			);
			wp_update_post( $my_post );
					
	}
	  
	 /* Insert listing feature */
	 $proprty_feature = $wpdb->get_row("SELECT post_title,ID FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'proprty_feature' and $wpdb->posts.post_type = 'custom_fields'");
 	 if(count($proprty_feature) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Special Offers',
			 'post_content' => 'Enter any special offers (optional)',
			 'post_status' => 'publish',
			 'post_author' => 1,
			 'post_name' => 'proprty_feature',
			 'post_type' => "custom_fields",
			);
		$post_meta = array(
			'heading_type' => 'Label of Field',
			'listing_heading_type'=>'Label of Field',
			'post_type'=> $custom_post_type_listing,
			'post_type_listing'=> $custom_post_type_listing,
			'ctype'=>'texteditor',
			'htmlvar_name'=>'proprty_feature',
			'field_category' =>'all',
			'is_require' => '',
			'sort_order' => '19',
			'listing_sort_order' => '19',
			'is_active' => '1',
			'show_on_page' => 'both_side',
			'show_in_column' => '0',
			'show_on_listing' => '0',
			'is_edit' => 'true',
			'show_on_detail' => '1',
			'is_delete' => '0',
			'show_on_success' => '1',
			'is_search'=>'0',
			'show_in_email'  =>'1',
			'field_require_desc' => '',
			'validation_type' => ''
			);
		$post_id = wp_insert_post( $my_post );
		wp_set_post_terms($post_id,'1','category',true);
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			$current_lang_code= ICL_LANGUAGE_CODE;
			$default_language = $sitepress->get_default_language();	
			/* Insert wpml  icl_translations table*/
			$sitepress->set_element_language_details($post_id, $el_type='post_custom_fields',$post_id, $current_lang_code, $default_language );
			if(function_exists('wpml_insert_templ_post'))
				wpml_insert_templ_post($post_id,'custom_fields'); /* insert post in language */
		}
		foreach($post_meta as $key=> $_post_meta)
		 {
			add_post_meta($post_id, $key, $_post_meta);
		 }
 		
	 }else{
		$post_type=get_post_meta($proprty_feature->ID, 'post_type',true );
		 	if(!strstr($post_type,'listing'))
				update_post_meta($proprty_feature->ID, 'post_type',$post_type.',listing' );
					
			update_post_meta($proprty_feature->ID, 'post_type_listing','listing' );
			update_post_meta($proprty_feature->ID, 'taxonomy_type_listingcategory','listingcategory' );
			
			if(get_post_meta($proprty_feature->ID,'listing_sort_order',true)){
				update_post_meta($proprty_feature->ID, 'listing_sort_order',get_post_meta($post_content->ID,'listing_sort_order',true) );
			}else{
				update_post_meta($proprty_feature->ID, 'listing_sort_order',19);
			}
			if(!get_post_meta($proprty_feature->ID,'listing_heading_type',true) || get_post_meta($post_content->ID,'listing_heading_type',true) =='[#taxonomy_name#]' ){
				update_post_meta($proprty_feature->ID, 'listing_heading_type','Label of Field');
			}else{
				update_post_meta($proprty_feature->ID, 'listing_heading_type',get_post_meta($post_content->ID,'heading_type',true) );
			}
	 }
	

		/*Set the Submit listing page */
	 $post_content = $wpdb->get_row("SELECT post_title FROM $wpdb->posts WHERE $wpdb->posts.post_name = 'submit-listing' and $wpdb->posts.post_type = 'page'");
 	 if(count($post_content) == 0)
	 {
		$my_post = array(
			 'post_title' => 'Submit Listing',
			 'post_content' => "<p>This is your listing submission form from where users will be able to add their business listings on your website. 
			 You can manage which fields appear here from Tevolution>> Custom Fields section in backend. You can change this message by editing the Submit Listing page in WordPress pages section.</p> <p>One of the most important part for the business directory site is Submit listing page. Detailed information is available <a href='http://templatic.com/docs/directory-theme-guide/#frontend' targe='_blank'>here</a>. New user registration can be enabled from WordPress general settings -> 'Anyone can register' option. Enable the option</p>   [submit_form post_type='listing']",
			 'post_status' => 'publish',
			 'comment_status' => 'closed',
			 'post_author' => 1,
			 'post_name' => 'submit-listing',
			 'post_type' => "page",
			);		
		$post_id = wp_insert_post( $my_post );
		update_post_meta($post_id, '_wp_page_template','default' );
		update_post_meta($post_id, 'submit_post_type','listing' );
		update_post_meta($post_id, 'is_tevolution_submit_form','1' );


	 }
	 
	$tmpdata = get_option('templatic_settings');
	$templatic_settings['related_radius']='1000';
	update_option('templatic_settings',array_merge( $templatic_settings, $tmpdata ));
}
if(is_admin()){
	$results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type='monetization_package'");
	if(count($results)!=0 && !get_option('update_directory_price')){
		foreach($results as $res){			
			$package_post_type=get_post_meta($res->ID,'package_post_type',true);
			$package_post_type.=$custom_post_type_listing.',';
			update_post_meta($res->ID,'package_post_type',substr($package_post_type,0,-1));
			
		}
		update_option('update_directory_price',1);
	}
}
?>