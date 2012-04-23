<?php

if (version_compare(PHP_VERSION, '5.3.0') <= 0) {
	throw new Exception('Menta needs at least PHP 5.3');
}

define('MENTA_ROOTDIR', dirname(__FILE__));

/**
 * Simple autoloading
 *
 * @param string $className
 * @return bool
 * @throws Exception
 * @author Fabrizio Branca
 * @since 2011-11-24
 */
spl_autoload_register(function ($className) {

	// don't do autoloading for external classes
	if (strpos($className, 'Menta_') !== 0) {
		return false;
	}

	$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if (!file_exists($fileName)) {
		throw new Exception("File '$fileName' not found.");
	}
	require_once($fileName);
	if (!class_exists($className) && !interface_exists($className)) {
		throw new Exception("File '$fileName' does not contain class/interface '$className'");
	}
	return true;

});

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


/**
 * Close existing Selenium server sessions
 *
 * @return void
 */
function closeSeleniumSession() {
	if (Menta_SessionManager::activeSessionExists()) {
		echo "\n[Closing remote selenium session]\n";
		Menta_SessionManager::closeSession();
	}
	exit;
}

if (function_exists('pcntl_signal')) {
	declare(ticks = 1);
	pcntl_signal(SIGTERM, 'closeSeleniumSession');
	pcntl_signal(SIGINT, 'closeSeleniumSession');
}
register_shutdown_function('closeSeleniumSession'); // will also be called on "PHP Fatal Error"

// WebDriver_Base::$debugFile = 'debug.txt';
