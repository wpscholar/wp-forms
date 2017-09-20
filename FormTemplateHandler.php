<?php

namespace wpscholar\WordPress;

/**
 * Class FormTemplateHandler
 *
 * @package wpscholar\WordPress
 */
class FormTemplateHandler {

	/**
	 * Instance of this class.
	 *
	 * @var FormTemplateHandler
	 */
	protected static $_instance;

	/**
	 * @var \Twig_Environment
	 */
	protected $_twig;

	/**
	 * Get an instance of this class.
	 *
	 * @return FormTemplateHandler
	 */
	public static function getInstance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * FormTemplateHandler constructor.
	 */
	protected function __construct() {
		$loader = new \Twig_Loader_Filesystem( $this->getTemplatePaths() );
		$twig = new \Twig_Environment( $loader, [ 'debug' => $this->_isDebugMode() ] );

		if ( $this->_isDebugMode() ) {
			$twig->addExtension( new \Twig_Extension_Debug() );
		}

		$this->_twig = $twig;
	}

	/**
	 * Get template paths
	 *
	 * Template paths at the beginning of the array are checked first.
	 *
	 * @return array
	 */
	protected function getTemplatePaths() {
		return apply_filters( __METHOD__, [ __DIR__ . '/templates' ] );
	}

	/**
	 * Render template
	 *
	 * @param string $template
	 * @param array $data
	 */
	public function render( $template, array $data = [] ) {
		echo $this->asString( $template, $data );
	}

	/**
	 * Return template as string.
	 *
	 * @param string $template
	 * @param array $data
	 *
	 * @return string
	 */
	public function asString( $template, array $data = [] ) {
		return $this->_twig->render( $template, $data );
	}

	/**
	 * Check if we are in debug mode.
	 *
	 * @return bool
	 */
	protected function _isDebugMode() {
		return defined( 'WP_DEBUG' ) && WP_DEBUG ? WP_DEBUG : false;
	}

}
