<?php

namespace Menta;

if (version_compare(PHP_VERSION, '5.3.0') <= 0) {
    throw new Exception('Menta needs at least PHP 5.3');
}


/**
 * Menta bootstrap
 */
class Bootstrap
{

    /**
     * Initialize basic component system
     */
    public static function init()
    {

        define('MENTA_ROOTDIR', dirname(__FILE__));

        // Provide configuration object to all components
        Events::addObserver(
            'before_component_get',
            function (Menta_Component_Abstract $component) {

                // set configuration to each component
                $component->setConfiguration(ConfigurationPhpUnitVars::getInstance());

                // pass current test to components inheriting from AbstractComponentTest
                if ($component instanceof _AbstractComponentTest
                    && isset($GLOBALS['current_testcase'])
                    && $GLOBALS['current_testcase'] instanceof PHPUnit_Framework_TestCase
                ) {
                    /* @var $component _AbstractComponentTest */
                    $component->setTest($GLOBALS['current_testcase']);
                }
            }
        );

        $shutDownCallback = array('Bootstrap', 'closeSeleniumSession');

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
    public static function closeSeleniumSession()
    {
        if (SessionManager::activeSessionExists()) {
            echo "\n[Closing remote selenium session]\n";
            SessionManager::closeSession();
        }
        exit;
    }

}

