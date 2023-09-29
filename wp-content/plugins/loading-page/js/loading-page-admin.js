jQuery(window).on( 'load', function($){
	var $ = jQuery;

	window['loading_page_collapse_expand_video_tutorial'] = function(e){
		var t = $(e).text(), n = 'X', v = 'expanded', a = 'removeClass';
		if(t == 'X')
		{
			n = '+';
			v = 'collapsed';
			a = 'addClass';
		}
		$(e).text(n);
		$('[name="loading_page_video_tutorial"]').val(v);
		$('.lp-video-tutorial')[a]('lp-video-collapsed');
	};

    // Main application
    window['loading_page_selected_image'] = function(fieldName){
        var img_field = $('input[name="'+fieldName+'"]');
        var media = wp.media({
				title: 'Select Media File',
				library:{
					type: 'image'
				},
				button: {
				text: 'Select Item'
				},
				multiple: false
		}).on('select',
			(function( field ){
				return function() {
					var attachment = media.state().get('selection').first().toJSON();
					var url = attachment.url;
					field.val( url );
				};
			})( img_field )
		).open();
		return false;
    };

    function setPicker(field, colorPicker){
        $(colorPicker).hide();
        $(colorPicker).farbtastic(field);
        $(field).click(function(){$(colorPicker).slideToggle()});
    };

	$( document ).on(
		'change',
		'[name="lp_loading_screen"]',
		function( evt, mssg ){
			if( typeof mssg == 'undefined' || mssg == true )
			{
				var	t = $(evt.target.options[evt.target.selectedIndex]).attr('title');
				if( t && t.length ){ alert(t); }
			}
		}
	);

    $(function(){
        setPicker("#lp_backgroundColor", "#lp_backgroundColor_picker");
        setPicker("#lp_foregroundColor", "#lp_foregroundColor_picker");
    });

	$( '[name="lp_loading_screen"]' ).trigger( 'change', [ false ] );
});