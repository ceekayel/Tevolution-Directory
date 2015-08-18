<?php
/*
 * This file user for listing category, listing archive and listing tags page
 */

add_action('directory_before_container_breadcrumb','directory_breadcrumb');
/*
 * display the bread crumb
 * Function Name:single_post_type_breadcrumb 
 */
if(!function_exists('directory_breadcrumb')){
	function directory_breadcrumb()
	{
		if ( current_theme_supports( 'breadcrumb-trail' ) && supreme_get_settings('supreme_show_breadcrumb')){
			breadcrumb_trail( array( 'separator' => '&raquo;' ) );
		}
	}
}

/* Show sub categories on category listing page */
add_action('directory_subcategory','directory_subcategory');

add_action('directory_category_page_image','directory_category_page_image');

/* action to get the custom fields on category pages,archive pages and search page + home page listing widget */
add_action('listing_post_info','directory_listing_after_title',13);

add_action('directory_after_taxonomies','directory_after_taxonomies_content');

/* Archive Page */
add_action('directory_archive_page_image','directory_category_page_image');

/* when sorting options common loop executes - the taxonomy name should be pass as "directory" instead of "listings" */
add_filter('tmpl_tev_sorting_for_listing','tmpl_tev_sorting_for_listing');

function tmpl_tev_sorting_for_listing(){	
	return 'directory';
}

/* when mapview common loop executes - the taxonomy name should be pass as "directory" instead of "listings" */
add_filter('tmpl_mapview_for_listing','tmpl_tev_mapview_for_listing');

function tmpl_tev_mapview_for_listing(){	
	return 'directory';
}
if(isset($_REQUEST['nearby']) && $_REQUEST['nearby'] == 'search')
{
	add_action('directory_after_search_title','directory_listing_city_name');
}
/*Remove Tevolution favourite html function */
add_action('init','remove_tevolution_favourites',11);
function remove_tevolution_favourites(){
	global $post;
	remove_action('templ_post_title','tevolution_favourite_html',11,@$post);
}
/*
 * Function Name: directory_subcategory
 * Return: display the listing category sub categories
 */
function directory_subcategory(){
	global $wpdb,$wp_query;	
	$term_id = $wp_query->get_queried_object_id();
	$taxonomy_name = $wp_query->queried_object->taxonomy;
	do_action('tevolution_category_query');
	$featured_catlist_list =  wp_list_categories('title_li=&child_of=' . $term_id .'&echo=0&taxonomy='.$taxonomy_name.'&show_count=0&hide_empty=1&pad_counts=0&show_option_none=&orderby=name&order=ASC');
	if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		remove_filter( 'terms_clauses','locationwise_change_category_query',10,3 );
	}

	if(!strstr(@$featured_catlist_list,'No categories') && !empty($featured_catlist_list))
	{
		echo '<div id="sub_listing_categories">';
		echo '<ul>';
			echo $featured_catlist_list;
		echo '</ul>';
		echo '</div>';
	}
}
/*
 * Function Name: directory_category_page_image
 * Return: display the listing image 
 */
function directory_category_page_image(){
	global $post,$wpdb,$wp_query;

	if(is_home() || is_front_page())
	{
		  $package_select =get_post_meta(get_the_ID(),'package_select',true);
		  $package_featured_type = get_post_meta($package_select,'is_home_page_featured',true);
		  if($package_featured_type == 1){
					$featured=get_post_meta(get_the_ID(),'featured_h',true);
					$featured=($featured=='h')?'featured_h':'';
		  }else{
					$featured = '';
		  }        

		}else{
		  $package_select1 =get_post_meta(get_the_ID(),'package_select',true);
		  $package_featured_type1 = get_post_meta($package_select1,'is_category_page_featured',true);
		  if( $package_featured_type1 == 1){
					$featured=get_post_meta(get_the_ID(),'featured_c',true);
					$featured=($featured=='c')?'featured_c':'';
		  }else{
					$featured = '';
		  }  
	}
	
	if(isset($_REQUEST['sort']) && $_REQUEST['sort'] =='favourite'){
		$post_type_tag = "-".$post->post_type;
	}else{
		$post_type_tag = '';
	}
	if ( has_post_thumbnail()):
		echo '<div class="listing_img">';
		do_action('inside_listing_image');
		echo '<a href="'.get_permalink().'" >';
		if($featured){echo '<span class="featured_tag">'; _e('Featured',DIR_DOMAIN); echo '</span>';}
		the_post_thumbnail('directory-listing-image'); 
		echo '</a></div>';
	else:
		if(function_exists('bdw_get_images_plugin'))
		{
			$post_img = bdw_get_images_plugin(get_the_ID(),'directory-listing-image');
			$thumb_img='';
			if(!empty($post_img)){
				$thumb_img = apply_filters('tmpl_thumb_image',$post_img[0]['file']);
				$attachment_id = $post_img[0]['id'];
				$attach_data = get_post($attachment_id);
				$img_title = $attach_data->post_title;
				$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
			}
		}		
		?>
		<div class="listing_img">
			<?php do_action('inside_listing_image');?>
			<?php if($thumb_img): ?>
				<a href="<?php the_permalink();?>">
				<?php if($featured){echo '<span class="featured_tag">'.__('Featured',DIR_DOMAIN)." ".$post_type_tag; echo'</span>';} ?>
				<img src="<?php echo $thumb_img; ?>"  alt="<?php echo $img_alt; ?>" title="<?php echo $img_title; ?>" />
				</a>
			<?php else:?>
				<a href="<?php the_permalink();?>">
				<?php if($featured){echo '<span class="featured_tag">'.__('Featured',DIR_DOMAIN)." ".$post_type_tag; echo'</span>';} ?>
				<img src="<?php echo TEVOLUTION_DIRECTORY_URL; ?>images/noimage-220x150.jpg" alt="" />
				</a>
			<?php
			endif;
			do_action('after_entry_image');
			?>		
		</div>
   <?php endif;
}
/*
 * Function Name: directory_listing_after_title
 * Return: display the listing rating, listing address and contact info after listing title
 */
function directory_listing_after_title($htmlvar_name = ''){
	global $post,$posttitle,$wp_query;
	$post_type = get_post_type();
	
	if(!empty($htmlvar_name)){

                              $htmlvar_name = $htmlvar_name; }
	else{
                                        global $htmlvar_name;
                              }               
	
	if(isset($_REQUEST['custom_post']) && $_REQUEST['custom_post'] !=''){
		$post_type = $_REQUEST['custom_post'];
	}
	$post_id =  $post->ID;

	/* get all the custom fields which select as " Show field on listing page" from back end */
	if(empty($htmlvar_name)){
		$htmlvar_name = tmpl_get_category_list_customfields($post_type);
	}

	$is_archive = get_query_var('is_ajax_archive');
	$custom_post_type = tevolution_get_post_type();	
	if(((is_archive() || $is_archive == 1) || in_array(get_post_type(),$custom_post_type))  && $post_type != 'event'){

		$tmpdata = get_option('templatic_settings');

		$address=get_post_meta($post_id,'address',true);
		$phone=get_post_meta($post_id,'phone',true);
		$time=get_post_meta($post_id,'listing_timing',true); 
		echo (@$htmlvar_name['phone'] && $phone)? '<p class="phone">'.$phone.'</p>' : '';
		echo (@$htmlvar_name['address'] && $address)? '<p class="address">'.$address.'</p>' : '';
		echo (@$htmlvar_name['listing_timing'] && $time)? '<p class="time">'.$time.'</p>' : '';

		if((!empty($htmlvar_name)) && (isset($htmlvar_name['twitter'])  || isset($htmlvar_name['facebook']) || isset($htmlvar_name['google_plus'])))
		{
			$twitter=get_post_meta($post_id,'twitter',true);
			$facebook=get_post_meta($post_id,'facebook',true);
			$google_plus=get_post_meta($post_id,'google_plus',true);
			echo "<div class='social_wrapper'>";

			if($twitter != '' && $htmlvar_name['twitter'])
			{
			?>
				<a class='twitter <?php echo $htmlvar_name['twitter']['style_class']; ?>' href="<?php echo $twitter;?>"><label><?php _e('twitter',DIR_DOMAIN); ?></label></a>
			<?php
			}
			if($facebook != '' && $htmlvar_name['facebook'])
			{
			?>
				<a class='facebook <?php echo $htmlvar_name['facebook']['style_class']; ?>' href="<?php echo $facebook;?>"><label><?php _e('facebook',DIR_DOMAIN); ?></label></a>
			<?php
			}
			if($google_plus != '' && $htmlvar_name['google_plus'])
			{
			?>
				<a class='google_plus <?php echo $htmlvar_name['google_plus']['style_class']; ?>' href="<?php echo $google_plus;?>"><label><?php _e('Google+',DIR_DOMAIN); ?></label></a>
			<?php
			}
			echo "</div>";
		}
		$j=0;
		if(!empty($htmlvar_name)){
			foreach($htmlvar_name as $key=>$value){
				$i=0;
				if(empty($value)){
					continue;
				}
	
				$key = ($key=='basic_inf')?'Listing Information': $key;
				if($key!='post_title' && $key!='post_content' && $key!='post_excerpt' && $key!='post_images' && $key!='listing_timing' && $key!='address' && $key!='listing_logo' && $key!='post_tags' && $key!='map_view'  && $key!='phone' && $key!='twitter' && $key!='facebook' && $key!='google_plus' && $key!='contact_info')
				{
					$field= get_post_meta($post_id,$key,true);
					if($value['type'] == 'multicheckbox' && $field!=""):
						$checkbox_value='';
						$option_values = explode(",",$value['option_values']);
						$option_titles = explode(",",$value['option_title']);
						for($i=0;$i<count($option_values);$i++){
							if(is_array($field) && in_array($option_values[$i],$field)){
								if($option_titles[$i]!=""){
									$checkbox_value .= $option_titles[$i].', ';
								}else{
									$checkbox_value .= $option_values[$i].', ';
								}
							}
						}
					?>
					<p class='<?php echo $key;?>'><label><?php echo $value['label']; ?></label>: <?php echo substr($checkbox_value,0,-2);?></p>
					<?php
					elseif($value['type']=='radio'):
						$option_values = explode(",",$value['option_values']);
						$option_titles = explode(",",$value['option_title']);
						for($i=0;$i<count($option_values);$i++){
							if($field == $option_values[$i]){
								if($option_titles[$i]!=""){
									$rado_value = $option_titles[$i];
								}else{
									$rado_value = $option_values[$i];
								}
								?>
								<p class='<?php echo $key;?>'><label><?php echo $value['label']; ?></label>: <?php echo $rado_value;?></p>
								<?php
							}
						}
					elseif($value['type']=='oembed_video' ):?>
						<p class='<?php echo $value['style_class'];?>'><label><?php echo $value['label']; ?>:&nbsp;</label>
						<span><?php
						$embed_video= wp_oembed_get( $field);
						if($embed_video!=""){
							echo $embed_video;
						}else{
							echo $field;
						}
						?></span></p>
					<?php
					elseif($value['type']=='multicity'):
						global $wpdb,$country_table,$zones_table,$multicity_table;
						$multicity_table = $wpdb->prefix . "multicity";
						$cityinfo = $wpdb->get_results($wpdb->prepare("select cityname from $multicity_table where city_id =%d",$field )); ?>
						<p class='<?php echo $value['style_class'];?>'><label><?php echo $value['label']; ?>: </label> <?php echo $cityinfo[0]->cityname; ?></p>
					<?php 
					elseif(@$key=='website' &&  $field!= ''):
						$website = $field;
						if(!strstr(@$website,'http') )
						{
							$website = 'http://'.$website;
						}
					 ?>
						<p class='<?php echo $value['style_class'];?>'><label><?php echo $value['label']; ?>: </label> <a target="_blank" href="<?php echo $website;?>"><?php echo $website;?></a></p>
					<?php endif;
					if($value['type']!='multicity' && @$value['type'] != 'multicheckbox' && @$value['type'] != 'radio' && $value['type'] !='' &&  @$value['type'] != 'oembed_video' && $key!='website' && $field!=''): ?>                              
					<p class='<?php echo $value['style_class'];?>'><label><?php echo $value['label']; ?>: </label> <?php echo $field;?></p>
					<?php
					endif;
				
				}/* End If condition*/

				$j++;

			}/*foreach loop*/
		}/*htmlvar_name if condition*/
	}
}
/*
 * display rating views, and other content after taxonoimies in listing page
 */
function directory_after_taxonomies_content(){
	global $post,$htmlvar_name,$templatic_settings;	
	$is_archive = get_query_var('is_ajax_archive');
	$is_related = get_query_var('is_related');
	$custom_post_type = apply_filters('directory_post_type_template',tevolution_get_post_type());
	$address = get_post_meta($post->ID,'address',true);
	if(in_array(get_post_type(),$custom_post_type)){
		echo '<div class="rev_pin">';
		echo '<ul>';
		$post_id=get_the_ID();
		$templatic_settings=get_option('templatic_settings');
		$comment_count= count(get_comments(array('post_id' => $post_id,	'status'=> 'approve')));
		$review=($comment_count <=1 )? __('review',DIR_DOMAIN):__('reviews',DIR_DOMAIN);
		$review=apply_filters('tev_review_text',$review);

		if(current_theme_supports('tevolution_my_favourites') ):?>
			<li class="favourite"><?php tevolution_favourite_html();?></li>

		<?php endif;

		if(get_option('default_comment_status')=='open' || $post->comment_status =='open'){
			?>
			<li class="review"> <?php echo '<a href="'.get_permalink($post_id).'#comments">'.$comment_count.' '.$review.'</a>';?></li>
			<?php
		}
		if( !empty($address) && isset($templatic_settings['category_googlemap_widget']) &&  @$templatic_settings['category_googlemap_widget']!='yes' && @$templatic_settings['pippoint_oncategory'] ==1 && !is_author() && !$is_related && !is_home()):?> 
          	<li class='pinpoint'><a id="pinpoint_<?php echo $post_id;?>" class="ping" href="#map_canvas"><?php _e('Pinpoint',DIR_DOMAIN);?></a></li>
		<?php endif;

		echo '</ul>';
		echo '</div>';
	}
}
/* Display no results found message as per search criteria on search page */
function directory_listing_city_name()
{
	global $post;
	echo sprintf(__('We have found these results for listings matching your search criteria.',DIR_DOMAIN),$post->post_type); 
}
?>