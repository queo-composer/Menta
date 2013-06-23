<?php


/**
 * Global session manager for connections to the Selenium 2 server
 */
class Menta_SessionManager
{

    /**
     * @var \WebDriver\Session
     */
    protected static $session;

    /**
     * @var \WebDriver\WebDriver
     */
    protected static $webdriver;

    /**
     * @var string
     */
    protected static $browser = 'firefox';

    /**
     * @var string
     */
    protected static $serverUrl = 'http://localhost:4444/wd/hub';

    /**
     * @var array
     */
    protected static $additionalCapabilities = array();

    /**
     * Init settings
     *
     * @static
     * @param $serverUrl
     * @param string $browser
     * @param array $additionalCapabilities
     * @return void
     */
    public static function init($serverUrl = null, $browser = null, array $additionalCapabilities = null)
    {
        if (!is_null($serverUrl)) {
            self::$serverUrl = $serverUrl;
        }
        if (!is_null($browser)) {
            self::$browser = $browser;
        }
        if (!is_null($additionalCapabilities)) {
            self::$additionalCapabilities = $additionalCapabilities;
        }
    }


    /**
     * @static
     * @return \WebDriver\WebDriver
     */
    public static function getWebdriver()
    {
        if (is_null(self::$webdriver)) {
            if (empty(self::$serverUrl)) {
                throw new Exception('No serverUrl set. Call Menta_SessionManager::init() to configure first');
            }
            self::$webdriver = new \WebDriver\WebDriver(self::$serverUrl);
        }
        return self::$webdriver;
    }

    /**
     * @param bool $forceNew
     * @static
     * @return \WebDriver\Session
     */
    public static function getSession($forceNew = false)
    {
        if ($forceNew) {
            self::closeSession();
        }
        if (is_null(self::$session)) {
            self::$session = self::getWebdriver()->session(self::$browser, self::$additionalCapabilities);
            if (!self::$session instanceof \WebDriver\Session) {
                throw new Exception('Error while creating new session');
            }
            Menta_Events::dispatchEvent(
                'after_session_create',
                array(
                    'session' => self::$session,
                    'forceNew' => $forceNew
                )
            );
        }
        return self::$session;
    }

    /**
     * Get session id.
     * If no session is given the current session will be used
     *
     * @param WebDriver\Session $session
     * @throws Exception
     * @return string
     */
    public static function getSessionId(\WebDriver\Session $session = null)
    {
        if (is_null($session)) {
            if (self::activeSessionExists()) {
                $session = self::getSession();
            } else {
                throw new Exception('No session given and no active session found');
            }
        }
        // the session id is the last part of the url
        $sessionId = array_pop(explode('/', $session->getUrl()));
        return $sessionId;
    }

    /**
     * Check if an active session exists
     *
     * @static
     * @return bool
     */
    public static function activeSessionExists()
    {
        return (self::$session instanceof \WebDriver\Session);
    }

    /**
     * Close existing session
     *
     * @static
     * @return void
     */
    public static function closeSession()
    {
        if (self::activeSessionExists()) {
            Menta_Events::dispatchEvent('before_session_close', array('session' => self::$session));
            self::$session->close();
            self::$session = null;
            Menta_Events::dispatchEvent('after_session_close');
        }
    }

}
