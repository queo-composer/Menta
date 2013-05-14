<?php
/**
 * Abstract component class
 *
 * @author Fabrizio Branca
 * @since 2011-11-24
 */
abstract class Menta_Component_Abstract implements Menta_Interface_Component {

	/**
	 * @var Menta_Interface_Configuration
	 */
	protected $configuration;

	/**
	 * Array with label translations
	 * @var array | NULL
	 */
	protected $translationArray = NULL;

	/**
	 * Constructor
	 */
	public function __construct() {
		// check if we got constructed inside the component manager
		$trace = debug_backtrace(false);
		if ($trace[1]['class'] != 'Menta_ComponentManager' && $trace[1]['function'] != '__construct') {
			throw new Exception(sprintf('Use "Menta_ComponentManager::get(\'%1$s\')" instead of "new %1$s()" to get an instance of this component.', get_class($this)));
		}
		$this->loadTranslation();
	}

	/**
	 * Override this method to add new label translation
	 */
	public function loadTranslation() {
		if ($this->translationArray === NULL) {
			$this->translationArray = array();
		}
	}

	/**
	 * Returns translation of given label
	 *
	 * @param string $key
	 * @return string
	 */
	public function __($key) {
		if(isset($this->translationArray[$key])) {
			return $this->translationArray[$key];
		} else {
			return $key;
		}
	}

	/**
	 * Get session
	 *
	 * @return \WebDriver\Session
	 */
	public function getSession() {
		return Menta_SessionManager::getSession();
	}

	/**
	 * Set configuration
	 *
	 * @param Menta_Interface_Configuration $configuration
	 * @return Menta_Component_Abstract
	 */
	public function setConfiguration(Menta_Interface_Configuration $configuration) {
		$this->configuration = $configuration;
		return $this;
	}

	/**
	 * Get configuration
	 *
	 * @return Menta_Interface_Configuration
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * Some common components that are needed almost everywhere.
	 * These are convenience methods...
	 */

	/**
	 * Get common helper
	 *
	 * @return Menta_Component_Helper_Common
	 */
	public function getHelperCommon() {
		return Menta_ComponentManager::get('Menta_Component_Helper_Common');
	}

	/**
	 * Get assert helper
	 *
	 * @return Menta_Component_Helper_Assert
	 */
	public function getHelperAssert() {
		return Menta_ComponentManager::get('Menta_Component_Helper_Assert');
	}

	/**
	 * Get wait helper
	 *
	 * @return Menta_Component_Helper_Wait
	 */
	protected function getHelperWait() {
		return Menta_ComponentManager::get('Menta_Component_Helper_Wait');
	}

}
