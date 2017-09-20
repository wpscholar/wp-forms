<?php

namespace wpscholar\WordPress;

/**
 * Class Form
 *
 * @package wpscholar\WordPress
 *
 * @property FieldContainer $fields
 * @property callable $handler
 * @property string $method
 * @property string $name
 */
class Form {

	/**
	 * @var array The form attributes.
	 */
	protected $_atts;

	/**
	 * Reference to field container.
	 *
	 * @var FieldContainer
	 */
	protected $_fields;

	/**
	 * Reference to form handler callback function.
	 *
	 * @var callable
	 */
	protected $_handler;

	/**
	 * The form name.
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Form constructor.
	 *
	 * @param string $name The name of the form.
	 * @param callable $handler Callback function that handles processing of the form.
	 * @param array $attributes The attributes for the form. Only used during render.
	 */
	public function __construct( $name, callable $handler, array $attributes = [] ) {
		$this->_name = $name;
		$this->_handler = $handler;
		$this->_atts = $attributes;
		$this->_set_method( ( isset( $attributes['method'] ) ? $attributes['method'] : 'GET' ) );
		$this->_set_fields( new FieldContainer() );
	}

	/**
	 * Process the form.
	 */
	public function process() {
		$this->_setFieldValues();
		call_user_func( $this->_handler, $this );
	}

	/**
	 * Render the form.
	 */
	public function render() {
		echo $this->__toString();
	}

	/**
	 * Check if this form should handle a request.
	 *
	 * @return bool
	 */
	public function shouldHandle() {
		return $this->_name === filter_input( constant( 'INPUT_' . $this->method ), 'form' );
	}

	/**
	 * Set field values from form submission.
	 */
	protected function _setFieldValues() {
		foreach ( $this->fields as $field ) {
			/**
			 * @var Field $field
			 */
			switch ( $this->method ) {
				case 'GET':
					if ( isset( $_GET[ $field->name ] ) ) {
						$field->value = $_GET[ $field->name ];
					}
					break;
				case 'POST':
					if ( isset( $_POST[ $field->name ] ) ) {
						$field->value = $_POST[ $field->name ];
					}
					break;
			}
		}
	}

	/**
	 * Get the fields for this form
	 *
	 * @return string
	 */
	protected function _get_fields() {
		return $this->fields;
	}

	/**
	 * Get the handler for this form
	 *
	 * @return string
	 */
	protected function _get_handler() {
		return $this->_handler;
	}

	/**
	 * Get the method for this form
	 *
	 * @return string
	 */
	protected function _get_method() {
		return isset( $this->_atts['method'] ) ? $this->atts['method'] : 'GET';
	}

	/**
	 * Get the name for this form
	 *
	 * @return string
	 */
	protected function _get_name() {
		return $this->_name;
	}

	/**
	 * Set form fields
	 *
	 * @param FieldContainer $fields
	 */
	protected function _set_fields( FieldContainer $fields ) {
		$fields->addField(
			new InputField( 'form', [
				'type' => 'hidden',
				'atts' => [
					'value' => $this->_name,
				]
			] )
		);
		$this->_fields = $fields;
	}

	/**
	 * Set form method
	 *
	 * @param string $value Allowed values are GET or POST
	 */
	protected function _set_method( $value ) {
		$value = strtoupper( $value );
		$allowed_methods = [ 'GET', 'POST' ];
		if ( ! in_array( $value, $allowed_methods ) ) {
			throw new \InvalidArgumentException( 'Invalid form method' );
		}
		$this->_atts['method'] = $value;
	}

	/**
	 * Getter function.
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get( $property ) {
		$value = null;
		$method = "_get_{$property}";
		if ( method_exists( $this, $method ) && is_callable( [ $this, $method ] ) ) {
			$value = $this->$method();
		}

		return $value;
	}

	/**
	 * Setter function.
	 *
	 * @param string $property
	 * @param mixed $value
	 */
	public function __set( $property, $value ) {
		$method = "_set_{$property}";
		if ( method_exists( $this, $method ) && is_callable( [ $this, $method ] ) ) {
			$this->$method( $value );
		}
	}

	/**
	 * Return form as string
	 *
	 * @return string
	 */
	public function __toString() {

		$templateHandler = FormTemplateHandler::getInstance();

		return $templateHandler->asString( 'form.twig', [
			'atts'    => $this->_atts,
			'content' => $this->fields->__toString(),
		] );
	}


}