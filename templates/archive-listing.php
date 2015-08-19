<?php
/**
 * Directory archive page
 *
**/
get_header(); /*Header Portion*/
$tmpdata = get_option('templatic_settings');
global $posts,$htmlvar_name;
	
	do_action('after_directory_header');
	/*do action for display the breadcrumb in between header and container. */
	do_action('directory_before_container_breadcrumb');
	
	/* get all the custom fields which select as " Show field on listing page" from back end */
	
	if(function_exists('tmpl_get_category_list_customfields')){
		$htmlvar_name = tmpl_get_category_list_customfields(get_post_type());
	}
	
	do_action( 'before_content' );
?>
	
<div id="content" class="contentarea large-9 small-12 columns <?php directory_class();?>">
	<?php 
		do_action( 'open_content' );
		
		/*do action to display the breadcrumb  inside the container. */
		do_action('directory_inside_container_breadcrumb');  
		
		/* Archive page title */
		do_action('directory_before_archive_title'); ?>      
		<h1 class="page-title">
			<?php echo ucfirst(apply_filters('tevolution_archive_page_title','Listing'));?>		
		</h1>
                <?php  
		/* archive - custom post type description */
		 $archive_description = get_option('templatic_custom_post');
		if($archive_description[CUSTOM_POST_TYPE_LISTING]['description'] !=''){
			?>
			 <div class="archive-meta"><?php echo $archive_description[CUSTOM_POST_TYPE_LISTING]['description']; ?></div>
			<?php 
		}
		do_action('directory_after_archive_title'); 
		
		/* Loads the sidebar-before-content. */
		if(function_exists('supreme_sidebar_before_content'))
                    apply_filters('tmpl_before-content',supreme_sidebar_before_content() ); 
		
		do_action('directory_before_loop_archive'); ?>
		<!--Start loop archive page-->  
        <section id="loop_listing_taxonomy" class="search_result_listing <?php if($tmpdata['default_page_view']=="gridview"){echo 'grid';}else{echo 'list';}?>" <?php if( is_plugin_active('Tevolution-Directory/directory.php') && $tmpdata['default_page_view']=="mapview"){ echo 'style="display: none;"';}?>>
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
            <?php endif;?>
	</section>
     
        <?php do_action('directory_after_loop_archive');	 
        if(function_exists('supreme_sidebar_after_content'))
        apply_filters('tmpl_after-content',supreme_sidebar_after_content());  /* after-content-sidebar - use remove filter to don't display it */

        do_action( 'close_content' );
	 ?>
      <!--End loop archive page -->
</div>
<!--archive sidebar -->
<?php 
    do_action( 'after_content' );
    $posttype = get_post_type();
    $taxonomy_names = get_object_taxonomies( $posttype );
    $taxonomy_name = $taxonomy_names[0];
	if ( is_active_sidebar( $taxonomy_name.'_listing_sidebar' )) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar( $taxonomy_name.'_listing_sidebar' ); ?>		
	</aside>
<?php elseif ( is_active_sidebar( 'primary-sidebar') ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar('primary-sidebar'); ?>
	</aside>
<?php elseif ( is_active_sidebar( 'primary') ) : ?>
	<aside id="sidebar-primary" class="sidebar large-3 small-12 columns">
		<?php dynamic_sidebar('primary'); ?>
	</aside>
<?php endif;
get_footer(); ?>