<?php
/**
 * Tevolution single custom post type Preview Page template
 *
**/
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-tabs');
$cur_post_type = get_post_meta($cur_post_id,'submit_post_type',true);
$tmpdata = get_option('templatic_settings');	
$address=$_REQUEST['address'];
$geo_latitude =$_REQUEST['geo_latitude'];
$geo_longitude = $_REQUEST['geo_longitude'];
$map_type =$_REQUEST['map_view'];
$website=$_REQUEST['website'];
$phone=$_REQUEST['phone'];
$listing_logo=$_REQUEST['listing_logo'];
$listing_timing=$_REQUEST['listing_timing'];
$email=$_REQUEST['email'];
$special_offer=$_REQUEST['proprty_feature'];
$video=$_REQUEST['video'];
$facebook=$_REQUEST['facebook'];
$google_plus=$_REQUEST['google_plus'];
$twitter=$_REQUEST['twitter'];
$zooming_factor=$_POST['zooming_factor'];
$_REQUEST['imgarr'] = explode(",",$_REQUEST['imgarr']);

/* Set curent language in cookie */
if(is_plugin_active('wpml-translation-management/plugin.php')){
	global $sitepress;
	$_COOKIE['_icl_current_language'] = $sitepress->get_current_language();
}
global $htmlvar_name,$heading_type,$tmpl_flds_varname;
/* Get heading type to display the custom fields as per selected section.  */
if(function_exists('tmpl_fetch_heading_post_type')){

	$heading_type = tmpl_fetch_heading_post_type($cur_post_type);
}

/* get all the custom fields which select as " Show field on detail page" from back end */

if(function_exists('tmpl_single_page_custom_field')){
	$htmlvar_name = tmpl_single_page_custom_field($cur_post_type);
}else{
	global $htmlvar_name;
}

/* to get the common/context custom fields display by default with current post type */
if(function_exists('tmpl_single_page_default_custom_field')){	
	$tmpl_flds_varname = tmpl_single_page_default_custom_field($cur_post_type);
}
?>
<script id="tmpl-foudation" src="<?php echo TEMPL_PLUGIN_URL; ?>js/foundation.min.js"> </script>
<div id="content" role="main" class="directory-single-page large-9 small-12 columns">
	<div class="listing type-listing listing-type-preview hentry" >  
    	<header class="entry-header">
			<?php if($listing_logo!=""):?>
                <div class="entry-header-logo">
                    <img src="<?php echo $listing_logo?>" alt="<?php _e('Logo','templatic');?>" />
                </div>
            <?php endif;?>
        	<section class="entry-header-title">
            	<h1 itemprop="name" class="entry-title"><?php echo stripslashes($_REQUEST['post_title']); ?></h1>
                 <article class="entry-header-custom-wrap">
                    <div class="entry-header-custom-left">
                    <?php if($address!=""):?>
                   		<p><?php echo $address?></p>
                    <?php endif;?>
                    <?php if($website!=""):
                    	if(!strstr($website,'http'))
                    		$website = 'http://'.$website; ?>
                    	<p><a href="<?php echo $website;?>"><?php _e('Website','templatic');?></a></p>
                    <?php endif;?>
                    <?php do_action('directory_display_custom_fields_preview_default_left'); ?>
                    </div>
                    <div class="entry-header-custom-right">
						<?php if($phone!=""):?>
                        	<p class="phone"><label><?php _e('Phone','templatic');?>: </label><span class="listing_custom"><?php echo $phone;?></span></p>
                        <?php endif;?>
                        <?php if($listing_timing!=""):?>
                        	<p class="time"><label><?php _e('Time','templatic');?>: </label><span class="listing_custom"><?php echo $listing_timing;?></span></p>
                        <?php endif;?>
                        <?php if($email!=""):?>
                        	<p class="email"><label><?php _e('Email','templatic');?>: </label><span class="listing_custom"><?php echo antispambot($email);?></span></p>
                        <?php endif;?>    
                        <?php do_action('directory_display_custom_fields_preview_default_right'); ?>                              
                        </div>
                        
                 </article>
            </section>
        </header>
        <section class="entry-content">
        	<?php do_action('directory_preview_before_post_content'); /*Add Action for after preview post content. */?>
        	<div class="share_link">  
				<?php if(is_ssl()){ $http = "https://"; }else{ $http ="http://"; } ?>			
               <script  type="text/javascript" async  src="<?php echo $http; ?>s7.addthis.com/js/250/addthis_widget.js#username=xa-4c873bb26489d97f"></script>
                     <?php if($facebook!=""):?>
                          <a href="<?php echo $facebook;?>" id="facebook"><i class="fa fa-facebook"></i> Facebook</a>
                     <?php endif;?>
                     
                     <?php if($twitter!=""):?>
                          <a href="<?php echo $twitter;?>" id="twitter"><i class="fa fa-twitter"></i> Twitter</a>
                     <?php endif;?>
                     
                     <?php if($google_plus!=""):?>
                          <a href="<?php echo $google_plus;?>" id="google_plus"><i class="fa fa-google-plus"></i> Google Plus</a>
                     <?php endif;?>
            </div>
            
            
            	 <ul class="tabs" data-tab role="tablist">
                      <li class="tab-title active"><a href="#listing_description"><?php _e('Overview','templatic');?></a></li>
                      
                      <?php if($address!=''):?>
                      <li class="tab-title"><a href="#listing_map"><?php _e('Map','templatic');?></a></li>
                      <?php endif;?>
                      
                      <?php if($special_offer!=""):?>
                      <li class="tab-title"><a href="#special_offer"><?php _e('Special Offer','templatic');?></a></li>
                      <?php endif;?>
                      
                      <?php if($video!=""):?>
                      <li class="tab-title"><a href="#listing_video"><?php _e('Video','templatic');?></a></li>
                      <?php endif;?>
    
                      <?php do_action('dir_end_tabs_preview'); ?>
    
                </ul>
                <div class="tabs-content"> 
                <!--Overview Section Start -->
                <section role="tabpanel" aria-hidden="false" class="content active" id="listing_description" >
                      <div class="<?php if($_REQUEST['imgarr'][0]!=''):?>listing_content<?php else:?>content_listing <?php endif;?>">                      
                      <?php echo stripslashes($_REQUEST['post_content']);?>                      
                      </div>
                      <?php if($_REQUEST['imgarr'][0]!='' ):?>
                      	<div id="directory_detail_img" class="entry-header-image">
                        	<?php do_action('directory_preview_before_post_image');
								$thumb_img_counter = 0;
								$thumb_img_counter = $thumb_img_counter+count($_REQUEST['imgarr']);
								$image_path = get_image_phy_destination_path_plugin();
								$tmppath = "/".$upload_folder_path."tmp/";
								
								foreach($_REQUEST['imgarr'] as $image_id=>$val):
									 $thumb_image = get_template_directory_uri().'/images/tmp/'.trim($val);
									break;
								endforeach;
							if(isset($_REQUEST['pid']) && $_REQUEST['pid']!="")
							{	/* execute when comes for edit the post */
								$large_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'directory-single-image');
								$thumb_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'tevolution_thumbnail');
								$largest_img_arr = bdw_get_images_plugin($_REQUEST['pid'],'large');		
							}
						 ?>
                         	<div class="listing-image">
								 <?php $f=0; foreach($_REQUEST['imgarr'] as $image_id=>$val):
										$curry = date("Y");
										$currm = date("m");
										$src = get_template_directory().'/images/tmp/'.$val;
										$img_title = pathinfo($val);
										
								  ?>
									<?php if($largest_img_arr): ?>
											<?php  foreach($largest_img_arr as $value):
												$tmp_v = explode("/",$value['file']);
												 $name = end($tmp_v);
												  if($val == $name):	
											?>
												<img src="<?php echo  $value['file'];?>" alt="" width="300" height="230" class="Thumbnail thumbnail large post_imgimglistimg"/>
											<?php endif;
												endforeach;
										else: ?>								
										<img src="<?php echo $thumb_image;?>" alt="" width="300" height="230" class="Thumbnail thumbnail large post_imgimglistimg"/>
									<?php endif;
									if($f==0) break; endforeach;?>								 
							 </div>	
                             <?php  if(count(array_filter($_REQUEST['imgarr']))>1):?>					
							 <div id="gallery" class="image_title_space">
								<ul class="more_photos">
								 <?php foreach($_REQUEST['imgarr'] as $image_id=>$val)
									{
										$curry = date("Y");
										$currm = date("m");
										$src = get_template_directory().'/images/tmp/'.$val;
										$img_title = pathinfo($val);						
										if($val):
										if(file_exists($src)):
												 $thumb_image = get_template_directory_uri().'/images/tmp/'.$val; ?>
												 <li><img src="<?php echo $thumb_image;?>" alt="" height="60" width="60" title="<?php echo $img_title['filename'] ?>" /></li>
										<?php else: ?>
											<?php
												if($largest_img_arr):
												foreach($largest_img_arr as $value):	
													$tmpl = explode("/",$value['file']);
													$name = end($tmpl);									
													if($val == $name):?>
													<li><img src="<?php echo $value['file'];?>" alt="" height="60" width="60" title="<?php echo $img_title['filename'] ?>" /></li>
											<?php
													endif;
												endforeach;
												endif;
											?>
										<?php endif;
										
										else: 
											if($thumb_img_arr): ?>
											<?php 
											$thumb_img_counter = $thumb_img_counter+count($thumb_img_arr);
											for($i=0;$i<count($thumb_img_arr);$i++):
												$thumb_image = $large_img_arr[$i];
												
												if(!is_array($thumb_image)):
											?>
											  <li><img src="<?php echo $thumb_image;?>" alt="" height="60" width="60" title="<?php echo $img_title['filename'] ?>" /></li>
											  <?php endif;?>
										<?php endfor; ?>
										<?php endif; ?>	
										<?php endif; ?>
									<?php
									$thumb_img_counter++;
									} ?>
									
									</ul>
							 </div>                 
						   <?php endif;?>
                           <!-- -->                                   
                           <?php do_action('directory_preview_after_post_image');?>                              
                        </div>                      
                      <?php endif;?>
				</section>
                 
                 
				<?php if($address!=''):?>
                <!--Map Section Start -->
                <section role="tabpanel" aria-hidden="false" class="content" id="listing_map" >
                    <div id="directory_location_map" style="width:100%;">
                        <div class="directory_google_map" id="directory_google_map_id" style="width:100%;"> 
                        <?php 
						include_once (TEMPL_MONETIZE_FOLDER_PATH.'templatic-custom_fields/google_map_detail.php');?>
                        </div>  <!-- google map #end -->
                    </div>
                </section>
                <!--Map Section End -->
                <?php endif;
				
				if($special_offer!=""):?>
                    <!--Special Offer Start -->
                    <section role="tabpanel" aria-hidden="false" class="content" id="special_offer" >
                    	<?php echo stripslashes($special_offer);?>
                    </section>
                    <!--Special Offer End -->
                <?php endif;?>
				<?php if($video!=""): ?>
                    <!--Video Code Start -->
                    <section role="tabpanel" aria-hidden="false" class="content" id="listing_video" >
                    	<?php
						$embed_video= wp_oembed_get( $video);            
						if($embed_video!=""){
							echo $embed_video;
						}else{
							echo $video;
						}?>						
                    </section>
                    <!--Video code End -->
                <?php endif;
				
				do_action('listing_end_preview');?>
                </div>            
             <?php do_action('directory_preview_page_fields_collection'); /*Add Action for after preview post content. */?>
             <div class="post-meta">  
			  <?php 
			  /* Display selected category and listing tags */
              if(function_exists('directory_post_preview_categories_tags') ){				  
                	echo directory_post_preview_categories_tags($_REQUEST['category'],$_REQUEST['post_tags']);
              } 
			  ?>
			</div>
        </section>
    
    </div>
</div>

<script  type="text/javascript" async >	
jQuery(function(){jQuery("ul.tabs li a").live('click',function(){
	var n=jQuery(this).attr("href");
	if(n=="#listing_map")
	{
		Demo.init();
	}
 })});
</script>