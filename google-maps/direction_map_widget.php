<?php
/* direction widget map*/
class widget_googlemap_diection_widget extends WP_Widget {

	function widget_googlemap_diection_widget() {	
		$widget_ops = array('classname' => 'widget googlemap direction widget
		widget', 'description' => __('Shows a map of the posts location. By entering their address, visitors can also get directions to the location. Use the widget in detail page sidebar areas.','templatic') );		
		$this->WP_Widget('widget_googlemap_diection_widget', __('T &rarr; Detail Page Map','templatic'), $widget_ops);
	}
	function widget($args, $instance) {
		global $current_cityinfo;
		$width = empty($instance['width']) ? '940' : apply_filters('widget_width', $instance['width']);
      	$heigh = empty($instance['heigh']) ? '425' : apply_filters('widget_heigh', $instance['heigh']);		
			
		if(is_single()){ 
			global $post;
			if(get_post_meta(get_the_ID(),'_event_id',true)){
				$post->ID=get_post_meta(get_the_ID(),'_event_id',true);
			}	

			$geo_latitude = get_post_meta($post->ID,'geo_latitude',true);
			$geo_longitude = get_post_meta($post->ID,'geo_longitude',true);
			$address = get_post_meta($post->ID,'address',true);
			$map_type =get_post_meta($post->ID,'map_view',true);			
			$templatic_settings=get_option('templatic_settings');
			if($address && $templatic_settings['direction_map']!='yes'){
			do_action('tmpl_before_detailpage_map_widget');	
				echo $args['before_widget'];
			?>
				   <div id="tevolution_location_map" class="widget">
						<div class="tevolution_google_map" id="tevolution_detail_google_map_id"> 
						<?php include_once (TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/google_map_detail.php');?>
						</div>  <!-- google map #end -->
				   </div>
			<?php
				do_action('tmpl_after_detailpage_map_widget');	
				echo $args['after_widget'];
			}
		}
          
	}
	/*Widget update function */
	function update($new_instance, $old_instance) {
		/*save the widget*/
		return $new_instance;
	}
	
	/*Widget admin form display function */
	function form($instance) {
		/*widgetform in backend*/
		$instance = wp_parse_args( (array) $instance, array( 'width' => '', 'heigh' => '') );		
		$width = strip_tags($instance['width']);
		$heigh = strip_tags($instance['heigh']);
		?>
	
		<p>
		 <label for="<?php echo $this->get_field_id('heigh'); ?>"><?php echo __('Map Height <small>(default height: 425px) to change, only enter a numeric value.)</small>','templatic-admin');?>:
		 <input class="widefat" id="<?php echo $this->get_field_id('heigh'); ?>" name="<?php echo $this->get_field_name('heigh'); ?>" type="text" value="<?php echo esc_attr($heigh); ?>" />
		 </label>
	    </p>
	    <?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("widget_googlemap_diection_widget");') );
?>