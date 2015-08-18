<?php
/*
 * Function name: templ_add_admin_menu_
 * Return: display the admin submenu page of tevolution menu page
 */
include_once(plugin_dir_path( __FILE__ ).'direction_map_widget.php');
include_once(plugin_dir_path( __FILE__ ).'widgets.php');
include_once(plugin_dir_path( __FILE__ ).'map_functions.php');

/*
 * add page view options in general settings
 */
add_action('tmpl_other_page_view_option','dir_page_view_options');
function dir_page_view_options(){
	$get_plug_data = get_option('templatic_settings');
	$googlemap_setting=get_option('city_googlemap_setting'); ?>
	  &nbsp;&nbsp;
    <label for="default_page_view3" " <?php if($get_plug_data['category_googlemap_widget']!='yes') { ?> style="display:none;" <?php } ?>>
      <input type="radio" id="default_page_view3" name="default_page_view" value="mapview" <?php if( @$get_plug_data['default_page_view']== "" || @$get_plug_data['default_page_view']=='mapview') echo "checked=checked";?> />
      <?php echo __('Map',THEME_DOMAIN); ?>
    </label>
    <?php 
}

/*
 *Add settings for pinpoints on category pages - in tevolution-> general settings -> category page settings option 
 */
add_action('after_listing_page_setting','directory_listing_map_setting');
if( !function_exists( 'directory_listing_map_setting' ) ){
function directory_listing_map_setting(){
	$tmpdata = get_option('templatic_settings');
	define('MAP_SETTINGS_TEXT',__('Map Settings',LMADMINDOMAIN));
	do_action('tmpl_before_pippoint_category_googlemap_widget');
	?>
	<tr>
		 <th valign="top"><label><?php echo __('Map view',ADMINDOMAIN);?></label></th>
		 <td>
			<div class="input-switch">
			  <input type="checkbox" id="category_googlemap_widget" name="category_googlemap_widget" value="yes" <?php if($tmpdata['category_googlemap_widget']=='yes') echo 'checked';?> onclick="tmpl_show_pinpoint_script();"/>
			  <label for="category_googlemap_widget">&nbsp;<?php echo __('Enable',ADMINDOMAIN);?></label>
			</div>
			  <p class="description"><?php echo __('Enable to show map tab on category pages. Disable to use "T -> Category Page Map" instead.',ADMINDOMAIN);?></p>
		 </td>
	</tr>
	<?php	
		
		do_action('tmpl_before_pippoint_oncategory1');
		
		if($tmpdata['category_googlemap_widget'] == 'yes'){ $css = 'style="display:none;"';}else{ $css=''; }
		do_action('tmpl_after_pippoint_category_googlemap_widget'); 
	?>
	<tr id="tmpl_show_pinpoint" <?php echo $css; ?>>
	  <th valign="top"><label><?php echo __('Pinpoint functionality',LMADMINDOMAIN);?></label></th>
	  <td>
		<div class="input-switch">
			<input id="pippoint_oncategory1" type="checkbox" value="1" <?php if(@$tmpdata['pippoint_oncategory']=='1') echo "checked=checked";?> name="pippoint_oncategory">
			<label for="pippoint_oncategory1"><?php _e('Enable',LMADMINDOMAIN); ?></label>    
		</div>
		   <p class="description"><?php echo __('Pinpoint button allows you to focus the map on a specific entry. Only work with "Show Map view" above unchecked and the "T-> Category Page Map"',LMADMINDOMAIN);?></p>
	  </td>
	</tr>
	<?php 
		do_action('tmpl_after_pippoint_oncategory1'); 
		do_action('tmpl_before_pippoint_effects1');
	?>
	<tr id="tmpl_show_pinpoint_effect" <?php echo $css; ?>>
	  <th valign="top"><label><?php echo __('Pinpoint activation',LMADMINDOMAIN);?></label></th>
	  <td>
		   <label for="pippoint_effects1"><input type="radio" id="pippoint_effects1" name="pippoint_effects" value="hover" <?php if($tmpdata['pippoint_effects']=='hover') echo "checked=checked";?> /> <?php echo __('Mouse hover',LMADMINDOMAIN); ?></label>&nbsp;&nbsp;&nbsp;
				<label for="pippoint_effects2"><input type="radio" id="pippoint_effects2" name="pippoint_effects" value="click" <?php if($tmpdata['pippoint_effects']=='click') echo "checked=checked";?> /> <?php _e('Click',LMADMINDOMAIN); ?></label>
			<p class="description"><?php echo sprintf(__('"Mouse hover" option will not work if you have "Show map view in category pages" option ( given above ) enabled. ', LMADMINDOMAIN));?></p>
	  </td>  
	  </td>
	</tr>
    <?php
		do_action('tmpl_after_pippoint_effects1');
	}
}

/* detail page goggle map settings */
add_action('admin_init', 'googlemap_setting_add_page_menu', 20);

function googlemap_setting_add_page_menu(){

	global $location_settings_option;
	
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return ;
	}
	add_action('after_map_setting','googlemap_settings');	
	
	if(isset($_POST['settings-submit'])){
		update_option('city_googlemap_setting',$_POST);
		
		echo '<div id="setting-error-settings_updated" class="updated settings-error">';
		echo '<p>';
		echo '<strong>'.__('Settings saved',ADMINDOMAIN).'</strong>';
		echo '</p>';
		echo '</div>';
	}
	if(!get_option('maps_setting')){
		$city_googlemap_setting=array('map_city_name'             => 'New york',
								'map_city_latitude'         => '40.70591499925218',
								'map_city_longitude'        => '-73.9780035',
								'map_city_type'             => 'ROADMAP',
								'map_city_scaling_factor'   => '12',
								'set_zooming_opt'           => '0',
								'category_googlemap_widget' => 'yes',
								'direction_map'             => 'yes',
								'google_map_full_width'     => 'yes',
								);
		
		update_option('city_googlemap_setting',$city_googlemap_setting);	
		update_option('maps_setting',1);
	}
}

/* Display the single city map settings on detail page */

function googlemap_settings(){
	
	$googlemap_setting=get_option('city_googlemap_setting');		
	?>
	<script>
		function tmpl_change_option(id){
			if(id == 'set_zooming_opt1' && document.getElementById('tmpl_fids').style.display !='none'){ 
				document.getElementById('tmpl_fids').style.display ='none';
				document.getElementById('tmpl_fids1').style.display ='none';
			}else{
				document.getElementById('tmpl_fids').style.display ='';
				document.getElementById('tmpl_fids1').style.display ='';
			}
		}
	</script>
	       
			<?php do_action('before_map_setting');?>
			
			<tr valign="top">
				<th colspan="2">
					<div class="tevo_sub_title"><?php echo __('Single city map settings',ADMINDOMAIN);?></div>
				 </th>
			</tr>
			<?php do_action('tmpl_single_citysettings_start'); ?>
			<tr valign="top">
				<th scope="row"><label for="map_city_name"><?php echo __('City name',ADMINDOMAIN);?></label></th>
				 <td><input id="map_city_name" type="text" name="map_city_name" value="<?php echo $googlemap_setting['map_city_name'];?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="map_city_latitude"><?php echo __('City latitude',ADMINDOMAIN);?></label></th>
				 <td><input id="map_city_latitude" type="text" name="map_city_latitude" value="<?php echo $googlemap_setting['map_city_latitude'];?>" /><p class="description"><?php echo __('Enter the latitude for the city defined above. Generate the value using',ADMINDOMAIN).' <a href="http://itouchmap.com/latlong.html" target="_blank">'.__('this website',ADMINDOMAIN).'</a>'; ?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="map_city_longitude"><?php echo __('City longitude',ADMINDOMAIN);?></label></th>
				 <td><input id="map_city_longitude" type="text" name="map_city_longitude" value="<?php echo $googlemap_setting['map_city_longitude'];?>" /><p class="description"><?php echo __('Enter the longitude for the city defined above. Generate the value using',ADMINDOMAIN).' <a href="http://itouchmap.com/latlong.html" target="_blank">'.__('this website',ADMINDOMAIN).'</a>';?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="map_city_type"><?php echo __('Map type',ADMINDOMAIN);?></label></th>
				 <td>
					  <fieldset> 
						   <label for="roadmap"><input type="radio" id="roadmap" name="map_city_type" value="ROADMAP" <?php if($googlemap_setting['map_city_type']=='ROADMAP'){echo 'checked';}?>  /><?php echo __('Road Map',ADMINDOMAIN);?></label>
						   
						   <label for="terrain"><input type="radio" id="terrain" name="map_city_type" value="TERRAIN" <?php if($googlemap_setting['map_city_type']=='TERRAIN'){echo 'checked';}?>/><?php echo __('Terrain Map',ADMINDOMAIN);?></label>
						   
						   <label for="satellite"><input type="radio" id="satellite" name="map_city_type" value="SATELLITE" <?php if($googlemap_setting['map_city_type']=='SATELLITE'){echo 'checked';}?>/><?php echo __('Satellite Map',ADMINDOMAIN);?></label>
						   
					  </fieldset> <p class="description"><?php echo __('The selection made here will affect your homepage and category page map.',ADMINDOMAIN)?></p> 
											
					</td>
			</tr>
			 <tr valign="top">
				<th scope="row"><label for="map_city_display"><?php echo __('Map display',ADMINDOMAIN);?></label></th>
				 <td>
					<fieldset>
						   <label for="set_zooming_opt"> <input type="radio" id="set_zooming_opt" name="set_zooming_opt" value="0"  <?php if($googlemap_setting['set_zooming_opt']=='0'){echo 'checked';}?> onclick="tmpl_change_option(this.id);"/><?php echo __('According to Map Scaling factor',ADMINDOMAIN);?></label>                 
						   <label for="set_zooming_opt1"><input type="radio" id="set_zooming_opt1" name="set_zooming_opt"  value="1" <?php if($googlemap_setting['set_zooming_opt']=='1'){echo 'checked';}?> onclick="tmpl_change_option(this.id);" /><?php echo __('Fit all available listings',ADMINDOMAIN);?></label>
					  </fieldset> <p class="description"><?php echo __('If "Fit all available listings" is selected the map scaling factor set above is ignored. The zoom factor will be set automatically so that all listings fit the screen.',ADMINDOMAIN)?></p>                        	
				 </td>
			</tr>
			<?php
			if($googlemap_setting['set_zooming_opt'] ==0){
					$style = "";
			}else{
					$style = "style=display:none;";
			}
			?>
			
			<tr valign="top">
				<th scope="row" id="tmpl_fids1" <?php echo $style; ?>><label for="map_city_scaling_factor"><?php echo __('Map scaling factor',ADMINDOMAIN);?></label></th>
				 <td id="tmpl_fids" <?php echo $style; ?>>
					<select id="map_city_scaling_factor" name="map_city_scaling_factor">
						<?php for($sf=1; $sf < 20 ; $sf++){ ?>
							<?php 
							$sf1=($googlemap_setting['map_city_scaling_factor'] !='')?$googlemap_setting['map_city_scaling_factor'] :'13';
							if($sf == $sf1){ $sel ="selected=selected"; }else{ $sel =''; }
							?>
							<option value="<?php echo $sf; ?>" <?php echo $sel; ?>><?php echo $sf; ?></option>
						<?php } ?>							
				</select> <p class="description"><?php echo __('Set to zoom level for the map. The higher the number, the larger the zoom. To show a city area set the factor to around 13.',ADMINDOMAIN)?></p>                         	
				  </td>
			</tr>
			<?php do_action('tmpl_single_citysettings_end');
}


/*
	homepage and listing page map widget register
 */

add_action('widgets_init','google_maps_widgets_init');
function google_maps_widgets_init()
{
	register_widget('widget_homepagemap');
	register_widget('widget_listingpagemap');
}

/*
 * Class Name: widget_homepagemap
 * Create Home map widget
 */
class widget_homepagemap extends WP_Widget {
	function widget_homepagemap() {	
		$widget_ops = array('classname' => 'widget homepagemap', 'description' => __('Use it while operating a single city. Edit the map location in Tevolution &raquo; map settings. Widget works best inside the Homepage Slider or Homepage - Main Content area.',ADMINDOMAIN) );		
		$this->WP_Widget('homepagemap', __('T &rarr; Homepage Map - single city',ADMINDOMAIN), $widget_ops);
	}
	function widget($args, $instance) {
		global $wp_query,$clustering;
		$height = empty($instance['height']) ? '425' : apply_filters('widget_height', $instance['height']);
		$post_type = empty($instance['post_type']) ? '' : apply_filters('widget_post_type', $instance['post_type']);
		$clustering = empty($instance['clustering']) ? '' : apply_filters('widget_clustering', $instance['clustering']);
		$single_category = empty($instance['single_category']) ? '' : apply_filters('widget_single_category', $instance['single_category']);
		$mapcategory_info =get_googlemap_categoryinfo($post_type,$single_category);			
		echo $args['before_widget'];
		wp_enqueue_script( 'widget-googlemap-js', plugin_dir_url( __FILE__ ).'google_map.js',true);	
		add_action('wp_footer','tmpl_single_city_homepage_map_script',99);
		?>          
          <div class="map_sidebar">
               <div class="top_banner_section_in clearfix">
               	<div class="TopLeft"><span id="triggermap"></span></div>
               	<div class="TopRight"></div>
                    <div class="iprelative">
						<div style="display: none;" class="map_loading_div"></div>
						<div style="display: none;" class="map_loading_div"></div>
						<div style="display: none;" class="map_loading_div map_loading_div_bg"></div>
                         <div id="map_canvas" style="width: 100%; height:<?php echo $height;?>px" class="map_canvas"></div>               
                         <div id="map_loading_div" style="width: 100%; height:<?php echo $height;?>px; display: none;""></div>                     
                         <div id="map_marker_nofound" style="display:none;">
							<?php 
								echo '<h3>';
									_e('No Records Found',DOMAIN);
								echo '</h3><p>';
									_e('Sorry, no records were found. Please adjust your search criteria and try again.',DOMAIN);
								echo '</p>'; 
							?>
						 </div>     
                    </div>             
               
                    <form id="ajaxform" name="slider_search" class="pe_advsearch_form" action="javascript:void(0);"  onsubmit="return(googlemap_ajaxSearch());">
                    	<div class="paf_search"><input  type="text" class="" id="search_string" name="search_string" value="" placeholder="<?php _e('Title or Keyword',DOMAIN);?>" onclick="this.placeholder=''" onmouseover="this.placeholder='<?php _e('Title or Keyword',DOMAIN);?>'"/></div>
					<?php 
			
					if($post_type):
									
					?>
                              <div class="paf_row map_post_type" id="toggle_postID" style="display:block; max-height:<?php echo $height-105;?>px;"">
							<?php for($c=0;$c<count($post_type);$c++):
                                   if($post_type[$c])
                                   { $obj = get_post_type_object($post_type[$c]);
								 
										$name = $obj->labels->name; /* to get taxonomy name */
										if (function_exists('icl_register_string')) {									
											icl_register_string(DOMAIN, $name,$name);
											$name = icl_t(DOMAIN, $name,$name);		
										}
										?>
                                        <div class="mw_cat_title">
                                             <label>
                                             	<input type="checkbox" onclick="googlemap_initialize(this,'');"  value="<?php echo str_replace("&",'&amp;',$post_type[$c]);?>"  <?php if(!empty($_POST['posttype']) && !in_array(str_replace("&",'&amp;',$post_type[$c]) ,$_POST['posttype'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo str_replace("&",'&amp;',$post_type[$c]).'custom_categories';?>" name="posttype[]"> <?php echo ucfirst($name);?>
                                             </label>
                                             	<span id='<?php echo $post_type[$c].'_toggle';?>' class="toggle_post_type toggleon" onclick="custom_post_type_taxonomy('<?php echo $post_type[$c].'_category';?>',this)"></span>
                                         </div>
                                         <div class="custom_categories <?php echo str_replace("&",'&amp;',$post_type[$c]).'custom_categories';?>" id="<?php echo $post_type[$c].'_category';?>">
                                             <?php 
										if(!empty($mapcategory_info[$post_type[$c]])){
										foreach($mapcategory_info[$post_type[$c]] as $key => $value){ ?>
                                             	<label for="<?php echo $key;?>" style="margin-left: <?php echo 3*$value['parent'];?>px;">
                                                  <input type="checkbox" onclick="googlemap_initialize(this,'<?php echo str_replace("&",'&amp;',$post_type[$c]);?>');"  value="<?php echo $value['term_id'];?>"  <?php if(!empty($_POST['categoryname']) && !in_array($key,$_POST['categoryname'])):?> <?php else:?> checked="checked" <?php endif;?> id="<?php echo $key;?>" name="categoryname[]"><img height="14" width="8" alt="" src="<?php echo $value['icon']?>"> <?php echo $value['name']?>
                                                  </label>
                                             
                                             <?php }
										}/*if*/?>
                                        </div>
                                   <?php }
                                   endfor;?>
                              </div>
                              <div id="toggle_post_type" class="paf_row toggleon" onclick="googlemap_toggle_post_type();"></div>
                         <?php endif;?>
                    </form>     
               
               </div>
          </div>
          
         
          <?php
		echo $args['after_widget'];

		
	
	}
	
	/*Widget update function */
	function update($new_instance, $old_instance) {
		/*save the widget*/
		return $new_instance;
	}
	/*Widget admin form display function */
	function form($instance) {
		/*widgetform in backend*/
		$instance = wp_parse_args( (array) $instance, array( 'width' => '', 'height' => '','post_type'=>'','clustering' => '','single_category' => '') );				
		$height = strip_tags($instance['height']);
		$post_type=$instance['post_type'];	
		$city_post_type=$post_type;
		$single_category=$instance['single_category'];
		$clustering = strip_tags($instance['clustering']);		
		?>
          <p>
               <label for="<?php echo $this->get_field_id('height'); ?>"><?php echo __('Map Height <small>(default height: 425px) to change, only enter a numeric value.)</small>',ADMINDOMAIN);?>:
               <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($height); ?>" />
               </label>
          </p>
          <div class="googlemap_post_type clearfix">
               <span><label for="<?php echo $this->get_field_id('post_type');?>" ><?php _e('Select Post:',ADMINDOMAIN);?>     </label></span>
               <span>	               
               <?php
               $all_post_types = get_option("templatic_custom_post");
               foreach($all_post_types as $key=>$post_types){
                    ?>
                    <input id="<?php echo $this->get_field_id('widget_home_'.$key); ?>" type="checkbox" name="<?php echo $this->get_field_name('post_type'); ?>[]" value="<?php echo $key;?>" <?php if(isset($key) && !empty($post_type) && in_array($key,$post_type)){echo 'checked';}?>  onclick="get_single_city_categories_checklist('<?php echo $key; ?>','<?php echo $this->get_field_id('single_field_category'); ?>','<?php echo $this->get_field_name('single_category'); ?>','<?php echo $this->get_field_name('post_type'); ?>');" />&nbsp;&nbsp;<label for="<?php echo $this->get_field_id('widget_home_'.$key); ?>"><?php echo esc_attr(ucfirst($post_types['label']));?></label> <br />
                    <?php
               }
               ?>	
               </span>
          </div>
          
          <div class="">
          	<span><label for="<?php echo $this->get_field_id('post_type_category');?>" ><?php _e('Categories:',ADMINDOMAIN);?>     </label></span>
               <div class="element wp-tab-panel" id="<?php echo $this->get_field_id('single_field_category'); ?>" style="height:300px;overflow-y: scroll; margin-bottom:5px;">
				 <?php 
                         $post_types = get_option("templatic_custom_post");
                         $categories=( @$single_category!='')? implode(',',$single_category):'';

						 
                         $c=0;
					
                         if(!empty($city_post_type)){
                         foreach($post_types as $key=>$post_type):
                              if(in_array($key,$city_post_type)){
                                   if($c == 0){
                                        echo get_single_location_category_checklist($key,$categories,'','select_all',$this->get_field_name('single_category'));
                                   }else{ 
                                        echo get_single_location_category_checklist($key,$categories,'','',$this->get_field_name('single_category'));
                                   }
                                   $c++;
                              }
                         endforeach;
                         }else{
                              foreach($post_types as $key=>$post_type):									
                                   if($c == 0){
                                        echo get_single_location_category_checklist($key,$categories,'','select_all',$this->get_field_name('single_category'));
                                   }else{ 
                                        echo get_single_location_category_checklist($key,$categories,'','',$this->get_field_name('single_category'));
                                   }
                                   $c++;									
                              endforeach;
                         }
                    ?>  
                 </div>
                 <span id='<?php echo $this->get_field_id('single_city_process');?>' style='display:none;'><i class="fa fa-circle-o-notch fa-spin"></i></span>
          </div>
          <p>
		<?php if($clustering) { $checked = "checked=checked"; }else{ $checked =''; } ?>
		 <label for="<?php echo $this->get_field_id('clustering'); ?>">
		 <input id="<?php echo $this->get_field_id('clustering'); ?>" name="<?php echo $this->get_field_name('clustering'); ?>" type="checkbox" value="1" <?php echo $checked; ?>/>
		 <?php echo __('Disable Clustering','lm-templatic-admin'); ?></label>
	    </p>
        <script  type="text/javascript" async >
	    function get_single_city_categories_checklist(str,city_id,single_category,post_type){
			
			document.getElementById(city_id).innerHTML='<i class="fa fa-circle-o-notch fa-spin"></i>';
			
			
			var checkedValue = null; 
			var checkedValue = ''; 
			var inputElements = document.getElementsByName(post_type+'[]');			
			for(var i=0; inputElements[i]; ++i){				
				if(inputElements[i].checked){
					checkedValue += inputElements[i].value +",";
				}
			}			
			var cityid='';
			if(city_id!=''){
				cityid='&city_id='+city_id;
			}
			jQuery.ajax({
				url:ajaxurl,
				type:'POST',
				data:'action=get_single_city_categories&post_type='+checkedValue+'&cat_name='+single_category,
				success:function(results){					
					document.getElementById(city_id).innerHTML=results;
				}
			});
		}
		function single_city_displaychk_frm(single_category){			
			chk = document.getElementsByName(single_category+'[]');
			len = document.getElementsByName(single_category+'[]').length;	
			if((chk[0]).checked == true) { 
				for (i = 0; i < len; i++)
				chk[i].checked = true ;
			} else { 
				for (i = 0; i < len; i++)
				chk[i].checked = false ;
			}
		}
	    </script>
	    <?php
	}
}




/*
 * Class Name: widget_listingpagemap
 * Create listing map widget
 */
class widget_listingpagemap extends WP_Widget {
	function widget_listingpagemap() {	
		$widget_ops = array('classname' => 'widget listingpagemap', 'description' => __('Show a map on category pages while operating a single city. Use in category page sidebar and category page - below header areas.',ADMINDOMAIN) );		
		$this->WP_Widget('listingpagemap', __('T &rarr; Category Page Map - single city',ADMINDOMAIN), $widget_ops);
	}
	function widget($map_args, $instance) {
		global $wp_query;
		$heigh = strip_tags($instance['height']);
		$clustering = empty($instance['clustering']) ? '' : apply_filters('widget_heigh', $instance['clustering']);
		$show_all_posts = empty($instance['show_all_posts']) ? '' : apply_filters('widget_show_all_posts', $instance['show_all_posts']);
		
		if($heigh ==''){ $heigh ='425'; }
		$cur_lang_code=(is_plugin_active('wpml-translation-management/plugin.php'))? ICL_LANGUAGE_CODE :'';
		$templatic_settings=get_option('templatic_settings');
		$googlemap_setting=get_option('city_googlemap_setting');
		$taxonomy= get_query_var( 'taxonomy' );
		$slug=get_query_var( get_query_var( 'taxonomy' ) );
		$term=get_term_by( 'slug',$slug , $taxonomy ) ;
		
		$term_icon=$term->term_icon;	
		if($term_icon=='')
			$term_icon = apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png');
		if($taxonomy==''){
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));
			$taxonomy=$taxonomies[0];
		}
		/* Show map view in catgeory page tyen return in google map widget */		
		if($templatic_settings['category_googlemap_widget']=='yes'){
			return;
		}		

		/*Get the directory listing page map settings */		
		$current_term = $wp_query->get_queried_object();
		if($show_all_posts == 'yes' && $templatic_settings['category_googlemap_widget']!='yes' && get_post_type()!='' ){
			if(is_tax()){
				$args = array(
					'post_type' => get_post_type(),
					'tax_query' => array(
						array(
							'taxonomy' => $taxonomy,
							'field' => 'slug',
							'terms' => $term
						)
					),
					'posts_per_page' => -1
				);
			}else{
				$args = array(
					'post_type' => get_post_type(),					
					'posts_per_page' => -1
				);
			}

			if (get_option('tevolution_cache_disable')==1 && false === ($query = get_transient( '_tevolution_query_googlemap_single'.trim(get_post_type()).trim($term->slug).$cur_lang_code )) ) {
				$query = new WP_Query( $args );
				set_transient( '_tevolution_query_googlemap_single'.trim(get_post_type()).trim($term->slug).$cur_lang_code, $query, 12 * HOUR_IN_SECONDS );
			}elseif(get_option('tevolution_cache_disable')==''){
				$query = new WP_Query( $args );
			}
		}else{
			$query = $wp_query;
		}
		
		/* create query string for taxonomy page */
		if(get_post_type()!='' && get_post_type()!='custom_fields' && (is_tax() || is_archive())){
			$operator=(isset($_SERVER['QUERY_STRING'])&& $_SERVER['QUERY_STRING']!='') ?'&':'';
			$_SERVER['QUERY_STRING'].= $operator. 'post_type='.get_post_type().'&taxonomy='.$taxonomy.'&slug='.$slug;
		}
		
		$pids=array("");
		$cat_name = single_cat_title('',false);
		$srcharr = array("'","\r\n");
		$replarr = array("\'","");
		if ($query->have_posts() && $templatic_settings['category_googlemap_widget']!='yes') :
			echo $map_args['before_widget'];
			while ($query->have_posts()) : $query->the_post(); 
				global $post;
				$ID = get_the_ID();
				
				$post_id=get_the_ID();
				if(get_post_meta($post_id,'_event_id',true)){
					$post_id=get_post_meta($post_id,'_event_id',true);
				}
				$title = get_the_title(get_the_ID());
				$marker_title = str_replace("'","\'",$post->post_title);
				$plink = get_permalink(get_the_ID());
				$lat = get_post_meta(get_the_ID(),'geo_latitude',true);
				$lng = get_post_meta(get_the_ID(),'geo_longitude',true);					
				$address = get_post_meta(get_the_ID(),'address',true);
				$address = str_replace($srcharr,$replarr,$address);
				if(is_search() || is_archive() && !is_tax()){
					$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));				
					$post_categories = get_the_terms( get_the_ID() ,$taxonomies[0]);
					foreach($post_categories as $post_category)
					if($post_category->term_icon){
						$term_icon=$post_category->term_icon;
					}
				}
				if(get_post_type()=='listing'){
					$timing=str_replace($srcharr,$replarr,get_post_meta(get_the_ID(),'listing_timing',true));	
					$contact=get_post_meta(get_the_ID(),'phone',true);
				}
				if(get_post_type()=='event'){
					$st_time=get_post_meta(get_the_ID(),'st_time',true);
					$end_time=get_post_meta(get_the_ID(),'end_time',true);
					$timing=$st_time.' To '.$end_time;
					$contact=get_post_meta(get_the_ID(),'phone',true);
				}
				if ( has_post_thumbnail()){
					$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');						
					$post_images=$post_img[0];
				}else{
					$post_img = bdw_get_images_plugin($post_id,'thumbnail');					
					$post_images = $post_img[0]['file'];
				}
				$imageclass='';
				if($post_images)
					$post_image='<div class=map-item-img><a href='.get_permalink($ID).'><img width="150" height="150" class="map_image" src="'.$post_images.'" /></a></div>';
				else{
					$post_image='';
					$imageclass='no_map_image';
				}
				
				$image_class=($post_image)?'map-image' :'';
				$comment_count= count(get_comments(array('post_id' => $ID)));
				$review=($comment_count ==1 )? __('review',ADMINDOMAIN):__('reviews',ADMINDOMAIN);
				if($lat && $lng && !in_array($post_id,$pids))
				{ 
					$retstr ="{";
					$retstr .= "'name':'$marker_title',";
					$retstr .= "'location': [$lat,$lng],";
					$retstr .= "'message':'<div class=\"google-map-info $image_class\"><div class=\"map-inner-wrapper\"><div class=\"map-item-info ".$imageclass."\">$post_image";
					$retstr .= "<h6><a href=\"$plink\" class=\"ptitle\" ><span>$title</span></a></h6>";					
					if($address){$retstr .= '<p class=address>'.trim($address).'</p>';}
					$retstr .= apply_filters('tmpl_map_after_address_fields','',$post->ID);
					if($contact){$retstr .= '<p class=contact>'.ltrim(rtrim($contact)).'</p>';}
					$retstr .= apply_filters('tmpl_map_after_contact_fields','',$post->ID);
					if($website){$retstr .= '<p class=website><a href= '.trim($website).'>'.trim($website).'</a></p>';}
					$retstr .= apply_filters('tmpl_map_custom_fields','',$post->ID);
					if(get_option('default_comment_status')=='open' || $post->comment_status =='open'){
						if($templatic_settings['templatin_rating']=='yes'){
							$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
							$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
						}else{
							$retstr .= apply_filters('show_map_multi_rating',get_the_ID(),$plink,$comment_count,$review);
						}
					}
					$retstr .= "</div></div></div>";
					$retstr .= "',";
					$retstr .= "'icons':'$term_icon',";
					$retstr .= "'pid':'$ID'";
					$retstr .= "}";						
					$content_data[] = $retstr;
				}		
				$pids[]=$post_id;
			endwhile;
			$term_name = str_replace("'","\'",$term->name);
			if($content_data)	
				$catinfo_arr= "[".implode(',',$content_data)."]";	
			wp_reset_query();
			
			$googlemap_setting=get_option('city_googlemap_setting');
			
			$map_type    = ($googlemap_setting['map_city_type'] != '')? $googlemap_setting['map_city_type']: 'ROADMAP';		
			$latitude    = $googlemap_setting['map_city_latitude'];
			$longitude   = $googlemap_setting['map_city_longitude'];	
			$map_display = ($googlemap_setting['set_zooming_opt']!='')? $googlemap_setting['set_zooming_opt']:'0';
			$zoom_level  = ($googlemap_setting['map_city_scaling_factor'])? $googlemap_setting['map_city_scaling_factor'] :'13';
			
			
			wp_print_scripts( 'google-maps-apiscript' );
			wp_print_scripts( 'google-clusterig' );			

			
			$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate.*/
			?>
           <script  type="text/javascript" >
			
				var CITY_MAP_CENTER_LAT= '<?php echo $latitude?>';
				var CITY_MAP_CENTER_LNG= '<?php echo $longitude?>';
				var CITY_MAP_ZOOMING_FACT= <?php echo $zoom_level;?>;
				var infowindow;		
				var zoom_option = '<?php echo (is_search()) ? 0 : $map_display; ?>';
				var markers = <?php echo $catinfo_arr;?>;
				var clustering = '<?php echo $clustering;?>';
				var map;
				var mgr;
				var markerClusterer;		
				var markerArray = [];		
				var pippoint_effects='<?php echo ($templatic_settings['pippoint_effects'] == 'hover')? 'hover':'click';?>';
				var PIN_POINT_ICON_HEIGHT = 32;
				var PIN_POINT_ICON_WIDTH = 20;						
				var infoBubble = new InfoBubble({maxWidth:210,minWidth:210,minHeight:"auto",padding:0,borderRadius:0,borderWidth:0,overflow:"visible",backgroundColor:"#fff"});
				var isDraggable = jQuery(document).width() > 480 ? true : false;
				var isscrollwheel = jQuery(document).width() > 480 ? true : false;
				var dragging = zoom_changed = false;		
				var bounds_modified = false;
				var new_bounds;
				var query_string='<?php echo $_SERVER['QUERY_STRING'];?>';
				var zoom_changed=1;
				function initialize() {
				bounds = new google.maps.LatLngBounds();
				var myOptions = {
					scrollwheel: isscrollwheel,
					draggable: isDraggable,
					zoom: CITY_MAP_ZOOMING_FACT,
					center: new google.maps.LatLng(CITY_MAP_CENTER_LAT, CITY_MAP_CENTER_LNG),
					mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
				}
				map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);			
				/* map style customizer */
				var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
				map.setOptions({styles: styles});		   
				
				/* Add Google Map marlkers */
				mgr = new MarkerManager( map );
				templ_add_googlemap_markers(markers);		   
			   
			   
				if(zoom_option==1){				
					map.fitBounds(bounds);
					map.setCenter(bounds.getCenter());
				}				
				/* but that message is not within the marker's instance data */
				google.maps.event.addListener(map, 'dragstart', function() {
					dragging = false;
				});
				google.maps.event.addListener(map, 'dragend', function() {
					dragging = true;				
				});			
				
				google.maps.event.addListener(map, 'bounds_changed', function() {
				  bounds_modified = true;
				  new_bounds = map.getBounds();
				});			
				google.maps.event.addListener(map, 'zoom_changed', function() {
					if(zoom_changed!=1){
						dragging = true;					
					}
					zoom_changed=0;
				});
							
				google.maps.event.addListener(map, 'idle', function(ev){
					refresh_markers();				
				});
			}
				google.maps.event.addDomListener(window, 'load', initialize);
			</script>
               <div id="listing_google_map" class="listing_google_map" >
                    <div class="map_sidebar">
                    <div class="top_banner_section_in clearfix">
                         <div class="TopLeft"><span id="triggermap"></span></div>
                         <div class="TopRight"></div>
                         <div id="map_canvas" style="width: 100%; height:<?php echo $heigh;?>px" class="map_canvas"></div>
                    </div>
                    </div>
               </div>
               <script>
			var maxMap = document.getElementById( 'triggermap' );		
			google.maps.event.addDomListener(maxMap, 'click', showFullscreen);
			function showFullscreen() {				 
					jQuery('#map_canvas').toggleClass('map-fullscreen');
					jQuery('.map_category').toggleClass('map_category_fullscreen');
					jQuery('.map_post_type').toggleClass('map_category_fullscreen');
					jQuery('#trigger').toggleClass('map_category_fullscreen');
					jQuery('body').toggleClass('body_fullscreen');
					jQuery('#loading_div').toggleClass('loading_div_fullscreen');
					jQuery('#advmap_nofound').toggleClass('nofound_fullscreen');
					jQuery('#triggermap').toggleClass('triggermap_fullscreen');
					jQuery('.TopLeft').toggleClass('TopLeft_fullscreen');
						 
						 window.setTimeout(function() { 
						var center = map.getCenter(); 
						google.maps.event.trigger(map, 'resize'); 
						map.setCenter(center); 
						}, 100);			 }
			</script>     
               <?php
			   echo $map_args['after_widget'];
		endif;/* Finish have_posts if condition*/
		
		
	}/* Finish The widget function*/
	
	/*Widget update function */
	function update($new_instance, $old_instance) {
		/*save the widget*/
		return $new_instance;
	}
	/*Widget admin form display function */
	function form($instance) {
		/*widgetform in backend*/
		$instance = wp_parse_args( (array) $instance, array( 'width' => '', 'height' => '','clustering' => '','show_all_posts'=>'') );		
		$width = strip_tags($instance['width']);
		$height = strip_tags($instance['height']);
		$clustering = strip_tags($instance['clustering']);
		$show_all_posts = strip_tags($instance['show_all_posts']);
		
		?>
		<p>
		   <label for="<?php echo $this->get_field_id('height'); ?>"><?php echo __('Map Height <small>(default height: 425px) to change, only enter a numeric value.)</small>',ADMINDOMAIN);?>:
		   <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($height); ?>" />
		   </label>
		</p>
        
		<p>
			<?php if($clustering) { $checked = "checked=checked"; }else{ $checked =''; } ?>
			<label for="<?php echo $this->get_field_id('clustering'); ?>">
			<input  id="<?php echo $this->get_field_id('clustering'); ?>" name="<?php echo $this->get_field_name('clustering'); ?>" type="checkbox" value="1" <?php echo $checked; ?>/>&nbsp;<?php echo __('Disable Clustering','lm-templatic-admin');?></label>
	    </p>
		
		<p>
		   <label id="<?php echo $this->get_field_id('show_all_posts'); ?>"><input type="checkbox" id="<?php echo $this->get_field_id('show_all_posts'); ?>" name="<?php echo $this->get_field_name('show_all_posts'); ?>" value="yes" <?php if(@$show_all_posts =='yes') echo 'checked';?>/>&nbsp;<?php echo __('Show all posts on category map',ADMINDOMAIN);?></label>
            <p class="description"><?php echo __('With large categories this can significantly increase category page load time. When this option is disabled, the map will only show posts from the current page.',ADMINDOMAIN);?></p>
		</p>
	    <?php
	}
}


/*
 * Function Name: get_googlemap_categoryinfo
 * Return: fetch the custom post type category
 *
 */

function get_googlemap_categoryinfo($post_type,$single_category){
	

	for($i=0;$i<count($post_type);$i++){
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));
			remove_filter('get_terms', array($sitepress, 'get_terms_filter'), 1, 3);
			add_filter('terms_clauses', 'templ_single_googlemap_terms_clauses',10,3);		
		}
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));
		$cat_args = array(
					'taxonomy'=>$taxonomies[0],					
					'hierarchical' => true,
					'title_li' =>'',
					'hide_empty' =>false,
					'child_of' => 0,					
					'orderby'  => 'name',
					'order'    => 'ASC',
					'parent'   => '0',
					'supress_filter'=>false
				);	
		$r = wp_parse_args( $cat_args);	
		$catname_arr=get_categories( $r );	
		$categoriesinfo='';
		foreach($catname_arr as $cat)	{
			$term_id=$org_term_id=$cat->term_id;			
			if(function_exists('icl_object_id')){
				global $sitepress;	
				$language = $sitepress->get_default_language();
				$cat->term_id=icl_object_id($cat->term_id, 'tax_'.$taxonomies[0], false,$language);
				$term = get_term_by( 'id', $cat->term_id,$taxonomies[0]);
				$cat->term_icon = $term->term_icon;
				$term_id = $term->term_id;
				$cat->name = $term->name;
				$cat->slug = $term->slug;
			}			
			if(!in_array($cat->term_id,$single_category))
					continue;
			if($cat->term_icon)
				$term_icon=$cat->term_icon;
			else
				$term_icon=apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png');
			
			$categoriesinfo[]=array('term_id'=>$term_id, 'slug'=>$cat->slug,'name'=>$cat->name,'icon'=>$term_icon,'parent'=>0);	
			
			$child_cat_args = array('taxonomy'=>$taxonomies[0],'hierarchical' => true,'title_li'=>'','child_of' => $org_term_id,'orderby'  => 'name','order'    => 'ASC','hide_empty'=>false);	
			$child_r = wp_parse_args( $child_cat_args);		
			$child_catname_arr=get_categories( $child_r );
			foreach($child_catname_arr as $child_cat)	{
				$term_id=$child_cat->term_id;				
				if(function_exists('icl_object_id')){
					global $sitepress;	
					$language = $sitepress->get_default_language();
					$child_cat->term_id=icl_object_id($child_cat->term_id, 'tax_'.$taxonomies[0], false,$language);	
					$term = get_term_by( 'id', $child_cat->term_id,$taxonomies[0]);
					$child_cat->term_icon = $term->term_icon;
					$term_id = $term->term_id;
					$child_cat->name = $term->name;
					$child_cat->slug = $term->slug;				
				}
				if(!in_array($child_cat->term_id,$single_category))
					continue;
				if(@$child_cat->term_icon)
					$term_icon=$child_cat->term_icon;
				else
					$term_icon=apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png');
				
				$categoriesinfo[]=array('term_id'=>$term_id, 'slug'=>$child_cat->slug,'name'=>$child_cat->name,'icon'=>$term_icon,'parent'=>5);	
				
			}
		}
		if(!empty($categoriesinfo)){
			$catinfo_arr[$post_type[$i]]=$categoriesinfo;
		}
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
			global $sitepress;
			add_filter('terms_clauses', array($sitepress, 'terms_clauses'),10,3);			
			remove_filter('terms_clauses', 'templ_single_googlemap_terms_clauses',10,3);		
		}
		
		
		
	}
	return $catinfo_arr;	
}

/* initialize the map on load*/
add_action('wp_ajax_nopriv_google_map_initialize','google_map_initialize');
add_action('wp_ajax_google_map_initialize','google_map_initialize');
function google_map_initialize(){

	global $wpdb;
	$j=0;
	$pids=array("");
	$srcharr = array('"');
	$replarr = array('\"');
	$title_srcharr = array('"');
	$title_replarr = array('\"');
	$post_type =(explode(',',substr($_REQUEST['posttype'],0,-1)));
	$categoryname =(explode(',',substr($_REQUEST['categoryname'],0,-1)));
	$templatic_settings=get_option('templatic_settings');
	for($i=0;$i<count($post_type);$i++){
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));	
		$cat_args = array(
					'taxonomy'=>$taxonomies[0],
					'orderby' => 'name', 				
					'hierarchical' => true,
					'hide_empty'=>false,
					'title_li'=>''
				);
		$r = wp_parse_args( $cat_args);	
		$catname_arr=get_categories( $r );	
		foreach($catname_arr as $cat)	{
			$catname=$cat->slug;
			if(!in_array($cat->term_id,$categoryname))
				continue;
				
			$cat_ID=$cat->term_id;		
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type[$i],'public'   => true, '_builtin' => true ));
				
			$args=array( 
					   'post_type'      => trim($post_type[$i]),
					   'posts_per_page' => 200,
					   'post_status'    => 'publish',     
					   'tax_query'      => array(                
										  array(
											 'taxonomy' =>$taxonomies[0],
											 'field'    => 'id',
											 'terms'    => $categoryname,
											 'operator' => 'IN'
										  )            
									   ), 					  
					 'orderby' => 'RAND',
					   );
			
			add_filter( 'posts_where', 'googlesearch_posts_where', 10, 2 );
			$post_details= new WP_Query($args);
			
			remove_filter( 'posts_where', 'googlesearch_posts_where', 10, 2 );
			$content_data='';					
			if ($post_details->have_posts()) :				
				while ( $post_details->have_posts() ) : $post_details->the_post();
						global $post;
						$post_categories = get_the_terms( get_the_ID() ,$taxonomies[0]);
						foreach($post_categories as $post_category){
							if($post_category->term_icon && in_array($post_category->term_id,$categoryname)){
								$term_icon=$post_category->term_icon;
								break;
							}else{
								$term_icon=apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png');
							}
						}						
						$ID =get_the_ID();				
						$title = get_the_title($ID);
						$plink = get_permalink($ID);
						$lat = get_post_meta($ID,'geo_latitude',true);
						$lng = get_post_meta($ID,'geo_longitude',true);
						$address1 = stripcslashes(str_replace($srcharr,$replarr,(get_post_meta($ID,'address',true))));
						$address = preg_replace( "/\s+/", " ", $address1 ); /* removal of extra spaces from address */
						$contact = str_replace($srcharr,$replarr,(get_post_meta($ID,'phone',true)));
						$website = str_replace($srcharr,$replarr,(get_post_meta($ID,'website',true)));			
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
							$post_image='<div class=map-item-img><a href='.get_permalink($ID).'><img src='.$post_images.' width=150 height=150/></a></div>';
						else{
							$post_image='';
							$imageclass='no_map_image';
						}												
						
						$image_class=($post_image)?'map-image' :'';
						$comment_count= count(get_comments(array('post_id' => $ID)));
						$review=($comment_count ==1 )? __('review',DOMAIN):__('reviews',DOMAIN);
						if(($lat && $lng )&& !in_array($ID,$pids))
						{ 	
							$retstr ='{';
							$retstr .= '"name":"'.str_replace($title_srcharr,$title_replarr,$post->post_title).'",';
							$retstr .= '"location": ['.$lat.','.$lng.'],';
							$retstr .= '"message":"<div class=\"google-map-info '.$image_class.'\"><div class=map-inner-wrapper><div class=\"map-item-info '.$imageclass.'\">'.$post_image;
							$retstr .= '<h6><a href='.$plink.' class=ptitle ><span>'.$title.'</span></a></h6>';

							if($address){$retstr .= '<p class=address>'.trim($address).'</p>';}
							$retstr .= apply_filters('tmpl_map_after_address_fields','',$post->ID);
							if($contact){$retstr .= '<p class=contact>'.ltrim(rtrim($contact)).'</p>';}
							$retstr .= apply_filters('tmpl_map_after_contact_fields','',$post->ID);
							if($website){$retstr .= '<p class=website><a href= '.trim($website).'>'.trim($website).'</a></p>';}
							$retstr .= apply_filters('tmpl_map_custom_fields','',$post->ID);
							
							if(get_option('default_comment_status')=='open' || $post->comment_status =='open'){
								if($templatic_settings['templatin_rating']=='yes'){
									$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
									$retstr .= '<div class=map_rating>'.str_replace('"','',$rating).' <span><a href='.$plink.'#comments>'.$comment_count.' '.$review.'</a></span></div>';
								}else{
									$retstr .= apply_filters('show_map_multi_rating',get_the_ID(),$plink,$comment_count,$review);
								}
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
				wp_reset_postdata();
			endif;
			
			if($content_data)	
				$cat_content_info[]= implode(',',$content_data);
				
		}	
	}
	if($cat_content_info)
	{
		echo '[{"totalcount":"'.$j.'",'.substr(implode(',',$cat_content_info),1).']';
	}else
	{
		echo '[{"totalcount":"0"}]';
	}
	exit;
}
/*
 It will return the search results on home page map - search from search text box provided on map
 */
function googlesearch_posts_where( $where, &$wp_query){
	global $wpdb;
	if(isset($_REQUEST['search_string']) && $_REQUEST['search_string']!=''){
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $_REQUEST['search_string']) ) . '%\'';
	}	
	return $where;	
}

/* Script to hide the google map in mobile view / in responsive*/
add_action('wp_head','google_maps_responsive');
function google_maps_responsive(){
	$city_googlemap_setting = get_option('city_googlemap_setting'); 		
	if(isset($city_googlemap_setting['google_map_show']) && strtolower( @$city_googlemap_setting['google_map_show']) == strtolower('yes')){ ?>
		<style type='text/css'>
			@media only screen and (max-width: 719px){
				.map_sidebar{ display:none; }
			}
		</style>
	<?php }	
}


/*
get the categories check boxes on Map
 */
function get_single_location_category_checklist($post_type,$pid,$mod='',$select_all='',$single_category){
	
	global $wpdb;
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global $sitepress;
		remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));
		remove_filter('get_terms', array($sitepress, 'get_terms_filter'), 1, 3);
		add_filter('terms_clauses', 'templ_single_googlemap_terms_clauses',10,3);
		
	}
	$post_taxonomy = get_object_taxonomies( (object) array( 'post_type' => $post_type,'public'   => true, '_builtin' => true ));
	$pid = explode(',',$pid);
	/* From the option of tevolution here we get all the taxonomies details*/
	$taxonomy_details = get_option('templatic_custom_taxonomy');
	$taxonomy = $post_taxonomy[0];
	$post_taxonomy = $post_taxonomy[0];
	$table_prefix = $wpdb->prefix;
	$wpcat_id = NULL;
	if($taxonomy==''){
		return ;	
	}
	/*-Fetch main category-*/
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		$language = $sitepress->get_default_language();
		$sql= $wpdb->prepare("SELECT * FROM {$table_prefix}terms t, {$table_prefix}term_taxonomy tt,{$wpdb->prefix}icl_translations icl_t   WHERE t.term_id = tt.term_id AND tt.taxonomy =%s and  tt.parent=%d  AND icl_t.element_id = tt.term_taxonomy_id AND icl_t.element_type IN('%s') AND icl_t.language_code = '%s' ORDER BY t.name",$post_taxonomy,0,'tax_'.$post_taxonomy,$language);
	}else{
		$sql= $wpdb->prepare("SELECT * FROM {$table_prefix}terms, {$table_prefix}term_taxonomy WHERE {$table_prefix}terms.term_id = {$table_prefix}term_taxonomy.term_id AND {$table_prefix}term_taxonomy.taxonomy =%s and  {$table_prefix}term_taxonomy.parent=%d  ORDER BY {$table_prefix}terms.name",$post_taxonomy,0);
	}		
	$wpcategories = (array)$wpdb->get_results($sql);
	
	
	$wpcategories = array_values($wpcategories);
	$wpcat2 = NULL;
	if($wpcategories)
	{
			$counter = 0;
		echo "<ul>";
		if($select_all == 'select_all')
		{
		?>
		<li><label for="selectall"><input type="checkbox" name="<?php echo $single_category;?>[]" id="selectall" value="all" class="checkbox" onclick="single_city_displaychk_frm('<?php echo $single_category;?>');" <?php if( @$pid[0]){ if(in_array('all',$pid)){ echo "checked=checked"; } }else{  }?>/>&nbsp;<?php echo __("Select All",'lm-templatic-admin'); ?></label></li>
		<?php
		}
		foreach ($wpcategories as $wpcat)
		{ 
			if($counter ==0){ 
				$tname = $taxonomy_details[$post_taxonomy]['label']; 
				if($post_taxonomy =='category' || $post_taxonomy ==''): ?>
				<li><label style="font-weight:bold;"><?php _e('Categories','lm-templatic-admin'); ?></label></li>
				<?php else:?>
						<li><label style="font-weight:bold;"><?php echo $tname; ?></label></li>
			<?php 	
				endif;
			}
		
		$counter++;
		$termid = $wpcat->term_id;;
		$name = ucfirst($wpcat->name); 
		$termprice = $wpcat->term_price;
		$tparent =  $wpcat->parent;	
		
		if(function_exists('icl_object_id')){
			$icl_post_id=icl_object_id($termid, 'tax_'.$post_taxonomy, false);
			$term = get_term_by( 'id', $icl_post_id,$post_taxonomy);
			$name = ucfirst($term->name);
		}
		?>
		<li><label for="singe_category_<?php echo $termid; ?>"><input type="checkbox" name="<?php echo $single_category;?>[]" id="singe_category_<?php echo $termid; ?>" value="<?php echo $termid; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($termid,$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; ?></label></li>
		<?php
		
		if($taxonomy !=""){
		 $categories = get_term_children( $termid, $post_taxonomy );
		 
		 foreach($categories as $child_of)
		 { 			
		 	$p = 0;
			$term = get_term_by( 'id', $child_of,$post_taxonomy);
			$termid = $term->term_taxonomy_id;
			$term_tax_id = $term->term_id;
			$termprice = $term->term_price;
			$name = $term->name;			
			if(function_exists('icl_object_id')){
				$icl_post_id=icl_object_id($termid, 'tax_'.$post_taxonomy, false);
				$term = get_term_by( 'id', $icl_post_id,$post_taxonomy);
				$name = $term->name;
			}
			if($child_of)
			{				
				$catprice = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND tt.taxonomy =%s",$child_of,$taxonomy));
				for($i=0;$i<count($catprice);$i++)
				{
					if($catprice->parent)
					{	
						$p++;
						$catprice1 = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND tt.taxonomy =%s",$catprice->parent,$taxonomy));
						if($catprice1->parent)
						{
							$i--;
							$catprice = $catprice1;
							continue;
						}
					}
				}
			}
			$p = $p*15;
		 ?>
			<li style="margin-left:<?php echo $p; ?>px;"><label for="singe_category_<?php echo $term_tax_id; ?>"><input type="checkbox" name="<?php echo $single_category;?>[]" id="singe_category_<?php echo $term_tax_id; ?>" value="<?php echo $term_tax_id; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($term_tax_id,$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; ?></label></li>
		<?php  }	}else{
		 $child = get_term_children( $termid, $post_taxonomy );
		 
		 foreach($child as $child_of)
		 { 

		 	$p = 0;
			$term = get_term_by( 'id', $child_of,$post_taxonomy);
			$termid = $term->term_taxonomy_id;
			$term_tax_id = $term->term_id;
			$termprice = $term->term_price;
			$name = $term->name;
			if(function_exists('icl_object_id')){
				$icl_post_id=icl_object_id($child_of, 'tax_'.$post_taxonomy, false);
				$term = get_term_by( 'id', $icl_post_id,$post_taxonomy);
				$name = $term->name;
			}
			if($child_of)
			{
				$catprice = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND (tt.taxonomy =%s)",$child_of,$post_taxonomy));
				for($i=0;$i<count($catprice);$i++)
				{
					if($catprice->parent)
					{	
						$p++;
						$catprice1 = $wpdb->get_row($wpdb->prepare("select * from $wpdb->term_taxonomy tt ,$wpdb->terms t where t.term_id=%s and t.term_id = tt.term_id AND (tt.taxonomy =%s)",$catprice->parent,$post_taxonomy));
						if($catprice1->parent)
						{
							$i--;
							$catprice = $catprice1;
							continue;
						}
					}
				}
			}
			$p = $p*15;
		 ?>
			<li style="margin-left:<?php echo $p; ?>px;"><label for="singe_category_<?php echo $term_tax_id; ?>"><input type="checkbox" name="<?php echo $single_category;?>[]" id="singe_category_<?php echo $term_tax_id; ?>" value="<?php echo $term_tax_id; ?>" class="checkbox" <?php if($pid[0]){ if(in_array($term_tax_id,$pid) || in_array('all',$pid)){ echo "checked=checked"; } }else{  }?> />&nbsp;<?php echo $name; ?></label></li>
		<?php  }	
				}		
}
	echo "</ul>"; } else{
			sprintf(__('There is no categories in %s','lm-templatic-admin'),$post_type);
	}
	
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		global $sitepress;
	
		remove_filter('terms_clauses', 'templ_single_googlemap_terms_clauses',10,3);
		
	}

}
/* get the categories for single city map */
add_action('wp_ajax_nopriv_get_single_city_categories','get_single_city_categories_callback');
add_action('wp_ajax_get_single_city_categories','get_single_city_categories_callback');
function get_single_city_categories_callback(){

	global $wpdb;
	if(isset($_REQUEST['lang']) && $_REQUEST['lang']!=""){
		$_COOKIE['_icl_current_language']=$_REQUEST['lang'];
	}
	if(empty($_REQUEST['post_type']) || $_REQUEST['post_type']==""){
		echo '<ul><li>'.__("Please select any post type.",'lm-templatic-admin').'</li></ul>';			
		exit;
	}
	$my_post_type = explode(",",$_REQUEST['post_type']);
	$single_category=$_REQUEST['cat_name'];
	$categories='';
	/* loop of post types - Map will display the categories of all post types */
	for($c=0 ; $c < count($my_post_type) ; $c ++){
		if($my_post_type[$c] !=''){
			if($c ==0){
				/* function to get all the categories check box of passed post type in argument  */
				get_single_location_category_checklist($my_post_type[$c],$categories,$_REQUEST['mod'],'select_all',$single_category);
			}else{
				/* function to get all the categories check box of passed post type in argument  */
				get_single_location_category_checklist($my_post_type[$c],$categories,$_REQUEST['mod'],'',$single_category);
			}
		}
	}
	exit;
	
}

/* 
	Add filter to set latitude and longitude on submit page.
*/
add_filter('tmpl_mapcenter_lat','tevolution_tmpl_mapcenter_lat');
add_filter('tmpl_mapcenter_lang','tevolution_tmpl_mapcenter_lang');
if( !function_exists( 'tevolution_tmpl_mapcenter_lat' ) ){
function tevolution_tmpl_mapcenter_lat($lat)
{
	if(!is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		$city_map=get_option('city_googlemap_setting');
		if($city_map['map_city_latitude'] != '')
		{
			return $city_map['map_city_latitude'];
		}
		else
		{
			return 40.714623;
		}
	}
	else
	{
		global $current_cityinfo;
		if(!empty($current_cityinfo))
		{
			return $current_cityinfo['lat'];
		}
		else
		{
			return 40.714623;
		}
	}
}
}
if( !function_exists( 'tevolution_tmpl_mapcenter_lang' ) ){
function tevolution_tmpl_mapcenter_lang($long)
{
	if(!is_plugin_active('Tevolution-LocationManager/location-manager.php'))
	{
		$city_map=get_option('city_googlemap_setting');
		if($city_map['map_city_longitude'] != '')
		{
			return $city_map['map_city_longitude'];
		}
		else
		{
			return '-74.006605';
		}
	}
	else
	{
		global $current_cityinfo;
		if(!empty($current_cityinfo))
		{
			return $current_cityinfo['lng'];
		}
		else
		{
			return '-74.006605';
		}
	}
}
}


/* add the map settings in tevolution general settings section */

add_action('tevolution_after_subsettings','tevolution_after_subsettings_li');
function tevolution_after_subsettings_li(){ ?>
	<li class="submit_page_settings"><a id="map_settings" href="javascript:void(0);"><?php _e('Map Settings',ADMINDOMAIN); ?></a></li>
<?php
} 

/* Show the map settings option tevolution->general settings section */

add_action('tmpl_before_start_captcha_settings','templatic_general_setting_data_mapsetting',13);
function templatic_general_setting_data_mapsetting(){

	$templatic_settings = get_option('templatic_settings');		
	do_action('tmpl_before_mapsettings_tab');
	?>
	<table id="map_settings" class="tmpl-general-settings form-table">
	<?php do_action('tmpl_after_mapsettings_submenu'); ?>
	<tr>
		<th valign="top"><label><?php echo __('Hide maps on mobile devices',ADMINDOMAIN); ?></label></th>
		<td>
			<div class="input-switch">
				<input type="checkbox" id="google_map_show" name="google_map_show" value="yes" <?php if( @$templatic_settings['google_map_show']=='yes') echo 'checked';?>/>
				<label for="google_map_show">&nbsp;<?php echo __('Enable',ADMINDOMAIN);?></label><br/> 
			</div>
			<p class="description"><?php echo __('With this option enabled, maps won&rsquo;t be shown on mobile phones and tablets.',ADMINDOMAIN);?></p>
		</td>
	</tr>
	<?php 
	if(current_theme_supports('map_fullwidth_support')) :?>		
	<tr>
		<th valign="top"><label><?php echo __('Show map in full width',ADMINDOMAIN);?></label></th>
		<td>
			<div class="input-switch">
				<input type="checkbox" id="google_map_full_width" name="google_map_full_width" value="yes" <?php if($templatic_settings['google_map_full_width']=='yes') echo 'checked';?>/>
				<label for="google_map_full_width">&nbsp;<?php echo __('Enable',ADMINDOMAIN);?></label><br/>       
			</div>
			<p class="description"><?php echo __("Along with the homepage map, this setting will stretch any widget placed inside the 'Homepage Slider' and 'Category Page - Below Header' widget areas to full page width.  ",DOMAIN);?></p>
		</td>
	</tr>
	<?php
	endif;
	do_action('after_map_setting');
	?>
	<tr>
		<td colspan="2">
			<p class="submit" style="clear: both;">
			<input type="submit" name="Submit"  class="button button-primary button-hero" value="<?php echo __('Save All Settings',ADMINDOMAIN);?>" />
			<input type="hidden" name="settings-submit" value="Y" />
			</p>
		</td>
	</tr>
	</table>
	<?php
	do_action('tmpl_after_mapsettings_tab');
}
add_action('before_related_post','tmpl_detailpage_direction_map');

/* detail page settings for direction map tab */

function tmpl_detailpage_direction_map(){
	
	global $templatic_settings;
	do_action('tmpl_before_direction_map'); 
	?>
	<tr>
		<th valign="top"><label><?php echo __("Show 'Map' tab",ADMINDOMAIN);?></label></th>
		<td>
			<div class="input-switch">
				<input type="checkbox" id="direction_map" name="direction_map" value="yes" <?php if($templatic_settings['direction_map']=='yes') echo 'checked';?>/>
				<label for="direction_map">&nbsp;<?php echo __('Enable',ADMINDOMAIN);?></label><br/>
			</div>
			<p class="description"><?php echo __("Enable to show the 'Map' tab (where available) above listing description. Disable to use the 'T > Detail Page Map' widget in the sidebar area instead.",ADMINDOMAIN);?></p>
		</td>
	</tr>
	<?php
	do_action('tmpl_before_direction_map'); 	
}



/*Call single city related script on footer for home page widget */
function tmpl_single_city_homepage_map_script(){
	global $clustering;
	$googlemap_setting=get_option('city_googlemap_setting');
	$map_type    = ($googlemap_setting['map_city_type'] != '')? $googlemap_setting['map_city_type']: 'ROADMAP';		
	$latitude    = $googlemap_setting['map_city_latitude'];
	$longitude   = $googlemap_setting['map_city_longitude'];	
	$map_display = ($googlemap_setting['set_zooming_opt']!='')? $googlemap_setting['set_zooming_opt']:'0';
	$zoom_level  = ($googlemap_setting['map_city_scaling_factor'])? $googlemap_setting['map_city_scaling_factor'] :'13';
	
	
	
	$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate.*/
	?>
	 <script  type="text/javascript" async >
        var map_latitude= '<?php echo $latitude?>';
        var map_longitude= '<?php echo $longitude?>';
        var map_zomming_fact= <?php echo $zoom_level;?>;
        var zoom_option = '<?php echo $map_display; ?>';
        var markers = '';
        var markerArray = [];
        var map = null;
        var mgr = null;
        var mClusterer = null;
        var PIN_POINT_ICON_HEIGHT = 32;
        var PIN_POINT_ICON_WIDTH = 20;
        var infowindow = new google.maps.InfoWindow();
        var clustering = '<?php echo $clustering; ?>';
        var infoBubble;
        var isDraggable = jQuery(document).width() > 480 ? true : false;
        function initialize(){
            var myOptions = {
                scrollwheel: false,
                draggable: isDraggable,
                zoom: map_zomming_fact,
                center: new google.maps.LatLng(map_latitude, map_longitude),
                mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
            }
            map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
            var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
            map.setOptions({styles: styles});            
            mgr = new MarkerManager( map );
        }
        
        google.maps.event.addDomListener(window, 'load', initialize);
        google.maps.event.addDomListener(window, 'load', googlemap_initialize);
        
    var maxMap = document.getElementById( 'triggermap' );		
    google.maps.event.addDomListener(maxMap, 'click', showFullscreen);
    function showFullscreen() {          
            jQuery('#map_canvas').toggleClass('map-fullscreen');
            jQuery('.map_category').toggleClass('map_category_fullscreen');
            jQuery('.map_post_type').toggleClass('map_category_fullscreen');
            jQuery('#toggle_post_type').toggleClass('map_category_fullscreen');
            jQuery('#trigger').toggleClass('map_category_fullscreen');
            jQuery('body').toggleClass('body_fullscreen');
            jQuery('#loading_div').toggleClass('loading_div_fullscreen');
            jQuery('#advmap_nofound').toggleClass('nofound_fullscreen');
            jQuery('#triggermap').toggleClass('triggermap_fullscreen');            
            jQuery('.TopLeft').toggleClass('TopLeft_fullscreen');
                 
                 window.setTimeout(function() { 
                var center = map.getCenter(); 
                google.maps.event.trigger(map, 'resize'); 
                map.setCenter(center); 
                }, 100);			 }
    </script>
    <?php
}

/* change pinpoint options when click on enable map view on category page */
add_action('admin_footer','tmpl_show_pinpoint_script_');

function tmpl_show_pinpoint_script_(){
	?>
	<script  type="text/javascript" async >
		function tmpl_show_pinpoint_script(val){
			
			if(document.getElementById('category_googlemap_widget').checked){
				document.getElementById('tmpl_show_pinpoint').style.display ="none";
				document.getElementById('tmpl_show_pinpoint_effect').style.display ="none";
				jQuery("label[for='default_page_view3']").css('display','');
			}else{
				document.getElementById('tmpl_show_pinpoint').style.display ="table-row";
				document.getElementById('tmpl_show_pinpoint_effect').style.display ="table-row";
				jQuery("label[for='default_page_view3']").css('display','none');
			}
		}
	</script>
	<?php
}

function templ_single_googlemap_terms_clauses( $clauses, $taxonomies, $args ){
	global $wpdb, $pagenow, $wp_taxonomies,$ljoin,$sitepress;
	
	$language = $sitepress->get_default_language();
	$clauses[ 'join' ] .= " LEFT JOIN {$wpdb->prefix}icl_translations icl_t ON icl_t.element_id = tt.term_taxonomy_id";
	$clauses[ 'where' ] .= " AND icl_t.language_code = '{$language}' AND icl_t.element_type IN('tax_".$taxonomies[0]."')";	
	return $clauses;
}
?>