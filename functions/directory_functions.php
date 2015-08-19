<?php
/*
 * This file use for include plugin related js, css and inline script and use frontend related function.
 */
/* Script for detail page map and cookies js*/
add_action('wp_footer','directory_script_style');
function directory_script_style(){
	$custom_post_type = tevolution_get_post_type();
	if((is_archive() && in_array(get_post_type(),$custom_post_type)  && get_post_type()!='event' && !is_author())|| is_search()){
	 	wp_enqueue_script('directory-cookies-script', TEVOLUTION_DIRECTORY_URL.'js/jquery_cokies.js',array( 'jquery' ),'',false);
	}
	$custom_post_type = tevolution_get_post_type();
	if((is_single() || is_singular()) && (in_array(get_post_type(),$custom_post_type)  && get_post_type()!='event' )){
		wp_enqueue_script('jquery-ui-tabs');
		?>
       <script  type="text/javascript" async >
			jQuery(function() {
				jQuery('.listing-image a.listing_img').lightBox();
			});
			jQuery('.tabs').bind('tabsshow', function(event, ui) {
				if (ui.panel.id == "listing_map"){
					Demo.init();
				}
			});
			jQuery(function(){ var n=jQuery("ul.tabs li a, .tmpl-accordion dd a").attr("href");if(n=="#listing_map"){Demo.init();}})
			
			jQuery(function(){jQuery("ul.tabs li a, .tmpl-accordion dd a").live('click',function(){
				var n=jQuery(this).attr("href");if(n=="#listing_map"){Demo.init();}
			})});
		</script>
		<?php
	}
}

/* It Will Display the Directions map on detail page */
add_action('directory_single_page_map','directory_singlemap_after_post_content');
function directory_singlemap_after_post_content(){
	global $post,$templatic_settings,$tmpl_flds_varname;
	$templatic_settings=get_option('templatic_settings');

	if(is_single() && $templatic_settings['direction_map']=='yes'){
		$geo_latitude = get_post_meta(get_the_ID(),'geo_latitude',true);
		$geo_longitude = get_post_meta(get_the_ID(),'geo_longitude',true);
		$address = get_post_meta(get_the_ID(),'address',true);
		$map_type =get_post_meta(get_the_ID(),'map_view',true);
		$zooming_factor =get_post_meta(get_the_ID(),'zooming_factor',true);
		if($address && $tmpl_flds_varname['address']){
		?>
           <div id="directory_location_map" style="width:100%;">
                <div class="directory_google_map" id="directory_google_map_id" style="width:100%;"> 
                <?php include_once (TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/google_map_detail.php');?>
                </div>  <!-- google map #end -->
           </div>
		<?php
		}
	}
}
/*
 * Add class name related to directory addon on every page
 */
add_filter('body_class','directory_body_class',11,2);
function directory_body_class($classes,$class){
	$custom_post_type = apply_filters('directory_post_type_template',tevolution_get_post_type());
	if ( is_front_page() )
		$classes[] = 'tevolution-directory directory-front-page';
	elseif ( is_home() )
		$classes[] = 'tevolution-directory directory-home';
	elseif ( is_single() && get_post_type()==CUSTOM_POST_TYPE_LISTING || (isset($_REQUEST['page']) && $_REQUEST['page']=='preview'))
		$classes[] = 'tevolution-directory directory-single-page';
	elseif ( is_single() && (in_array(get_post_type(),$custom_post_type)  && get_post_type()!='event' )|| (isset($_REQUEST['page']) && $_REQUEST['page']=='preview'))
		$classes[] = 'tevolution-directory directory-single-page';
	elseif ( is_page() || isset($_REQUEST['page']) )
		$classes[] = 'tevolution-directory directory-page';
	elseif ( is_tax() )
		$classes[] = 'tevolution-directory directory-taxonomy-page';
	elseif ( is_tag() )
		$classes[] = 'tevolution-directory directory-tag-page';
	elseif ( is_date() )
		$classes[] = 'tevolution-directory directory-date-page';
	elseif ( is_author() )
		$classes[] = 'tevolution-directory directory-author-page';
	elseif ( is_search() )
		$classes[] = 'tevolution-directory directory-search-page';
	elseif ( is_post_type_archive() )
		$classes[] = 'tevolution-directory directory-post-type-page';
	elseif((isset($_REQUEST['page']) && $_REQUEST['page'] == "preview")  && isset($_POST['cur_post_type']) && $_POST['cur_post_type']==CUSTOM_POST_TYPE_LISTING)
	{
		$classes[] = 'tevolution-directory directory-single-page';
	}	
	return $classes;
}

/*
 * Add class name on container div
 */
function directory_class(){	
	echo get_directory_class();
}
function get_directory_class(){
	global $wpdb,$templatic_settings,$wp_query,$city_id;
	if($templatic_settings['pippoint_effects'] =='click')
	{ 
		$classes[]="wmap_static";
	}else{
		$classes[]="wmap_scroll";
	}
	$classes = apply_filters( 'get_directory_class', $classes);

	if(!empty($classes))
		$classes = join( ' ', $classes );
	return $classes;
}

/*
 * This function will return the results after drag the miles range slider
 */
add_action('wp_ajax_nopriv_listing_search','directory_listing_search');
add_action('wp_ajax_listing_search','directory_listing_search');
function directory_listing_search(){
	global $wp_query,$wpdb,$current_cityinfo;
        
        /* get all the custom fields which select as " Show field on listing page" from back end */
	
	if(function_exists('tmpl_get_category_list_customfields')){
		$posttype = $_REQUEST['posttype'];
		$htmlvar_name = tmpl_get_category_list_customfields($posttype);
	}else{
		global $htmlvar_name;
	}


	$per_page=get_option('posts_per_page');
	$paged = (isset($_REQUEST['page_num']) && $_REQUEST['page_num'] != '') ? $_REQUEST['page_num'] : 1;
	if(isset($_REQUEST['term_id']) && $_REQUEST['term_id']!=""){
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $_REQUEST['posttype'],'public'   => true, '_builtin' => true ));
		$args=array(
				 'post_type'      => $_REQUEST['posttype'],
				 'posts_per_page' => $per_page,
				 'paged' 		  => $paged,
				 'post_status'    => 'publish',
				 'tax_query'      => array(
										  array(
											 'taxonomy' => $taxonomies[0],
											 'field'    => 'id',
											 'terms'    => explode(',',$_REQUEST['term_id']),
											 'operator' => 'IN'
										  )
									   ),
				);

	}else{
		$args=array(
				 'post_type'      => $_REQUEST['posttype'],
				 'posts_per_page' => $per_page,
				 'paged' 		  => $paged,
				 'post_status'    => 'publish',
				 );
	}
	
	directory_manager_listing_custom_field();
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		add_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	
	add_action('pre_get_posts','directot_search_get_posts');
	add_filter( 'posts_where', 'directory_listing_search_posts_where', 10, 2 );
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		add_filter('posts_where', 'location_multicity_where');
	}
	$post_details= new WP_Query($args);
	
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		remove_filter('posts_where', 'location_multicity_where');
	}
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		remove_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	if ($post_details->have_posts()) :
		while ( $post_details->have_posts() ) : $post_details->the_post();
		
		/* 
                    loads template part for search result - loads template if it is available in theme otherwise it loads the template from perticuler plugins.
                    And template name should be "content-{your-posttype}.php"
                 */


                 if(function_exists('tmpl_wp_is_mobile') && tmpl_wp_is_mobile()){
                         if (locate_template('entry-mobile-' . $_REQUEST['posttype'] . '.php') != ''){
                                 get_template_part('entry-mobile', $_REQUEST['posttype']);
                         }else{
                                 do_action('get_template_part_tevolution-search','entry-mobile',$_REQUEST['posttype'],$htmlvar_name);
                         }
                 }else{
                         if (locate_template('entry-' . $_REQUEST['posttype'] . '.php') != ''){
                                 get_template_part('entry', $_REQUEST['posttype']);
                         }else{
                                 do_action('get_template_part_tevolution-search','entry',$_REQUEST['posttype'],$htmlvar_name);
                         }
                 }

                        
		endwhile;
		if($post_details->max_num_pages !=1):
		?>
		 <div id="list_paggination">
			  <div class="pagination pagination-position">
					<?php 
				
					$big = 999999999; /* need an unlikely integer */
					
						echo paginate_links( array(
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '/page/%#%',
							'current' => max( 1, $paged ),
							'total' => $post_details->max_num_pages,
							'before_page_number' => '<strong>',
							'after_page_number' => '</strong>',
							'prev_text'    => '<strong>'.__('Previous',SF_DOMAIN).'</strong>',
							'next_text'    => '<strong>'.__('Next',SF_DOMAIN).'</strong>',
							'type'         => 'plain',
						) );
					?>
			  </div>
		 </div>
		 <?php endif;
		wp_reset_query();
	else:
		?>
        <p class='nodata_msg'><?php _e( 'Apologies, but no results were found for the requested archive.', 'templatic' ); ?></p>
        <?php
	endif;
	exit;
}
function directot_search_get_posts($wp_query){
	$wp_query->set('is_archive',1);	
}

/*
 *This function will return the HTMl  after the filter results on category page ( like miles range )
 */
function directory_archive_search_listing($wp_query){

	add_filter( "pre_get_posts", "directot_search_get_posts" );
	global $post,$wp_query;
	$wp_query->set('is_ajax_archive',1);
	do_action('directory_before_post_loop');
	
	$featured=get_post_meta(get_the_ID(),'featured_c',true);
	$classes=($featured=='c')?'featured_c':'';
	?>
     <div class="post <?php echo $classes;?>">
        <?php do_action('directory_before_archive_image');/*do_action before the post image */

				do_action('directory_archive_page_image');

				do_action('directory_after_archive_image');/*do action after the post image */?>
		<div class="entry"> 
               <!--start post type title -->
               <?php do_action('directory_before_post_title');/* do action for before the post title.*/ ?>
               
				<div class="listing-wrapper">
						<!-- Entry title start -->
						<div class="entry-title">

						<?php do_action('templ_post_title');/* do action for display the single post title */?>

						</div>

						<?php do_action('directory_after_post_title');/* do action for after the post title.*/?>

						<!-- Entry title end -->

						<!-- Entry details start -->
						<div class="entry-details">

						<?php  /* Hook to get Entry details - Like address,phone number or any static field  */
						do_action('listing_post_info');   ?>

						</div>
						<!-- Entry details end -->
				</div>
				<!--Start Post Content -->
				<?php do_action('directory_before_post_content');       /* do action for before the post content. */ 
				$tmpdata = get_option('templatic_settings');
				if($tmpdata['listing_hide_excerpt']=='' || !in_array(get_post_type(),$tmpdata['listing_hide_excerpt'])){
                    if(function_exists('supreme_prefix')){
                         $theme_settings = get_option(supreme_prefix()."_theme_settings");
                    }else{
                         $theme_settings = get_option("supreme_theme_settings");
                    }
                    if($theme_settings['supreme_archive_display_excerpt']){
                         echo '<div itemprop="description" class="entry-summary">';
                         the_excerpt();
                         echo '</div>';
                    }else{
                         echo '<div itemprop="description" class="entry-content">';
                         the_content(); 
                         echo '</div>';
                    }
				}
               do_action('directory_after_post_content');        /* do action for after the post content. */?>
               <!-- End Post Content -->

               <!-- Show custom fields where show on listing = yes -->
               <?php do_action('directory_listing_custom_field');/*add action for display the listing page custom field */?>
              
               <?php do_action('templ_the_taxonomies');?>

               <?php do_action('directory_after_taxonomies');?>
        </div>
     </div>
     <?php do_action('directory_after_post_loop');
}

/*
 * Display edit link on front end - detail page when user logged in.
 */
add_action('directory_edit_link','directory_edit_link');
function directory_edit_link() {
	$post_type = get_post_type_object( get_post_type() );
	if ( !current_user_can( $post_type->cap->edit_post, get_the_ID() ) )
		return '';
	
	$args = wp_parse_args( array( 'before' => '', 'after' => ' ' ), @$args );
	echo $args['before'] . '<span class="edit"><a class="post-edit-link" href="' . esc_url( get_edit_post_link( get_the_ID() ) ) . '" title="' . sprintf( esc_attr__( 'Edit %1$s', 'templatic' ), $post_type->labels->singular_name ) . '">' . __( 'Edit', 'templatic' ) . '</a></span>' . $args['after'];
}
/*
 * Display the after directory header widget
 */
add_action('after_directory_header','after_directory_header');
function after_directory_header(){
	global $wp_query;
	$taxonomy_name=$wp_query->queried_object->taxonomy;
	if(is_archive() && !is_tax() && $wp_query->query['post_type'] != 'listing')
	{
		$posttype = get_post_type();
		$taxonomy_names = get_object_taxonomies( $posttype );
		$taxonomy_name = $taxonomy_names[0];
	}
	if($taxonomy_name == 'listingcategory' || $taxonomy_name == 'listingtags' || (is_archive() && !is_tax() && $wp_query->query['post_type'] == 'listing'))
	{
		$taxonomy_name = 'directory';
	}
	if ( is_active_sidebar( 'after_'.$taxonomy_name.'_header') ) : ?>
	<div id="category-widget" class="category-widget columns">
		<?php dynamic_sidebar('after_'.$taxonomy_name.'_header'); ?>
	</div>
	<?php endif;
}
/* Add add to favourite html for directory theme on listings page  */
function directory_favourite_html($user_id,$post)
{
	global $current_user,$post;
	$add_to_favorite = __('Add to favorites','templatic');
	$added = __('Added','templatic');
	if(function_exists('icl_register_string')){
		icl_register_string('templatic','directory'.$add_to_favorite,$add_to_favorite);
		$add_to_favorite = icl_t('templatic','directory'.$add_to_favorite,$add_to_favorite);
		icl_register_string('templatic','directory'.$added,$added);
		$added = icl_t('templatic','directory'.$added,$added);
	}
	$post_id = $post->ID;
	$user_meta_data = get_user_meta($current_user->ID,'user_favourite_post',true);
	if($post->post_type !='post'){
		do_action('tmpl_after_addtofav_link');
		if($user_meta_data && in_array($post_id,$user_meta_data))
		{
			?>
			<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav"  > <a href="javascript:void(0);" class="removefromfav" onclick="javascript:addToFavourite('<?php echo $post_id;?>','remove');"><?php echo $added;?></a></li>
			<?php
		}else{
		?>
			<li id="tmplfavorite_<?php echo $post_id;?>" class="fav_<?php echo $post_id;?> fav"><a href="javascript:void(0);" class="addtofav"  onclick="javascript:addToFavourite('<?php echo $post_id;?>','add');"><?php echo $add_to_favorite;?></a></li>
		<?php }
		do_action('tmpl_after_addtofav_link');
	}
}

/*
 * Display the category and tags on category page
 */
add_action('directory_the_taxonomies','directory_post_categories_tags');
function directory_post_categories_tags()
{
	global $wp_query, $post,$htmlvar_name,$tmpl_flds_varname;
	
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));
	$terms = get_the_terms($post->ID, $taxonomies[0]);
	$sep = ", ";
	$i = 0;
	if(!empty($terms) && (!empty($htmlvar_name['basic_inf']['category']) || !empty($tmpl_flds_varname['category']))){
    //if(!empty($terms)){     
		foreach($terms as $term){		
			if($i == ( count($terms) - 1)){
				$sep = '';
			}elseif($i == ( count($terms) - 2)){
				$sep = __(' and ','templatic');
			}
			$term_link = get_term_link( $term, $taxonomies[0] );
			if( is_wp_error( $term_link ) )
				continue;
			$taxonomy_category .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
			$i++;
		}
	
		echo '<p class="bottom_line"><span class="i_category">';
		echo '<span>'.__('Posted in','templatic').' '.$taxonomy_category.'</span>';
		echo '</span></p>';
	}
	global $post;
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post->post_type,'public'   => true, '_builtin' => true ));	

	$tag_terms = get_the_terms($post->ID, $taxonomies[1]);
	$sep = ",";
	$i = 0;
	if(!empty($tag_terms)  && (!empty($htmlvar_name['basic_inf']['post_tags']) || !empty($tmpl_flds_varname['post_tags'])))
	{

		foreach($tag_terms as $term){	
			if($i == ( count($tag_terms) - 1)){
				$sep = '';
			}elseif($i == ( count($tag_terms) - 2)){
				$sep = __(' and ','templatic');
			}
			$term_link = get_term_link( $term, $taxonomies[1] );
			if( is_wp_error( $term_link ) )
				continue;
			$taxonomy_tag .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
			$i++;
		}

	
		echo '<p class="bottom_line"><span class="i_category">';
		echo '<span>'.__('Tagged In ','templatic').$taxonomy_tag.'</span>';
		echo '</span></p>';
	}
}
/* 
 *Link of sample listings CSV
 */
add_action('tevolution_listing_sample_csvfile','tevolution_listing_sample_csvfile');
function tevolution_listing_sample_csvfile(){
	?>
	<a href="<?php echo TEVOLUTION_DIRECTORY_URL.'functions/listing_sample.csv';?>"><?php _e('(Sample csv file)','templatic');?></a>
	<?php	
}
/*
 *This function will return the search page map listings
 */
add_action('wp_ajax_nopriv_listing_search_map','directory_listing_search_map');
add_action('wp_ajax_listing_search_map','directory_listing_search_map');
function directory_listing_search_map(){
	global $wp_query,$wpdb,$current_cityinfo;

	$per_page=get_option('posts_per_page');
	if(isset($_REQUEST['term_id']) && $_REQUEST['term_id']!=""){
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => 'listing','public'   => true, '_builtin' => true ));
		$args=array(
				 'post_type'      => 'listing',
				 'posts_per_page' => $per_page,
				 'post_status'    => 'publish',
				 'tax_query'      => array(
										  array(
											 'taxonomy' => $taxonomies[0],
											 'field'    => 'id',
											 'terms'    => explode(',',$_REQUEST['term_id']),
											 'operator' => 'IN'
										  )
									   ),
				);

	}else{
		$args=array(
				 'post_type'      => 'listing',
				 'posts_per_page' => $per_page,
				 'post_status'    => 'publish',
				 );
	}
	directory_manager_listing_custom_field();
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		add_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	
	add_action('pre_get_posts','directot_search_get_posts');
	add_filter( 'posts_where', 'directory_listing_search_posts_where', 10, 2 );
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		add_filter('posts_where', 'location_multicity_where');
	}
	$post_details= new WP_Query($args);
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		remove_filter('posts_where', 'location_multicity_where');
	}
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		remove_filter('posts_where', 'wpml_listing_milewise_search_language');
	}
	$term_icon='';
	if(isset($_REQUEST['taxonomy']) && $_REQUEST['taxonomy']!='' && isset($_REQUEST['slug']) && $_REQUEST['slug']!=''){
		$term=get_term_by( 'slug',$_REQUEST['slug'] , $_REQUEST['taxonomy'] ) ;
		$term_icon=$term->term_icon;
	}
		
	if ($post_details->have_posts()) :
	$pids=array();
		while ( $post_details->have_posts() ) : $post_details->the_post();
			$ID =get_the_ID();
			$title = get_the_title($ID);
			$plink = get_permalink($ID);
			$lat = get_post_meta($ID,'geo_latitude',true);
			$lng = get_post_meta($ID,'geo_longitude',true);					
			$address = stripcslashes(str_replace($srcharr,$replarr,(get_post_meta($ID,'address',true))));
			$contact = str_replace($srcharr,$replarr,(get_post_meta($ID,'phone',true)));
			$website = get_post_meta($ID,'website',true);
			/*Fetch the image for display in map */
			if ( has_post_thumbnail()){
				$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');
				$post_images=$post_img[0];
			}else{
				$post_img = bdw_get_images_plugin($ID,'thumbnail');
				$post_images = $post_img[0]['file'];
			}

			$imageclass='';
			if($post_images)
				$post_image='<div class=map-item-img><img src='.$post_images.' width=150 height=150/></div>';
			else{
				$post_image='';
				$imageclass='no_map_image';
			}
			
			if($term_icon=='')
				$term_icon=apply_filters('tmpl_default_map_icon',TEVOLUTION_DIRECTORY_URL.'images/pin.png');
			
			$image_class=($post_image)?'map-image' :'';
			$comment_count= count(get_comments(array('post_id' => $ID)));
			$review=($comment_count ==1 )? __('review','templatic'):__('reviews','templatic');

			if(($lat && $lng )&& !in_array($ID,$pids))
			{ 	
				$retstr ='{';
				$retstr .= '"name":"'.$title.'",';
				$retstr .= '"location": ['.$lat.','.$lng.'],';
				$retstr .= '"message":"<div class=\"google-map-info '.$image_class.'\"><div class=map-inner-wrapper><div class=\"map-item-info '.$imageclass.'\">'.$post_image;
				$retstr .= '<h6><a href='.$plink.' class=ptitle><span>'.$title.'</span></a></h6>';
				if($address){$retstr .= '<p class=address>'.$address.'</p>';}
				if($contact){$retstr .= '<p class=contact>'.$contact.'</p>';}
				if($website){$retstr .= '<p class=website><a href= '.$website.'>'.$website.'</a></p>';}
				if($templatic_settings['templatin_rating']=='yes'){
					$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
					$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
				}else{
					$retstr .= apply_filters('show_map_multi_rating',get_the_ID(),$plink,$comment_count,$review);
				}
				$retstr .= '</div></div></div>';
				$retstr .= '",';
				$retstr .= '"icons":"'.$term_icon.'",';
				$retstr .= '"pid":"'.$ID.'"';
				$retstr .= '}';
				$content_data[] = $retstr;
				$j++;
			}

			$pids[]=$ID;
		endwhile;
		wp_reset_query();	
		
	endif;
	if($content_data)
		$cat_content_info[]= implode(',',$content_data);

	if($cat_content_info){
		$catinfo_arr= '{"markers":['.implode(',',$content_data)."]}";
	}else{
		$catinfo_arr= '{"markers":[]}';
	}
	echo $catinfo_arr;	
	exit;
}



/* Remove the listing post type from back end custom fields section - because we want to show the listing post type first on custom fields filter */

/* pass blank post type when tmpl_get_posttype() function is used to get post types */
add_filter('tmpl_custom_fields_filter','tmpl_custom_fields_filter_return');
add_action('tmpl_custom_fields_post_type','tmpl_custom_fields_post_type_return');

/* pass blank post type when get_option('tevolution_custom_post_type'); is used to get post types */
add_filter('tevolution_custom_post_type','tevolution_custom_post_type_return');

add_action('tmpl_before_author_page_posttype_tab','tmpl_before_author_page_posttype_tab_return');

/* Unset the listing post type in custom field section under Tevolution menu in backend */
function tmpl_custom_fields_filter_return($post_type){
	if(($key = array_search(CUSTOM_POST_TYPE_LISTING, $post_type)) !== false) {
		unset($post_type[$key]);
	}
	return $post_type;
}

/*unset the listing post type in author dashboard section on frontend and also call in home page google map */
function tevolution_custom_post_type_return($post_types){
	unset($post_types[CUSTOM_POST_TYPE_LISTING]);	
	return $post_types;
}

/* Add the listing tab FIRST in Manage custom fields section backend */
function tmpl_custom_fields_post_type_return(){
	global $wp_query;
	/* get the submit form page using post type wise */
	$args=array('s'=>'submit_form','post_type'=>'page','posts_per_page'=>-1,
				'meta_query'     => array('relation' => 'AND',
						   array('key' => 'submit_post_type','value' => CUSTOM_POST_TYPE_LISTING,'compare' => '='),
						   array('key' => 'is_tevolution_submit_form','value' => '1','compare' => '=')
						),
				);
	$post_query = new WP_Query($args);

	$obj = get_post_type_object( CUSTOM_POST_TYPE_LISTING);
	if($obj->labels->singular_name !=''){
		$submit_link='';
		if($post_query->have_posts()){
			while ($post_query->have_posts()) { $post_query->the_post();
				$submit_link='<a href="'.get_permalink().'" target="_blank" class="view_frm_link"><small>'.__(' View Form','templatic-admin').'</small></a>';
			}
		}
		if((isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']=='listing') || $_REQUEST['post_type_fields']==''){ $class="current"; }else{ $class=""; }
		if(!isset($_REQUEST['post_type_fields']) && $_REQUEST['post_type_fields']==''){
			$_REQUEST['post_type_fields'] = CUSTOM_POST_TYPE_LISTING;
		}
		?>
		<li><a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=custom_setup&amp;ctab=custom_fields&amp;post_type_fields=<?php echo CUSTOM_POST_TYPE_LISTING; ?>" class="<?php echo $class; ?>"><?php echo $obj->labels->singular_name; ?></a>(<?php echo $submit_link; ?>) </li>
	<?php
		}
}
/* return the listing tab first in author page */
function tmpl_before_author_page_posttype_tab_return(){
	global $current_user,$wp_query,$curauth,$wpdb;

	/* get current author informations - specially when logged out */
	$qvar = $wp_query->query_vars;
	$authname = $qvar['author_name'];
	$qvar = $wp_query->query_vars;
	$author = $qvar['author'];
	if(isset($author) && $author !='') :
		$curauth = get_userdata($qvar['author']);
	else :
		$curauth = get_userdata(intval($_REQUEST['author']));
	endif;

	$author_link=apply_filters('templ_login_widget_dashboardlink_filter',get_author_posts_url($curauth->ID));
	if(strpos($author_link, "?"))
		$author_link=apply_filters('templ_login_widget_dashboardlink_filter',get_author_posts_url($curauth->ID))."&";
	else
		$author_link=apply_filters('templ_login_widget_dashboardlink_filter',get_author_posts_url($curauth->ID))."?";
	
	$obj = get_post_type_object( CUSTOM_POST_TYPE_LISTING);

	if($obj->labels->singular_name !=''){
		if(!isset($_REQUEST['custom_post']) && $_REQUEST['custom_post']==''){
			$_REQUEST['custom_post'] = CUSTOM_POST_TYPE_LISTING;
		}
		$active_tab=(isset($_REQUEST['custom_post']) && CUSTOM_POST_TYPE_LISTING == $_REQUEST['custom_post']) ?'active':''; 
		?>
		<li class="tab-title <?php echo $active_tab;?>" role="presentational"><a href="<?php echo $author_link;?>custom_post=<?php  echo CUSTOM_POST_TYPE_LISTING;?>" ><?php  echo $obj->labels->singular_name; ?></a></li>
		<?php
	}
}

/* Display the listing post type first on home page map */
add_action('tmpl_before_map_post_type','tmpl_homepage_map_add_listing');
function tmpl_homepage_map_add_listing($post_info){
	
	global $city_category_id;
	if(in_array(CUSTOM_POST_TYPE_LISTING,$post_info)){		
		/* To Display the listing post type first on home page map */
		$tevolution_all_post= get_option('templatic_custom_post');
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => CUSTOM_POST_TYPE_LISTING,'public'   => true, '_builtin' => true ));
		?>
		<div class="mw_cat_title">
			<label><input type="checkbox" data-category="<?php echo str_replace("&",'&amp;',CUSTOM_POST_TYPE_LISTING).'categories';?>" onclick="newgooglemap_initialize(this,'');"  value="<?php echo str_replace("&",'&amp;',CUSTOM_POST_TYPE_LISTING);?>"  <?php if(!empty($_POST['posttype']) && !in_array(str_replace("&",'&amp;',CUSTOM_POST_TYPE_LISTING) ,$_POST['posttype'])):?> <?php else:?> checked="checked" <?php endif;?> class="<?php echo str_replace("&",'&amp;',CUSTOM_POST_TYPE_LISTING).'custom_categories';?>" id="<?php echo str_replace("&",'&amp;',CUSTOM_POST_TYPE_LISTING).'custom_categories';?>" name="posttype[]"> <?php echo ($tevolution_all_post[CUSTOM_POST_TYPE_LISTING]['label'])? $tevolution_all_post[CUSTOM_POST_TYPE_LISTING]['label']: ucfirst(CUSTOM_POST_TYPE_LISTING);?></label><span id='<?php echo CUSTOM_POST_TYPE_LISTING.'_toggle';?>' class="toggle_post_type toggleon" onclick="custom_post_type_taxonomy('<?php echo CUSTOM_POST_TYPE_LISTING.'categories';?>',this)"></span>
		</div>	
		 <div class="custom_categories <?php echo str_replace("&",'&amp;',CUSTOM_POST_TYPE_LISTING).'custom_categories';?>" id="<?php echo str_replace("&",'&amp;',CUSTOM_POST_TYPE_LISTING).'categories';?>" >
			 <?php homepage_map_wp_terms_checklist(0, array( 'taxonomy' =>$taxonomies[0],'post_type'=>CUSTOM_POST_TYPE_LISTING,'selected_cats'=>$city_category_id) );?>
		 </div>
		<?php
	}
}

/* remove transient for home page display opsts widget when save post so latest posts shoe first */
add_action('save_post','tmpl_remove_home_page_featured_');
function tmpl_remove_home_page_featured_(){
	global $wpdb;
	$wpdb->query($wpdb->prepare( "delete from $wpdb->options where option_name LIKE %s",'%tev_hdpw_%'));
}


/* add directory plugin widgets */
add_action('widgets_init','tmpl_directory_custom_widgets',99); // unregister display home page widget
function tmpl_directory_custom_widgets(){

	register_widget( 'TmplListingOwner');

}


/* Widget to display the agent details on detail page */
class TmplListingOwner extends WP_Widget {
	/*
	 * Register widget with WordPress.
	 */	
	function __construct() {
		$widget_ops = array('classname' => 'tmpl_listing_owner', 'description' => __('Display the agent details on property detail page sidebar.','templatic') );
		$this->WP_Widget('TmplListingOwner', __('T &rarr; Listing Owner','templatic-admin'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
	
		/* prints the widget*/
		extract($args, EXTR_SKIP);
		/* Show this widget only on preview page single page and on author page */
		
		if(isset($_REQUEST['p']) || (isset( $_REQUEST['page']) && $_REQUEST['page'] == 'preview') || is_author() || is_single()){
			echo $args['before_widget'];
			global $post,$curauth,$current_user;
			
			if(empty($curauth)){
				if($post->post_author !=''){
					$curauth = $post->post_author;
				}else{
					$curauth = $current_user->ID;
				}
				$curauth = get_userdata($curauth);
			} 
			$title = empty($instance['title']) ? 'Listing Owner' : apply_filters('widget_title', $instance['title']);
			
			if(@$_REQUEST['page'] == 'preview')
			 {
				global $current_user;
				$userid = $current_user->ID;
				$user_details = get_userdata( $userid );
				$property_id = $_SESSION['custom_fields']['cur_post_id'];
			 }elseif(is_author()){
				$author = get_userdata(get_query_var('author'));
				$userid = $author->ID;
				$user_details = get_userdata( $userid );
			 }else{
				if(isset($_REQUEST['p'])){
					$post = get_post(@$_REQUEST['p']);
				}
				
				$userid =  $post->post_author;
				$user_details = get_userdata( $userid );
				$property_id  = $post->ID;
			 }
			if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; } 
			$facebook = (get_user_meta( $userid,'facebook',true))?((strstr('http',get_user_meta( $userid,'facebook',true))) ? get_user_meta( $userid,'facebook',true) : $http.get_user_meta( $userid,'facebook',true)):'';
			$twitter = (get_user_meta( $userid,'twitter',true))?((strstr('http',get_user_meta( $userid,'twitter',true))) ? get_user_meta( $userid,'twitter',true) : $http.get_user_meta( $userid,'twitter',true)):'';
			$google = (get_user_meta( $userid,'user_google',true))?((strstr('http',get_user_meta( $userid,'user_google',true))) ? get_user_meta( $userid,'user_google',true) : $http.get_user_meta( $userid,'user_google',true)) : '';
			$website = (get_user_meta( $userid,'url',true))?((strstr('http',get_user_meta( $userid,'url',true))) ? get_user_meta( $userid,'url',true) : $http.get_user_meta( $userid,'url',true)):'';
			$phone = get_user_meta( $userid,'user_phone',true);
			echo $args['before_title'].$title.$args['after_title']; 
			
			/* Show the agent details on preview page */
			
			/* Fetch the details of user custom fields */
			$form_fields_usermeta=fetch_user_custom_fields();

			$submited_user_count = tevolution_get_posts_count($userid);
			
				?>
				<div class="tmpl-agent-details">
					<div class="agent-top_wrapper">
					  <div class="tmpl-agent-photo">
						<?php
						/* get user ID on preview page */
						if(!empty($_SESSION['custom_fields'])){
							$curauth = get_user_by( 'email', $_SESSION['custom_fields']['user_email'] );
							$user_details = get_user_by( 'email', $_SESSION['custom_fields']['user_email'] );
							$user_id = $curauth->ID;
						}
						/* get user ID on preview page end */
						
						if($form_fields_usermeta['profile_photo']['on_author_page']){
							if(get_user_meta($curauth->ID,'profile_photo',true) != ""){
								echo '<img src="'.get_user_meta($curauth->ID,'profile_photo',true).'" width="90px" alt="'.$curauth->display_name.'" title="'.$curauth->display_name.'" />';
							}else{
								echo get_avatar($curauth->ID, apply_filters('tev_agent_photo_size',90) ); 
							}
						}
						?>
					  </div>
					  <div class="tmpl-agent-detail-rt">
						<!-- Listing details -->
						<p class="title"><a href="<?php echo get_author_posts_url($user_details->ID); ?>"><strong><?php echo $user_details->display_name; ?></strong></a></p>
						<p><?php _e('Listing Owner','templatic');?></p>
					  </div>
					</div>
					<div class="auther-other-details">
						<?php /* About User */
						
						/* Display Phone Website */
						if($form_fields_usermeta['user_phone']['on_author_page'] && $phone ){ ?>
						<p><strong><?php _e('Phone','templatic'); ?>: </strong><?php echo $phone; ?></p>
						<?php } 
						
						/* Display User Description */
						if($form_fields_usermeta['description']['on_author_page'] && $user_details->description !=''){
						?>
							<p class="user_biography"><strong><?php _e('Profile','templatic'); ?>: </strong><?php echo $user_details->description; ?></p>
						<?php } ?>
						
						<p><strong><?php _e('Total Submissions','templatic'); ?>: </strong><a href="<?php echo get_author_posts_url($user_details->ID); ?>"><?php echo $submited_user_count; ?></a></p>
						<div class="enquiry-list"><a id="send_inquiry_id" title="Send Inquiry" href="javascript:void(0)" data-reveal-id="tmpl_send_inquiry" class="small_btn tmpl_mail_friend"><?php _e('Send inquiry','templatic');?></a></div>
					</div>
					<!-- Display user details -->
					<div class="agent-social-networks">
						<?php
						/* facebook link display */
						if($form_fields_usermeta['facebook']['on_author_page'] && $facebook){ ?>
							<a href="<?php echo $facebook; ?>"><i class="fa fa-facebook"></i></a>
						<?php }
						/* Twitter link display */
						if($form_fields_usermeta['twitter']['on_author_page'] && $twitter){ ?>
							<a href="<?php echo $twitter; ?>"><i class="fa fa-twitter"></i></a>
						<?php }
						/* Google Plus link display */
						if($form_fields_usermeta['user_google']['on_author_page'] && $google ){ ?>
							<a href="<?php echo $google; ?>"><i class="fa fa-google-plus"></i></a>
						<?php } ?>
					</div>
				</div>
			<?php
			echo $args['after_widget'];
		}
		
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;		
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	
	public function form( $instance ) {
		$instance = wp_parse_args((array)$instance, array('title' =>'') );
		$title = (strip_tags($instance['title'])) ? strip_tags($instance['title']) : __("Author",'templatic');
		?>
		<p>
		  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title','templatic'); ?>:
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		  </label>
		</p>
		<?php
	}
}
?>