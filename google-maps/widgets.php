<?php
/* Widgets - widgets.php */
/*
 * Common widgets for all tevolution add ons
 */
if (!defined('DIR_DOMAIN'))
          @define('DIR_DOMAIN', 'templatic');
add_action('widgets_init', 'tmpl_plugin_reg_widgets');

function tmpl_plugin_reg_widgets() {
          register_widget('directory_neighborhood');
          register_widget('directory_featured_category_list');
          register_widget('directory_mile_range_widget');
          register_widget('directory_featured_homepage_listing');
}

/* End of location wise search widget */

/*
  Name : directory_neighborhood
  Desc: neighborhood posts Widget (particular category)
 */

class directory_neighborhood extends WP_Widget {

          function directory_neighborhood() {
                    /* Constructor */
                    $widget_ops = array('classname' => 'widget In the neighborhood', 'description' => __('Display posts that are in the vicinity of the post that is currently displayed. Use in detail page sidebar areas.', DIR_DOMAIN));
                    $this->WP_Widget('directory_neighborhood', __('T &rarr; In The Neighborhood', DIR_DOMAIN), $widget_ops);
          }

          function widget($args, $instance) {
                    extract($args, EXTR_SKIP);
                    global $miles, $wpdb, $post, $single_post, $wp_query, $current_cityinfo;
                    global $current_post, $post_number;
                    $current_post = $post->ID;
                    $title = empty($instance['title']) ? __("Nearest Listing", DIR_DOMAIN) : apply_filters('widget_title', $instance['title']);
                    $post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
                    $post_number = empty($instance['post_number']) ? '5' : apply_filters('widget_post_number', $instance['post_number']);
                    $radius = empty($instance['radius']) ? '0' : apply_filters('widget_radius', $instance['radius']);
                    $closer_factor = empty($instance['closer_factor']) ? 0 : apply_filters('widget_closer_factor', $instance['closer_factor']);
                    $radius_measure = empty($instance['radius_measure']) ? '0' : apply_filters('widget_radius_measure', $instance['radius_measure']);

                    if(function_exists('tmpl_single_page_default_custom_field')){
                              $varname = tmpl_single_page_default_custom_field($post_type);
                    }
                    
                    /* get the current post details */
                    $current_post_details = get_post($post->ID);
                    echo $before_widget;
                    ?>

                    <div class="neighborhood_widget">
                      <?php
                        echo '<h3 class="widget-title">' . $title . '</h3>';
                         $miles = (strtolower($radius_measure) == strtolower('Kilometer')) ? $radius / 0.621 : $radius;

                            add_filter('posts_where', 'directory_nearby_filter');
                            if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
                                      add_filter('posts_where', 'wpml_listing_milewise_search_language');
                            }
                            $args = array(
                                   'post__not_in' => array($current_post),
                                      'post_status' => 'publish',
                                      'post_type' => $post_type,
                                      'posts_per_page' => $post_number,
                                      'ignore_sticky_posts' => 1,
                                      'orderby' => 'rand'
                            );
                            if (is_plugin_active('Tevolution-LocationManager/location-manager.php')) {
                                      add_filter('posts_where', 'location_multicity_where');
                            }
                            $wp_query_near = new WP_Query($args);
                            if (is_plugin_active('Tevolution-LocationManager/location-manager.php')) {
                                      remove_filter('posts_where', 'location_multicity_where');
                            }
                            if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
                                      remove_filter('posts_where', 'wpml_listing_milewise_search_language');
                            }
                            if ($wp_query_near->have_posts()):
                                      echo '<ul class="nearby_distance">';
                                      while ($wp_query_near->have_posts()) {
                                           $wp_query_near->the_post();
                                           echo '<li class="nearby clearfix">';

                                           if (has_post_thumbnail()) {
                                                     $post_img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'tevolution_thumbnail');
                                                     $post_images = $post_img[0];
                                           } else {
                                                     $post_img = bdw_get_images_plugin(get_the_ID(), 'tevolution_thumbnail');
                                                     $post_images = $post_img[0]['file'];
                                           }
                                           $image = ($post_images) ? $post_images : TEVOLUTION_DIRECTORY_URL . 'images/no-image.png';
                                           ?>
                                              <div class='nearby_image'> <a href="<?php echo get_permalink($post->post_id); ?>"> <img src="<?php echo $image ?>" alt="<?php echo get_the_title($post->post_id); ?>" title="<?php echo get_the_title($post->post_id); ?>" class="thumb" /> </a> </div>
                                              <div class='nearby_content'>
                                                <h4><a href="<?php echo get_permalink($post->post_id); ?>">
                                                  <?php the_title(); ?>
                                                  </a></h4>
                                                <?php if($varname['address']): ?>
                                                <p class="address">
                                                  <?php
                                                     $address = get_post_meta(get_the_ID(), 'address', true);
                                                     echo $address;
                                                     ?>
                                                  </p>
                                                  <?php endif; ?>
                                                </div>
                                                <?php
                                           echo '</li>';
                                     }
                                     echo '</ul>';
                            else:
                                      _e('Sorry! There is no near by results found', DIR_DOMAIN);
                            endif;
                            remove_filter('posts_where', 'nearby_filter');
                            wp_reset_query();
                         ?>
                    </div>
                    <?php
                    echo $after_widget;
          }

          function update($new_instance, $old_instance) {
                    /* save the widget		 */
                    return $new_instance;
          }

          function form($instance) {
                    /* widgetform in backend */
                    $instance = wp_parse_args((array) $instance, array('title' => __("Nearest Listing", DIR_DOMAIN), 'post_type' => 'listing', 'post_number' => 5, 'closer_factor' => 2));
                    $title = strip_tags($instance['title']);
                    $post_type = strip_tags($instance['post_type']);
                    $post_number = strip_tags($instance['post_number']);
                    $post_link = strip_tags($instance['post_link']);
                    $closer_factor = strip_tags($instance['closer_factor']);

                    $distance_factor = strip_tags($instance['radius']);
                    $radius_measure = strip_tags($instance['radius_measure']);
                    ?>
					<script  type="text/javascript" async >
                              function select_show_list(id, div_def, div_custom)
                              {
                                   var checked = id.checked;
                                   jQuery('#' + div_def).slideToggle('slow');
                                   jQuery('#' + div_custom).slideToggle('slow');
                              }
                    </script>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'templatic-admin'); ?>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_type'); ?>" ><?php echo __('Select Post:', DIR_DOMAIN); ?> </label>
  <select  id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat">
    <?php
                              $all_post_types = get_option("templatic_custom_post");
                              foreach ($all_post_types as $key => $post_types) {
                                        ?>
    <option value="<?php echo $key; ?>" <?php if ($key == $post_type) echo "selected"; ?>><?php echo esc_attr($post_types['label']); ?></option>
    <?php
                              }
                              ?>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_number'); ?>"><?php echo __('Number of posts', DIR_DOMAIN); ?>
    <input class="widefat" id="<?php echo $this->get_field_id('post_number'); ?>" name="<?php echo $this->get_field_name('post_number'); ?>" type="text" value="<?php echo esc_attr($post_number); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('radius'); ?>"><?php echo __('Select Distance', DIR_DOMAIN); ?>
    <select id="<?php echo $this->get_field_id('radius'); ?>" name="<?php echo $this->get_field_name('radius'); ?>">
      <option value="1" <?php
                                   if (esc_attr($distance_factor) == '1') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('1', DIR_DOMAIN); ?></option>
      <option value="5" <?php
                                   if (esc_attr($distance_factor) == '5') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('5', DIR_DOMAIN); ?></option>
      <option value="10" <?php
                                   if (esc_attr($distance_factor) == '10') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('10', DIR_DOMAIN); ?></option>
      <option value="100" <?php
                                   if (esc_attr($distance_factor) == '100') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('100', DIR_DOMAIN); ?></option>
      <option value="1000" <?php
                                   if (esc_attr($distance_factor) == '1000') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('1000', DIR_DOMAIN); ?></option>
      <option value="5000" <?php
                                   if (esc_attr($distance_factor) == '5000') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('5000', DIR_DOMAIN); ?></option>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('radius_measure'); ?>"><?php echo __('Display By', DIR_DOMAIN); ?>
    <select id="<?php echo $this->get_field_id('radius_measure'); ?>" name="<?php echo $this->get_field_name('radius_measure'); ?>">
      <option value="kilometer" <?php
                                   if (esc_attr($radius_measure) == 'kilometer') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('Kilometers', DIR_DOMAIN); ?></option>
      <option value="miles" <?php
                                   if (esc_attr($radius_measure) == 'miles') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('Miles', DIR_DOMAIN); ?></option>
    </select>
  </label>
</p>
<?php
          }

}

/* End of directory_neighborhood */

if (!function_exists('directory_content_limit')) {

          function directory_content_limit($max_char, $more_link_text = '', $stripteaser = true, $more_file = '') {
                    global $post;

                    $content = get_the_content();
                    $content = strip_tags($content);
                    $content = substr($content, 0, $max_char);
                    $content = substr($content, 0, strrpos($content, " "));
                    $more_link_text = '<a href="' . get_permalink() . '">' . $more_link_text . '</a>';
                    $content = $content . " " . $more_link_text;
                    echo $content;
          }

}
/*
 * Class Name: directory_featured_category_list
 * Return: display all the category list on home page
 */

class directory_featured_category_list extends WP_Widget {

          function directory_featured_category_list() {
                    /* Constructor */
                    $widget_ops = array('classname' => 'all_category_list_widget', 'description' => __('Shows a list of all categories and their sub-categories. Works best in main content and subsidiary areas.', 'templatic-admin'));
                    $this->WP_Widget('directory_featured_category_list', __('T &rarr; All Categories List', 'templatic-admin'), $widget_ops);
          }

          function widget($args, $instance) {
                    /* prints the widget */
                    global $current_cityinfo;
                    extract($args, EXTR_SKIP);
                    $cur_lang_code = (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) ? ICL_LANGUAGE_CODE : 'en';
                    $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
                    $post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
                    $category_level = empty($instance['category_level']) ? '1' : apply_filters('widget_category_level', $instance['category_level']);
                    $number_of_category = ($instance['number_of_category'] == '') ? '6' : apply_filters('widget_number_of_category', $instance['number_of_category']);
                    $hide_empty_cat = ($instance['hide_empty_cat'] == '') ? '0' : apply_filters('widget_hide_empty_cat', $instance['hide_empty_cat']);
                    $taxonomies = get_object_taxonomies((object) array('post_type' => $post_type, 'public' => true, '_builtin' => true));

                    echo $before_widget;

                    if ($title) {
                        echo '<h3 class="widget-title">' . $title . '</h3>';
                    }
                    
                        /* Rest api plugin active than result fetch with ajax */
                        if(is_plugin_active( 'json-rest-api/plugin.php' ) && is_plugin_active('Tevolution/templatic.php')){
                            $ajaxurl = site_url()."/wp-json/browse/all-categories-list";
                            $unique_string = rand();
                            echo "<div id=all_category_list_widget_$unique_string></div>";
							?>
                            <script type="text/javascript" async>
                                var acl_ajaxUrl = '<?php echo $ajaxurl;?>'
                                var acl_post_type = '<?php echo $post_type;?>';
                                var acl_category_level = '<?php echo $category_level;?>';
                                var acl_number_of_category = '<?php echo $number_of_category;?>';
                                var acl_hide_empty_cat = '<?php echo $hide_empty_cat;?>';
                                var acl_unique_string = '<?php echo $unique_string;?>';
                            </script>
                            <?php
                                add_action('wp_footer','tmpl_all_category_list_api_widget',99);
                                if(!function_exists('tmpl_all_category_list_api_widget')){
                                    function tmpl_all_category_list_api_widget(){
                                    ?>
                                        <script type="text/javascript" async>
                                            jQuery(document).ready(function(){
                                                jQuery('#all_category_list_widget_'+acl_unique_string).html('<br><i class="fa fa-2x fa-circle-o-notch fa-spin"></i>');
                                                 jQuery.ajax({
                                                        url: acl_ajaxUrl,
                                                        async: true,
                                                        data:{
                                                            filter: {
                                                                'post_type': acl_post_type,
                                                                'category_level': acl_category_level,
                                                                'number_of_category': acl_number_of_category,
                                                                'hide_empty_cat': acl_hide_empty_cat
                                                                }
                                                            },
                                                        dataType: 'json',
                                                        type: 'GET',
                                                        success:function(results){	
                                                                jQuery('#all_category_list_widget_'+acl_unique_string).html(results);
                                                        },
                                                        error:function(){
                                                                jQuery('#all_category_list_widget_'+acl_unique_string).html('<h3>No category found.</h3>');
                                                        }   
                                                });	
                                            });
                                        </script>
                                <?php 
                                    }
                                }
                            }else{ 
                                    
                                            $args5 = array(
                                                'orderby' => 'name',
                                                'taxonomy' => $taxonomies[0],
                                                'order' => 'ASC',
                                                'parent' => '0',
                                                'show_count' => 0,
                                                'hide_empty' => 0,
                                                'pad_counts' => true,
                                            );
                                        ?>
					<section class="category_list_wrap row">
					<?php
						/* set wp_categories on transient */
						if (get_option('tevolution_cache_disable') == 1 && false === ( $categories = get_transient('_tevolution_query_catwidget' . $post_type . $cur_lang_code) )) {
								  $categories = get_categories($args5);
								  set_transient('_tevolution_query_catwidget' . $post_type . $cur_lang_code, $categories, 12 * HOUR_IN_SECONDS);
						} elseif (get_option('tevolution_cache_disable') == '') {
								  $categories = get_categories($args5);
						}
					
						if (!isset($categories['errors'])) {
						foreach ($categories as $category) {
							/* set child wp_categories on transient */

						$transient_name = (!empty($current_cityinfo)) ? $current_cityinfo['city_slug'] : '';
						if (get_option('tevolution_cache_disable') == 1 && false === ( $featured_catlist_list = get_transient('_tevolution_query_catwidget' . $category->term_id . $post_type . $transient_name . $cur_lang_code) )) {
						   do_action('tevolution_category_query');
						   $featured_catlist_list = wp_list_categories('title_li=&child_of=' . $category->term_id . '&echo=0&depth=' . $category_level . '&number=' . $number_of_category . '&taxonomy=' . $taxonomies[0] . '&show_count=1&hide_empty=' . $hide_empty_cat . '&pad_counts=0&show_option_none=');
						   set_transient('_tevolution_query_catwidget' . $category->term_id . $post_type . $transient_name . $cur_lang_code, $featured_catlist_list, 12 * HOUR_IN_SECONDS);
						} elseif (get_option('tevolution_cache_disable') == '') {
						   do_action('tevolution_category_query');
						   $featured_catlist_list = wp_list_categories('title_li=&child_of=' . $category->term_id . '&echo=0&depth=' . $category_level . '&number=' . $number_of_category . '&taxonomy=' . $taxonomies[0] . '&show_count=1&hide_empty=' . $hide_empty_cat . '&pad_counts=0&show_option_none=');
						}
						if (is_plugin_active('Tevolution-LocationManager/location-manager.php')) {
						   remove_filter('terms_clauses', 'locationwise_change_category_query', 10, 3);
						}
						$parent = get_term($category->term_id, $taxonomies[0]);
						if ($hide_empty_cat == 1) {
						   if ($parent->count != 0 || $featured_catlist_list != "") {
									 ?>
						<article class="category_list large-4 medium-4 small-6 xsmall-12 columns">
						<?php
						if ($parent) {
								$parents = '<a href="' . get_term_link($parent, $taxonomies[0]) . '" title="' . esc_attr($parent->name) . '">' . apply_filters('list_cats', $parent->name, $parent) . '</a>';
								if ($hide_empty_cat == 1) {
									if ($parent->count != 0) {
									?>
						<h3>
							<?php
								do_action('show_categoty_map_icon', $parent->term_icon);
								echo $parents;
							?>
						</h3>
						<?php
									}
								}else {
						?>
									<h3>
									<?php
									do_action('show_categoty_map_icon', $parent->term_icon);
									echo $parents;
									?>
									</h3>
						<?php
							}

													if (@$featured_catlist_list != "") {
															  if ($number_of_category != 0) {
																		if ($parent->count == 0) {
																				  ?>
						<h3>
						<?php
																					   do_action('show_categoty_map_icon', $parent->term_icon);
																					   echo $parents;
																					   ?>
						</h3>
						<?php } ?>
						<ul>
						<?php echo $featured_catlist_list; ?>
						<li class="view"> <a href="<?php echo get_term_link($parent, $taxonomies[0]); ?>">
						<?php _e('View all &raquo;', DIR_DOMAIN) ?>
						</a> </li>
						</ul>
						<?php
															  }
													}
										  }
										  ?>
						</article>
						<?php
						   }
						} else {
						   ?>
						<article class="category_list large-4 medium-4 small-6 xsmall-12 columns">
						<?php
								if ($parent && $taxonomies[0]) {
										  $parents = '<a href="' . get_term_link($parent, $taxonomies[0]) . '" title="' . esc_attr($parent->name) . '">' . apply_filters('list_cats', $parent->name, $parent) . '</a>';
										  ?>
						<h3>
						<?php
											   include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
											   if (is_plugin_active('Tevolution-CategoryIcon/tevolution-categoryicon.php')) {
														 if (@$parent->category_icon == '') {
																   do_action('show_categoty_map_icon', $term_icon);
														 }
											   } else {
														 do_action('show_categoty_map_icon', $parent->term_icon);
											   }echo $parents;
											   ?>
						</h3>
						<?php
										  if (@$featured_catlist_list != "") {
													if ($number_of_category != 0) {
															  ?>
						<ul>
						<?php echo $featured_catlist_list; ?>
						<li class="view"> <a href="<?php echo get_term_link($parent, $taxonomies[0]); ?>">
						<?php _e('View all &raquo;', DIR_DOMAIN) ?>
						</a> </li>
						</ul>
						<?php
													}
										  }
								}
								?>
						</article>
						<?php
						}
						}
						} else {
						echo '<p>' . __('Invalid Category.', DIR_DOMAIN) . '</p>';
						}
					
					?>
					</section>
					<?php
          }
                    echo $after_widget;
          }

          function update($new_instance, $old_instance) {
                    /* save the widget	 */
                    global $wpdb;
                    $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name like '%s'", '%_tevolution_query_catwidget%'));
                    return $new_instance;
          }

          function form($instance) {
                    /* widgetform in backend */
                    $instance = wp_parse_args((array) $instance, array('title' => '', 'category_level' => '1', 'number_of_category' => '5'));
                    $title = strip_tags($instance['title']);
                    $my_post_type = ($instance['post_type']) ? $instance['post_type'] : 'listing';
                    $category_level = ($instance['category_level']);
                    $number_of_category = ($instance['number_of_category']);
                    $hide_empty_cat = ($instance['hide_empty_cat']);
                    ?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title:', 'templatic-admin'); ?>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php echo __('Post Type:', 'templatic-admin') ?>
    <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
      <?php
                                   $all_post_types = apply_filters('tmpl_allow_fields_posttype', get_option("templatic_custom_post"));
                                   foreach ($all_post_types as $key => $post_type) {
                                             ?>
      <option value="<?php echo $key; ?>" <?php if ($key == $my_post_type) echo "selected"; ?>><?php echo esc_attr($post_type['label']); ?></option>
      <?php } ?>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('category_level'); ?>"><?php echo __('Category Level', 'templatic-admin'); ?>:
    <select id="<?php echo $this->get_field_id('category_level'); ?>" name="<?php echo $this->get_field_name('category_level'); ?>">
      <?php
                                   for ($i = 1; $i <= 10; $i++) {
                                             ?>
      <option value="<?php echo $i; ?>" <?php if (esc_attr($category_level) == $i) { ?> selected="selected" <?php } ?>><?php echo $i; ?></option>
      <?php }
                                   ?>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('number_of_category'); ?>"><?php echo __('Number of child categories', 'templatic-admin'); ?>:
    <input class="widefat" id="<?php echo $this->get_field_id('number_of_category'); ?>" name="<?php echo $this->get_field_name('number_of_category'); ?>" type="text" value="<?php echo esc_attr($number_of_category); ?>" />
  </label>
</p>
<?php if (!is_plugin_active('Tevolution-LocationManager/location-manager.php')) { ?>
<p>
  <label for="<?php echo $this->get_field_id('hide_empty_cat'); ?>">
    <input class="widefat" id="<?php echo $this->get_field_id('hide_empty_cat'); ?>" name="<?php echo $this->get_field_name('hide_empty_cat'); ?>" type="checkbox" value="1" <?php
                                        if (@$hide_empty_cat == 1) {
                                                  echo "checked=checked";
                                        }
                                        ?>/>
    <?php echo __('Hide empty categories', 'templatic-admin'); ?></label>
</p>
<?php } ?>
<?php
          }

}

/*
  directory_mile_range_widget : Miles wise searching widget
 */

class directory_mile_range_widget extends WP_Widget {

          function directory_mile_range_widget() {
                    /* Constructor */
                    $widget_ops = array('classname' => 'search_miles_range', 'description' => __('Search through nearby posts by setting a range. Use in category page sidebar areas.', 'templatic-admin'));
                    $this->WP_Widget('directory_mile_range_widget', __('T &rarr; Search by Miles Range', 'templatic-admin'), $widget_ops);
          }

          function widget($args, $instance) {
                    /* prints the widget */
                    extract($args, EXTR_SKIP);
                    $title = empty($instance['title']) ? 'Search Near By Miles Range' : apply_filters('widget_title', $instance['title']);
                    /* $post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']); */
                    $post_type = get_post_type();
                    $miles_search = empty($instance['miles_search']) ? '' : apply_filters('widget_miles_search', $instance['miles_search']);
                    $max_range = empty($instance['max_range']) ? '' : apply_filters('widget_max_range', $instance['max_range']);
                    $radius_measure = empty($instance['radius_measure']) ? 'miles' : apply_filters('widget_radius_measure', $instance['radius_measure']);
                    echo $before_widget;
                    $search_txt = sprintf(__('Find a %s', DIR_DOMAIN), $post_type);
                    wp_enqueue_script("jquery-ui-slider");
                    echo '<div class="search_nearby_widget">';
                    if ($title) {
                              echo '<h3 class="widget-title">' . $title . '</h3>';
                    }
                    global $wpdb, $wp_query;

                    if (is_tax()) {
                              $list_id = 'loop_' . $post_type . '_taxonomy';
                              $page_type = 'taxonomy';
                    } else {
                              $list_id = 'loop_' . $post_type . '_taxonomy';
                              $page_type = 'archive';
                    }


                    $queried_object = get_queried_object();
                    $term_id = $queried_object->term_id;
                    $query_string = '&term_id=' . $term_id;
                    ?>
<form method="get" id="searchform" action="<?php echo home_url(); ?>/">
  <input type="hidden"  class="miles_range_post_type" name="post_type" value="<?php echo $post_type; ?>" />
  <div class="search_range">
    <input type="text" name="range_address" id="range_address" value="" class="range_address location placeholder" placeholder="<?php _e('Enter your address', DOMAIN); ?>"/>
    <?php if ($radius_measure == "miles"): ?>
    <label>
      <?php _e('Mile range:', DIR_DOMAIN); ?>
    </label>
    <?php else: ?>
    <label>
      <?php _e('Kilometer range:', DIR_DOMAIN); ?>
    </label>
    <?php endif; ?>
    <input type="text" name="radius" id="radius_range" value="<?php echo $max_range; ?>" style="border:0; font-weight:bold;"  readonly="readonly"/>
  </div>
  <div id="radius-range"></div>
	<script  type="text/javascript" async >
					var slider_ajax = map_ajax = null;
					jQuery('#radius-range').bind('slidestop', function (event, ui) {
					var miles_range = jQuery('#radius_range').val();
					var range_address = jQuery('#range_address').val();
					var list_id = '<?php echo $list_id ?>';
					jQuery('.' + list_id + '_process').remove();
					jQuery('#' + list_id).addClass("loading_results");
                    <?php
                    if (isset($_SERVER['QUERY_STRING'])) {
                              $query_string.='&' . $_SERVER['QUERY_STRING'];
                    }
                    ?>
                                        slider_ajax = jQuery.ajax({
                                             url: ajaxUrl,
                                             type: 'POST',
                                             beforeSend: function () {
                                                  if (slider_ajax != null) {
                                                       slider_ajax.abort();
                                                  }
                                             },
                                             data: 'action=<?php echo $post_type . "_search"; ?>&posttype=<?php echo $post_type; ?>&range_address=' + range_address + '&miles_range=' + miles_range + '&defaul_range=<?php echo '1-' . $max_range; ?>&page_type=<?php echo $page_type . $query_string; ?>&radius_measure=<?php echo $radius_measure; ?>',
                                             success: function (results) {
                                                  jQuery('.' + list_id + '_process').remove();
                                                  jQuery('#' + list_id).html(results);
                                                  jQuery('#listpagi').remove();
                                                  jQuery('#' + list_id).removeClass("loading_results");
                                             }
                                        });

                                        map_ajax = jQuery.ajax({
                                             url: ajaxUrl,
                                             type: 'POST',
                                             beforeSend: function () {
                                                  if (map_ajax != null) {
                                                       map_ajax.abort();
                                                  }
                                             },
                                             dataType: 'json',
                                             data: 'action=<?php echo $post_type . "_search_map"; ?>&posttype=<?php echo $post_type; ?>&range_address=' + range_address + '&miles_range=' + miles_range + '&defaul_range=<?php echo '1-' . $max_range; ?>&page_type=<?php echo $page_type . $query_string; ?>&radius_measure=<?php echo $radius_measure; ?>',
                                             success: function (results) {
                                                  googlemaplisting_deleteMarkers();
                                                  markers = results.markers;
                                                  templ_add_googlemap_markers(markers);
                                             }
                                        });
                                   });
                                   jQuery('#list_paggination .page-numbers').on('click', function (e) {
                                        e.preventDefault();
                                        var page_link = jQuery(this).children().html();
                                        var miles_range = jQuery('#radius_range').val();
                                        var range_address = jQuery('#range_address').val();
                                        var list_id = '<?php echo $list_id ?>';
                    <?php
                    if (isset($_SERVER['QUERY_STRING'])) {
                              $query_string.='&' . $_SERVER['QUERY_STRING'];
                    }
                    ?>

                                        slider_ajax = jQuery.ajax({
                                             url: ajaxUrl,
                                             type: 'POST',
                                             beforeSend: function () {
                                                  if (slider_ajax != null) {
                                                       slider_ajax.abort();
                                                  }
                                             },
                                             data: 'action=<?php echo $post_type . "_search"; ?>&posttype=<?php echo $post_type; ?>&range_address=' + range_address + '&miles_range=' + miles_range + '&defaul_range=<?php echo '1-' . $max_range; ?>&page_type=<?php echo $page_type . $query_string; ?>&radius_measure=<?php echo $radius_measure; ?>&page_num=' + page_link,
                                             success: function (results) {
                                                  jQuery('.' + list_id + '_process').remove();
                                                  jQuery('#' + list_id).html(results);
                                                  jQuery('#listpagi').remove();
                                                  jQuery('#' + list_id).removeClass("loading_results");
                                             }
                                        });
                                        map_ajax = jQuery.ajax({
                                             url: ajaxUrl,
                                             type: 'POST',
                                             beforeSend: function () {
                                                  if (map_ajax != null) {
                                                       map_ajax.abort();
                                                  }
                                             },
                                             dataType: 'json',
                                             data: 'action=<?php echo $post_type . "_search_map"; ?>&posttype=<?php echo $post_type; ?>&range_address=' + range_address + '&miles_range=' + miles_range + '&defaul_range=<?php echo '1-' . $max_range; ?>&page_type=<?php echo $page_type . $query_string; ?>&radius_measure=<?php echo $radius_measure; ?>&page_num=' + page_link,
                                             success: function (results) {
                                                  googlemaplisting_deleteMarkers();
                                                  markers = results.markers;
                                                  templ_add_googlemap_markers(markers);
                                             }
                                        });
                                   });
                                   /* Click event on range address */
                                   jQuery('#range_address').live('keypress', function (e) {
                                        if (e.which == 13) {
                                             jQuery('#radius-range').trigger('slidestop');
                                        }
                                   });
                                   jQuery(function () {
                                        jQuery("#radius-range").slider({range: true, min: 1, max:<?php echo $max_range; ?>, values: [1,<?php echo $max_range; ?>], slide: function (e, t) {
                                                  jQuery("#radius_range").val(t.values[0] + " - " + t.values[1])
                                             }});
                                        jQuery("#radius_range").val(jQuery("#radius-range").slider("values", 0) + " - " + jQuery("#radius-range").slider("values", 1))
                                   })
                         </script>
</form>
<?php
                    echo '</div>';
                    echo $after_widget;
          }

          function update($new_instance, $old_instance) {
                    /* save the widget */
                    return $new_instance;
          }

          function form($instance) {
                    /* widgetform in backend */
                    $instance = wp_parse_args((array) $instance, array('title' => 'Search Nearby Miles Range', 'max_range' => 500, 'post_type' => 'listing'));
                    $title = strip_tags(@$instance['title']);
                    $post_type = strip_tags(@$instance['post_type']);
                    $max_range = strip_tags(@$instance['max_range']);
                    $miles_search = strip_tags(@$instance['miles_search']);
                    $radius_measure = strip_tags(@$instance['radius_measure']);
                    ?>
					<p>
					  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'templatic-admin'); ?>:
						<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
					  </label>
					</p>
					<p>
					  <label for="<?php echo $this->get_field_id('radius_measure'); ?>"><?php echo __('Search By', 'templatic-admin'); ?>
						<select id="<?php echo $this->get_field_id('radius_measure'); ?>" name="<?php echo $this->get_field_name('radius_measure'); ?>">
						  <option value="miles" <?php
													   if (esc_attr($radius_measure) == 'miles') {
																 echo 'selected="selected"';
													   }
													   ?>><?php echo __('Miles', 'templatic-admin'); ?></option>
						  <option value="kilometer" <?php
													   if (esc_attr($radius_measure) == 'kilometer') {
																 echo 'selected="selected"';
													   }
													   ?>><?php echo __('Kilometers', 'templatic-admin'); ?></option>
						</select>
					  </label>
					</p>
					<p>
					  <label for="<?php echo $this->get_field_id('max_range'); ?>"><?php echo __('Max Range', 'templatic-admin'); ?>:
						<input class="widefat" id="<?php echo $this->get_field_id('max_range'); ?>" name="<?php echo $this->get_field_name('max_range'); ?>" type="text" value="<?php echo esc_attr($max_range); ?>" />
					  </label>
					</p>
<?php
          }

}

/* End directory_mile_range_widget */
/*
  Name : slider_search_option
  Desc : Add the JS Of sliding search(miles wise searching) in footer
 */

function slider_search_option() {
          ?>
			<script  type="text/javascript" async >
                              jQuery(function () {
                                   jQuery("#radius-range").slider({range: true, min: 1, max: 500, values: [1, 500], slide: function (e, t) {
                                             jQuery("#radius_range").val(t.values[0] + " - " + t.values[1])
                                        }});
                                   jQuery("#radius_range").val(jQuery("#radius-range").slider("values", 0) + " - " + jQuery("#radius-range").slider("values", 1))
                              })
          </script>
<?php
}

/*

 * display near by distance listing where condition

 */
if (!function_exists('directory_nearby_filter')) {

          function directory_nearby_filter($where) {

                    global $wpdb, $current_post, $miles, $post, $post_number;

                    /* $geo_latitude=get_post_meta($current_post,'geo_latitude',true); */

                    $geo_latitude = (get_post_meta($post->ID, 'geo_latitude', true)) ? get_post_meta($post->ID, 'geo_latitude', true) : $_SESSION['custom_fields']['geo_latitude'];

                    /* $geo_longitude=get_post_meta($current_post,'geo_longitude',true); */

                    $geo_longitude = (get_post_meta($post->ID, 'geo_longitude', true)) ? get_post_meta($post->ID, 'geo_longitude', true) : $_SESSION['custom_fields']['geo_longitude'];

                    $post_type = ($post->post_type != "") ? $post->post_type : $_SESSION['custom_fields']['geo_longitude'];

                    $postid = ($post->ID != "") ? $post->ID : '';

                    $postcode = $wpdb->prefix . "postcodes";
					 $posttable = $wpdb->prefix . "posts";
                    if ($geo_latitude != '' && $geo_longitude != '' && $post_type != 'custom_fields' && $postid != '') {

                              if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {

                                        $language = ICL_LANGUAGE_CODE;

                                        $sql = " SELECT post_id FROM $postcode {$ljoin} JOIN {$wpdb->prefix}icl_translations t ON {$postcode}.post_id = t.element_id AND t.element_type IN ('post_" . $post_type . "') JOIN {$wpdb->prefix}icl_languages l ON t.language_code=l.code AND l.active=1 AND t.language_code='" . $language . "' WHERE post_id!=" . $postid . " AND post_type='" . $post_type . "' AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) <= " . $miles . " ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) ASC LIMIT 0," . $post_number;
                              } else {

                                       $sql = " SELECT $postcode.post_id FROM $postcode,$posttable  WHERE $postcode.post_id!=" . $postid . " AND $posttable.ID = $postcode.post_id  AND $postcode.post_type='" . $post_type . "' AND truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) <= " . $miles . " ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) ASC LIMIT 0," . $post_number;
                              }



                              $result = $wpdb->get_results($sql);

                              $post_id = '';

                              foreach ($result as $val) {

                                        $post_id.=$val->post_id . ",";
                              }

                              if ($post_id != "") {

                                        $where .= " AND ($wpdb->posts.ID in (" . substr($post_id, 0, -1) . "))";
                              }
                    } elseif ($geo_latitude != '' && $geo_longitude != '') {

                              if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {

                                        $language = ICL_LANGUAGE_CODE;

                                        $sql = " SELECT post_id FROM $postcode {$ljoin} JOIN {$wpdb->prefix}icl_translations t ON {$postcode}.post_id = t.element_id AND t.element_type IN ('post_" . $post_type . "') JOIN {$wpdb->prefix}icl_languages l ON t.language_code=l.code AND l.active=1 AND t.language_code='" . $language . "' WHERE truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) <= " . $miles . " ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) ASC LIMIT 0," . $post_number;
                              } else {

                                        $sql = " SELECT post_id FROM $postcode WHERE truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) <= " . $miles . " ORDER BY truncate((degrees(acos( sin(radians(`latitude`)) * sin( radians('" . $geo_latitude . "')) + cos(radians(`latitude`)) * cos( radians('" . $geo_latitude . "')) * cos( radians(`longitude` - '" . $geo_longitude . "') ) ) ) * 69.09),1) ASC LIMIT 0," . $post_number;
                              }



                              $result = $wpdb->get_results($sql);

                              $post_id = '';

                              foreach ($result as $val) {

                                        $post_id.=$val->post_id . ",";
                              }

                              if ($post_id != "") {

                                        $where .= " AND ($wpdb->posts.ID in (" . substr($post_id, 0, -1) . "))";
                              }
                    }

                    return $where;
          }

}
/*
 * Widget of show the featured listing on home page
 */

class directory_featured_homepage_listing extends WP_Widget {

          function directory_featured_homepage_listing() {
                    /* Constructor */
                    global $thumb_url;
                    $widget_ops = array('classname' => 'special', 'description' => __('Showcase posts from any post type, including those created by you. Featured posts are displayed at the top. Works best in the Homepage - Main Content area.', 'templatic-admin'));
                    $this->WP_Widget('directory_featured_homepage_listing', __('T &rarr; Homepage Display Posts', 'templatic-admin'), $widget_ops);
          }

          function widget($args, $instance) {
                    /* prints the widget */

                    extract($args, EXTR_SKIP);

                    $widget_title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
                    $category = empty($instance['category']) ? '' : apply_filters('widget_category', $instance['category']);
                    $number = empty($instance['number']) ? '5' : apply_filters('widget_number', $instance['number']);
                    $my_post_type = empty($instance['post_type']) ? 'listing' : apply_filters('widget_post_type', $instance['post_type']);
                    $link = empty($instance['link']) ? '#' : apply_filters('widget_link', $instance['link']);
                    $text = empty($instance['text']) ? '' : apply_filters('widget_text', $instance['text']);
                    $view = empty($instance['view']) ? 'list' : apply_filters('widget_view', $instance['view']);
                    $more_text = empty($instance['more_text']) ? __('Read more', DIR_DOMAIN) : apply_filters('widget_view', $instance['more_text']);
                    $sorting_options = empty($instance['sorting_options']) ? '' : apply_filters('widget_sorting_options', $instance['sorting_options']);
                    $content_limit = empty($instance['content_limit']) ? '' : apply_filters('widget_content_limit', $instance['content_limit']);

                    global $post, $wpdb, $wp_query, $current_cityinfo, $htmlvar_name;

                    $cus_post_type = empty($instance['post_type']) ? 'listing' : $instance['post_type'];

                    /* get all the custom fields which select as " Show field on listing page" from back end */
                    if (function_exists('tmpl_get_category_list_customfields')) {
                              $htmlvar_name = tmpl_get_category_list_customfields($cus_post_type);
                    } else {
                              global $htmlvar_name;
                    }

                    remove_filter('pre_get_posts', 'home_page_feature_listing');
                    $taxonomies = get_object_taxonomies((object) array('post_type' => $my_post_type, 'public' => true, '_builtin' => true));
                    if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
                              if (@$category != "") {
                                        foreach ($category as $cat) {
                                                  $category_ID = get_term_by('id', $cat, $taxonomies[0]);
                                                  $category_id.=$category_ID->term_id . ',';
                                        }
                                        $category = explode(',', substr($category_id, 0, -1));
                              }
                    }
                    /* Check for existing user if user add category slug by common seprator */
                    $field = 'id';

                    if ($category != '' && !is_array($category)) {
                              $category = explode(',', $category);
                              $category = array_map('trim', $category);
                              if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
                                        if (@$category != "") {
                                                  foreach ($category as $cat) {
                                                            $category_ID = get_term_by('slug', $cat, $taxonomies[0]);
                                                            $category_slug.=$category_ID->slug . ',';
                                                  }
                                                  $category = explode(',', substr($category_slug, 0, -1));
                                        }
                              }
                              $field = 'slug';
                    }

                    if ($sorting_options == 'random') {
                              /* for random sorting but arrange featured first in the list */
                              $order_arg = array('meta_key' => 'featured_type', 'orderby' => 'meta_value rand', 'order' => 'ASC');
                    } elseif ($sorting_options == 'total_price') {
                              /* Fetch the listing first which paid more */
                              $order_arg = array('meta_key' => 'paid_amount',
                                        'orderby' => 'meta_value_num',
                                        'meta_value_num' => 'paid_amount',
                                        'order' => 'DESC');
                    } elseif ($sorting_options == 'date') {
                              /* Fetch the listings order by date */
                              $order_arg = array('orderby' => 'date', 'order' => 'DESC');
                    } elseif ($sorting_options == 'alphabetical') {
                              /* Fetch the listing as per property price low to high */
                              $order_arg = array('meta_key' => 'featured_type', 'orderby' => 'meta_value post_title', 'order' => 'ASC');
                    } elseif ($sorting_options == 'reviews') {
                              /* Fetch the listing as per property price high to low */
                              $order_arg = array('orderby' => 'comment_count', 'order' => 'DESC');
                    } elseif ($sorting_options == 'featured_listing') {
                              /* Fetch only featured listing */
                              $order_arg = array('meta_key' => 'featured_h', 'meta_value' => array('h', 'both'));
                    } elseif ($sorting_options == 'featured_first') {
                              /* Fetch featured listing first */
                              $order_arg = array('meta_key' => 'featured_type', 'orderby' => 'meta_value', 'order' => 'ASC');
                    } else {
                              /* Fetch the order by featured on home page listings first */
                              $order_arg = array('meta_key' => 'featured_type', 'orderby' => 'meta_value', 'order' => 'ASC');
                    }

                    $order_arg = apply_filters('tmpl_homepage_sorting_options_orderby', $order_arg);

                    if (!empty($category)) {
                              $args = array(
                                        'post_type' => $my_post_type,
                                        'posts_per_page' => $number,
                                        'post_status' => 'publish',
                                        'tax_query' => array(
                                                  array(
                                                            'taxonomy' => $taxonomies[0],
                                                            'field' => $field,
                                                            'terms' => $category,
                                                  )
                                        ),
                              );
                    } else {
                              $args = array('post_type' => $my_post_type,
                                        'post_status' => 'publish',
                                        'posts_per_page' => $number,
                              );
                    }
                    $args = array_merge($args, $order_arg);

                    $my_query = null;

                    remove_filter('posts_orderby', 'home_page_feature_listing_orderby');

                    if (is_plugin_active('Tevolution-LocationManager/location-manager.php')) {
                              $flg = 0;
                              $location_post_type = ',' . implode(',', get_option('location_post_type'));
                              if (isset($my_post_type) && $my_post_type != '') {
                                        if (strpos($location_post_type, ',' . $my_post_type) !== false) {
                                                  $flg = 1;
                                        }
                              }
                              if ($flg == 1) {
                                        add_filter('posts_where', 'location_multicity_where');
                              }
                    }
                    if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
                              add_filter('posts_where', 'wpml_listing_milewise_search_language');
                    }

                    if ($taxonomies[0] != '' && (is_array($category) && count($category) > 0)) {
                              $tax_trans_slug = str_replace(',', '-', implode(',', $category));
                    } else {
                              $tax_trans_slug = 'na';
                    }

                    /* Add query to transient */
                    if ($sorting_options != 'random') {
                              if (get_option('tevolution_cache_disable') == 1 && false === ( $my_query = get_transient('tev_hdpw_' . $widget_id . $current_cityinfo['city_id']) )) {
                                        $my_query = new WP_Query($args);

                                        set_transient('tev_hdpw_' . $widget_id . $current_cityinfo['city_id'], $my_query, 12 * HOUR_IN_SECONDS);
                              } elseif (get_option('tevolution_cache_disable') == '') {
                                        $my_query = new WP_Query($args);
                              }
                    } else {
                              $my_query = new WP_Query($args);
                    }

                    remove_filter('posts_join', 'custom_field_posts_where_filter');

                    if (is_plugin_active('Tevolution-LocationManager/location-manager.php')) {
                              add_filter('posts_where', 'location_multicity_where');
                    }
                    if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
                              remove_filter('posts_where', 'wpml_listing_milewise_search_language');
                    }
                    global $htmlvar_name, $wp_query;
                    $wp_query->set('is_ajax_archive', 1);
                    $wp_query->set('is_related', 1);
                    if (function_exists('icl_register_string')) {
                              icl_register_string(DIR_DOMAIN, $widget_id . 'view_link' . $text, $link);
                              $link = icl_t(DIR_DOMAIN, $widget_id . 'view_link' . $text, $link);
                    }
                    
                    if ($my_query->have_posts()):
                              echo $before_widget;
                              ?>
                        <div id="widget_loop_<?php echo $my_post_type ?>" class="widget widget_loop_taxonomy widget_loop_<?php echo $my_post_type ?>">
                          <?php if ($widget_title) { ?>
                          <h3 class="widget-title"><span><?php echo $widget_title; ?></span>
                            <?php if ($link) { ?>
                            <a href="<?php echo $link; ?>" class="more" >
                            <?php
                                /*  for translation for widget text  */
                                if (function_exists('icl_register_string')) {
                                          icl_register_string(DIR_DOMAIN, 'directory' . $text, $text);
                                          $text = icl_t(DIR_DOMAIN, 'directory' . $text, $text);
                                }
                                echo $text;
                                ?>
                            </a>
                            <?php } ?>
                          </h3>
                          <?php } ?>
                          <!-- widget_loop_taxonomy_wrap START -->
                          <section id="loop_listing_taxonomy" class="widget_loop_taxonomy_wrap  <?php echo $view ?>">
                            <?php
                                        while ($my_query->have_posts()) : $my_query->the_post();
                                                  global $post;
                                                  $addons_posttype = tmpl_addon_name();
                                                  if (function_exists('tmpl_wp_is_mobile') && tmpl_wp_is_mobile()) {
                                                            /* this content will load in mobile only */
                                                            if (file_exists(WP_PLUGIN_DIR . '/Tevolution-' . $addons_posttype[get_post_type()] . '/templates' . '/entry-mobile-' . $post->post_type . '.php')) {
                                                                      include(WP_PLUGIN_DIR . '/Tevolution-' . $addons_posttype[get_post_type()] . '/templates' . '/entry-mobile-' . $post->post_type . '.php');
                                                            } else {
                                                                      include(WP_PLUGIN_DIR . '/Tevolution-' . $addons_posttype['listing'] . '/templates/entry-mobile-listing.php');
                                                            }
                                                  } else {
                                                            ?>
                                                            <!-- inside loop div start -->
                                                            <article id="<?php echo $my_post_type . '_' . get_the_ID(); ?>" <?php
                                                            if ((get_post_meta($post->ID, 'featured_h', true) == 'h')) {
                                                                      post_class('post featured_post ');
                                                            } else {
                                                                      post_class('post large-4 medium-4 small-6 xsmall-12 columns');
                                                            }
                                                            ?>>
                                                            <?php
                                                                          /* Hook to display before image */
                                                                          do_action('directory_before_category_page_image');

                                                                          /* Hook to Display Listing Image  */
                                                                          do_action('directory_category_page_image');

                                                                          /* Hook to Display After Image  */
                                                                          do_action('directory_after_category_page_image');

                                                                          /* Before Entry Div  */
                                                                          do_action('directory_before_post_entry');
                                                                          ?>
      
                                                                        <!-- Entry Start -->
                                                                        <div class="entry">
                                                                          <?php
                                                                      /* do action for before the post title. */
                                                                      do_action('directory_before_post_title');
                                                                      do_action('templ_before_title_' . $my_post_type);
                                                                      ?>
                                                                            <div class="<?php echo $my_post_type; ?>-wrapper"> 
                                                                              <!-- Entry title start -->
                                                                              <div class="entry-title">
                                                                                <?php
                                                                                do_action('templ_post_title');                /* do action for display the single post title */
                                                                                do_action('tevolution_title_text');
                                                                                ?>
                                                                                </div>
                                                                                <?php do_action('directory_after_post_title');          /* do action for after the post title. */ ?>
                                                                                <!-- Entry title end --> 
          
                                                                                <!-- Entry details start -->
                                                                                <div class="entry-details">
                                                                                  <?php
                                                                                /* Hook to get Entry details - Like address,phone number or any static field  */
                                                                                global $addons_posttype;
                                                                                $widget_post_types = array_keys($addons_posttype);
                                                                                if (in_array($post->post_type, $widget_post_types)) {
                                                                                          $widget_post_type = $post->post_type;
                                                                                } else {
                                                                                          $widget_post_type = 'listing';
                                                                                }
                                                                                do_action($widget_post_type . '_post_info');
                                                                                ?>
                                                                            </div>
                                                                            <!-- Entry details end --> 
                                                                          </div>
                                                                          <!--Start Post Content -->
                                                                          <?php
                                                                      /* Hook for before post content . */

                                                                      do_action('directory_before_post_content');

                                                                      /* Hook to display post content . */
                                                                      if (isset($content_limit) && $content_limit != '') {
                                                                                $permalink = get_permalink($post->ID);
                                                                                /* remove count filter as read more link shows twice */
                                                                                remove_filter('excerpt_length', 'tevolution_excerpt_len', 20);
                                                                                remove_filter('excerpt_more', 'tevolution_excerpt_more', 20);
                                                                                remove_filter('excerpt_length', 'supreme_excerpt_length', 11);
                                                                                remove_filter('excerpt_more', 'new_excerpt_more');

                                                                                echo '<div class="entry-summary" itemprop="description"><p>';
                                                                                $excerpt = substr(str_replace(' [&hellip;]', '', get_the_excerpt()), 0, $content_limit);
                                                                                $excerpt .= '<a class="moretag" href="' . $permalink . '">&nbsp;' . $more_text . ' &raquo;</a>';
                                                                                echo $excerpt;
                                                                                echo '</p></div>';
                                                                      } else
                                                                                do_action('templ_taxonomy_content');

                                                                      /* Hook for after the post content. */
                                                                      do_action('directory_after_post_content');
                                                                      ?>
                                                                        <!-- End Post Content -->
                                                                        <?php
                                                                      /* Hook for before listing categories */
                                                                      do_action('directory_before_taxonomies');

                                                                      /* Display listing categories */
                                                                      do_action('templ_the_taxonomies');

                                                                      /* Hook to display the listing comments, add to favorite and pinpoint */
                                                                      do_action('directory_after_taxonomies');
                                                                      do_action($widget_post_type . '_after_post_entry');
                                                                      ?>
                                                                    </div>
                                                                    <!-- Entry End -->
                                                                    <?php do_action('directory_after_post_entry');
                                                                 ?>
                                                        </article>
                                                        <?php
                                                  }

                                        endwhile;
                                        wp_reset_query();
                                        ?>
                                    </section>
                                    <!-- widget_loop_taxonomy_wrap eND --> 
                                  </div>
                                  <?php
                              echo $after_widget;
                    endif;
                    ?>
            <!-- widget_loop_taxonomy -->
            <?php
          }

          function update($new_instance, $old_instance) {
                    /* save the widget */
                    global $wpdb;
                    $wpdb->query($wpdb->prepare("delete from $wpdb->options where option_name LIKE %s", '%transient_directory_featured_homepage_listing%'));
                    return $new_instance;
          }

          function form($instance) {

                    $all_post_types = apply_filters('tmpl_allow_fields_posttype', get_option("templatic_custom_post"));
                    $k = 1;
                    foreach ($all_post_types as $key => $post_type) {
                              if ($k == 1) {
                                        $posttype = $key;
                                        break;
                              }
                              $k++;
                    }

                    $instance = wp_parse_args((array) $instance, array('title' => __("Featured Listing", 'templatic-admin'), 'category' => '', 'number' => 5, 'post_type' => $posttype, 'link' => '#', 'text' => __("View All", 'templatic-admin'), 'view' => 'list', 'read_more' => ''));

                    $title = strip_tags($instance['title']);
                    $category = $instance['category'];
                    $number = strip_tags($instance['number']);
                    $my_post_type = strip_tags($instance['post_type']);
                    $link = strip_tags($instance['link']);
                    $text = strip_tags($instance['text']);
                    $view = strip_tags($instance['view']);
                    $read_more = strip_tags($instance['read_more']);
                    $sort_opt = (!empty($instance['sorting_options'])) ? $instance['sorting_options'] : 'featured_listing';
                    $rand = rand();
                    /* check for existing user category slug commna seprator */

                    if (!is_array($category) || strstr($category_array, ',')) {
                              $category = explode(',', $category);
                              /* trim array value */
                              $category = array_map('trim', $category);
                    }
                    ?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title', 'templatic-admin'); ?>:
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('text'); ?>"><?php echo __('View All Text', 'templatic-admin'); ?>:
    <input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo esc_attr($text); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('link'); ?>"><?php echo __('View All Link URL: (ex.http://templatic.com/events)', 'templatic-admin'); ?>
    <input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo esc_attr($link); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('number'); ?>"><?php echo __('Number of posts', 'templatic-admin'); ?>:
    <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr($number); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('view'); ?>"><?php echo __('View', 'templatic-admin') ?>:
    <select id="<?php echo $this->get_field_id('view'); ?>" onchange="show_content_limit(this.value,<?php echo $rand; ?>)"  name="<?php echo $this->get_field_name('view'); ?>">
      <option value="list" <?php
                                   if ($view == 'list') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('List view', 'templatic-admin'); ?></option>
      <option value="grid" <?php
                                   if ($view == 'grid') {
                                             echo 'selected="selected"';
                                   }
                                   ?>><?php echo __('Grid view', 'templatic-admin'); ?></option>
    </select>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('post_type'); ?>"><?php echo __('Post Type', 'templatic-admin') ?>:
    <select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widget_post_type widefat" onchange="display_post_type_category(this, '<?php echo $this->get_field_id('category'); ?>', '<?php echo implode(',', $category); ?>')">
      <?php
                                   $all_post_types = apply_filters('tmpl_allow_fields_posttype', get_option("templatic_custom_post"));
                                   foreach ($all_post_types as $key => $post_type) {
                                             ?>
      <option value="<?php echo $key; ?>" <?php if ($key == $my_post_type) echo "selected"; ?>><?php echo esc_attr($post_type['label']); ?></option>
      <?php } ?>
    </select>
  </label>
</p>
<p id="show_content_limit_<?php echo $rand; ?>" <?php if (@$view == 'grid') { ?> style="display:none;"<?php } ?> >
  <label for="<?php echo $this->get_field_id('content_limit'); ?>"><?php echo __('Limit content to', 'templatic-admin'); ?>: </label>
  <input type="text" id="<?php echo $this->get_field_id('image_alignment'); ?>" name="<?php echo $this->get_field_name('content_limit'); ?>" value="<?php echo esc_attr(intval($instance['content_limit'])); ?>" size="3" />
  <?php echo __('characters', 'templatic-admin'); ?> </p>
<p>
  <label style="vertical-align:top;" for="<?php echo $this->get_field_id('category'); ?>"><?php echo __('Categories:', 'templatic-admin'); ?></label>
  <select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>[]" class="<?php echo $this->get_field_id('category'); ?> widefat" multiple="multiple">
    <option value=""><?php echo __('Select Category', 'templatic-admin'); ?></option>
    <?php
                              if ($my_post_type != '') {
                                        $taxonomies = get_object_taxonomies((object) array('post_type' => $my_post_type, 'public' => true, '_builtin' => true));
                                        $terms = get_terms($taxonomies[0], 'orderby=name&hide_empty=0');
                                        foreach ($terms as $term) {
                                                  $term_value = $term->term_id;
                                                  $selected = (in_array($term_value, $category) || in_array($term->slug, $category)) ? "selected" : '';
                                                  ?>
    <option value="<?php echo $term_value ?>" <?php echo $selected ?>> <?php echo esc_attr($term->name); ?> </option>
    <?php
                                        }
                              }
                              ?>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id('sorting_options'); ?>"><?php echo __('Sorting Options', ADMINDOMAIN) ?>:
    <select id="<?php echo $this->get_field_id('sorting_options'); ?>" name="<?php echo $this->get_field_name('sorting_options'); ?>">
      <?php
                                   $sorting_options = apply_filters('tmpl_homepage_sorting_options', array('alphabetical' => __('Alphabetical', ADMINDOMAIN), 'random' => __('Random', ADMINDOMAIN), 'date' => __('Published Date', ADMINDOMAIN), 'total_price' => __('Higher Paid First', ADMINDOMAIN), 'featured_listing' => __('Only Featured', ADMINDOMAIN), 'featured_first' => __('Featured First', ADMINDOMAIN)));
                                   if (get_option('default_comment_status') == 'open') {
                                             $sorting_options = array_merge($sorting_options, array('reviews' => 'Reviews/Comments'));
                                   }
                                   foreach ($sorting_options as $key => $value) {
                                             if ($sort_opt == $key) {
                                                       $sel = "selected=selected";
                                             } else {
                                                       $sel = '';
                                             }
                                             ?>
      <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $value; ?></option>
      <?php } ?>
    </select>
  </label>
</p>
<script>
                              function show_content_limit(val, rand_var)
                              {
                                   if (val == 'list') {
                                        document.getElementById("show_content_limit_" + rand_var).style.display = '';
                                   } else {
                                        document.getElementById("show_content_limit_" + rand_var).style.display = 'none';
                                   }
                              }
                              function display_post_type_category(post_type, category_id, cat_val) {
                                   jQuery.ajax({
                                        url: ajaxUrl,
                                        type: 'POST',
                                        async: true,
                                        data: 'action=callwidget_post_type_category&post_type=' + jQuery(post_type).val() + '&cat_val=' + cat_val,
                                        success: function (results) {
                                             jQuery('#' + category_id).html(results);
                                        },
                                   });
                              }
                    </script>
<?php
          }

}

/* End directory_featured_homepage_listing widget */

add_action('wp_ajax_callwidget_post_type_category', 'wp_ajax_callwidget_post_type_category');

function wp_ajax_callwidget_post_type_category() {

          $taxonomies = get_object_taxonomies((object) array('post_type' => $_REQUEST['post_type'], 'public' => true, '_builtin' => true));
          $terms = get_terms($taxonomies[0], 'orderby=name&hide_empty=0');
          $select_option = '<option value="">' . __('Select Category', 'templatic-admin') . '</option>';
          $category = explode(',', $_REQUEST['cat_val']);
          foreach ($terms as $term) {
                    $term_value = $term->term_id;
                    $selected = (in_array($term_value, $category) || in_array($term->slug, $category)) ? "selected" : '';

                    $select_option.='<option value="' . $term_value . '" ' . $selected . '>' . esc_attr($term->name) . ' </option>';
          }
          echo $select_option;
          exit;
}
?>
