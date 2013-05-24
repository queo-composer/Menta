<?php

/**
 * View helper for HTML Result view
 *
 * @author Fabrizio Branca
 */
class Menta_PHPUnit_Listener_Resources_HtmlResultView extends Menta_Util_View {

	/**
	 * Print test
	 *
	 * @param array $test
	 * @param null $name
	 * @return string
	 */
	public function printTest(array $test, $name=NULL) {
		$testName = $name ? $name : $test['testName'];
		$roundPrecision = ($test['time'] < 10) ? 2 : 0;
		$result = '';
		$result .= '<div class="test '.$this->getStatusName($test['status']).'">';
			$result .= '<div class="duration">'.round($test['time'], $roundPrecision).'s</div>';
			$result .= '<h2>'.$this->shorten($testName).'</h2>';

			if (!empty($test['description'])) {
				$result .= '<div class="description">' . nl2br($test['description']) . '</div>';
			}

			if (is_array($test['info'])) {
				$result .= '<ul class="info">';
				foreach ($test['info'] as $info) {
					$result .= '<li>'.$info.'</li>';
				}
				$result .= '</ul>';
			}

			$result .= '<div class="content">';

				if ($test['exception'] instanceof Exception) {
					$e = $test['exception']; /* @var $e Exception */
					$result .= '<div class="exception">';
						$result .= '<i>'. nl2br($this->escape(PHPUnit_Util_Filter::getFilteredStacktrace($e))) . '</i>'."<br />\n";
						$result .= '<pre>' . $this->escape(PHPUnit_Framework_TestFailure::exceptionToString($e)) . '</pre>';
					$result .= '</div><!-- exception -->';
				}

				if (isset($test['screenshots'])) {
					$result .= '<div class="screenshots">';
					$result .= $this->printScreenshots($test['screenshots']);
					$result .= '</div><!-- screenshots -->';
				}
		
			$result .= '</div><!-- content -->';
		$result .= '</div><!-- test -->';
		return $result;
	}

	protected function getPreviousPath() {
		$conf = Menta_ConfigurationPhpUnitVars::getInstance();
		if ($conf->issetKey('report.previous')) {
			$previousReport = $conf->getValue('report.previous');
			if (is_dir($previousReport)) {
				return $previousReport;
			}
		}
		return false;
	}

	/**
	 * Print screenshots
	 *
	 * @param array $screenshots
	 * @return string
	 */
	protected function printScreenshots(array $screenshots) {
		$result = '';
		$directory = $this->get('basedir');
		$result .= '<ul class="screenshots-list">';
		foreach ($screenshots as $screenshot) { /* @var $screenshot Menta_Util_Screenshot */
			$result .= '<li class="screenshot">';

			try {
				$fileName = 'screenshot_' . $screenshot->getId() . '.png';
				$thumbnailName = 'screenshot_' . $screenshot->getId() . '_thumb.png';

				$screenshot->writeToDisk($directory . DIRECTORY_SEPARATOR . $fileName);

				// create thumbnail
				$simpleImage = new Menta_Util_SimpleImage($directory . DIRECTORY_SEPARATOR . $fileName);
				$simpleImage->resizeToWidth(100)->save($directory . DIRECTORY_SEPARATOR . $thumbnailName, IMAGETYPE_PNG);

				$result .= '<a class="current" title="'.$screenshot->getTitle().'" href="'.$fileName.'">';
					$result .= '<img src="'.$thumbnailName.'" width="100" />';
				$result .= '</a>';



				$previousPath = $this->getPreviousPath();
				$previousScreenshot = $previousPath . DIRECTORY_SEPARATOR . $fileName;

				if ($previousPath && is_file($previousScreenshot)) {
					if (md5_file($directory . DIRECTORY_SEPARATOR . $fileName) != md5_file($previousScreenshot)) {

						$fileNamePrev = 'screenshot_' . $screenshot->getId() . '.prev.png';
						$thumbnailNamePrev = 'screenshot_' . $screenshot->getId() . '_thumb.prev.png';


						// actual image
						if (file_exists($directory . DIRECTORY_SEPARATOR . $fileNamePrev)) {
							unlink($directory . DIRECTORY_SEPARATOR . $fileNamePrev);
						}
						link($previousScreenshot, $directory . DIRECTORY_SEPARATOR . $fileNamePrev);

						// thumbnail
						if (file_exists($directory . DIRECTORY_SEPARATOR . $thumbnailNamePrev)) {
							unlink($directory . DIRECTORY_SEPARATOR . $thumbnailNamePrev);
						}
						link($previousPath . DIRECTORY_SEPARATOR . $thumbnailName, $directory . DIRECTORY_SEPARATOR . $thumbnailNamePrev);

						$result .= '<a class="previous" title="'.$screenshot->getTitle().'" href="'.$fileNamePrev.'">';
							$result .= '<img src="'.$thumbnailNamePrev.'" width="100" />';
						$result .= '</a>';

						$fileNameDiff = 'screenshot_' . $screenshot->getId() . '.diff.png';
						$thumbnailNameDiff = 'screenshot_' . $screenshot->getId() . '_thumb.diff.png';

						$this->createPdiff(
							$directory . DIRECTORY_SEPARATOR . $fileNamePrev,
							$directory . DIRECTORY_SEPARATOR . $fileName,
							$directory . DIRECTORY_SEPARATOR . $fileNameDiff
						);

						// create thumbnail
						$simpleImage = new Menta_Util_SimpleImage($directory . DIRECTORY_SEPARATOR . $fileNameDiff);
						$simpleImage->resizeToWidth(100)->save($directory . DIRECTORY_SEPARATOR . $thumbnailNameDiff, IMAGETYPE_PNG);

						$result .= '<a class="current" title="'.$screenshot->getTitle().'" href="'.$fileNameDiff.'">';
							$result .= '<img src="'.$thumbnailNameDiff.'" width="100" />';
						$result .= '</a>';

					}
				}

			} catch (Exception $e) {
				$result .= 'EXCEPTION: '.$e->getMessage();
			}
			$result .= '</li>';
		}
		$result .= '</ul>';
		return $result;
	}

	/**
	 * Create pdiff
	 *
	 * @param $imageA
	 * @param $imageB
	 * @param $target
	 * @throws Exception
	 */
	public function createPdiff($imageA, $imageB, $target) {
		$command = Menta_ConfigurationPhpUnitVars::getInstance()->getValue('report.pdiff_command');
		$command = sprintf($command,
			escapeshellarg($imageA),
			escapeshellarg($imageB),
			escapeshellarg($target)
		);
		// TODO: check return value
		exec($command);
	}

	/**
	 * Print tests
	 *
	 * @param array $tests
	 * @return string
	 */
	public function printTests(array $tests) {
		$result = '<div class="wrapper tests">';
		foreach ($tests as $key => $values) {
			if ($key == '__datasets') {
				$result .= '<div class="wrapper dataset">';
				foreach ($values as $dataSetName => $test) {
					$result .= $this->printTest($test, $dataSetName);
				}
				$result .= '</div><!-- dataset -->';
			} else {
				$result .= $this->printTest($values);
			}
		}
		$result .= '</div><!-- tests -->';
		return $result;
	}

	/**
	 * Print browsers
	 *
	 * @param array $browsers
	 * @return string
	 */
	public function printBrowsers(array $browsers) {
		$result = '<div class="wrapper browsers">';
		foreach ($browsers as $browserName => $values) {
			$result .= '<div class="browser">';
			$result .= '<h2>'.$browserName.'</h2>';
			$result .= $this->printResult($values);
			$result .= '</div><!-- browser -->';
		}
		$result .= '</div><!-- browsers -->';
		return $result;
	}

	/**
	 * Print suites
	 *
	 * @param array $suites
	 * @return string
	 */
	public function printSuites(array $suites) {
		$result = '<div class="wrapper suites">';
		foreach ($suites as $suiteName => $suite) {
			$result .= '<div class="suite">';
			$result .= '<h2>'.$suiteName.'</h2>';
			$result .= $this->printResult($suite);
			$result .= '</div><!-- suite -->';
		}
		$result .= '</div><!-- suites -->';
		return $result;
	}

	/**
	 * Print result
	 *
	 * @throws Exception
	 * @param array $array
	 * @return string
	 */
	public function printResult(array $array) {
		$result = '';
		foreach ($array as $key => $value) {
			if ($key == '__browsers') {
				$result .= $this->printBrowsers($value);
			} elseif ($key == '__suites') {
				$result .= $this->printSuites($value);
			} elseif ($key == '__tests') {
				$result .= $this->printTests($value);
			} else {
				throw new Exception("Unexpected key $key");
			}
		}
		return $result;
	}

	/**
	 * Shorten name by removing class name part
	 *
	 * @param string $name
	 * @return string
	 */
	public function shorten($name) {
		return preg_replace('/.*::/', '', $name);
	}

	/**
	 * Get speaking status name
	 *
	 * @param int $status
	 * @return string
	 */
	public function getStatusName($status) {
		$names = array(
			PHPUnit_Runner_BaseTestRunner::STATUS_PASSED => 'passed',
			PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED => 'skipped',
			PHPUnit_Runner_BaseTestRunner::STATUS_INCOMPLETE => 'incomplete',
			PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE => 'failed',
			PHPUnit_Runner_BaseTestRunner::STATUS_ERROR => 'error',
		);
		return $names[$status];
	}
		
}