<?php
global $post, $custom_fields_as_tabs;
$is_edit = '';
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
          $is_edit = 1;
}
$tmpdata = get_option('templatic_settings');
$googlemap_setting = get_option('city_googlemap_setting');
$special_offer = get_post_meta(get_the_ID(), 'proprty_feature', true);
$video = get_post_meta(get_the_ID(), 'video', true);
$facebook = get_post_meta(get_the_ID(), 'facebook', true);
$google_plus = get_post_meta(get_the_ID(), 'google_plus', true);
$twitter = get_post_meta(get_the_ID(), 'twitter', true);
$listing_address = get_post_meta(get_the_ID(), 'address', true);
if (function_exists('bdw_get_images_plugin')) {
          $post_img = bdw_get_images_plugin(get_the_ID(), 'directory-single-image');
          $postimg_thumbnail = bdw_get_images_plugin(get_the_ID(), 'thumbnail');
          $more_listing_img = bdw_get_images_plugin(get_the_ID(), 'tevolution_thumbnail');
		  $thumb_img = apply_filters('tmpl_thumb_image',$post_img[0]['file']);
          $attachment_id = @$post_img[0]['id'];
          $image_attributes = wp_get_attachment_image_src($attachment_id, 'large');
          $attach_data = get_post($attachment_id);
          $img_title = $attach_data->post_title;
          $img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
}
?>
<div class="claim-post-wraper">
     <?php echo '<div style="display: none; opacity: 0.5;" id="lean_overlay"></div>'; ?>
     <ul>
          <?php tevolution_dir_popupfrms($post); /* show sent to friend and send inquiry form popup */ ?>
     </ul>
</div>

<?php
if (isset($post)) {
          $post_img = bdw_get_images_plugin($post->ID, 'directory-single-image');
          $post_images = @$post_img[0]['file'];
          $title = urlencode($post->post_title);
          $url = urlencode(get_permalink($post->ID));
          $summary = urlencode(htmlspecialchars($post->post_content));
          $image = $post_images;
}
?>

<!--Directory Share Link Coding Start -->
<?php
tevolution_socialpost_link($post); /* to show the link of current post in social media */
?>
<!--Directory Share Link Coding End -->

<?php
global $htmlvar_name, $tmpl_flds_varname;
do_action('dir_before_tabs');
?>
<ul class="tabs" data-tab role="tablist">
     <?php
     do_action('dir_start_tabs');

     if ($post->post_content != '' || count($post_img) > 0 || count($custom_fields_as_tabs) > 0 || (isset($_REQUEST['action']) && $_GET['action'] == 'edit')):
               ?>	
               <li class="tab-title active" role="presentational"><a href="#listing_description" role="tab" tabindex="0" aria-selected="false" controls="listing_description"><?php _e('Overview', 'templatic'); ?></a></li>
               <?php
     endif;

     if (@$tmpdata['direction_map'] == 'yes' && $listing_address):
               ?>
               <li class="tab-title" role="presentational"><a href="#listing_map" role="tab" tabindex="1" aria-selected="false" controls="listing_map"><?php _e('Map', 'templatic'); ?></a></li>
               <?php
     endif;

     if (($special_offer != "" && $tmpl_flds_varname['proprty_feature']) || ($is_edit == 1 && $tmpl_flds_varname['proprty_feature'])):
               ?>
               <li class="tab-title" role="presentational"><a href="#special_offer" role="tab" tabindex="2" aria-selected="false" controls="special_offer"><?php echo $tmpl_flds_varname['proprty_feature']['label']; ?></a></li>
               <?php
     endif;

     if ($video != "" && $tmpl_flds_varname['video'] || ($is_edit == 1 && $tmpl_flds_varname['video'])):
               ?>
               <li class="tab-title" role="presentational"><a href="#listing_video" role="tab" tabindex="3" aria-selected="false" controls="listing_video"><?php echo $tmpl_flds_varname['video']['label']; ?></a></li>
               <?php
     endif;

     /* To display the events "Tab" available on that place */
     do_action('tmpl_show_events_tab');

     global $post, $events_list;

     $event_for_listing = get_post_meta($post->ID, 'event_for_listing', true);
     if (!empty($event_for_listing)) {
               $event_for_list = explode(',', $event_for_listing);

               if (function_exists('tmpl_get_events_list')) {
                         $events_list = tmpl_get_events_list($event_for_list);

                         if (!empty($events_list)) {
                                   ?><li class="tab-title" role="presentational"><a href="#listing_event" role="tab" tabindex="4" aria-selected="false" controls="listing_event"><?php _e('Events', 'templatic'); ?></a></li><?php
                              }
                    }
          }


          do_action('dir_end_tabs');
          ?>	
</ul>
<?php do_action('dir_after_tabs'); ?>

<div class="tabs-content">


     <!--Overview Section Start -->
     <section role="tabpanel" aria-hidden="false" class="content active" id="listing_description">
          <div class="entry-content frontend-entry-content <?php if ($is_edit == 1): ?>editblock listing_content <?php
          endif;
          if (!$thumb_img):
                    ?>content_listing<?php else: ?>listing_content <?php endif; ?>">

               <?php
               do_action('directory_before_post_content');
               the_content();
               do_action('directory_after_post_content');
               ?>

          </div>      

          <!-- Image Gallery Div --> 
          <?php if ($thumb_img && $is_edit == ''): ?>		
                    <div id="directory_detail_img" class="entry-header-image">

                         <?php
                         do_action('directory_before_post_image');
                         if ($is_edit == ""):
                                   ?>
                                   <div id="slider" class="listing-image flexslider frontend_edit_image">    

                                        <ul class="slides">
                                             <?php
                                             if (!empty($post_img)):
                                                       foreach ($post_img as $key => $value):
                                                                 $attachment_id = $value['id'];
                                                                 $attach_data = get_post($attachment_id);
                                                                 $image_attributes = wp_get_attachment_image_src($attachment_id, 'large'); /* returns an array							 */
                                                                 $img_title = $attach_data->post_title;
                                                                 $img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
																 $thumb_img = apply_filters('tmpl_thumb_image',$value['file']);
																 $img_src = apply_filters('tmpl_thumb_image',$image_attributes['0']);
                                                                 ?>
                                                                 <li>
                                                                      <a href="<?php echo $img_src; ?>" title="<?php echo $img_title; ?>" class="listing_img" >		
                                                                           <img src="<?php echo $thumb_img; ?>" alt="<?php echo $img_title; ?>"/>
                                                                      </a>
                                                                 </li>
                                                                 <?php
                                                       endforeach;
                                             endif;
                                             ?>
                                        </ul>

                                   </div>


                                   <!-- More Image gallery -->
                                   <div id="silde_gallery" class="flexslider<?php
                                   if (!empty($more_listing_img) && count($more_listing_img) > 4) {
                                             echo ' slider_padding_class';
                                   }
                                   ?>">
                                        <ul class="more_photos slides">
                                             <?php
                                             if (!empty($more_listing_img) && count($more_listing_img) > 1):

                                                       foreach ($more_listing_img as $key => $value):
                                                                 $attachment_id = $value['id'];
                                                                 $attach_data = get_post($attachment_id);
                                                                 $image_attributes = wp_get_attachment_image_src($attachment_id, 'large'); /* returns an array							 */
                                                                 $img_title = $attach_data->post_title;
                                                                 $img_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
																 $thumb_img = apply_filters('tmpl_thumb_image',$value['file']);
                                                                 ?>
                                                                 <li>				          	
                                                                      <img src="<?php echo $thumb_img; ?>"alt="<?php echo $img_title; ?>"  />				            
                                                                 </li>

                                                                 <?php
                                                       endforeach;
                                             endif;
                                             ?>
                                        </ul>
                                   </div>
                                   <!-- Finish More Image gallery -->
                                   <?php
                         endif;

                         do_action('directory_after_post_image');
                         ?>
                    </div><!-- .entry-header-image -->
                    <?php
          endif;

          if ($is_edit == "1"):
                    ?>
                    <!-- Front end edit upload image-->
                    <div id="directory_detail_img" class="entry-header-image">
                         <!--editing post images -->
                         <div id="slider" class="listing-image flexslider frontend_edit_image flex-viewport">
                              <ul class="frontend_edit_images_ul">
                                   <?php
                                   $post_img = bdw_get_images_plugin($post->ID, 'large');
                                   if (!empty($post_img)):
                                             foreach ($post_img as $key => $value):
                                                       echo "<li class='image' data-attachment_id='" . basename($value['file']) . "' data-attachment_src='" . $value['file'] . "'><img src='" . $value['file'] . "' alt='" . $img_title . "' /></li>";
                                                       break;
                                             endforeach;
                                   endif;
                                   ?>
                              </ul>
                              <div id="uploadimage" class="upload button secondary_btn clearfix">
                                   <span><?php _e("Upload Images", 'templatic'); ?></span>					
                              </div>
                         </div>

                         <div id="frontend_images_gallery_container" class="clearfix flex-viewport">
                              <ul class="frontend_images_gallery more_photos slides">
                                   <?php
                                   if (!empty($post_img)):
                                             foreach ($post_img as $key => $value):
                                                       echo "<li class='image' data-attachment_id='" . basename($value['file']) . "' data-attachment_src='" . $value['file'] . "'><img src='" . $value['file'] . "' alt='" . $img_title . "' /><span>
							<a class='delete' title='Delete image' href='#' id='" . $value['id'] . "' ><i class='fa fa-times-circle redcross'></i>";
                                                       echo "</a></span></li>";
                                             endforeach;
                                   endif;
                                   ?>
                              </ul>
                              <input type="hidden" id="fontend_image_gallery" name="fontend_image_gallery" value="<?php echo esc_attr(substr(@$image_gallery, 0, -1)); ?>" />		
                         </div>
                         <span id="forntend_status" class="message_error2 clearfix"></span>
                         <!--finish editing post images -->
                    </div>
          <?php endif; ?>

          <!-- Finish Image Gallery Div -->

     </section>
     <!--Overview Section End -->

     <?php if ($tmpdata['direction_map'] == 'yes' && $listing_address): ?>
               <!--Map Section Start -->
               <section role="tabpanel" aria-hidden="false" class="content" id="listing_map">
                    <?php do_action('directory_single_page_map') ?>
               </section>
               <!--Map Section End -->
               <?php
     endif;

     if (($special_offer != "" && $tmpl_flds_varname['proprty_feature'] ) || ($is_edit == 1 && $tmpl_flds_varname['proprty_feature'])):
               ?>
               <!--Special Offer Start -->
               <section role="tabpanel" aria-hidden="false" class="content" id="special_offer">
                    <div class="entry-proprty_feature frontend_proprty_feature <?php if ($is_edit == 1): ?>editblock <?php endif; ?>">
                         <?php
                         $special_offer = apply_filters('the_content', $special_offer);
                         $special_offer = str_replace(']]>', ']]&gt;', $special_offer);
                         echo $special_offer;
                         ?>
                    </div>
               </section>
               <!--Special Offer End -->
               <?php
     endif;

     if (($video != "" && $tmpl_flds_varname['video'] ) || ($is_edit == 1 && $tmpl_flds_varname['video'])):
               ?>
               <!--Video Code Start -->
               <section role="tabpanel" aria-hidden="false" class="content" id="listing_video">
                    <?php
                    if ($is_edit == 1):
                              do_action('oembed_video_description');
                              ?>
                              <span id="frontend_edit_video" class="frontend_oembed_video button" ><?php _e('Edit Video', 'templatic'); ?></span>
                              <input type="hidden" class="frontend_video" name="frontend_edit_video" value='<?php echo $video; ?>' />
                    <?php endif; ?>
                    <div class="frontend_edit_video"><?php
                         $embed_video = wp_oembed_get($video);
                         if ($embed_video != "") {
                                   echo $embed_video;
                         } else {
                                   echo $video;
                         }
                         ?>
                    </div>
               </section>
               <!--Video code End -->
               <?php
     endif;

     do_action('listing_extra_details');

     /* Display the events list on listing detail page */
     echo tmpl_events_on_place_list_details($events_list, $post);
     ?>


</div>
<?php
/* Display heading type with custom fields */
global $htmlvar_name, $heading_title;
$j = 0;
/* array of fields which we are not going to show on detail page */
$not_include = apply_filters('tmpl_exclude_custom_fields', array('category', 'post_title', 'post_content', 'post_excerpt', 'post_images', 'post_city_id', 'listing_timing', 'address', 'listing_logo', 'post_coupons', 'video', 'post_tags', 'map_view', 'proprty_feature', 'phone', 'email', 'website', 'twitter', 'facebook', 'google_plus', 'contact_info'));
/* get detail page custom fields selected as show on detail page yes */
do_action('tmpl_display_before_listing_custom_fields');
tmpl_fields_detail_informations($not_include, __('Other Details', DOMAIN));
do_action('tmpl_display_after_listing_custom_fields');
?>
<!--Directory Social Media Coding Start -->
<?php
if (function_exists('tevolution_socialmedia_sharelink'))
          tevolution_socialmedia_sharelink($post);
?>
<!--Directory Social Media Coding End -->

<?php
if (isset($tmpdata['templatic_view_counter']) && $tmpdata['templatic_view_counter'] == 'Yes') {
          if (function_exists('view_counter_single_post')) {
                    view_counter_single_post(get_the_ID());
          }
          $post_visit_count = (get_post_meta(get_the_ID(), 'viewed_count', true)) ? get_post_meta(get_the_ID(), 'viewed_count', true) : '0';
          $post_visit_daily_count = (get_post_meta(get_the_ID(), 'viewed_count_daily', true)) ? get_post_meta(get_the_ID(), 'viewed_count_daily', true) : '0';
          $custom_content = '';
          echo "<div class='view_counter'>";
          echo "<p>";
          _e('Visited', 'templatic');
          echo " " . $post_visit_count . " ";
          ($post_visit_count == 1) ? _e('time', 'templatic') : _e('times', 'templatic');
          echo ', ' . $post_visit_daily_count . " ";
          ($post_visit_daily_count == 1) ? _e("Visit today", 'templatic') : _e("Visits today", 'templatic');
          echo "</p>";
          echo '</div>';
}
/* click on map tab if overview tab was not there */
if ($post->post_content == '' || count($post_img) <= 0 || count($custom_fields_as_tabs) <= 0 || !isset($_REQUEST['action'])) {
          ?>
          <script>
                    jQuery(document).ready(function () {
                         jQuery('.tabs li').first().find("a").trigger('click');
                    });
          </script>
          <?php
}
?>