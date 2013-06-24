<?php

namespace Menta\PHPUnit\Listener;

use Menta\ScreenshotInterface;

/**
 * HTML result printer
 *
 * @author Fabrizio Branca
 * @since 2011-11-13
 */
class HtmlResultPrinter extends AbstractTemplatablePrinter implements \PHPUnit_Framework_TestListener
{

    /**
     * @var string
     */
    protected $templateFile = '###MENTA_ROOTDIR###/PHPUnit/Listener/Resources/Templates/HtmlResultTemplate.php';

    /**
     * @var array
     */
    protected $additionalFiles = array();

    protected $lastResult;

    protected $lastStatus;

    protected $level = 0;

    protected $suiteStack = array();

    protected $results = array();

    protected $count = array();

    protected $viewClass = 'HtmlResultView';

    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }

    public function addError(\PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->lastResult = $e;
        $this->lastStatus = \PHPUnit_Runner_BaseTestRunner::STATUS_ERROR;
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->lastResult = $e;
        $this->lastStatus = \PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE;
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->lastResult = $e;
        $this->lastStatus = \PHPUnit_Runner_BaseTestRunner::STATUS_INCOMPLETE;
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->lastResult = $e;
        $this->lastStatus = \PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED;
    }

    public function getDocComment(\PHPUnit_Framework_Test $test)
    {
        $class = new \ReflectionClass($test);
        $method = $class->getMethod($test->getName(false));
        $docComment = $method->getDocComment();
        $docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
        $docComment - "\n" . $docComment;
        $endOfDescription = strpos($docComment, "\n@");
        if ($endOfDescription !== false) {
            $docComment = substr($docComment, 0, $endOfDescription);
        }
        $docComment = trim($docComment);
        return $docComment;
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {

        $testName = \PHPUnit_Util_Test::describe($test);

        // store in result array
        $currentArray =& $this->results;
        foreach ($this->suiteStack as $suiteName) {
            $colonPos = strpos($suiteName, ': ');
            if ($colonPos !== false) {
                $browser = substr($suiteName, $colonPos + 2);
                $suiteName = substr($suiteName, 0, $colonPos);
                // $currentArray =& $currentArray['__suites'][$suiteName]['__browsers'][$browser];
                $currentArray =& $currentArray['__browsers'][$browser];
            } else {
                $currentArray =& $currentArray['__suites'][$suiteName];
            }
        }

        if (is_null($this->lastStatus)) {
            $this->lastStatus = \PHPUnit_Runner_BaseTestRunner::STATUS_PASSED;
        }

        $result = array(
            'testName' => $testName,
            'time' => $time,
            'exception' => $this->lastResult,
            'status' => $this->lastStatus,
            'description' => $this->getDocComment($test),
        );

        if ($test instanceof ScreenshotInterface) {
            /** @var $test ScreenshotInterface */
            $screenshots = $test->getScreenshots();
            if (is_array($screenshots) && count($screenshots) > 0) {
                $result['screenshots'] = $screenshots;
            }
        }

        if (method_exists($test, 'getInfo')) {
            $result['info'] = $test->getInfo();
        }

        if (isset($this->count[$this->lastStatus])) {
            $this->count[$this->lastStatus]++;
        } else {
            $this->count[$this->lastStatus] = 1;
        }

        $dataSetPos = strpos($testName, ' with data set ');
        if ($dataSetPos !== false) {
            $dataSet = substr($testName, $dataSetPos + 5);
            $dataSet = ucfirst(trim($dataSet));
            $testName = substr($testName, 0, $dataSetPos);
            $currentArray['__tests']['__datasets'][$dataSet] = $result;
        } else {
            $currentArray['__tests'][$testName] = $result;
        }

        $this->lastResult = null;
        $this->lastStatus = null;
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->level++;
        $name = PHPUnit_Util_Test::describe($suite);
        if (empty($name)) {
            //$name = get_class($suite);
            $name = '-';
        }
        $this->suiteStack[] = $name;
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->level--;
        array_pop($this->suiteStack);
    }

    /**
     * Flush: Copy images and additional files to folder and generate index file using a template
     *
     * This method is called once after all tests have been processed.
     * HINT: The flush method is only called if the TestListener inherits from PHPUnit_Util_Printer
     *
     * @param array $templateVars
     * @return void
     * @author Fabrizio Branca
     */
    public function flush(array $templateVars = array())
    {
        ksort($this->count);
        $sum = array_sum($this->count);
        $templateVars['percentages'] = array();
        foreach ($this->count as $key => $value) {
            $templateVars['percentages'][$key] = 100 * $value / $sum;
        }
        $templateVars['basedir'] = dirname($this->targetFile);
        $templateVars['results'] = $this->results;
        $templateVars['count'] = $this->count;

        return parent::flush($templateVars);
    }
}
