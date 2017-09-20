<?php

namespace wpscholar\WordPress;

/**
 * Class FormHandler
 *
 * @package wpscholar\WordPress
 *
 * @property FormContainer $forms
 */
class FormHandler {

	/**
	 * Reference to the form container
	 *
	 * @var FormContainer
	 */
	protected $_forms;

	/**
	 * FormHandler constructor.
	 */
	public function __construct() {
		$this->_forms = new FormContainer();
	}

	/**
	 * Handler that initiates processing of forms
	 */
	public function maybeProcessForms() {
		if ( isset( $_GET['form'] ) || isset( $_POST['form'] ) ) {
			foreach ( $this->_forms as $form ) {
				/**
				 * @var Form $form
				 */
				if ( $form->shouldHandle() ) {
					$form->process();
				}
			}
		}
	}

	/**
	 * Get forms for this handler
	 *
	 * @return FormContainer
	 */
	protected function _get_forms() {
		return $this->_forms;
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

}