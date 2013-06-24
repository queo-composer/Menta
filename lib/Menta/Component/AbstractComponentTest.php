<?php

namespace Menta\Component;

use Menta\PHPUnit\Testcase\Selenium2Testcase;

/**
 * Abstract component class for components that need access to the current PHPUnit testcase
 *
 * @author Fabrizio Branca
 * @since 2011-11-24
 */
abstract class AbstractComponentTest extends AbstractComponent
{

    /**
     * @var PHPUnit_Framework_TestCase
     */
    protected $test;

    /**
     * Set test object
     *
     * @param PHPUnit_Framework_TestCase $test
     * @return AbstractComponentTest
     */
    public function setTest(\PHPUnit_Framework_TestCase $test)
    {
        $this->test = $test;
        return $this;
    }

    /**
     * Get test object
     *
     * @return Selenium2Testcase
     * @throws Exception if testcase is not available
     */
    public function getTest()
    {
        if (is_null($this->test)) {
            throw new \Exception('No testcase object available, check if you are calling parent::setUp() in your test class.');
        }
        return $this->test;
    }
}
