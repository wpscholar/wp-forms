<?php

namespace wpscholar\WordPress;

/**
 * Class FormFactory
 *
 * @package wpscholar\WordPress
 */
class FormFactory {

	/**
	 * Factory for generating a field
	 *
	 * @param string $name
	 * @param callable $handler
	 * @param array $args
	 *
	 * @return Form
	 */
	public static function create( $name, callable $handler, array $args ) {

		$formAtts = isset( $args['atts'] ) ? $args['atts'] : [];
		$form = new Form( $name, $handler, $formAtts );

		$fields = isset( $args['fields'] ) && is_array( $args['fields'] ) ? $args['fields'] : [];

		foreach ( $fields as $name => $fieldArgs ) {
			$form->fields->addField( FieldFactory::create( $name, $fieldArgs ) );
		}

		return $form;

	}

}