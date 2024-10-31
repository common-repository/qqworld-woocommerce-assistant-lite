if (typeof QWAC == 'undefined') var QWAC = {};
QWAC.help_tip = function() {
	var $ = jQuery;
	$('#qqworld-woocommerce-assistant-container .woocommerce-help-tip').click(function(){
		var $this = $(this);

		// hide this pointer if other pointer is opened.
		$('.wp-pointer').fadeOut(100);

		$(this).pointer({
			content: '<h3>'+$this.data('header')+'</h3><p>'+$this.data('content')+'</p>',
			position: {
				edge: 'left',
				align: 'center',
				offset: '15 0'
			}
		}).pointer('open');
	});
};

jQuery(function($) {
	QWAC.help_tip();

	$('#extension-list').masonry({
		itemSelector: '.extension'
	});
});