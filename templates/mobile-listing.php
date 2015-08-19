<?php
/**
 * Directory Category taxonomy page for mobile view
 *
**/
get_header(); /*Header Portion*/
$tmpdata = get_option('templatic_settings');
global $posts,$htmlvar_name;
?>	
	
	<div class="mobile-listing-header">

		<?php 
			if(function_exists('tmpl_wp_is_mobile') && tmpl_wp_is_mobile() &&  is_active_sidebar(get_post_type().'_above_mobile_listing')) {
				echo '<a href="#" data-reveal-id="mobile_listing_popup" id="mobile_listing_popup_link">Popup</a>';
				echo "<div id='mobile_listing_popup' class='header-widget-wrap reveal-modal mobile_data_reveal' data-reveal>";
					echo '<a class="close-reveal-modal">?</a>';
		        	dynamic_sidebar( get_post_type().'_above_mobile_listing' );
		        echo "</div>";	
			}
			
		?>
	<?php

		/* Hooks for category title */
		do_action('directory_before_categories_title'); ?>

		<h1 class="loop-title">	
			<?php 
				if(is_tax())
					echo single_cat_title();
				elseif(is_archive()){
					$post_type = get_post_type();
					$PostTypeObject = get_post_type_object($post_type);
					echo $_PostTypeName = $PostTypeObject->labels->name;
				}	
                else
					echo CUSTOM_MENU_TITLE_LISTING;  
			?>	
		</h1>

		<?php do_action('directory_after_categories_title'); 
		/* Hooks for category title end */

		/* Hooks for category sorting and list-grid buttons */
		do_action('directory_after_subcategory');
	?>
	
	</div> <!-- mobile-listing-header -->
	

	<div id="content" class="contentarea large-9 small-12 columns <?php directory_class();?>">
	
	<?php 
	do_action( 'open_content' );
	?>
     
    <div class="view_type_wrap">
		<?php
		/* Hooks for category title */
		do_action('directory_before_categories_title'); 
		
		do_action('directory_after_categories_title'); 
		/* Hooks for category title end */

		/* Hooks for category description */
		do_action('directory_before_categories_description'); 

		do_action('directory_after_categories_description');
		/* Hooks for category description */
		
		do_action('directory_before_subcategory');
	 
		do_action('directory_subcategory');
		
		/* Loads the sidebar-before-content. */
		if(function_exists('supreme_sidebar_before_content'))
		apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); 

		
	 ?>
    </div>
     <!--Start loop taxonomy page-->
   
	<?php do_action('directory_before_loop_taxonomy');?>
     
    <!--Start loop taxonomy page-->
    <section id="loop_listing_taxonomy" class="search_result_listing list" <?php if( is_plugin_active('Tevolution-Directory/directory.php') && isset($tmpdata['default_page_view']) && $tmpdata['default_page_view']=="mapview"){ echo 'style="display: none;"';}?>>
    <?php if (have_posts()) : 
			while (have_posts()) : the_post(); ?>
				<?php do_action('directory_before_post_loop');?>
				 
				<article class="post  <?php templ_post_class();?>" >  
					
					<?php 
					/* Hook to display before image */	
					do_action('directory_before_category_page_image');
						
					/* Hook to Display Listing Image  */
					echo tmpl_mobile_archive_image('mobile-thumbnail');
					 
					/* Hook to Display After Image  */						 
					do_action('directory_after_category_page_image'); 
					   
					/* Before Entry Div  */	
					do_action('directory_before_post_entry');?> 
					
					<!-- Entry Start -->
					<div class="entry"> 
					   
						<?php  /* do action for before the post title.*/ 
						do_action('directory_before_post_title');         ?>
					   <div class="listing-wrapper">
						<!-- Entry title start -->
						<div class="entry-title">
					   
							<?php do_action('templ_post_title');                /* do action for display the single post title */?>
					   
						</div>
						
						<?php do_action(' tev_after_entry_title');          /* do action for after the post title.*/?>
					   
						<!-- Entry title end -->
						
						<!-- Entry details start -->
						<div class="entry-details">
							<?php
							do_action('tmpl_before_maddress'); ?>
							<p class="address"><?php echo get_post_meta($post->ID,'address',true); ?></p>
							<?php do_action('tmpl_after_maddress'); ?>
						</div>
						<!-- Entry details end -->
					   </div>
						<!--Start Post Content -->
						<?php 
							/* Hook for before post content . */ 
					   		do_action('directory_before_post_content'); 	   
						?>
						<!-- End Post Content -->
							
					</div>
					<!-- Entry End -->
					<?php do_action('directory_after_post_entry');?>
				</article>
			<?php do_action('directory_after_post_loop');
          	endwhile;
			wp_reset_query(); 
		else:?>
          	<p class='nodata_msg'><?php _e( 'Apologies, but no results were found for the requested archive.', 'templatic' ); ?></p>              
        <?php endif; 
        
        /* pagination start */
		if($wp_query->max_num_pages !=1):?>
		<div id="listpagi">
			<div class="pagination pagination-position">
				<?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
			</div>
		</div>
		<?php endif; /* pagination end */ ?>
    </section>
    <?php 
	do_action('directory_after_loop_taxonomy');		
	do_action( 'close_content' );
	?>
      <!--End loop taxonomy page -->
	</div>

<?php get_footer(); ?>