<?php
/*
 * Add The listing custom categories fields like map marker
 */	 
add_action( 'admin_init', 'directory_category_custom_field');
function directory_category_custom_field()
{
	global $wpdb;
	/*
	 * created and edit the listing custom post type category custom field store in term table
	 */
	$tevolution_taxonomy_marker=get_option('tevolution_taxonomy_marker');	
	if($tevolution_taxonomy_marker['listingcategory']!='enable'){
		return '';
	}
	add_action('edited_listingcategory','directory_custom_fields_AlterFields');
	add_action('created_listingcategory','directory_custom_fields_AlterFields');
	/*add_action('edited_listingtags','directory_custom_fields_AlterFields');
	add_action('created_listingtags','directory_custom_fields_AlterFields');*/
	
	add_filter('manage_listingcategory_custom_column', 'manage_directory_category_columns', 10, 3);
	/*add_filter('manage_listingtags_custom_column', 'manage_directory_category_columns', 10, 3);*/
	add_filter('manage_edit-listingcategory_columns', 'directory_category_columns');
	/*add_filter('manage_edit-listingtags_columns', 'directory_category_columns');*/
	/*
	 * created and edit the event custom post type category custom field store in term table
	 */	
	if(isset($_GET['taxonomy']) && ($_GET['taxonomy']== 'listingcategory')) 
	{
		$taxnow=$_GET['taxonomy'];
		add_action($taxnow.'_edit_form_fields','directory_custom_fields_EditFields',11);
		add_action($taxnow.'_add_form_fields','directory_custom_fields_AddFieldsAction',11);		
	}
}
/* custom fields display on edit taxonomy */
function directory_custom_fields_EditFields($tag)
{
	directory_custom_fields_AddFields($tag,'edit');	
}
/* custom fields display on add taxonomy */
function directory_custom_fields_AddFieldsAction($tag)
{
	directory_custom_fields_AddFields($tag,'add');
}
/*
 * Display the map marker upload field in add/edit category of custom post type.
 */
function directory_custom_fields_AddFields($tag,$screen)
{	
	$tax = @$tag->taxonomy;
	?>
     	<div class="form-field-category">
		<tr class="form-field form-field-category">
			<th scope="row" valign="top"><label for="cat_icon"><?php echo __("Map Marker", 'templatic-admin'); ?></label></th>
			<td> 
                    <input id="cat_icon" type="text" size="60" name="cat_icon" value="<?php echo (@$tag->term_icon)? @$tag->term_icon:apply_filters('tmpl_default_map_icon',TEMPL_PLUGIN_URL.'images/pin.png'); ?>"/>	
                    <?php echo __('Or','templatic-admin');?>
                    <a data-id="cat_icon" id="Map Marker" type="submit" class="upload_file_button button"><?php  echo __('Browse','templatic-admin');?></a>   	
                    <p class="description"><?php echo __('It will appear on the homepage Google map for listings placed in this category. ','templatic-admin');?></p>    
			</td>
		</tr>
		</div>
	<?php
}
/*
 * Add/ edit listing and event custom taxonomy custom field 
 */
function directory_custom_fields_AlterFields($termId)
{
	global $wpdb;
	$term_table=$wpdb->prefix."terms";		
	$cat_icon=$_POST['cat_icon'];	
	/*update the service price value in terms table field*/
	if(isset($_POST['cat_icon']) ){
		$sql="update $term_table set term_icon='".$cat_icon."' where term_id=".$termId;
		$wpdb->query($sql);
	}
	
}
/*
 * To Add the column of post type in categories listed table e.g. wp-admin/edit-tags.php?taxonomy=listingcategory&post_type=listing
 */
function directory_category_columns($columns)
{
	$columns['icon'] = __('Map Marker','templatic-admin');	
	if(isset($_GET['taxonomy']) && $_GET['taxonomy']=='ecategory')
		$columns['posts'] = __('Events','templatic-admin');
	if(isset($_GET['taxonomy']) && $_GET['taxonomy']=='listingcategory')
		$columns['posts'] = __('Listings','templatic-admin');
	
	return $columns;	
}
/*
 * Display listing and event custom taxonomy custom field display in category columns
 */
function manage_directory_category_columns($out, $column_name, $term_id){
	global $wpdb;
	$term_table=$wpdb->prefix."terms";		
	$sql="select * from $term_table where term_id=".$term_id;
	$term=$wpdb->get_results($sql);	
	if($term[0]->term_icon) { $icon = $term[0]->term_icon; }else{ $icon = apply_filters('tmpl_default_map_icon',TEVOLUTION_DIRECTORY_URL.'images/pin.png'); }
	switch ($column_name) {
		
		case 'icon':					
				 $out= '<img src="'.$icon.'" >';
			break;
		default:
			break;
	}
	return $out;	
}
/* addmin and posted on columns in manage edit listing sortable columns */
add_filter( 'manage_edit-listing_sortable_columns', 'templatic_edit_listing_columns',11 ) ;
function templatic_edit_listing_columns( $columns ) {
	$columns['address'] = 'address';	
	$columns['posted_on'] = 'posted_on';
	return $columns;
}
?>