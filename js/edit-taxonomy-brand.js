jQuery( document ).ready( function( $ ){
	var file_frame = [],
		use_logo_default = $( '#use_logo_default'),
		logo_default = $( '#logo_default'),
		single_product_brands_position = $( '#single_product_brands_position'),
		single_product_brands_content = $( '#single_product_brands_content'),
		loop_product_brands_position = $( '#loop_product_brands_position'),
		loop_product_brands_content = $( '#loop_product_brands_content');

	// handles upload image
	$( '.upload_image_button').on( 'click', function( event ){
		var t = $(this),
			id = t.attr('id');

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame[id] ) {
			file_frame[id].open();
			return;
		}

		// Create the media frame.
		file_frame[id] = wp.media.frames.downloadable_file = wp.media( {
			title: QAWC_EXTENSION.labels.upload_file_frame_title,
			button: {
				text: QAWC_EXTENSION.labels.upload_file_frame_button
			},
			multiple: false
		} );

		// When an image is selected, run a callback.
		file_frame[id].on( 'select', function() {
			attachment = file_frame[id].state().get( 'selection' ).first().toJSON();

			t.prev().val( attachment.id );
			t.parent().prev().find( 'img' ).attr( 'src', attachment.sizes.thumbnail.url );
			t.next().show();
		} );

		// Finally, open the modal.
		file_frame[id].open();
	} );

	// handles remove image
	$( '.remove_image_button').on( 'click', function( event ){
		var t = $(this);

		event.preventDefault();

		t.siblings('input').val('');
		t.parent().prev().find( 'img' ).attr( 'src', QAWC_EXTENSION.wc_placeholder_img_src );
		t.hide();
		return false;
	} );

	// hide remove button when not needed
	$( '.upload_image_id' ).each( function(){
		var t = $(this);

		if( ! t.val() || t.val() == '0' ){
			t.siblings( '.remove_image_button').hide();
		}
	} );

	// handle panel dependencies
	use_logo_default.on( 'change', function(){
		var t = $(this);

		if( t.is( ':checked' ) ){
			logo_default.parents( 'tr' ).show();
		}
		else{
			logo_default.parents( 'tr' ).hide();
		}
	}).change();

	single_product_brands_position.on( 'change', function(){
		var t = $(this);

		if( t.val() == 'none' ){
			single_product_brands_content.parents('tr').hide();
		}
		else{
			single_product_brands_content.parents('tr').show();
		}
	}).change();

	loop_product_brands_position.on( 'change', function(){
		var t = $(this);

		if( t.val() == 'none' ){
			loop_product_brands_content.parents('tr').hide();
		}
		else{
			loop_product_brands_content.parents('tr').show();
		}
	}).change();

	// remove duplicated product_cat thumbnail form
	$( '#product_cat_thumbnail').parents('.form-field').remove();
} );