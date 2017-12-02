<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_custom_checkbox') ) :


class acf_field_custom_checkbox extends acf_field {

	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct( $settings )
	{
		// vars
		$this->name = 'custom_checkbox';
		$this->label = __("Custom Checkbox with dynamic data",'acf');
		$this->category = __("Choice",'acf');
		$this->defaults = array(
			'layout'		=>	'vertical',
			'choices'		=>	array(),
			'other_choices' => array(),
			'default_value'	=>	'',
		);

		// do not delete!
    parent::__construct();


    // settings
		$this->settings = $settings;
	}


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options( $field )
	{
		// vars
		$key = $field['name'];

		// implode checkboxes so they work in a textarea
		if( is_array($field['choices']) )
		{
			foreach( $field['choices'] as $k => $v )
			{
				$field['choices'][ $k ] = $k . ' : ' . $v;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}

		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Choices",'acf'); ?></label>
		<p><?php _e("Enter each choice on a new line.",'acf'); ?></p>
		<p><?php _e("For more control, you may specify both a value and label like this:",'acf'); ?></p>
		<p><?php _e("red : Red",'acf'); ?><br /><?php _e("blue : Blue",'acf'); ?></p>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'class' => 	'textarea field_option-choices',
			'name'	=>	'fields['.$key.'][choices]',
			'value'	=>	$field['choices'],
		));

		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Default Value",'acf'); ?></label>
		<p class="description"><?php _e("Enter each default value on a new line",'acf'); ?></p>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'name'	=>	'fields['.$key.'][default_value]',
			'value'	=>	$field['default_value'],
		));

		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Layout",'acf'); ?></label>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][layout]',
			'value'	=>	$field['layout'],
			'layout' => 'horizontal',
			'choices' => array(
				'vertical' => __("Vertical",'acf'),
				'horizontal' => __("Horizontal",'acf')
			)
		));

		?>
	</td>
</tr>
		<?php

	}


	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function create_field( $field )
	{

		// value must be array
		if( !is_array($field['value']) )
		{
			// perhaps this is a default value with new lines in it?
			if( strpos($field['value'], "\n") !== false )
			{
				// found multiple lines, explode it
				$field['value'] = explode("\n", $field['value']);
			}
			else
			{
				$field['value'] = array( $field['value'] );
			}
		}

		// Choices must be array
		$choices_temp = array();
		if( !is_array($field['choices']) ) {
			$choices_eol = explode(PHP_EOL, $field['choices']);
			foreach ($choices_eol as $value) {
				$choice_explode = explode(' : ', $value);
				$choice_temp[$choice_explode[0]] = $choice_explode[1];
			}
		}

		$field['choices'] = $choice_temp;

		// trim value
		$field['value'] = array_map('trim', $field['value']);


		// vars
		$i = 0;
		$e = '<input type="hidden" name="' .  esc_attr($field['name']) . '" value="" />';
		$e .= '<ul class="acf-checkbox-list ' . esc_attr($field['class']) . ' ' . esc_attr($field['layout']) . '">';


		// checkbox saves an array
		$field['name'] .= '[]';

		//Get other choice field
		if( $field['value'] && count( $field['value'] ) > 0 && $field['value'][0] != '' ) {
			$field['other_choices'] = array_diff( $field['value'] , array_flip( $field['choices'] ) );
			if( count( $field['other_choices'] ) > 0 ) {
				foreach ( array_values($field['other_choices']) as $value) {
					$field['choices'][$value] = $value;
				}
			}
		}

		// foreach choices
		foreach( $field['choices'] as $key => $value )
		{
			// vars
			$i++;
			$atts = '';
			$other_choice_class = '';


			if( in_array($key, $field['value']) )
			{
				$atts = 'checked="yes"';
			}

			//Check if current choice is other choice
			if( in_array($key, $field['other_choices'] ) ) {
				$other_choice_class = 'other-choice';
			}

			if( isset($field['disabled']) && in_array($key, $field['disabled']) )
			{
				$atts .= ' disabled="true"';
			}


			// each checkbox ID is generated with the $key, however, the first checkbox must not use $key so that it matches the field's label for attribute
			$id = $field['id'];

			if( $i > 1 )
			{
				$id .= '-' . $key;
			}
			if( in_array($key, $field['other_choices'] ) ) {
				$e .= '<li class="'.$other_choice_class.'"><label><input id="' . esc_attr($id) . '" type="checkbox" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['name']) . '" value="' . esc_attr($key) . '" ' . $atts . ' /><input type="text" style="display:none" value="'.$value.'"><span class="custom-checkbox-value">'.$value.'</span><div class="custom-button-group"><a href="#" class="btn-custom-choice-save-edit"><span class="dashicons dashicons-edit"></span></a><a href="#" class="btn-custom-choice-remove"><span class="dashicons dashicons-minus"></span></a></div></label></li>';
			}else{
				$e .= '<li><label><input id="' . esc_attr($id) . '" type="checkbox" class="' . esc_attr($field['class']) . '" name="' . esc_attr($field['name']) . '" value="' . esc_attr($key) . '" ' . $atts . ' />' . $value . '</label></li>';
			}
		}

		$e .= '</ul>';

		// return
		echo $e;
		echo '<hr>';
		echo '<a href="#" class="btn-add-custom-choice" data-name="'.esc_attr($field['name']).'" data-parent="acf-'.esc_attr($field['_name']).'">+ Add new option</a>';
	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used


		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];


		// register & include JS
		wp_register_script('custom-checkbox', "{$url}assets/js/input.js", array('acf-input'), $version);
		wp_enqueue_script('custom-checkbox');


		// register & include CSS
		wp_register_style('custom-checkbox', "{$url}assets/css/input.css", array('acf-input'), $version);
		wp_enqueue_style('custom-checkbox');

	}


	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add CSS and JavaScript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add CSS + JavaScript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add CSS and JavaScript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  load_value()
	*
		*  This filter is applied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in the database
	*/

	function load_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/

	function update_value( $value, $post_id, $field )
	{
		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  format_value()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/

		// perhaps use $field['preview_size'] to alter the $value?


		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  format_value_for_api()
	*
	*  This filter is applied to the $value after it is loaded from the db and before it is passed back to the API functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value_for_api( $value, $post_id, $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/

		// perhaps use $field['preview_size'] to alter the $value?


		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  load_field()
	*
	*  This filter is applied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/

	function load_field( $field )
	{
		// Note: This function can be removed if not used
		return $field;
	}


	/*
	*  update_field()
	*
	*  This filter is applied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field( $field, $post_id )
	{
		// Note: This function can be removed if not used
		return $field;
	}

}


// initialize
new acf_field_custom_checkbox( $this->settings );


// class_exists check
endif;

?>
