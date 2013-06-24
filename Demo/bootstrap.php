<?php

namespace Demo;

use Menta\Component\AbstractComponent;
use Menta\ConfigurationPhpUnitVars;
use Menta\Events;
use Menta\SessionManager;
use WebDriver\Session;

require_once __DIR__ . '/../vendor/autoload.php';

// Add additional files (with default values) to configuration
ConfigurationPhpUnitVars::addConfigurationFile(__DIR__ . '/defaults.xml');
ConfigurationPhpUnitVars::addConfigurationFile(__DIR__ . '/phpunit.xml');

// Initialize session manager and provider selenium server url
$configuration = ConfigurationPhpUnitVars::getInstance();
SessionManager::init(
    $configuration->getValue('testing.selenium.seleniumServerUrl'),
    $configuration->getValue('testing.selenium.browser')
);

// Do some stuff based on configuration values after the session is initialized
Events::addObserver(
    'after_session_create',
    function (Session $session, $forceNew) {
        $configuration = ConfigurationPhpUnitVars::getInstance();
        // window focus
        try {
            if ($configuration->issetKey('testing.selenium.windowFocus')
                && $configuration->getValue('testing.selenium.windowFocus')
            ) {
                $session->window('main'); // focus
            }
        } catch (\Exception $e) {
            // nevermind
        }

        // window position
        try {
            if ($configuration->issetKey('testing.selenium.windowPosition')) {
                list($x, $y) = explode(
                    ',',
                    $configuration->getValue('testing.selenium.windowPosition')
                );
                $x = intval(trim($x));
                $y = intval(trim($y));
                $session->window('main')->postPosition(array('x' => $x, 'y' => $y));
            }
        } catch (\Exception $e) {
            // nevermind
        }

        // window size
        try {
            if ($configuration->issetKey('testing.selenium.windowSize')) {
                list($width, $height) = explode(
                    'x',
                    $configuration->getValue('testing.selenium.windowSize')
                );
                $width  = intval(trim($width));
                $height = intval(trim($height));
                if (empty($height) || empty($width)) {
                    throw new \Exception('Invalid window size');
                }
                $session->window('main')->postSize(
                    array(
                        'width'  => $width,
                        'height' => $height
                    )
                );
            }
        } catch (\Exception $e) {
            // nevermind
        }

        // implicit wait
        try {
            if ($configuration->issetKey('testing.selenium.timeoutImplicitWait')) {
                $time = $configuration->getValue('testing.selenium.timeoutImplicitWait');
                $time = intval($time);
                $session->timeouts()->implicit_wait(
                    array(
                        'ms' => $time
                    )
                );
            }
        } catch (\Exception $e) {
            // nevermind
        }

    }
);

// Provide configuration object to all components
Events::addObserver(
    'after_component_create',
    function (AbstractComponent $component) {
        $component->setConfiguration(ConfigurationPhpUnitVars::getInstance());
    }
);
