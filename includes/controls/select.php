<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor select control.
 *
 * A base control for creating select control. Displays a simple select box.
 * It accepts an array in which the `key` is the option value and the `value` is
 * the option name.
 *
 * @since 1.0.0
 */
class Control_Select extends Base_Data_Control {

	/**
	 * Get select control type.
	 *
	 * Retrieve the control type, in this case `select`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'select';
	}

	/**
	 * Get select control default settings.
	 *
	 * Retrieve the default settings of the select control. Used to return the
	 * default settings while initializing the select control.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'options' => [],
		];
	}

	/**
	 * Render select control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-unit-5">
				<select id="<?php echo $control_uid; ?>" data-setting="{{ data.name }}">
				<#
					var printOptions = function( options ) {
						_.each( options, function( option_title, option_value ) { #>
								<option value="{{ option_value }}">{{{ option_title }}}</option>
						<# } );
					};

					if ( data.groups ) {
						for ( var groupIndex in data.groups ) {
							var groupArgs = data.groups[ groupIndex ];
								if ( groupArgs.options ) { #>
									<optgroup label="{{ groupArgs.label }}">
										<# printOptions( groupArgs.options ) #>
									</optgroup>
								<# } else if ( _.isString( groupArgs ) ) { #>
									<option value="{{ groupIndex }}">{{{ groupArgs }}}</option>
								<# }
						}
					} else {
						printOptions( data.options );
					}
				#>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	/**
	 * @param string $value
	 * @param array $config
	 *
	 * @return string
	 */
	public function before_save( $value, array $config ) {
		return $this->validate_value( $value, $config );
	}

	/**
	 * Validate the value is listed in the control options config.
	 *
	 * Avoid change settings like `html_tag` & `title_size` to a `script` tag.
	 *
	 * @param string $value
	 * @param array $config
	 *
	 * @return mixed|string
	 */
	private function validate_value( string $value, array $config ) {
		$is_valid = false;

		// Handle options groups.
		if ( isset( $config['groups'] ) ) {
			foreach ( $config['groups'] as $index => $group ) {
				if ( isset( $group['options'][ $value ] ) ) {
					$is_valid = true;
					break;
				}
			}
		} else {
			$is_valid = isset( $config['options'][ $value ] );
		}

		// If it's not one of the control options. reset it to default.
		if ( ! $is_valid ) {
			$value = $config['default'];
		}

		return $value;
	}
}
