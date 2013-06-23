<?php

namespace Demo;

use Menta\Component\Helper\Assert;
use Menta\ComponentManager;
use Menta\SessionManager;
use WebDriver\Key;
use WebDriver\LocatorStrategy;

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * MentaDemoTest
 * Very simple tests for demonstration purposes.
 *
 * @author Fabrizio Branca
 * @since 2011-11-24
 */
class MentaDemoTest extends PHPUnit_Framework_TestCase
{

    public function testDemo()
    {
        $session = SessionManager::getSession();
        $session->open('http://www.google.com/ncr');
        $input = $session->element(LocatorStrategy::ID, 'lst-ib');
        $input->value(array('value' => array('Fabrizio Branca')));
        $input->value(array('value' => array(Key::RETURN_KEY)));
        SessionManager::closeSession();
    }

    public function testTitle()
    {
        $session = SessionManager::getSession();
        $session->open('http://www.google.com/ncr');
        /** @var $assertHelper Assert */
        $assertHelper = ComponentManager::get('Assert');
        $assertHelper->setTest($this)->assertTitle('Google');
        SessionManager::closeSession();
    }

}
