<?php

namespace wpscholar\WordPress;

use wpscholar\Elements\EnclosingElement;
use wpscholar\Elements\TextNode;

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
	protected $attributes;

	/**
	 * Reference to field container.
	 *
	 * @var FieldContainer
	 */
	protected $fields;

	/**
	 * Reference to form handler callback function.
	 *
	 * @var callable
	 */
	protected $handler;

	/**
	 * The form method.
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * The form name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Form constructor.
	 *
	 * @param string $name The name of the form.
	 * @param callable $handler Callback function that handles processing of the form.
	 * @param string $method The method of the form. Allowed values are 'GET' or 'POST'.
	 * @param array $attributes The attributes for the form. Only used during render.
	 */
	public function __construct( $name, callable $handler, $method = 'GET', array $attributes = [] ) {
		$this->name = $name;
		$this->_set_method( $method );
		$this->_set_fields( new FieldContainer() );
	}

	/**
	 * Process the form.
	 */
	public function process() {
		$this->setFieldValues();
		call_user_func( $this->handler );
	}

	/**
	 * Render the form.
	 */
	public function render() {
		$atts = $this->attributes;
		$atts['method'] = $this->method;
		$form = new EnclosingElement( 'form', $atts );
		foreach ( $this->fields as $field ) {
			/**
			 * @var Field $field
			 */
			$form->append( new TextNode( $field->__toString() ) );
		}
		echo $form;
	}

	/**
	 * Check if this form should handle a request.
	 *
	 * @return bool
	 */
	public function shouldHandle() {
		return $this->name === filter_input( constant( 'INPUT_' . $this->method ), 'form' );
	}

	/**
	 * Set field values from form submission.
	 */
	protected function setFieldValues() {
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
		return $this->handler;
	}

	/**
	 * Get the method for this form
	 *
	 * @return string
	 */
	protected function _get_method() {
		return $this->method;
	}

	/**
	 * Get the name for this form
	 *
	 * @return string
	 */
	protected function _get_name() {
		return $this->name;
	}

	/**
	 * Set form fields
	 *
	 * @param FieldContainer $fields
	 */
	protected function _set_fields( FieldContainer $fields ) {
		$fields->addField( new InputField( 'form', '', [
			'type'  => 'hidden',
			'value' => $this->name,
		] ) );
		$this->fields = $fields;
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
		$this->method = $value;
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


}