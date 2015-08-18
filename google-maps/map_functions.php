<?php
/* Show Map view in category pages */
add_action('wp_head','tmpl_hooks_for_category_page_mapview',11);

function tmpl_hooks_for_category_page_mapview(){

	global $wp_query,$addons_posttype;
	$addons_posttype_key = array_keys($addons_posttype);
	$templatic_settings=get_option('templatic_settings');
	if(isset($templatic_settings['category_googlemap_widget']) && $templatic_settings['category_googlemap_widget']=='yes'){
		/* Set the sorting options for tevolution post type */
		$post_type = (get_post_type()!='')? get_post_type() : get_query_var('post_type');
		$post_type = apply_filters('tmpl_mapview_for_'.$post_type,$post_type);
		if(!in_array($post_type,$addons_posttype_key))
		{
			$post_type =  'directory';
		}
		/* category page */
		add_action($post_type.'_after_loop_taxonomy','tmpl_categorypage_mapview_opt');
		/* archive page*/
		add_action($post_type.'_after_loop_archive','tmpl_categorypage_mapview_opt');
		/*Search Page */
		add_action($post_type.'_before_loop_search','tmpl_categorypage_mapview_opt',11);
	}
	
}

/* Map view for category page maps */

function tmpl_categorypage_mapview_opt(){
	global $current_cityinfo,$wp_query;
	$heigh =apply_filters('directory_google_map_heigh', '500');
	$templatic_settings=get_option('templatic_settings');
	$taxonomy= get_query_var( 'taxonomy' );
	$slug=get_query_var( get_query_var( 'taxonomy' ) );
	$term=get_term_by( 'slug',$slug , $taxonomy ) ;	
	
	if($term):
		$term_icon=$term->term_icon;
	else:
		$term_icon='';
	endif;	
	
	if($taxonomy==''){
		$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));
		$taxonomy=$taxonomies[0];
	}
	
	if(!isset($term_icon) || $term_icon==''){
		$term_icon = apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png'); }
	/*Get the directory listing page map settings */
	$templatic_settings=get_option('templatic_settings');
	$googlemap_setting=get_option('city_googlemap_setting');	
	
	/* Show all posts on map view / because no pagination in map view */
	if(isset($templatic_settings['category_googlemap_widget']) && $templatic_settings['category_googlemap_widget']=='yes' && get_post_type()!='' && !is_search()){
		
		if(is_plugin_active('Tevolution-LocationManager/location-manager.php'))
		{
			add_filter('posts_where', 'location_multicity_where');
		}
		if(is_plugin_active('sitepress-multilingual-cms/sitepress.php')){
		
			add_filter('posts_where', 'wpml_listing_milewise_search_language');
	
		}
		if(is_tax() || is_search()){
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
		$query = new WP_Query( $args );		
	} else{
		$query = $wp_query;
	}	
	if(is_plugin_active('sitepress-multilingual-cms/sitepress.php'))
		
		{
		
			remove_filter('posts_where', 'wpml_listing_milewise_search_language');
	
		}
	$cat_name = single_cat_title('',false);		
	$srcharr = array("'","<br />","\n","\r");
	$replarr = array("\'","","","");
	if ($query ->have_posts() && $templatic_settings['category_googlemap_widget']=='yes'): 
		while ($query ->have_posts()) : $query ->the_post(); 
			global $post;
			$ID = get_the_ID();
					
			$title = get_the_title(get_the_ID());
			$marker_title = str_replace("'","\'",$post->post_title);
			$plink = get_permalink(get_the_ID());
			$lat = get_post_meta(get_the_ID(),'geo_latitude',true);
			$lng = get_post_meta(get_the_ID(),'geo_longitude',true);					
			$address = str_replace($srcharr,$replarr,get_post_meta(get_the_ID(),'address',true));
			$contact = str_replace($srcharr,$replarr,(get_post_meta(get_the_ID(),'phone',true)));
			$website = get_post_meta(get_the_ID(),'website',true);
			
			if(is_search() || is_archive() && !is_tax()){
				$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));				
				$post_categories = get_the_terms( get_the_ID() ,$taxonomies[0]);
				foreach($post_categories as $post_category)
				if($post_category->term_icon){
					$term_icon=$post_category->term_icon;
				}
			}
			if(get_post_type()=='event'){
				$st_time=get_post_meta(get_the_ID(),'st_time',true);
				$end_time=get_post_meta(get_the_ID(),'end_time',true);
				$timing=$st_time.' To '.$end_time;
				$contact=get_post_meta(get_the_ID(),'phone',true);
			}
			if(get_post_type()=='listing'){
				$timing=str_replace($srcharr,$replarr,get_post_meta(get_the_ID(),'listing_timing',true));	
				$contact = str_replace($srcharr,$replarr,(get_post_meta(get_the_ID(),'phone',true)));
			}			
			if ( has_post_thumbnail()){
				$post_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');						
				$post_images= @$post_img[0];
			}else{
				$post_img = bdw_get_images_plugin(get_the_ID(),'thumbnail');					
				$post_images = @$post_img[0]['file'];
			}
			$imageclass='';
			if($post_images)
				$post_image='<div class=map-item-img><img width="150" height="150" class="map_image" src="'.$post_images.'" /></div>';
			else{
				$post_image='';
				$imageclass='no_map_image';
			}
			
			$image_class=($post_image)?'map-image' :'';
			$comment_count= count(get_comments(array('post_id' => $ID)));
			$review=($comment_count <=1 )? __('review',DIR_DOMAIN):__('reviews',DIR_DOMAIN);
			if($lat && $lng)
			{ 
				$retstr ="{";
				$retstr .= "'name':'$marker_title',";
				$retstr .= "'location': [$lat,$lng],";
				$retstr .= "'message':'<div class=\"google-map-info $image_class forrent\"><div class=\"map-inner-wrapper\"><div class=\"map-item-info ".$imageclass."\">$post_image";
				$retstr .= "<h6><a href=\"$plink\" class=\"ptitle\" ><span>$title</span></a></h6>";				
				if($address){$retstr .= "<p class=address>$address</p>";}				
				if($timing){$retstr .= "<p class=timing style=\"font-size:10px;\">$timing</p>";}
				if($contact){$retstr .= "<p class=contact style=\"font-size:10px;\">$contact</p>";}
				if($website){$retstr .= '<p class=website><a href= \"'.$website.'\">'.$website.'</a></p>';}
				if($templatic_settings['templatin_rating']=='yes'){
					$rating=draw_rating_star_plugin(get_post_average_rating(get_the_ID()));
					$retstr .= "<p class=\"map_rating\">$rating</p>";
				}else{
					$retstr .= apply_filters('show_map_multi_rating',get_the_ID(),$plink,$comment_count,$review);
				}
				$retstr .= "</div></div></div>";
				$retstr .= "',";
				$retstr .= "'icons':'$term_icon',";
				$retstr .= "'pid':'$ID'";
				$retstr .= "}";						
				$content_data[] = $retstr;
			}		
		
		endwhile;
		$term_name = str_replace("'","\'",$term->name);
		if($content_data)	
			$catinfo_arr= "[".implode(',',$content_data)."]";
		wp_reset_query();
		wp_reset_postdata();
		
	/* $current_cityinfo variable not set or empty then set the city wise google map setting */	
	if(!isset($current_cityinfo) || empty($current_cityinfo)){
		$city_map_setting=get_option('city_googlemap_setting');
		$current_cityinfo=array(
						    'map_type'=>$city_map_setting['map_city_type'],
						    'lat'     =>$city_map_setting['map_city_latitude'],
						    'lng'     =>$city_map_setting['map_city_longitude'],
						    'is_zoom_home' =>$city_map_setting['set_zooming_opt'],
						    'scall_factor' =>$city_map_setting['map_city_scaling_factor'],
						    );
	}
	$maptype=($current_cityinfo['map_type'] != '')? $current_cityinfo['map_type']: 'ROADMAP';
	
	$latitude    = $current_cityinfo['lat'];
	$longitude   = $current_cityinfo['lng'];
	$map_type    = $current_cityinfo['map_type'];
	$map_display = ($current_cityinfo['is_zoom_cat'] != '')? $current_cityinfo['is_zoom_cat']: $current_cityinfo['is_zoom_home'];
	$zoom_level  = ($current_cityinfo['cat_scall_factor'] != '')? $current_cityinfo['cat_scall_factor']:$current_cityinfo['scall_factor'];	
	
	
	wp_print_scripts( 'google-maps-apiscript' );
	wp_print_scripts( 'google-clusterig' );
	
	$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate.*/
	
	/* create query string for taxonomy page */
	if(get_post_type()!='' && (is_tax() || is_archive())){		
		$operator=(isset($_SERVER['QUERY_STRING'])&& $_SERVER['QUERY_STRING']!='') ?'&':'';
		$_SERVER['QUERY_STRING'].= $operator. 'post_type='.get_post_type().'&taxonomy='.$taxonomy.'&slug='.$slug;
	}
	?>
	<script  type="text/javascript" async >
		var CITY_MAP_CENTER_LAT= '<?php echo $latitude?>';
		var CITY_MAP_CENTER_LNG= '<?php echo $longitude?>';
		var CITY_MAP_ZOOMING_FACT= <?php echo $zoom_level;?>;
		var infowindow;
		var zoom_option = '<?php echo $map_display; ?>';
		var markers = <?php echo $catinfo_arr;?>;
		var clustering = '';
		var map;
		var mgr;
		var markerClusterer;
		var markerArray = [];

		var mClusterer = null;
		var pippoint_effects='<?php echo ($templatic_settings['pippoint_effects'] == 'hover')? 'hover':'click';?>';
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
				/*new_bounds = map.getBounds();*/
				refresh_markers();				
			});
			
		}
		
		
		google.maps.event.addDomListener(window, 'load', initialize);
	</script>

	<div id="directory_listing_map" class="listing_map" style="<?php if($templatic_settings['default_page_view']=='mapview'){echo 'visibility: visible; height: auto;';}else{echo 'visibility:hidden; height:0;overflow:hidden;';} ?>">
		<div class="map_sidebar_listing">
		<div class="top_banner_section_in clearfix">
			<div class="TopLeft"><span id="triggermap"></span></div>
			<div class="TopRight"></div>
			<div class="iprelative">
			<div id="map_canvas" style="width: 100%; height:<?php echo $heigh;?>px" class="map_canvas"></div>
               </div>
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
	endif;	
}

add_action('wp_footer','tmpl_mapview_script2');
function tmpl_mapview_script2($show_all_posts){ ?>
	<script  type="text/javascript" async >
		var category_map = '<?php echo $show_all_posts;?>';
	</script>
<?php
}
?>