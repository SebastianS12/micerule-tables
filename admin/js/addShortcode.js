/**
* Adds the Shortcode for the current Table
* to the Input field
*/
jQuery(document).ready(function($){
	if($("#hValue").val()==0){
		$("#addShortcode").prop('checked',false);

	}else{
		$("#addShortcode").prop('checked',true);
	}

	$("#addShortcode").on('change',function(){
		var id = $(this).val();
		if($(this).prop('checked')){  $("#content_ifr").contents().find("#tinymce").children().text('[micerule_tables id="'+id+'"]');
		$("#content").text('[micerule_tables id="'+id+'"]');
		$("#hValue").val(1);
	}else{	$("#content_ifr").contents().find("#tinymce").children().text('');
	$("#hValue").val(0);
	$("#content").text('');
}
});
});
