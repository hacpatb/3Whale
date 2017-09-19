jQuery(document).ready(function($) {
	var urlStr = $('#url_youtube'),
		but = $('.button-primary'),
		checkY = $('#is_active_y'),
		checkN = $('#is_active_n'),
		radio = $('.form-table input[type=radio]'),
		err = $('#err'),
		valid = function () {
			if( ( urlStr.val().search( /embed/i ) !== -1 && checkY.prop("checked") === true ) || checkN.prop("checked") === true  ) {
				but.prop('disabled', false);
				err[0].style.display = 'none';
			}
			else {
				but.prop('disabled', true);
				err[0].style.display = 'block';
			}
		};
		radio.change(valid);
		urlStr.change(valid); 
});