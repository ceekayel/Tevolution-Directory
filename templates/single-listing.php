<?php
/**
 * Tevolution single custom post type template
 *
 * */
get_header();
$tmpdata = get_option('templatic_settings');
do_action('directory_before_container_breadcrumb'); /* do action for display the breadcrumb in between header and container. */
$is_edit = '';
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
          $is_edit = 1;
}

global $tmpl_flds_varname;
/* to get the common/context custom fields display by default with current post type */
if (function_exists('tmpl_single_page_default_custom_field')) {
          $tmpl_flds_varname = tmpl_single_page_default_custom_field(get_post_type());
}
?>
<!-- start content part-->
<div id="content" class="large-9 small-12 columns" role="main">	
     <?php
     /* do action for display the breadcrumb  inside the container. */
     do_action('directory_inside_container_breadcrumb');
     if (function_exists('supreme_sidebar_before_content')) {
               /* Loads the sidebar-before-content. */
               apply_filters('tmpl_before-content', supreme_sidebar_before_content());
     }
     while (have_posts()) : the_post();
               do_action('directory_before_post_loop');
               ?>
               <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>  
                    <!--start post type title -->
                    <?php do_action('directory_before_post_title');         /* do action for before the post title. */ ?>

                    <header class="entry-header">
                         <?php $listing_logo = get_post_meta(get_the_ID(), 'listing_logo', true); ?>
                         <!-- Start Image Upload -->
                         <?php if (($listing_logo != "" && $tmpl_flds_varname['listing_logo']) && ($is_edit == "")): ?>
                                   <div class="entry-header-logo">
                                        <img src="<?php echo $listing_logo ?>" alt="<?php echo $tmpl_flds_varname['listing_logo']['label']; ?>" />
                                   </div>
                         <?php elseif ($is_edit == 1 && $tmpl_flds_varname['listing_logo']): ?>
                                   <div class="entry-header-logo" >
                                        <div style="display:none;" class="frontend_listing_logo"><?php echo $listing_logo ?></div>
                                        <!--input id="fronted_files_listing_logo" class="fronted_files" type="file" multiple="true" accept="image/*" /-->
                                        <div id="fronted_upload_listing_logo" class="frontend_uploader button" data-src="<?php echo $listing_logo ?>">	                 	
                                             <span><?php _e('Upload ', DIR_DOMAIN) . $tmpl_flds_varname['listing_logo']['label']; ?></span>						
                                        </div>
                                   </div>
                         <?php endif; do_action('tmpl_after_logo');?>

                         <!-- End Image Upload -->
                         <section class="entry-header-title">
                              <h1 itemprop="name" class="entry-title <?php if ($is_edit == 1): ?>frontend-entry-title <?php endif; ?>" <?php if ($is_edit == 1): ?> contenteditable="true"<?php endif; ?> >
                                   <?php
                                   do_action('before_title_h1');
                                   the_title();
                                   do_action('after_title_h1');
                                   ?>
                              </h1>
                              <?php
                              if ($tmpdata['templatin_rating'] == 'yes'):
                                        $total = get_post_total_rating(get_the_ID());
                                        $total = ($total == '') ? 0 : $total;
                                        $review_text = ($total == 1) ? '<a href="#comments">' . $total . ' ' . __('Review', DIR_DOMAIN) . '</a>' : '<a href="#comments">' . $total . ' ' . __('Reviews', DIR_DOMAIN) . '</a>';
                                        ?>
                                        <div class="listing_rating">
                                             <div class="directory_rating_row"><span class="single_rating"> <?php echo draw_rating_star_plugin(get_post_average_rating(get_the_ID())); ?> <span><?php echo $review_text ?></span></span></div>
                                        </div>
                              <?php
                              endif;
                              do_action('directory_display_rating', get_the_ID());
                              ?>					
                              <article  class="entry-header-custom-wrap">
                                   <div class="entry-header-custom-left">
                                        <?php
                                        global $htmlvar_name;
                                        $address = get_post_meta(get_the_ID(), 'address', true);
                                        $website = get_post_meta(get_the_ID(), 'website', true);
                                        $phone = get_post_meta(get_the_ID(), 'phone', true);
                                        $listing_timing = get_post_meta(get_the_ID(), 'listing_timing', true);
                                        $email = get_post_meta(get_the_ID(), 'email', true);
                                        if ($address != "" && $tmpl_flds_varname['address']):
                                                  ?>
                                                  <p class="entry_address<?php echo $tmpl_flds_varname['address']['style_class']; ?>"><span id="frontend_address" class="listing_custom frontend_address" <?php if ($is_edit == 1): ?>contenteditable="true"<?php endif; ?>><?php echo $address; ?></span></p>
                                                  <?php
                                                  do_action('directory_after_address');
                                        endif;
                                        if ($website != "" && $tmpl_flds_varname['website'] || ($is_edit == 1)):
                                                  if (!strstr($website, 'http'))
                                                            $website = 'http://' . $website;
                                                  ?>
                                                  <p class="website <?php echo $tmpl_flds_varname['website']['style_class']; ?>"><a target="_blank" id="website" class="frontend_website <?php if ($is_edit == 1): ?>frontend_link<?php endif; ?>" href="<?php echo $website; ?>" ><span><?php echo $tmpl_flds_varname['website']['label']; ?></span></a></p>
                                                  <?php
                                        endif;
                                        do_action('directory_display_custom_fields_default_left');
                                        ?>
                                   </div>

                                   <div class="entry-header-custom-right">
                                        <?php if ($phone != "" && $tmpl_flds_varname['phone'] || ($is_edit == 1 && $tmpl_flds_varname['phone'])): ?>
                                                  <p class="phone <?php echo $tmpl_flds_varname['phone']['style_class']; ?>"><label><?php echo $tmpl_flds_varname['phone']['label']; ?>: </label><span class="entry-phone frontend_phone listing_custom" <?php if ($is_edit == 1): ?>contenteditable="true" <?php endif; ?>><?php echo $phone; ?></span></p>
                                        <?php
                                        endif;

                                        if ($listing_timing != "" && $tmpl_flds_varname['listing_timing'] || ($is_edit == 1 && $tmpl_flds_varname['listing_timing'])):
                                                  ?>
                                                  <p class="time <?php echo $tmpl_flds_varname['listing_timing']['style_class']; ?>"><label><?php echo $tmpl_flds_varname['listing_timing']['label']; ?>: </label><span class="entry-listing_timing frontend_listing_timing listing_custom" <?php if ($is_edit == 1): ?>contenteditable="true" <?php endif; ?>><?php echo $listing_timing; ?></span></p>
                                        <?php
                                        endif;

                                        if (@$email != "" && @$tmpl_flds_varname['email'] || ($is_edit == 1 && @$tmpl_flds_varname['email'])):
                                                  ?>
                                                  <p class="email  <?php echo $tmpl_flds_varname['email']['style_class']; ?>"><label><?php echo $tmpl_flds_varname['email']['label']; ?>: </label><span class="entry-email frontend_email listing_custom" <?php if ($is_edit == 1): ?>contenteditable="true"<?php endif; ?>><?php echo antispambot($email); ?></span></p>
                                                  <?php
                                        endif;
                                        do_action('directory_display_custom_fields_default_right');
                                        ?>
                                   </div>
                                   <?php do_action('directory_display_after_custom_fields_default'); ?>
                              </article>
                         </section>
                    </header>

                    <?php
                    /* do action for after the post title. */
                    do_action('directory_after_post_title');
                    ?>
                    <!--end post type title -->               


                    <!--Code start for single captcha -->   
                    <?php
                    $display = (isset($tmpdata['user_verification_page'])) ? $tmpdata['user_verification_page'] : array();
                    $captcha_dis = '';
                    if (count($display) > 0 && !empty($display)) {
                              foreach ($display as $_display) {
                                        if ($_display == 'claim' || $_display == 'emaitofrd' || $_display == 'sendinquiry') {
                                                  $captcha_dis = $_display;
                                                  break;
                                        }
                              }
                    }
                    $recaptcha = get_option("recaptcha_options");
                    global $current_user;
                    ?>

                    <div id="myrecap" style="display:none;"><?php
               if ($recaptcha['show_in_comments'] != 1 || $current_user->ID != '') {
                         templ_captcha_integrate($captcha_dis);
               }
               ?></div> 
                    <input type="hidden" id="owner_frm" name="owner_frm" value=""  />
                    <div id="claim_ship"></div>
                    <script type="text/javascript" async >
                              var RECAPTCHA_COMMENT = '';
					<?php if ($recaptcha['show_in_comments'] != 1 || $current_user->ID != '') { ?>
                            jQuery('#owner_frm').val(jQuery('#myrecap').html());
					<?php } else {
						?>
							RECAPTCHA_COMMENT = <?php echo $recaptcha['show_in_comments']; ?>;
                    <?php } ?>
                    </script>

                    <!--Code end for single captcha -->
                    <!-- listing content-->
                    <section class="entry-content">
          <?php
          get_template_part('directory', get_post_type() . '-single-content');
          ?>
                    </section>
                    <!--Finish the listing Content -->

                    <!--Custom field collection do action -->
                    <?php
                    do_action('directory_custom_fields_collection');

                    do_action('directory_extra_single_content');

                    /* Display categories on detail page */
                    do_action('directory_the_taxonomies');
                    ?>          

               </div>
               <?php
               do_action('directory_after_post_loop');

               do_action('directory_edit_link');
     endwhile; /* end of the loop. */

     wp_reset_query(); /* reset the wp query */

     /* add action for display the next previous pagination */
     do_action('tmpl_single_post_pagination');

     /* add action for display before the post comments. */
     do_action('tmpl_before_comments');

     do_action('after_entry');

     do_action('for_comments');

     /* Add action for display after the post comments. */
     do_action('tmpl_after_comments');

     global $post;

     do_action('tmpl_related_listings'); /* add action for display the related post list. */

     if (function_exists('supreme_sidebar_after_content'))
               apply_filters('tmpl_after-content', supreme_sidebar_after_content()); /* after-content-sidebar use remove filter to don't display it */
     ?>
</div><!-- #content -->

<!--single post type sidebar -->
     <?php if (is_active_sidebar(get_post_type() . '_detail_sidebar')) : ?>
          <aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
          <?php
          do_action('above_' . get_post_type() . '_detail_sidebar');
          dynamic_sidebar(get_post_type() . '_detail_sidebar');
          do_action('below_' . get_post_type() . '_detail_sidebar');
          ?>		
          </aside>
               <?php
     elseif (is_active_sidebar('primary-sidebar')) :
               do_action('above_' . get_post_type() . '_detail_sidebar');
               ?>
          <aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
          <?php dynamic_sidebar('primary-sidebar'); ?>
          </aside>
          <?php
          do_action('below_' . get_post_type() . '_detail_sidebar');
endif;
?>
<!--end single post type sidebar -->
<!-- end  content part-->
<?php get_footer(); ?>