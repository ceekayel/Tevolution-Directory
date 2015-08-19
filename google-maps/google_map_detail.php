<?php
wp_print_scripts( 'google-maps-apiscript' );
/* show map on detail page */
if($geo_latitude && $geo_longitude){

if(get_post_meta(get_the_ID(),'_event_id',true)){
	$post->ID=get_post_meta(get_the_ID(),'_event_id',true);
}	
$zoom_level=($zooming_factor!="")?$zooming_factor:'13';
$address=($post->ID)?get_post_meta($post->ID,'address',true) :$_SESSION['custom_fields']['address'];

$taxonomies = get_object_taxonomies( (object) array( 'post_type' => get_post_type(),'public'   => true, '_builtin' => true ));	
$post_categories = get_the_terms( $post->ID ,$taxonomies[0]);
foreach($post_categories as $post_category){
	if($post_category->term_icon){
		$term_icon=$post_category->term_icon;
		break;
	}
}
$term_icon=($term_icon!="")?$term_icon: apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png');
	?>

<div id="map-container" style="height:<?php echo $heigh?>px;"></div>

<div class=" get_direction clearfix">
<form action="" method="post" onsubmit="get_googlemap_directory(); return false;">
	<input id="to-input" type="hidden" value="<?php echo $address;?>"/>
	<select onchange="Demo.getDirections();" id="travel-mode-input" style="display:none;">
	  <option value="driving" selected="selected"><?php _e('By car','templatic');?></option>
	  <option value="transit"><?php _e('By public transit','templatic');?></option>
	  <option value="bicycling"><?php _e('By Bicycling','templatic');?></option>
	  <option value="walking"><?php _e('By Walking','templatic');?></option>
	</select>
	<select onchange="Demo.getDirections();" id="unit-input" style="display:none;">
	  <option value="metric"  selected="selected"><?php _e('Metric','templatic');?></option>
	  <option value="imperial"><?php _e('Imperial','templatic');?></option>
	</select>

	<input id="from-input" type="text" onblur="if (this.value == '') {this.value = '<?php _e('Enter Location','templatic');?>';}" onfocus="if (this.value == '<?php _e('Enter Location','templatic');?>') {this.value = '';}" value="<?php _e('Enter Location','templatic');?>" /> 

	<a href="javascript:void(0);" onclick="return set_direction_map()" class="b_getdirection getdir button" > <?php _e('Get Directions','templatic');?> </a>
	<a class="large_map b_getdirection button" target="_blank" href="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $address;?>&amp;sll=<?php echo $geo_latitude;?>,<?php echo $geo_longitude;?>&amp;ie=UTF8&amp;hq=&amp;ll=<?php echo $geo_latitude;?>,<?php echo $geo_longitude;?>&amp;spn=0.368483,0.891953&amp;z=14&amp;iwloc=A"><?php _e('View Large Map','templatic');?></a>
</form>
<?php
$address = get_post_meta($post->ID,'address',true);
$address = str_replace('++','+',str_replace(' ','+',str_replace(',','+',$address)));

$google_map_customizer=get_option('google_map_customizer');/* store google map customizer required formate.*/
?>
<div id="dir-container"></div>
</div>
<script type="text/javascript">
function get_googlemap_directory(){
	set_direction_map();
}
function set_direction_map()
{
	if(document.getElementById('from-input').value=='<?php _e('Enter Location','templatic');?>' || document.getElementById('from-input').value=='')
	{
		alert("<?php _e('Please enter your address to get the direction map.','templatic');?>");return false;
	}else
	{
		document.getElementById('travel-mode-input').style.display='';
		document.getElementById('unit-input').style.display='';
		Demo.getDirections();	
	}
}
var sv = new google.maps.StreetViewService();
var panorama;
var Demo = {
  /* HTML Nodes*/
  mapContainer: document.getElementById('map-container'),
  dirContainer: document.getElementById('dir-container'),
  fromInput: document.getElementById('from-input'),
  toInput: document.getElementById('to-input'),
  travelModeInput: document.getElementById('travel-mode-input'),
  unitInput: document.getElementById('unit-input'),

  /* API Objects*/
  dirService: new google.maps.DirectionsService(),
  dirRenderer: new google.maps.DirectionsRenderer(),
  map: null,

  showDirections: function(dirResult, dirStatus) {
    if (dirStatus != google.maps.DirectionsStatus.OK) {
      alert("<?php _e('Directions failed: ','templatic'); ?>" + dirStatus);
      return;
    }

    /* Show directions*/
    Demo.dirRenderer.setMap(Demo.map);
    Demo.dirRenderer.setPanel(Demo.dirContainer);
    Demo.dirRenderer.setDirections(dirResult);
  },	

  getSelectedTravelMode: function() {
    var value =Demo.travelModeInput.options[Demo.travelModeInput.selectedIndex].value;
    if (value == 'driving') {
      value = google.maps.DirectionsTravelMode.DRIVING;
    } else if (value == 'bicycling') {
      value = google.maps.DirectionsTravelMode.BICYCLING;
    } else if (value == 'walking') {
      value = google.maps.DirectionsTravelMode.WALKING;
    }else if (value == 'transit') {
      value = google.maps.DirectionsTravelMode.TRANSIT;
    } else {
      alert("<?php _e('Unsupported travel mode.','templatic'); ?>");
    }
    return value;
  },

  getSelectedUnitSystem: function() {
    return Demo.unitInput.options[Demo.unitInput.selectedIndex].value == 'metric' ?
        google.maps.DirectionsUnitSystem.METRIC :
        google.maps.DirectionsUnitSystem.IMPERIAL;
  },

  getDirections: function() {
    var fromStr = Demo.fromInput.value;
    var toStr = Demo.toInput.value;
    var dirRequest = {
      origin: fromStr,
      destination: toStr,
      travelMode: Demo.getSelectedTravelMode(),
      unitSystem: Demo.getSelectedUnitSystem(),
      provideRouteAlternatives: true
    };
    Demo.dirService.route(dirRequest, Demo.showDirections);
  },

  init: function() {
    var latLng = new google.maps.LatLng(<?php echo $geo_latitude;?>, <?php echo $geo_longitude;?>);
    var isDraggable = jQuery(document).width() > 480 ? true : false;
    
    Demo.map = new google.maps.Map(Demo.mapContainer, {  
      zoom: <?php echo $zoom_level;?>,
      center: latLng,
	  draggable: isDraggable,
     <?php if($map_type=='Road Map' || $map_type=='Satellite Map'|| $map_type=='Terrain Map'){
		if($map_type=='Satellite Map') { $map_type = SATELLITE; } elseif($map_type=='Terrain Map') { $map_type = TERRAIN; } else { $map_type = ROADMAP; } ?>
	 mapTypeId: google.maps.MapTypeId.<?php echo $map_type;?>
	 <?php }else{?>
	 mapTypeId: google.maps.MapTypeId.ROADMAP
	 <?php }?>
    });
    
 	var marker = new google.maps.Marker({
        position: latLng, 
        map: Demo.map,
		icon: '<?php echo $term_icon; ?>',
        title:"<?php echo trim(str_replace('"','\"',$post->post_title));?>"
    });   	
	
 	<?php if($map_type=='Street Map'):?>			
		panorama = new google.maps.StreetViewPanorama(document.getElementById('map-container'));	
		sv.getPanoramaByLocation(latLng, 50, processSVData);
	<?php endif;?>	
  	var styles = [<?php echo substr($google_map_customizer,0,-1);?>];			
	Demo.map.setOptions({styles: styles});
  }
};

function processSVData(data, status) {	
	if (status == google.maps.StreetViewStatus.OK) {
		var marker = new google.maps.Marker({
			position: data.location.latLng,
			map: Demo.map,
			title: data.location.description
		});
		
		panorama.setPano(data.location.pano);
			panorama.setPov({
			heading: 270,
			pitch: 0
		});
		panorama.setVisible(true);
		
		google.maps.event.addListener(marker, 'click', function() {
			var markerPanoID = data.location.pano;
			/* Set the Pano to use the passed panoID*/
			panorama.setPano(markerPanoID);
				panorama.setPov({
				heading: 270,
				pitch: 0
			});
			panorama.setVisible(true);
		});
	} else {
		alert("<?php _e('Street View data not found for this location.','templatic'); ?>");
	}
}
function toggleStreetView() {
	var toggle = panorama.getVisible();
	if (toggle == false) {
		panorama.setVisible(true);
	} else {
		panorama.setVisible(false);
	}
}
/* Onload handler to fire off the app.*/
google.maps.event.addDomListener(window, 'load', Demo.init);
</script>
<?php }else{ 
$address = get_post_meta($post->ID,'address',true);
$address = str_replace('++','+',str_replace(' ','+',str_replace(',','+',$address)));
$address = "Manhattan, NYC, USA";
if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; }
?>
<iframe width="580" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="
http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=<?php echo $address;?>&ie=UTF8&z=10"></iframe>



<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $http; ?>maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo $address;?>&amp;ie=UTF8&amp;hq=&amp;hnear=Surat,+Gujarat,+India&amp;ll=21.194655,72.557831&amp;spn=0.906514,1.783905&amp;z=10&amp;output=embed"></iframe><br /><small><a href="<?php echo $http; ?>maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=<?php echo $address;?>&amp;ie=UTF8&amp;hq=&amp;hnear=Surat,+Gujarat,+India&amp;ll=21.194655,72.557831&amp;spn=0.906514,1.783905&amp;z=10" style="color:#0000FF;text-align:left"><?php _e("View Larger Map",'templatic');?></a></small>
<?php }?>