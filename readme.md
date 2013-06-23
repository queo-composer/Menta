# Menta Documentation

## Getting started

## Setting up a test folder structure

### Bootstrapping and Autoloading

## Testcases

## Configuration handling

default.xml vs. phpunit xml configuration

## Components

### Organization of components

Project specific components
Component library (plattform specific, agency specific)
Rewrite mechanism to refine/override

### Rewrites

### Translations using __()

## Helpers

### Common
### Assert
### Wait
### Screenshot

## Event/Observers

## Configurations

## Session management

### Reuse browser sessions

### Setting up the session

## Screenshots

## PHPUnit

### Base classes

Selenium1 extends Selenium2 extends PHPUnit

### Running Tests

	cd /var/www
	git clone --recursive git@git.aoesupport.com:users/fabrizio.branca/TestSkeleton.git

	cd /var/www/TestSkeleton
	./composer.phar install

	cd /var/www/TestSkeleton/Tests
	mkdir -p ../../build/reports

	# Run single test
	../vendor/bin/phpunit -c ../conf/devfb.ff.vmhost.xml General/ScreenshotsTest.php

	# Run all tests
	../vendor/bin/phpunit -c ../conf/devfb.ff.vmhost.xml ../vendor/aoemedia/menta/lib/Menta/Util/CreateTestSuite.php

	# Report will be in /var/www/build/reports

### HTML Report

### Integration in Jenkins

### Text Result

## Sauce Labs

### Running on Sauce Labs

### Reporting test results to Sauce Labs

	/**
	 * Will send the test result to sauce labs in case we're running tests there
	 *
	 * @return void
	 */
	protected function tearDown() {

		$sauceUserId = $this->getConfiguration()->getValue('testing.sauce.userId');
		$sauceAccessKey = $this->getConfiguration()->getValue('testing.sauce.accessKey');

		if (!empty($sauceUserId) && !empty($sauceAccessKey) && SessionManager::activeSessionExists()) {
			$status = $this->getStatus();
			$passed = !($status == PHPUnit_Runner_BaseTestRunner::STATUS_ERROR || $status == PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE);
			$rest = new WebDriver\SauceLabs\SauceRest($sauceUserId, $sauceAccessKey);
			$rest->updateJob(SessionManager::getSessionId(), array(WebDriver\SauceLabs\Capability::PASSED => $passed));
		}

		parent::tearDown();
	}

![Alt text](Documentation/aoemedia-new_rgb_72dpi.jpg)