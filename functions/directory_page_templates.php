<?php
/*	Return the Templates for Listing post type . 
	- We need to write separately because if template available in child theme then give first priority to child theme template,
	Then check if template located in main theme then call it , else plug-in will call its own template. */
/*
 * the function will return the archive page template.
 */
add_filter( "archive_template", "directory_get_archive_page_template",13) ;

function directory_get_archive_page_template($archive_template)
{
	global $wpdb,$wp_query,$post;
	$custom_post_type = apply_filters('directory_post_type_template',tevolution_get_post_type());

	if(is_archive() && (in_array(get_post_type(),$custom_post_type) || in_array($wp_query->query_vars['post_type'],$custom_post_type)))
	{
		/* auto detect mobile devices */
		$template = '/archive-'.$wp_query->query_vars['post_type'].'.php';
		if ( file_exists(STYLESHEETPATH . $template)) {

			$archive_template = STYLESHEETPATH . $template;

		} else if ( file_exists(TEMPLATEPATH . $template) ) {

			$archive_template = TEMPLATEPATH .$template;

		}else{
			if(file_exists(TEVOLUTION_DIRECTORY_DIR . 'templates'.$template))
				$archive_template = TEVOLUTION_DIRECTORY_DIR . 'templates/'.$template;
			else
				$archive_template = TEVOLUTION_DIRECTORY_DIR . 'templates/archive-listing.php';
		}
	}
	return $archive_template;
}

/*
 * Same Way This function will return the taxonomy/category page template.
 */ 
add_filter( "taxonomy_template", "directory_get_taxonomy_page_template",13) ;
function directory_get_taxonomy_page_template($taxonomy_template)
{
	global $wpdb,$wp_query,$post;
	/* fetch the current page taxonomy */

	$current_term = $wp_query->get_queried_object();
	$custom_taxonomy = apply_filters('directory_taxonomy_template',tevolution_get_taxonomy());

	if(in_array($current_term->taxonomy,$custom_taxonomy)  && $current_term->taxonomy!='ecategory' )
	{
		/* auto detect mobile devices */
		$template = '/taxonomy-'.$current_term->taxonomy.'.php';

		/* load templates from available directory */
		if ( file_exists(STYLESHEETPATH . $template)) {

			$taxonomy_template = STYLESHEETPATH . $template;

		} else if ( file_exists(TEMPLATEPATH . $template) ) {

			$taxonomy_template = TEMPLATEPATH . $template;

		}else{
			if(file_exists(TEVOLUTION_DIRECTORY_DIR . 'templates'.$template))
				$taxonomy_template = TEVOLUTION_DIRECTORY_DIR . 'templates'.$template;
			else
				$taxonomy_template = TEVOLUTION_DIRECTORY_DIR . 'templates/taxonomy-listingcategory.php';
		}

	}
    return $taxonomy_template;
}
/*
 * Same Way This function will return the taxonomy/tags page template.
 */
add_filter( "taxonomy_template", "directory_get_tag_page_template",11);
function directory_get_tag_page_template($tags_template)
{
	global $wpdb,$wp_query,$post;
	/*fetch the current page taxonomy*/
	$current_term = $wp_query->get_queried_object();
	$custom_taxonomy_tag = apply_filters('directory_tag_template',tevolution_get_taxonomy_tags());
	if(in_array($current_term->taxonomy,$custom_taxonomy_tag)  &&$current_term->taxonomy!='etags' )
	{
		/* auto detect mobile devices */		
		$template = '/taxonomy-'.$current_term->taxonomy.'.php';

		/* load templates from available directory */
		if ( file_exists(STYLESHEETPATH . $template)) {

			$tags_template = STYLESHEETPATH . $template;

		} else if ( file_exists(TEMPLATEPATH . $template) ) {

			$tags_template = TEMPLATEPATH . $template;

		}else{
		
			if(file_exists(TEVOLUTION_DIRECTORY_DIR . 'templates'.$template))
				$tags_template = TEVOLUTION_DIRECTORY_DIR . 'templates'.$template;
			else
				$tags_template = TEVOLUTION_DIRECTORY_DIR . 'templates/taxonomy-listingtags.php';
		}
	}
    return $tags_template;
}

/*
 * This function will return the template for detail page
 */ 
add_filter( "single_template", "directory_get_single_template",13) ;
function directory_get_single_template($single_template)
{	
	global $wpdb,$wp_query,$post;
	$custom_post_type = apply_filters('directory_post_type_template',tevolution_get_post_type());
	if(in_array(get_post_type(),$custom_post_type)  && get_post_type()!='event')
	{
		/* auto detect mobile devices */	
		$template = '/single-'.get_post_type().'.php';
		
		if ( file_exists(STYLESHEETPATH . $template)) {

			$single_template = STYLESHEETPATH . $template;

		} else if ( file_exists(TEMPLATEPATH . $template) ) {

			$single_template = TEMPLATEPATH . $template;

		}else{

			if(file_exists(TEVOLUTION_DIRECTORY_DIR . 'templates'.$template)){
				$single_template = TEVOLUTION_DIRECTORY_DIR . 'templates'.$template;
			}else{
				$single_template = TEVOLUTION_DIRECTORY_DIR . 'templates/single-listing.php';
			}
		}
	}	
    return $single_template;
}
/*
 * Search Page template for only tevolution custom post type
 */
add_filter( "search_template",'tevolution_get_search_template',11 );
function tevolution_get_search_template($search_template)
{	
	global $wpdb,$wp_query,$post;
	$post_type=get_query_var('post_type');   
   /* we add this condition because when we get the post type as an array in url ( specially on search page ) , the post type pass blank , so it's not call the taxonomy-search template , instead it call the default tevolution search template. */
    if(is_array($post_type) && count($post_type) == 1)
	{
		$post_type = $post_type[0];
	}
	/* get all tevolution post types */
	$custom_post_type = apply_filters('directory_post_type_template',tevolution_get_post_type());
	if(in_array($post_type,$custom_post_type)  && $post_type!='event')
	{
		if ( file_exists(STYLESHEETPATH . '/listing-search.php')) {

			$search_template = STYLESHEETPATH . '/listing-search.php';

		}else if ( file_exists(TEMPLATEPATH . '/listing-search.php') ) {

			$search_template = TEMPLATEPATH . '/listing-search.php';

		}else{
			$search_template = TEVOLUTION_DIRECTORY_DIR. 'templates/listing-search.php';
		}
	}
    return $search_template;
}

/* return the template for detail page */
add_action( 'get_template_part_directory','single_directory_listing_template_part',12,2);

function single_directory_listing_template_part($slug,$name)
{
	if ( file_exists(STYLESHEETPATH . "/{$slug}-{$name}.php")) {

		$single_template = STYLESHEETPATH . "/{$slug}-{$name}.php";			

	}else if(file_exists(TEMPLATEPATH."/{$slug}-{$name}.php")){

		$single_template = TEMPLATEPATH. "/{$slug}-{$name}.php";

	}else{
		$single_template = TEVOLUTION_DIRECTORY_DIR. "templates/directory-listing-single-content.php";
		include_once($single_template);
	}
}

/* get the category and tag page wise custom fields htmlvarname variable set */
add_action('directory_before_categories_title','directory_manager_listing_custom_field');
add_action('directory_before_archive_title','directory_manager_listing_custom_field');
function directory_manager_listing_custom_field(){
	global $wpdb,$post,$htmlvar_name,$pos_title;

	$post_type = (isset($_REQUEST['action']))? CUSTOM_POST_TYPE_LISTING : get_post_type();
	global $wpdb,$post,$posttitle,$htmlvar_name;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	$args = apply_filters('tmpl_dir_category_vars_arg',array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
							array(
									  'key'     => 'post_type_'.$post_type.'',
									  'value'   => array($post_type,'all'),
									  'compare' => 'IN',
									  'type'    => 'text'
							),
							array(
									  'key'     => 'is_active',
									  'value'   =>  '1',
									  'compare' => '='
							),
							array(
									  'key'     => 'show_on_listing',
									  'value'   =>  '1',
									  'compare' => '='
							)
				  ),
				'meta_key' => 'sort_order',
				'orderby' => 'meta_value',
				'order' => 'ASC'
		),$post_type);
          
	remove_all_actions('posts_where');
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	/* Set the results in transient to get fast results */
	if (get_option('tevolution_cache_disable')==1  && false === ( $post_query = get_transient( '_tevolution_query_taxo'.trim($post_type).trim($heading_key).$cur_lang_code  ) )  ) {
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_taxo'.trim($post_type).trim($heading_key).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');

	$htmlvar_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$label=get_post_meta($post->ID,'admin_title',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);

			$htmlvar_name[$post_name] = array( 'type'=>$ctype,
					'htmlvar_name'=> $post_name,
					'style_class'=> $style_class,
					'option_title'=> $option_title,
					'option_values'=> $option_values,
					'label'=> $post->post_title
			);
			$posttitle[] = $post->post_title;
		endwhile;
		wp_reset_query();
	}
	
	return $htmlvar_name;
}
/*
 * get the heading type of your post types. 
 */
function directory_fetch_heading_post_type($post_type){

	global $wpdb,$post,$heading_title;
	/*get the cusrrent language code if wpml plugin activate */
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';

	remove_all_actions('posts_where');
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	remove_action('pre_get_posts','location_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');
	$heading_title = array();
	$args=
	array(
	'post_type' => 'custom_fields',
	'posts_per_page' => -1	,
	'post_status' => array('publish'),
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'ctype',
			'value' => 'heading_type',
			'compare' => '=',
			'type'=> 'text'
		),
		array(
			'key' => 'post_type',
			'value' => $post_type,
			'compare' => 'LIKE',
			'type'=> 'text'
		)
	),
	'meta_key' => 'sort_order',
	'orderby' => 'meta_value_num',
	'meta_value_num'=>'sort_order',
	'order' => 'ASC'
	);
	$post_query = null;
	remove_all_actions('posts_orderby');
	/*check heading custom fields query set in transient */
	if (get_option('tevolution_cache_disable')==1 && false === ($post_query = get_transient( '_tevolution_query_heading'.trim($post_type).$cur_lang_code))){
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_heading'.trim($post_type).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	$post_meta_info = $post_query;
	if($post_meta_info){
		while ($post_meta_info->have_posts()) : $post_meta_info->the_post();
			$otherargs=
			array( 
			'post_type' => 'custom_fields',
			'posts_per_page' => -1	,
			'post_status' => array('publish'),
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'is_active',
					'value' => '1',
					'compare' => '=',
					'type'=> 'text'
				),
				array(
					'key' => 'heading_type',
					'value' => $post->post_title,
					'compare' => '=',
					'type'=> 'text'
				)
			));
			$other_post_query = null;
			$htmlvar_name=get_post_meta(get_the_ID(),'htmlvar_name',true);

			if (get_option('tevolution_cache_disable')==1 && false === ($other_post_query = get_transient( '_tevolution_query_heading'.trim($post_type).trim($htmlvar_name).$cur_lang_code ))  ) {
				$other_post_query = new WP_Query($otherargs);
				set_transient( '_tevolution_query_heading'.trim($post_type).trim($htmlvar_name).$cur_lang_code, $other_post_query, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){
				$other_post_query = new WP_Query($otherargs);
			}
			if(count($other_post_query->post) > 0)
			{
				$heading_title[$htmlvar_name] = $post->post_title;
			}
		endwhile;
		wp_reset_query();
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	return $heading_title;
}
?>