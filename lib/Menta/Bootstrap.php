<?php

if (version_compare(PHP_VERSION, '5.3.0') <= 0) {
	throw new Exception('Menta needs at least PHP 5.3');
}



class Menta_Bootstrap {

	public static function init() {

		define('MENTA_ROOTDIR', dirname(__FILE__));

		// Provide configuration object to all components
		Menta_Events::addObserver('before_component_get', function(Menta_Component_Abstract $component) {

			// set configuration to each component
			$component->setConfiguration(Menta_ConfigurationPhpUnitVars::getInstance());

			// pass current test to components inheriting from Menta_Component_AbstractTest
			if ($component instanceof Menta_Component_AbstractTest
				&& isset($GLOBALS['current_testcase'])
				&& $GLOBALS['current_testcase'] instanceof PHPUnit_Framework_TestCase) {
					/* @var $component Menta_Component_AbstractTest */
					$component->setTest($GLOBALS['current_testcase']);
			}
		});


		$shutDownCallback = array('Menta_Bootstrap', 'closeSeleniumSession');

		if (function_exists('pcntl_signal')) {
			declare(ticks = 1);
			pcntl_signal(SIGTERM, $shutDownCallback);
			pcntl_signal(SIGINT, $shutDownCallback);
		}
		register_shutdown_function($shutDownCallback); // will also be called on "PHP Fatal Error"

	}

	/**
	 * Close existing Selenium server sessions
	 *
	 * @return void
	 */
	public static function closeSeleniumSession() {
		if (Menta_SessionManager::activeSessionExists()) {
			echo "\n[Closing remote selenium session]\n";
			Menta_SessionManager::closeSession();
		}
		exit;
	}

}

