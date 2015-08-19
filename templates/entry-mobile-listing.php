<?php
/* template for search result for mobile views. */
global $post;
 do_action('directory_before_post_loop');?>
				 
				<article <?php
					if ((get_post_meta($post->ID, 'featured_h', true) == 'h')) {
							  post_class('post featured_post ');
					} else {
							  post_class('post large-4 medium-4 small-6 xsmall-12 columns');
					}
					?>>  
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
						
						<?php do_action('tev_after_entry_title');          /* do action for after the post title.*/?>
					   
						<!-- Entry title end -->
						
						<!-- Entry details start -->
						<div class="entry-details">
							<?php do_action('tmpl_before_maddress'); ?>
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