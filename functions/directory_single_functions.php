<?php
/*
 * This file user for listing details page related functions
 */
/* return posted id taxonomies list */
function get_the_directory_taxonomies()
{
	global $post;
	$taxonomy_category = get_the_taxonomies();
	$post_type = get_post_type();
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));	
	$taxonomy_category = str_replace(CUSTOM_MENU_CAT_LABEL_LISTING.':',"<span>".__('Posted in','templatic')."</span> ",$taxonomy_category[CUSTOM_CATEGORY_TYPE_LISTING]);		
	$taxonomy_category = substr($taxonomy_category,0,-1);	
	return $taxonomy_category;
}
/* return posted in taxonomie tag list */
function get_the_directory_tag()
{
	global $post;
	$taxonomy_tag = get_the_taxonomies();		
	$taxonomy_tag = str_replace(CUSTOM_MENU_TAG_TITLE_LISTING.':',__('Tagged In ','templatic'), @$taxonomy_tag[CUSTOM_TAG_TYPE_LISTING]);		
	$taxonomy_tag = substr($taxonomy_tag,0,-1);	
	return $taxonomy_tag;
}


/* global set custom field htmlvarname array as per detail page custom fields on heading type wise */
add_action('directory_inside_container_breadcrumb','directory_detail_custom_field');
function directory_detail_custom_field(){
	
	$custom_post_type = tevolution_get_post_type();
	
	if(is_single() && (in_array(get_post_type(),$custom_post_type)  && get_post_type()!='event')){
		global $wpdb,$post,$htmlvar_name,$pos_title,$heading_type;
		
		$cus_post_type = get_post_type();
		if(function_exists('tmpl_fetch_heading_post_type')){
			/* Get the post type wise heading fields */
			$heading_type = tmpl_fetch_heading_post_type($cus_post_type);
		}		
		if(count($heading_type) > 0)
		{
			foreach($heading_type as $key=>$heading)
			{	
				$htmlvar_name[$key] = get_directory_single_customfields(get_post_type(),$heading,$key);/*custom fields for custom post type..*/
			}
		}
		return $htmlvar_name;
	}
}
/*
 *return array for event listing custom fields
 */
function get_directory_single_customfields($post_type,$heading='',$heading_key=''){	
	global $wpdb,$post,$posttitle;
	$cur_lang_code=(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))? ICL_LANGUAGE_CODE :'';
	remove_all_actions('posts_where');		
	$post_query = null;
	remove_action('pre_get_posts','event_manager_pre_get_posts');
	remove_action('pre_get_posts','directory_pre_get_posts',12);
	add_filter('posts_join', 'custom_field_posts_where_filter');


	$args = apply_filters('tmpl_htmlvar_name_query',array( 'post_type' => 'custom_fields',
				'posts_per_page' => -1	,
				'post_status' => array('publish'),
				'meta_query' => array('relation' => 'AND',
									  array(
												'key'     => 'post_type_'.$post_type.'',
												'value'   => $post_type,
												'compare' => '=',
												'type'    => 'text'
									  ),		
									  array(
												'key'     => 'is_active',
												'value'   =>  '1',
												'compare' => '='
									  ),
									  array(
												'key'     => 'show_on_detail',
												'value'   =>  '1',
												'compare' => '='
									  ),
									  array('key' => $post_type.'_heading_type','value' =>  array('basic_inf',$heading),'compare' => 'IN')
							),                                        
				'meta_key'       => $post_type.'_sort_order',
				'orderby'        => 'meta_value_num',
				'meta_value_num' => $post_type.'_sort_order',
				'order'          => 'ASC'
	),$post_type,$heading,$heading_key);
          
                              

	if (get_option('tevolution_cache_disable')==1 && false === ($post_query = get_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code ))  ) {
		$post_query = new WP_Query($args);
		set_transient( '_tevolution_query_single'.trim($post_type).trim($heading_key).$cur_lang_code, $post_query, 12 * HOUR_IN_SECONDS );
	}elseif(get_option('tevolution_cache_disable')==''){
		$post_query = new WP_Query($args);
	}
	remove_filter('posts_join', 'custom_field_posts_where_filter');
	
	$htmlvar_name='';
	if($post_query->have_posts())
	{
		while ($post_query->have_posts()) : $post_query->the_post();
			$ctype = get_post_meta($post->ID,'ctype',true);
			if($ctype=='heading_type')
				continue;
			$post_name=get_post_meta($post->ID,'htmlvar_name',true);
			$style_class=get_post_meta($post->ID,'style_class',true);
			$option_title=get_post_meta($post->ID,'option_title',true);
			$option_values=get_post_meta($post->ID,'option_values',true);
			$default_value=get_post_meta($post->ID,'default_value',true);
			$htmlvar_name[$post_name] = array( 'type'=>$ctype,
									    'label'=> $post->post_title,
										'style_class'=>$style_class,
										'option_title'=>$option_title,
										'option_values'=>$option_values,
										'default'=>$default_value,
										);			
		endwhile;
		wp_reset_query();
	}
	return $htmlvar_name;
	
}
/*
	display the additional custom field on preview page
 */
add_action('directory_preview_page_fields_collection','directory_preview_page_fields_collection');
function directory_preview_page_fields_collection(){
	
	global $heading_title;	
	$session=$_REQUEST;
	/*$cur_post_type='listing';*/
	$cur_post_type=($session['submit_post_type']!="")? $session['submit_post_type']:get_post_type();
	$heading_type = tmpl_fetch_heading_post_type($cur_post_type);		
	
	if(count($heading_type) > 0)
	{
		foreach($heading_type as $key=>$heading)
		{	
			$htmlvar_name[$key] = get_directory_single_customfields($cur_post_type,$heading,$key);/*custom fields for custom post type..*/
		}
	}
	$j=0;	
	if(!empty($htmlvar_name)){
		echo '<div class="listing_custom_field">';
		foreach($htmlvar_name as $key=>$value){
			$i=0;
			if(!empty($value)){
			foreach($value as $k=>$val){
				$tmpl_key = ($key=='basic_inf')?  __('Listing Information','templatic'): $heading_type[$key];
				if($k!='post_title' && $k!='post_content' && $k!='post_excerpt' && $k!='post_images' && $k!='category' && $k!='listing_timing' && $k!='listing_logo' && $k!='video' && $k!='post_tags' && $k!='map_view' && $k!='proprty_feature' && $k!='phone' && $k!='email' && $k!='website' && $k!='twitter' && $k!='facebook' && $k!='google_plus' && $k!='address' && $k!='post_city_id')
				{
					
					
					$field=$session[$k];
					/*Display multicheckbox */
					if($val['type'] == 'multicheckbox' && $field!=""):						
						if($i==0){echo '<h4 class="custom_field_headding">'.$tmpl_key.'</h4>';$i++;}	
						$option_values = explode(",",$val['option_values']);				
						$option_titles = explode(",",$val['option_title']);
						for($i=0;$i<count($option_values);$i++){
							if(in_array($option_values[$i],$field)){
								if($option_titles[$i]!=""){
									$checkbox_value .= $option_titles[$i].',';
								}else{
									$checkbox_value .= $option_values[$i].',';
								}
							}
						}	
						?>
						<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label> : <?php echo substr($checkbox_value,0,-1); ?></p>
					<?php 
					elseif($val['type'] == 'upload' && $field!=''):
					?>
						<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label>: <img height="80px" width="80px" src="<?php echo $field?>" alt="<?php echo $field;?>" /></p>
					
					<?php 
					/* Display radio custom fields value */
					elseif($val['type'] == 'radio' && $field!=''):
						$option_values = explode(",",$val['option_values']);				
						$option_titles = explode(",",$val['option_title']);
						for($i=0;$i<count($option_values);$i++){
							if($field == $option_values[$i]){
								if($option_titles[$i]!=""){
									$rado_value = $option_titles[$i];
								}else{
									$rado_value = $option_values[$i];
								}							
								?>
								<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label>: <?php echo $rado_value;?></p>
								<?php
							}
						}	
					endif;					
					
					if(!is_array($session[$k])){
						$field=stripslashes($session[$k]);
					}
					if($val['type'] != 'multicheckbox' && $val['type'] != 'radio' && $val['type'] != 'upload' && $field!=''):
					if($i==0){echo '<h4 class="custom_field_headding">'.$tmpl_key.'</h4>';$i++;}?>                              
					<p class='<?php echo $k;?>'><label><?php echo $val['label']; ?></label> : <?php echo $field;?></p>
					<?php
					endif;
				
				}/* End If condition*/
				
				$j++;
			}
			} /* End second foreach*/
		}/* END First foreach*/
		echo '</div>';
	}
}
/*
 * Shows category ang tags on preview page
 */

if(!function_exists('directory_post_preview_categories_tags')){
function directory_post_preview_categories_tags($cats,$tags)
{
	global $heading_title;
	$session=$_SESSION['custom_fields'];
	$cur_post_type=($_REQUEST['cur_post_type']!="")? $_REQUEST['cur_post_type']:'listing';
	$heading_type = tmpl_fetch_heading_post_type($cur_post_type);		
	$htmlvar_name = get_directory_single_customfields($cur_post_type,'[#taxonomy_name#]','basic_inf');/*custom fields for custom post type..*/
	$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $cur_post_type,'public'   => true, '_builtin' => true ));
	if(is_array($htmlvar_name) && !empty($htmlvar_name))
		$htm_keys = array_keys($htmlvar_name);
	
	$taxonomy_category='';
	for($c=0; $c < count($cats); $c++)
	{	
		if($c < ( count($cats) - 1)){
			$sep = ', ';
		}else{
			$sep = ' ';
		}
		$cat_id =  explode(',',$cats[$c]);
		$term = get_term_by('id', $cat_id[0], $taxonomies[0]);

		$term_link = get_term_link( $term, $taxonomies[0] );		
		$taxonomy_category .= '<a href="' . $term_link . '">' . $term->name . '</a>'.$sep; 
	}
	if($taxonomy_category !='' && !empty($htm_keys) && is_array($htm_keys) && in_array('category',$htm_keys))
	{		
		echo "<span>".__('Posted in','templatic')."</span> ".$taxonomy_category;		
	}
	
	$tag_terms = explode(',',$tags);
	$sep = ",";
	$i = 0;
	if(!empty($tag_terms[0])){
		for($t=0; $t < count($tag_terms); $t++){

			if($t < ( count($tag_terms) - 1)){
				$sep = ', ';
			}else{
				$sep = ' ';
			}
			$term = get_term_by('name', $tag_terms[$t], 'listingtags');

			if(empty($term)){
				$termname = $tag_terms[$t];
			}else{
				$termname = $term->name;
			}
			$taxonomy_tag .= '<a href="#">' .$termname. '</a>'.$sep;
		}
		if(!empty($tag_terms)){			
			echo sprintf(__('Tagged In %s','templatic'),$taxonomy_tag);			
		}
	}
}
}
/* display the events going to happens on listing */
add_action('tmpl_events_on_place_list','tmpl_events_on_place_list_details');
function tmpl_events_on_place_list_details($events_list,$post){
	global $wp_query;
	if(is_single() && get_post_type()=='listing'){
		$event_for_listing = get_post_meta($post->ID,'event_for_listing',true);
		/* Recurring Event  */
		if($event_for_listing !='')
		{
			if(!empty($events_list)){
		?>
        <!--Video Code Start -->
        <section role="tabpanel" aria-hidden="false" class="content" id="listing_event">
               <?php
			foreach($events_list as $event_detail) {
				if($event_detail->post_status =='publish' ){
				?>
			<div class="listed_events clearfix"> 
				<?php
				if ( has_post_thumbnail($event_detail->ID)){
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($event_detail->ID), 'tevolution_thumbnail' );
				$post_image=($thumb[0])? $thumb[0] :TEVOLUTION_DIRECTORY_URL.'images/noimage-150x150.jpg';
				}else{
					$post_image = bdw_get_images_plugin($event_detail->ID,'tevolution_thumbnail');
					$post_image=($post_image[0]['file'])? $post_image[0]['file'] :TEVOLUTION_DIRECTORY_URL.'images/noimage-150x150.jpg';
				}
				$e_id = $event_detail->ID;
				$e_title = $event_detail->post_title;
				if(get_post_meta($e_id,'st_date',true) !='' && get_post_meta($e_id,'end_date',true) !='' ){
					$date = "<strong>".__('From','templatic')."</strong>".' '.get_post_meta($e_id,'st_date',true)." ".get_post_meta($e_id,'st_time',true)." <strong>".__('To','templatic')."</strong> ".get_post_meta($e_id,'end_date',true)." ".get_post_meta($e_id,'end_time',true);
				}elseif(get_post_meta($e_id,'st_date',true) !='' && get_post_meta($e_id,'end_date',true) =='' ){
					$date = "<strong>".__('From','templatic')."</strong>".' '.get_post_meta($e_id,'st_date',true)." ".get_post_meta($e_id,'st_time',true);
				} ?>
				<a class="event_img" href="<?php echo get_permalink($e_id); ?>"><img src="<?php echo $post_image; ?>" width="60" height="60" alt="<?php echo $e_title; ?>"/></a>
				<div class="event_detail">
					<a class="event_title" href="<?php echo get_permalink($e_id); ?>"><strong><?php echo $e_title; ?></strong></a><br/>
					<?php $address=get_post_meta($e_id,'address',true);
					$phone=get_post_meta($e_id,'phone',true);
					$date_formate=get_option('date_format');
					$time_formate=get_option('time_format');
					$st_date= date_i18n($date_formate,strtotime(get_post_meta($e_id,'st_date',true)));
					$end_date= date_i18n($date_formate,strtotime(get_post_meta($e_id,'end_date',true)));

					$date=$st_date.' '. __('To','templatic').' '.$end_date;

					$st_time=date($time_formate,strtotime(get_post_meta($e_id,'st_time',true)));
					$end_time=date($time_formate,strtotime(get_post_meta($e_id,'end_time',true)));
					if($address){
						echo '<p class="address" >'.$address.'</p>';
					}
					if($date){
						echo '<p class="event_date"><strong>'.__('Date:','templatic').'&nbsp;</strong><span>'.$date.'</span></p>';
					}
					if($st_time || $end_time){
						echo '<p class="time"><strong>'.__('Timing:','templatic').'&nbsp;</strong><span>'.$st_time.' '.__('To','templatic').' '.$end_time.'</span></p>';
					}?>
				</div>
			</div>
			<?php }
			} /* end for each */
			?>
		</section>
		<!--Video code End -->			
	<?php }
	} }
}

/* for showing linked event in mobile view */
function tmpl_events_on_place_list_details_mobile($events_list,$post){
	global $wp_query;
	if(is_single() && get_post_type()=='listing'){
		$event_for_listing = get_post_meta($post->ID,'event_for_listing',true);
		/* Recurring Event  */
		if($event_for_listing !='')
		{
			if(!empty($events_list)){
		?>
        <!--Video Code Start -->
        <section role="tabpanel" aria-hidden="false" class="content search_result_listing list" id="listing_event">
               <?php
			foreach($events_list as $event_detail) {
				if($event_detail->post_status =='publish' ){ 
				
					if ( has_post_thumbnail($event_detail->ID)){
					$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($event_detail->ID), 'tevolution_thumbnail' );
					$post_image=($thumb[0])? $thumb[0] :TEVOLUTION_DIRECTORY_URL.'images/noimage-150x150.jpg';
					}else{
						$post_image = bdw_get_images_plugin($event_detail->ID,'tevolution_thumbnail');
						$post_image=($post_image[0]['file'])? $post_image[0]['file'] :TEVOLUTION_DIRECTORY_URL.'images/noimage-150x150.jpg';
					}
					$e_id = $event_detail->ID;
					$e_title = $event_detail->post_title;
					if(get_post_meta($e_id,'st_date',true) !='' && get_post_meta($e_id,'end_date',true) !='' ){
						$date = "<strong>".__('From','templatic')."</strong>".' '.get_post_meta($e_id,'st_date',true)." ".get_post_meta($e_id,'st_time',true)." <strong>".__('To','templatic')."</strong> ".get_post_meta($e_id,'end_date',true)." ".get_post_meta($e_id,'end_time',true);
					}elseif(get_post_meta($e_id,'st_date',true) !='' && get_post_meta($e_id,'end_date',true) =='' ){
						$date = "<strong>".__('From','templatic')."</strong>".' '.get_post_meta($e_id,'st_date',true)." ".get_post_meta($e_id,'st_time',true);
					} 
					$address=get_post_meta($e_id,'address',true);		
				?>

			
			<article class="post">  
					
				<div class="event_img"> 
					<a class="event_title" href="<?php echo get_permalink($e_id); ?>">
						<img src="<?php echo $post_image; ?>" width="60" height="60" alt="<?php echo $e_title; ?>"/>
					</a>	
				</div>
    
					
				<!-- Entry Start -->
				<div class="entry"> 

					<div class="listing-wrapper">
					<!-- Entry title start -->
						<div class="entry-title">

							<h2 class="entry-title"><a rel="bookmark" title="<?php echo $e_title; ?>" href="<?php echo get_permalink($e_id); ?>" itemprop="url"><?php echo $e_title; ?></a></h2>        
							<div class="listing_rating">

							</div>	

						</div>


					<!-- Entry title end -->

					<!-- Entry details start -->
						<div class="entry-details">
							<?php
								if($address){
									echo '<p class="address" >'.$address.'</p>';
								}
							?>								
						</div>
					<!-- Entry details end -->
					</div>
				<!--Start Post Content -->
				<!-- End Post Content -->

				</div>
					<!-- Entry End -->
									</article>
			
			<?php }
			} /* end for each */
			?>
		</section>
		<!--Video code End -->			
	<?php }
	} }
}
/* get related listings - on detail page */
add_action('tmpl_related_listings','tmpl_get_dir_related_listings');

if(!function_exists('tmpl_get_dir_related_listings')){
	function tmpl_get_dir_related_listings(){

		global $post,$htmlvar_name,$wpdb,$wp_query;
		/* get all the custom fields which select as " Show field on listing page" from back end */		
		if(function_exists('tmpl_get_category_list_customfields')){
			$htmlvar_name = tmpl_get_category_list_customfields(CUSTOM_POST_TYPE_LISTING);
		}else{
			global $htmlvar_name;
		}
		$wp_query->set('is_related',1);
		$related_posts = tmpl_get_related_posts_query();

			if(!empty($related_posts->posts)){
			echo "<div class='realated_post clearfix'>";
			echo "<h3>"; _e('Related Listings','templatic'); echo "</h3>";
			echo "<div id='loop_listing_taxonomy' class='grid'>";
			while($related_posts->have_posts()){  global $post;
				$related_posts->the_post();

				/* remove add to favourite from below title */
				remove_action('templ_post_title','tevolution_favourite_html',11);
				?>

				<article class="post  <?php templ_post_class();?>" >
					<?php
					/* Hook to display before image */
					do_action('directory_before_category_page_image');

					/* Hook to Display Listing Image  */
					do_action('directory_category_page_image');

					/* Hook to Display After Image  */
					do_action('directory_after_category_page_image');

					/* Before Entry Div  */
					do_action('directory_before_post_entry');?>

					<!-- Entry Start -->
					<div class="entry">
						
					   <div class="listing-wrapper">
						<!-- Entry title start -->
						<div class="entry-title">
					   
						<?php do_action('templ_post_title');                /* do action for display the single post title */?>

						</div>

						<!-- Entry title end -->

						<!-- Entry details start -->
						<div class="entry-details">

						<?php  /* Hook to get Entry details - Like address,phone number or any static field  */
						do_action('listing_post_info');   ?>

						</div>
						<!-- Entry details end -->
					   </div>
						<!--Start Post Content -->
						<?php

						/* Hook to display post content . */ 
						do_action('templ_taxonomy_content');

						?>
						<!-- End Post Content -->
					   <?php
						/* Hook for before listing categories*/
						do_action('directory_before_taxonomies');

						/* Display listing categories*/
						do_action('templ_the_taxonomies');

						/* Hook to display the listing comments, add to favorite and pinpoint   */
						do_action('directory_after_taxonomies');?>
					</div>
					<!-- Entry End -->
					<?php do_action('directory_after_post_entry');?>
				</article>
			<?php
			}
			echo "</div>";
			echo "</div>";
			wp_reset_query();
		}
	}
}
/* Display Ratings on detail page */
add_action('directory_display_rating','directory_display_rating');
function directory_display_rating($post_id){	
	/*action to show rating*/
	do_action('show_single_multi_rating');
}
?>