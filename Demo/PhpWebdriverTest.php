<?php

namespace Demo;

require __DIR__ . '/../vendor/autoload.php';

use WebDriver\Key;
use WebDriver\LocatorStrategy;
use WebDriver\WebDriver;

try {

    # get webdriver
    $webDriver = new WebDriver('http://localhost:4444/wd/hub');

    # create session
    $session = $webDriver->session('firefox');

    $session->window('main'); // focus
    $session->window('main')->postPosition(array('x' => 0, 'y' => 0)); // position
    $session->window('main')->postSize(array('width' => 1280, 'height' => 1024)); // size

    # Got to google
    $session->open('http://www.google.com/');

    # Search
    $input = $session->element(LocatorStrategy::NAME, 'q');
    $input->value(array('value' => array('AOE media')));
    $input->value(array('value' => array(Key::RETURN_KEY)));

    sleep(2);

    $firstResult = $session->element(LocatorStrategy::XPATH, '//ol[@id="rso"]/li[1]//a');
    printf("Search result: %s\n", $firstResult->text());

    $firstResult->click();

    sleep(5);

    # Go back to search results
    $session->back();

    sleep(5);

    # close session/connection
    $session->close();

} catch (Exception $e) {
    echo $e->getMessage();
}
