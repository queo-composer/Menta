<?php

namespace Menta\Component;

use Menta\Component\Helper\Assert;
use Menta\Component\Helper\Common;
use Menta\Component\Helper\Wait;
use Menta\ComponentInterface;
use Menta\ComponentManager;
use Menta\ConfigurationInterface;
use Menta\SessionManager;

/**
 * Abstract component class
 *
 * @author Fabrizio Branca
 * @since 2011-11-24
 */
abstract class AbstractComponent implements ComponentInterface
{

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * Array with label translations
     * @var array | NULL
     */
    protected $translationArray = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // check if we got constructed inside the component manager
        $trace = debug_backtrace(false);
        if ($trace[1]['class'] != 'Menta\ComponentManager' && $trace[1]['function'] != '__construct') {
            throw new \Exception(
                sprintf(
                    'Use "ComponentManager::get(\'%1$s\')" instead of "new %1$s()" to get an instance of this component.',
                    get_class($this)
                )
            );
        }
        $this->loadTranslation();
    }

    /**
     * Override this method to add new label translation
     */
    public function loadTranslation()
    {
        if ($this->translationArray === null) {
            $this->translationArray = array();
        }
    }

    /**
     * Returns translation of given label
     *
     * @param string $key
     * @return string
     */
    public function __($key)
    {
        if (isset($this->translationArray[$key])) {
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
    public function getSession()
    {
        return SessionManager::getSession();
    }

    /**
     * Set configuration
     *
     * @param ConfigurationInterface $configuration
     * @return AbstractComponent
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Get configuration
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Some common components that are needed almost everywhere.
     * These are convenience methods...
     */

    /**
     * Get common helper
     *
     * @return Common
     */
    public function getHelperCommon()
    {
        return ComponentManager::get('Menta\Component\Helper\Common');
    }

    /**
     * Get assert helper
     *
     * @return Assert
     */
    public function getHelperAssert()
    {
        return ComponentManager::get('Menta\Component\Helper\Assert');
    }

    /**
     * Get wait helper
     *
     * @return Wait
     */
    protected function getHelperWait()
    {
        return ComponentManager::get('Menta\Component\Helper\Wait');
    }
}
