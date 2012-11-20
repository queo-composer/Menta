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

	public function __construct() {
		// check if we got constructed inside the component manager
		$trace = debug_backtrace(false);
		if ($trace[1]['class'] != 'Menta_ComponentManager' && $trace[1]['function'] != '__construct') {
			throw new Exception(sprintf('Use "Menta_ComponentManager::get(\'%1$s\')" instead of "new %1$s()" to get an instance of this component.', get_class($this)));
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

}
