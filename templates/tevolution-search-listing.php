<?php
global $post,$wp_query;
$wp_query->set('is_ajax_archive',1);
do_action('directory_before_post_loop');
$featured=get_post_meta($post->ID,'featured_c',true);
$classes=($featured=='c')?'featured_c':'';
do_action('directory_before_post_loop');
if(!empty($htmlvar_name))
	$htmlvar_name = $htmlvar_name;
else
	global $htmlvar_name;
?>
				 
<article class="post hentry  <?php echo $classes;?>">  
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
						do_action('listing_post_info',$htmlvar_name);   ?>     
				
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
<?php do_action('directory_after_post_loop'); ?>