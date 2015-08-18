<?php
/**
 * Tevolution single custom post type template
 *
**/
get_header();
$tmpdata = get_option('templatic_settings');
$is_edit='';
if(isset($_REQUEST['action']) && $_REQUEST['action']=='edit'){
	$is_edit=1;
}

/* to get the common/context custom fields display by default with current post type */
if(function_exists('tmpl_single_page_default_custom_field')){
	$tmpl_flds_varname = tmpl_single_page_default_custom_field(get_post_type());
}

$tmpdata = get_option('templatic_settings');	
$googlemap_setting=get_option('city_googlemap_setting');
$special_offer=get_post_meta(get_the_ID(),'proprty_feature',true);
$video=get_post_meta(get_the_ID(),'video',true);
$facebook=get_post_meta(get_the_ID(),'facebook',true);
$google_plus=get_post_meta(get_the_ID(),'google_plus',true);
$twitter=get_post_meta(get_the_ID(),'twitter',true);
$listing_address=get_post_meta(get_the_ID(),'address',true);
if(function_exists('bdw_get_images_plugin'))
{
	$post_img = bdw_get_images_plugin(get_the_ID(),'directory-single-image');
	$postimg_thumbnail = bdw_get_images_plugin(get_the_ID(),'thumbnail');
	$more_listing_img = bdw_get_images_plugin(get_the_ID(),'tevolution_thumbnail');
	$thumb_img = @$post_img[0]['file'];
	$attachment_id = @$post_img[0]['id'];
	$image_attributes = wp_get_attachment_image_src( $attachment_id ,'large');
	$attach_data = get_post($attachment_id);
	$img_title = $attach_data->post_title;
	$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
}
	while ( have_posts() ) : the_post(); 
	do_action('directory_before_post_loop');
	
	?>
	<!-- start content part-->
	<div id="content" class="large-9 small-12 columns" role="main">	
	<?php 
  	if(function_exists('supreme_sidebar_before_content')){
		/* Loads the sidebar-before-content. */
	  	apply_filters('tmpl_before-content',supreme_sidebar_before_content() );

	} 

	
	global $htmlvar_name;
	$address=get_post_meta(get_the_ID(),'address',true);
	$website=get_post_meta(get_the_ID(),'website',true);
	$phone=get_post_meta(get_the_ID(),'phone',true);																		
	$listing_timing=get_post_meta(get_the_ID(),'listing_timing',true);
	$email=get_post_meta(get_the_ID(),'email',true);?>

	


											
	<!-- Finish Image Gallery Div -->
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>  
		<!--start post type title -->
		<?php do_action('directory_before_post_title');         /* do action for before the post title.*/ ?>
		   
			<header class="entry-header">
				<section class="entry-header-title">
					<?php $listing_logo=get_post_meta(get_the_ID(),'listing_logo',true); ?>
					<!-- Start Image Upload -->
					 <?php if(($listing_logo!="" && $tmpl_flds_varname['listing_logo']) && ($is_edit=="")):?>
							<div class="entry-header-logo">
								<img src="<?php echo $listing_logo?>" alt="<?php echo $tmpl_flds_varname['listing_logo']['label']; ?>" />
							</div>
					 <?php elseif($is_edit==1): ?>
						<div class="entry-header-logo" >
							<div style="display:none;" class="frontend_listing_logo"><?php echo $listing_logo?></div>
							<!--input id="fronted_files_listing_logo" class="fronted_files" type="file" multiple="true" accept="image/*" /-->
							<div id="fronted_upload_listing_logo" class="frontend_uploader button" data-src="<?php echo $listing_logo?>">	                 	
								<span><?php _e( 'Upload ', DIR_DOMAIN ).$tmpl_flds_varname['listing_logo']['label']; ?></span>						
							</div>
						</div>
					 <?php endif;	?>
					 <!-- End Image Upload -->
					<div class="entry-info">
						<!-- Page Title -->
						<h1 itemprop="name" class="entry-title <?php if($is_edit==1):?>frontend-entry-title <?php endif;?>" <?php if($is_edit==1):?> contenteditable="true"<?php endif;?> >
							<?php do_action('before_title_h1'); the_title(); do_action('after_title_h1');?>
						</h1>
						
						<!-- Ratings -->
						<?php
							
							if($tmpdata['templatin_rating']=='yes'):
								$total=get_post_total_rating(get_the_ID());
								$total=($total=='')? 0: $total;
								$review_text=($total==1 )? '<a id="reviews_show" href="#comments">'.__('Review',DIR_DOMAIN).'</a>': '<a id="reviews_show" href="#comments">'.$total.' '.__('Reviews',DIR_DOMAIN).'</a>';	?>
							<div class="listing_rating">
								<div class="directory_rating_row">
									<span class="single_rating">
										<?php echo draw_rating_star_plugin(get_post_average_rating(get_the_ID()));?>
										<span><?php echo $review_text?></span>
									</span>
								</div>
							</div>
						<?php endif; do_action('directory_display_rating',get_the_ID()); ?>		
					</div>
							
                    <div class="entry-links-mobile mobile">
					<ul>             		
							<?php if($phone!="" && $tmpl_flds_varname['phone'] || ($is_edit==1 && $tmpl_flds_varname['phone'])):?>
							<li class="phone <?php echo $tmpl_flds_varname['phone']['style_class']; ?>">
								<a href="tel:<?php echo $phone ?>" class="entry-phone frontend_phone listing_custom" <?php if($is_edit==1):?>contenteditable="true" <?php endif;?>><i class="fa fa-phone"></i> <?php echo _e('Call',DIR_DOMAIN);?></a>
							</li>
							<?php endif; 
							
							if(@$email!="" && @$tmpl_flds_varname['email'] || ($is_edit==1 && @$tmpl_flds_varname['email'])):?>
							<li class="email  <?php echo $tmpl_flds_varname['email']['style_class']; ?>">
							<a href="mailto:<?php echo $email; ?>" class="entry-email frontend_email listing_custom" <?php if($is_edit==1):?>contenteditable="true"<?php endif;?>><i class="fa fa-envelope"></i><?php _e('Email',DOMAIN);?></a>
							<?php endif; ?>
							</li>

							<?php if($website!="" && $tmpl_flds_varname['website'] || ($is_edit==1)): if(!strstr($website,'http')) $website = 'http://'.$website;?>
							<li class="website <?php echo $tmpl_flds_varname['website']['style_class']; ?>">
								<a target="_blank" id="website" class="frontend_website <?php if($is_edit==1):?>frontend_link<?php endif; ?>" href="<?php echo $website;?>" ><span><i class="fa fa-globe"></i><?php echo $tmpl_flds_varname['website']['label']; ?></span></a>
							</li>
						<?php endif;
							/* Add to fav link for mobile*/
							if(current_theme_supports('tevolution_my_favourites') && ($post->post_status == 'publish' )){
								global $current_user;
								$user_id = $current_user->ID;
								do_action('tmpl_before_addtofav');
								$link.= tmpl_detailpage_favourite_html($user_id,@$post);
								echo $link;
								
							} ?>
					</ul>
					</div> 
                     
					<article  class="entry-header-custom-wrap">
							<div class="entry-header-custom-left">
							  <?php
								if($address!="" && $tmpl_flds_varname['address']):?>
								   <p class="entry_address<?php echo $tmpl_flds_varname['address']['style_class'];?>"><span id="frontend_address" class="listing_custom frontend_address" <?php if($is_edit==1):?>contenteditable="true"<?php endif;?>><?php echo get_post_meta(get_the_ID(),'address',true);?></span></p>
								   <?php do_action('directory_after_address');
								endif;
								   do_action('directory_display_custom_fields_default_left');
								   ?>
							</div>
							<div class="entry-header-custom-right">
							<?php 
							   
							   if($listing_timing!="" && $tmpl_flds_varname['listing_timing'] || ($is_edit==1 && $tmpl_flds_varname['listing_timing'])):?>
									<p class="time <?php echo $tmpl_flds_varname['listing_timing']['style_class']; ?>"><span class="entry-listing_timing frontend_listing_timing listing_custom" <?php if($is_edit==1):?>contenteditable="true" <?php endif;?>><?php echo $listing_timing;?></span></p>
							   <?php endif;
							   
							   do_action('directory_display_custom_fields_default_right');	
							   ?>
							</div>
					</article>
				</section>
			</header>
		   
		<?php 
		 /* do action for after the post title.*/
		do_action('directory_after_post_title');       ?>
		<!--end post type title -->               
		
		
		<!--Code start for single captcha -->   
		<?php 			 
		  $display = (isset($tmpdata['user_verification_page']))?$tmpdata['user_verification_page']:array();
		  $captcha_set = array();
		  $captcha_dis = '';
		  if(count($display) > 0 && !empty($display) )
		   {
			  foreach($display as $_display)
			   {
				  if($_display == 'claim' || $_display == 'emaitofrd')
				   { 
					 $captcha_set[] = $_display;
					 $captcha_dis = $_display;
				   }
			   }
		   }
		   $recaptcha = get_option("recaptcha_options");
		   global $current_user;
		 ?>
		   
		<div id="myrecap" style="display:none;"><?php if($recaptcha['show_in_comments']!= 1 || $current_user->ID != ''){ templ_captcha_integrate($captcha_dis); }?></div> 
		<input type="hidden" id="owner_frm" name="owner_frm" value=""  />
		<div id="claim_ship"></div>
		<script type="text/javascript" async >
			var RECAPTCHA_COMMENT = '';
			<?php
			if($recaptcha['show_in_comments']!= 1 || $current_user->ID != ''){ ?>
				jQuery('#owner_frm').val(jQuery('#myrecap').html());
			<?php 	} else{ ?> 
				RECAPTCHA_COMMENT = <?php echo $recaptcha['show_in_comments']; ?>; 
			<?php } ?>
		</script>
		   
		<!--Code end for single captcha -->
		
		<!-- Image gallery -->
		<section id="listing_gallery">
		<!-- Image Gallery Div --> 	
		<?php if($thumb_img && $is_edit==''):?>		
		<div id="directory_detail_img" class="entry-header-image">
		
		<?php do_action('directory_before_post_image');
		if($is_edit==""):?>
		<div id="slider" class="listing-image flexslider frontend_edit_image">    
			<ul class="slides">
				<?php
					if(!empty($post_img)):
					foreach($post_img as $key=>$value):
					$attachment_id = $value['id'];
					$attach_data = get_post($attachment_id);
					$image_attributes = wp_get_attachment_image_src( $attachment_id ,'large'); /* returns an array							*/
					$img_title = $attach_data->post_title;
					$img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);	?>
					<li>
						<a href="<?php echo $image_attributes['0'];?>" title="<?php echo $img_title; ?>" >		
							<img src="<?php echo $value['file'];?>" alt="<?php echo $img_title; ?>"/>
						</a>
					</li>
					<?php 
					endforeach;
				endif;?>
			</ul>
		</div>
		<?php endif;
		
		do_action('directory_after_post_image');?>
		</div><!-- .entry-header-image -->
		<?php endif;
		
		if($is_edit=="1"):?>
		<!-- Front end edit upload image-->
		<div id="directory_detail_img" class="entry-header-image">
		<!--editing post images -->
		<div id="slider" class="listing-image flexslider frontend_edit_image flex-viewport">
		<ul class="frontend_edit_images_ul">
		<?php
		$post_img = bdw_get_images_plugin($post->ID,'large');
		if(!empty($post_img)):
		foreach($post_img as $key=>$value):
		echo "<li class='image' data-attachment_id='".basename($value['file'])."' data-attachment_src='".$value['file']."'><img src='".$value['file']."' alt='".$img_title."' /></li>";
		break;
		endforeach;
		endif;	
		?>
		</ul>
		<div id="uploadimage" class="upload button secondary_btn clearfix">
		<span><?php _e("Upload Images", DIR_DOMAIN); ?></span>					
		</div>
		</div>
		
		<div id="frontend_images_gallery_container" class="clearfix flex-viewport">
		<ul class="frontend_images_gallery more_photos slides">
		<?php
		if(!empty($post_img)):                				
		foreach($post_img as $key=>$value):
		echo "<li class='image' data-attachment_id='".basename($value['file'])."' data-attachment_src='".$value['file']."'><img src='".$value['file']."' alt='".$img_title."' /><span>
		<a class='delete' title='Delete image' href='#' id='".$value['id']."' >Delete</a></span></li>";
		endforeach;
		endif;	
		?>
		</ul>
		<input type="hidden" id="fontend_image_gallery" name="fontend_image_gallery" value="<?php echo esc_attr( substr(@$image_gallery,0,-1) ); ?>" />		
		</div>
		<span id="forntend_status" class="message_error2 clearfix"></span>
		<!--finish editing post images -->
		</div>
		<?php endif;?>
		</section>
		
		<!-- listing content-->
		<section class="entry-content">
			<?php do_action('directory_before_post_content');	?>
					<script  type="text/javascript" async >
					jQuery(function() {
						jQuery('.listing-image a.listing_img').lightBox();
					});	
					</script><?php
					
					if(isset($post)){
						$post_img = bdw_get_images_plugin($post->ID,'directory-single-image');
						$post_images = @$post_img[0]['file'];
						$title=urlencode($post->post_title);
						$url=urlencode(get_permalink($post->ID));
						$summary=urlencode(htmlspecialchars($post->post_content));
						$image=$post_images;
					}
					
					/* Directory Share Link Coding Start */
						tevolution_socialpost_link($post);
					/* Directory Share Link Coding End */
					global $htmlvar_name;
					do_action('dir_before_tabs');
					
					?>
					<dl class="tmpl-accordion" data-accordion>
							<?php	do_action('dir_start_tabs');
												
							if(get_the_content()):?>	
								<dd class="tmpl-accordion-navigation active"><a href="#listing_description" ><?php _e('Overview',DIR_DOMAIN);?></a>
									<div id="listing_description" class="content active">
										<!--Overview Section Start -->
											<div class="entry-content frontend-entry-content <?php if($is_edit==1):?>editblock listing_content <?php endif; if(!$thumb_img):?>content_listing<?php else:?>listing_content <?php endif;?>">
											
											<?php
												do_action('directory_before_post_content'); 
														the_content();
												do_action('direcotry_after_post_content'); 
											?>
											  
											</div>
										<!--Overview Section End -->
									</div>
								</dd>
							<?php if(@$tmpdata['direction_map']=='yes' && $listing_address):?>									
								<dd class="tmpl-accordion-navigation"><a href="#listing_map"><?php _e('Map',DIR_DOMAIN);?></a>
									<div id="listing_map" class="content">
										<?php if($tmpdata['direction_map']=='yes' && $listing_address):?>
											<!--Map Section Start -->
											<?php do_action('directory_single_page_map') ?>
											<!--Map Section End -->
										<?php endif; ?>
									</div>
								</dd>
							<?php endif;
							  
							if(($special_offer!="" && $tmpl_flds_varname['proprty_feature']) || ($is_edit==1 && $tmpl_flds_varname['proprty_feature'])): ?>
								<dd class="tmpl-accordion-navigation"><a href="#special_offer"><?php echo $tmpl_flds_varname['proprty_feature']['label'];?></a>
									<div id="special_offer" class="content">
										 <?php if(($special_offer!="" && $tmpl_flds_varname['proprty_feature'] ) || ($is_edit==1 && $tmpl_flds_varname['proprty_feature']) ):?>
											<!--Special Offer Start -->
											<div class="entry-proprty_feature frontend_proprty_feature <?php if($is_edit==1):?>editblock <?php endif;?>">
											<?php
												$special_offer = apply_filters( 'the_content', $special_offer );
												$special_offer = str_replace( ']]>', ']]&gt;', $special_offer );
												echo $special_offer;
											?>
											</div>
											<!--Special Offer End -->
										<?php endif; ?>
									</div>
								</dd>

							<?php endif;
							  
							if($video!="" && $tmpl_flds_varname['video'] || ($is_edit==1 && $tmpl_flds_varname['video'])):?>
								<dd class="tmpl-accordion-navigation"><a href="#listing_video"><?php echo $tmpl_flds_varname['video']['label'];?></a>
									<div id="listing_video" class="content">
										<?php if(($video!="" && $tmpl_flds_varname['video'] ) || ($is_edit==1 && $tmpl_flds_varname['video']) ):?>
											<!--Video Code Start -->
											<?php if($is_edit==1):
													do_action('oembed_video_description');?>  		
													<span id="frontend_edit_video" class="frontend_oembed_video button" ><?php _e('Edit Video',DIR_DOMAIN);?></span>
													<input type="hidden" class="frontend_video" name="frontend_edit_video" value='<?php echo $video;?>' />
											<?php endif;?>
											<div class="frontend_edit_video"><?php             
												$embed_video= wp_oembed_get( $video);            
												if($embed_video!=""){
													echo $embed_video;
												}else{
													echo $video;
												}	?>
											</div>
											<!--Video code End -->
										<?php endif; ?>


										<?php
										/* Display the events list on listing detail page */
										echo tmpl_events_on_place_list_details($events_list,$post); ?>
									</div>
								</dd>
							<?php endif; 
							endif;
							
							global $post,$events_list;
			
							$event_for_listing = get_post_meta($post->ID,'event_for_listing',true);		
							if(!empty($event_for_listing))
							{
							  $event_for_list = explode(',',$event_for_listing);
								
								if(function_exists('tmpl_get_events_list')){
									$events_list = tmpl_get_events_list($event_for_list);

									if(!empty($events_list)){
									?>
									<dd class="tmpl-accordion-navigation"><a href="#listing_event"><?php echo _e('Events',DIR_DOMAIN);?></a>
										<div id="listing_event" class="content">
											<?php echo tmpl_events_on_place_list_details_mobile($events_list,$post); ?>
										</div>
									</dd>
									<?php
									}
								}
							}
							
							do_action('dir_end_mobile_tabs');
							/* add action for display before the post comments. */
							do_action('tmpl_before_comments');  

							comments_template( get_template_directory().'/mobile-templates/comments.php', true ); /* Loads the comments.php template. */

							/*Add action for display after the post comments. */
							do_action('tmpl_after_comments'); 
		
							?>
					</dl>

						
					<?php do_action('dir_after_tabs');	
						/* Display heading type with custom fields */
						global $htmlvar_name,$heading_title;
						$j=0;
						/* array of fields which we are not going to show on detail page */
						$not_include = apply_filters('tmpl_exclude_custom_fields',array('category','post_title','post_content','post_excerpt','post_images','post_city_id','listing_timing','address','listing_logo','post_coupons','video','post_tags','map_view','proprty_feature','phone','email','website','twitter','facebook','google_plus','contact_info')); 
						/* get detail page custom fields selected as show on detail page yes */
						do_action('tmpl_display_before_listing_custom_fields');
							tmpl_fields_detail_informations($not_include,__('Other Details',DOMAIN));
						do_action('tmpl_display_after_listing_custom_fields');
					
					
					do_action('directory_after_post_content');?>
		</section>
		<!--Finish the listing Content -->


		<!--Custom field collection do action -->
		<?php do_action('directory_custom_fields_collection');
			
			do_action('directory_extra_single_content');
				
			/* Display categories on detail page */
		?>          
			   
		</div>
		<?php 
		do_action('directory_after_post_loop');

			   
		endwhile; /* end of the loop.*/

		wp_reset_query(); /* reset the wp query*/

		/* add action for display the next previous pagination */ 
		do_action( 'after_entry' ); 
		
		do_action('tmpl_single_post_pagination');
?>
</div><!-- #content -->

<!-- end  content part-->
<?php get_footer(); ?>