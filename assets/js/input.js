(function($){


	/**
	*  initialize_field
	*
	*  This function will initialize the $field.
	*
	*  @date	30/11/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function initialize_field( $field ) {

	}

	function custom_choice_events() {
		$('body').on('click', '.btn-add-custom-choice', function(e) {
			e.preventDefault();
			$(this).closest( '#' + $(this).data('parent') ).find('ul').append( custom_choice_template( $(this).data('parent'), $(this).data('name') ) );
		});

		$('body').on('click', '.btn-custom-choice-save-edit', function(e) {
			e.preventDefault();
			var value = $(this).closest('li').find('input[type="text"]').val();
			var id = $(this).closest('li').find('input[type="checkbox"]').attr('id');

			if( value.trim() == '' ) {
				$(this).closest('li').find('input[type="text"]').addClass('custom-checkbox-has-error');
				return;
			}

			$(this).closest('li').find('input[type="checkbox"]').attr('id', '');
			$(this).closest('li').find('input[type="checkbox"]').attr('id', id + '_' + value.replace(/\s+/g, '-').toLowerCase());
			$(this).closest('li').find('input[type="checkbox"]').attr('value', value);
			$(this).closest('li').find('.custom-checkbox-value').text(value);
			$(this).closest('li').find('input[type="text"]').removeClass('custom-checkbox-has-error');

			if( $(this).find('span').hasClass('dashicons-yes') ) {
				$(this).closest('li').find('input[type="text"]').hide();
				$(this).closest('li').find('.custom-checkbox-value').show();
				$(this).html('<span class="dashicons dashicons-edit"></span>');
			}else{
				$(this).closest('li').find('input[type="text"]').show();
				$(this).closest('li').find('.custom-checkbox-value').hide();
				$(this).html('<span class="dashicons dashicons-yes"></span>');
			}
		});

		$('body').on('click', '.btn-custom-choice-remove', function(e) {
			e.preventDefault();
			$(this).closest('li').remove();
		});
	}

	function custom_choice_template(id, name) {
		var template = '<li><label><input type="checkbox" id="'+ id +'" name="' + name + '" class="custom_checkbox" checked="checked">'+
		'<input type="text"><span class="custom-checkbox-value" style="display:none"></span>'+
		'<div class="custom-button-group">' +
		'<a href="#" class="btn-custom-choice-save-edit"><span class="dashicons dashicons-yes"></span></a>'+
		'<a href="#" class="btn-custom-choice-remove"><span class="dashicons dashicons-minus"></span></a>'+
		'</div>' +
		'</label></li>';

		return template;
	}


	if( typeof acf.add_action !== 'undefined' ) {

		/*
		*  ready & append (ACF5)
		*
		*  These two events are called when a field element is ready for initizliation.
		*  - ready: on page load similar to $(document).ready()
		*  - append: on new DOM elements appended via repeater field or other AJAX calls
		*
		*  @param	n/a
		*  @return	n/a
		*/

		acf.add_action('ready_field/type=custom_checkbox', 'initialize_field');
		acf.add_action('append_field/type=custom_checkbox', 'initialize_field');


	} else {

		/*
		*  acf/setup_fields (ACF4)
		*
		*  These single event is called when a field element is ready for initizliation.
		*
		*  @param	event		an event object. This can be ignored
		*  @param	element		An element which contains the new HTML
		*  @return	n/a
		*/

		$(document).on('acf/setup_fields', function(e, postbox){
			custom_choice_events();

			// find all relevant fields
			$(postbox).find('.field[data-field_type="custom_checkbox"]').each(function(){

				// initialize
				initialize_field( $(this) );

			});

		});

	}

})(jQuery);
