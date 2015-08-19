<?php
/**
 * Directory Category taxonomy page
 *
**/
get_header();
$tmpdata = get_option('templatic_settings');
global $posts;

do_action('after_directory_header');

/*do action for display the breadcrumb in between header and container. */
do_action('directory_before_container_breadcrumb'); 

do_action( 'before_content' );
?>
<div id="content" class="contentarea large-9 small-12 columns <?php directory_class();?>">
	
	<?php 
	do_action( 'open_content' );
	
	do_action('directory_inside_container_breadcrumb'); /*do action for display the breadcrumb  inside the container. */ ?>
     
    <div class="view_type_wrap">
		<?php
		/* Hooks for tag title */
		do_action('directory_before_categories_title'); ?>

		<h1 class="loop-title"><?php single_cat_title(); ?></h1>

		<?php do_action('directory_after_categories_title'); 
		/* Hooks for tag title end */

		/* Hooks for tag description */
		do_action('directory_before_categories_description'); 

		if ( tag_description() ) : /* Show an optional tag description */ ?>
          <div class="archive-meta"><?php echo tag_description(); ?></div>
		<?php 
		endif; 

		do_action('directory_after_categories_description');
		  /* Hooks for tag description */
		do_action('directory_before_subcategory');
	 
		do_action('directory_subtags');
			/* Loads the sidebar-before-content. */
			if(function_exists('supreme_sidebar_before_content'))
		   apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); 
		   
		do_action('directory_after_subcategory');
	 ?>
    </div>
     <!--Start loop taxonomy page-->
   
	<?php do_action('directory_before_loop_taxonomy');?>
     
    <!--Start loop taxonomy page-->
    <section id="loop_listing_taxonomy" class="search_result_listing <?php if(isset($tmpdata['default_page_view']) && $tmpdata['default_page_view']=="gridview"){echo 'grid';}else{echo 'list';}?>" <?php if( is_plugin_active('Tevolution-Directory/directory.php') && isset($tmpdata['default_page_view']) && $tmpdata['default_page_view']=="mapview"){ echo 'style="display: none;"';}?>>
    <?php if (have_posts()) : 
			while (have_posts()) : the_post(); ?>
				<?php do_action('directory_before_post_loop');?>
				 
				<article class="post  <?php templ_post_class();?>" >  
					<?php 
					/* Hook to display before image */	
					do_action('directory_before_category_page_image');
						
					/* Hook to Display Listing Image  */
					do_action('directory_category_page_image');
					 
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
						
						<?php do_action('directory_after_post_title');          /* do action for after the post title.*/?>
					   
						<!-- Entry title end -->
						
						<!-- Entry details start -->
						<div class="entry-details">
						
						<?php  /* Hook to get Entry details - Like address,phone number or any static field  */  
						do_action('listing_post_info');   ?>     
						
						</div>
						<!-- Entry details end -->
					   </div>
						<!--Start Post Content -->
						<?php /* Hook for before post content . */ 
					   
						do_action('directory_before_post_content'); 
						
						/* Hook to display post content . */ 
						do_action('templ_taxonomy_content');
					   
						/* Hook for after the post content. */
						do_action('directory_after_post_content'); 
						?>
						<!-- End Post Content -->
					   <?php 
					    /* Hook for before listing categories     */
						do_action('directory_before_taxonomies');
					    
						/* Display listing categories     */
					    do_action('templ_the_taxonomies'); 

						/* Hook to display the listing comments, add to favorite and pinpoint   */						
						do_action('directory_after_taxonomies');?>
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
		if($wp_query->max_num_pages !=1):?>
            <div id="listpagi">
                <div class="pagination pagination-position">
                    <?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
                </div>
            </div>
        <?php endif; /* pagination end */ ?>
    </section>
    <?php do_action('directory_after_loop_taxonomy');
	 
	if(function_exists('supreme_sidebar_after_content'))
		apply_filters('tmpl_after-content',supreme_sidebar_after_content());  /* after-content-sidebar - use remove filter to don't display it */
	/* pagination start */
	
	do_action( 'close_content' );
	?>
      <!--End loop taxonomy page -->
</div>
<!--taxonomy  sidebar -->
<?php 
do_action( 'after_content' );

if ( is_active_sidebar( 'listingcategory_listing_sidebar') ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar('listingcategory_listing_sidebar'); ?>
	</aside>
<?php 
elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</aside>
<?php endif; ?>
<!--end taxonomy sidebar -->
<?php get_footer(); ?>