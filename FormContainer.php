<?php

namespace wpscholar\WordPress;

/**
 * Class FormContainer
 *
 * @package wpscholar\WordPress
 */
class FormContainer implements \IteratorAggregate {

	/**
	 * Form collection
	 *
	 * @var array
	 */
	protected $_forms = [];

	/**
	 * Check if form exists in container.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasForm( $name ) {
		return isset( $this->_forms[ $name ] );
	}

	/**
	 * Get a form from the container by name.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function getForm( $name ) {
		$form = null;
		if ( $this->hasForm( $name ) ) {
			$form = $this->_forms[ $name ];
		}

		return $form;
	}

	/**
	 * Add a form to the container
	 *
	 * @param Form $form
	 */
	public function addForm( Form $form ) {
		$this->_forms[ $form->name ] = $form;
	}

	/**
	 * Remove a form from the container by name.
	 *
	 * @param string $name
	 */
	public function removeForm( $name ) {
		if ( $this->hasForm( $name ) ) {
			unset( $this->_forms[ $name ] );
		}
	}

	/**
	 * Setup iterator for looping through forms
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->_forms );
	}

}