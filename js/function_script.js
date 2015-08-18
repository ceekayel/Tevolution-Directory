/**
 *
 * Upload Option
 *
 * Allows window.send_to_editor to function properly using a private post_id
 * Dependencies: jQuery, Media Upload, Thickbox
 *
 */
(function ($) {
  uploadOption = {
    init: function () {
	 var formfield,
		formID,
		btnContent = true;
	 /* On Click*/
	 $('.upload_button').live("click", function () {
	   formfield = $(this).prev('input').attr('id');
	   formID = $(this).attr('rel');
	   tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
	   return false;
	 });
		  
	 window.original_send_to_editor = window.send_to_editor;
	 window.send_to_editor = function(html) {
		   if (formfield) {
			itemurl = $(html).attr('href');
			var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
			var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
			var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
			var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;
			if (itemurl.match(image)) {
			  btnContent = '<img src="'+itemurl+'" alt="" /><a href="javascript:(void);" class="remove">Remove Image</a>';
			} else {
			  btnContent = '<div class="no_image">'+html+'<a href="" class="remove">Remove</a></div>';
			}										
			$('#' + formfield).val(itemurl);
			$('#' + formfield).next().next('div').slideDown().html(btnContent);
			tb_remove();
		   } else {
			window.original_send_to_editor(html);
		   }
	 }
    }
  };
  $(document).ready(function () {
    uploadOption.init()
  })
})(jQuery);