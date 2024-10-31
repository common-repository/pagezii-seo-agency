jQuery(document).ready(function($){

	$('#logobutton').on('change', function(){
		var filename = $(this).val();
		filename = filename.substring(filename.lastIndexOf('\\')+1);
		$('#logo').val(filename);
	});

	$('#color_picker').wpColorPicker();

	$('.pz-btn-reset').click(function(){
		var r = confirm("Reset the cover letter to the defaults? Changes will be lost.");
		if (r == true) {
			var pzi_default_cover_letter = $("#defaultCoverLetter").html();
			$('#cover_letter').val(pzi_default_cover_letter);
		}
	});
});
