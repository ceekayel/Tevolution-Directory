<?php
/**
 * Directory search page
 *
**/
get_header(); /*Header Portion*/

	do_action('after_directory_header'); 

	do_action('directory_before_container_breadcrumb'); /*do action for display the breadcrumb in between header and container. */

	do_action( 'before_content' );
?>
<div id="content" class="contentarea large-9 small-12 columns <?php directory_class();?>">
	<?php 
	
	do_action( 'open_content' );
	
	/* do action for display the breadcrumb  inside the container. */ 
	do_action('directory_inside_container_breadcrumb'); 
	
	do_action('directory_before_search_title');/*do action for display before categories title*/
	global $current_cityinfo;
	if((isset($_REQUEST['radius']) && $_REQUEST['radius']!='') || (isset($_REQUEST['location']) && $_REQUEST['location']!='')){
		if(isset($_REQUEST['radius']) && $_REQUEST['radius']==1){
			$radius_type=(isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']=='kilometer')? 'kilometer': 'mile';
		}
		if(isset($_REQUEST['radius']) && $_REQUEST['radius']!=1 && $_REQUEST['radius']!=""){
			$radius_type=(isset($_REQUEST['radius_type']) && $_REQUEST['radius_type']=='kilometer')? 'kilometers': 'miles';
		}
		$radius=(isset($_REQUEST['location']) && $_REQUEST['location']!='')?  $_REQUEST['radius'].' '.$radius_type.' around "'.$_REQUEST['location'].'"' : $_REQUEST['radius'].' '.$radius_type.' around "'.$current_cityinfo['cityname'].'"';
	}	
	?>
	<header class="page-header extra-search-criteria-title">
	  <h1 class="page-title"><?php printf( _e( 'Search Results for: ', 'templatic' ). '<span>' . get_search_query() . '</span><span>' . $radius. '</span>'); ?></h1>
	  <?php do_action('after_search_result_label'); ?>
	</header>
	 
	<?php  do_action('directory_after_search_title');/* do action for display after categories title*/ ?>
     
     <!--Start loop search page-->    
     
		<?php do_action('directory_before_loop_search');?>
		<section  id="loop_listing_archive" class="list search_result_listing">
			<div class="listing-search-result">
     	<?php if (have_posts()) : 
				while (have_posts()) : the_post();
                  
				do_action('directory_before_post_loop');?>
				 
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
						do_action('directory_before_post_title');    
						do_action('templ_before_title_'.$post->post_type);     ?>
						<div class="<?php echo $post->post_type; ?>-wrapper">
							<!-- Entry title start -->
							<div class="entry-title-wrapper">
						   
							<?php do_action('templ_post_title');                /* do action for display the single post title */ ?>
						   
							</div>
							
							<?php do_action('directory_after_post_title');          /* do action for after the post title.*/?>
						   
							<!-- Entry title end -->
							
							<!-- Entry details start -->
							<div class="entry-details">
							
							<?php  /* Hook to get Entry details - Like address,phone number or any static field  */  
							
							echo do_action($post->post_type.'_post_info'); ?>
							
							</div>
							<!-- Entry details end -->
							<?php if(is_author() && (!isset($_REQUEST['sort']))){
								do_action('templ_show_edit_renew_delete_link');	
							} ?>
					
						</div>
						<!--Start Post Content -->
						<?php /* Hook for before post content . */ 
					   
						do_action('directory_before_post_content'); 
						
						/* Hook to display post content . */ 
						do_action('templ_taxonomy_content');
					   
						/* Hook for after the post content. */
						do_action('directory_after_post_content'); 
						
						/* Hook for before listing categories     */
						do_action('directory_before_taxonomies');
						
						/* Display listing categories     */
						do_action('templ_the_taxonomies'); 

						/* Hook to display the listing comments, add to favorite and pinpoint   */						
						do_action('directory_after_taxonomies');
						?>
					</div>
					<!-- Entry End -->
					<?php do_action('directory_after_post_entry'); ?>
					</article>
					<?php
					do_action('directory_after_post_loop'); 
          	endwhile; 
			wp_reset_query();
			else: ?>
				<p class='nodata_msg'><?php _e( 'Sorry! No results were found for the requested search. Try searching with some different keywords', 'templatic' ); ?></p>
               
				<?php get_template_part( 'directory-listing','search-form' ); 
				
			endif;?>
		</div>
     
		<?php 
		do_action('directory_after_loop_search');
	 
		if($wp_query->max_num_pages !=1):?>
		<div id="listpagi">
		  <div class="pagination pagination-position">
				<?php if(function_exists('pagenavi_plugin')) { pagenavi_plugin(); } ?>
		  </div>
		</div>
		<?php endif;?>
		</section>
      <!--End loop search page -->
	<?php do_action( 'close_content' ); ?>
</div>
<!--search sidebar -->
<?php 
	do_action( 'after_content' );
	
	if ( is_active_sidebar( 'primary' )) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar( 'primary' ); ?>		
	</aside>
<?php elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</aside>
<?php elseif ( is_active_sidebar( 'listingcategory_listing_sidebar') ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar('listingcategory_listing_sidebar'); ?>
	</aside>
<?php endif; 

get_footer(); ?>
