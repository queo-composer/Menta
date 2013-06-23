<?php

namespace Menta;
/**
 * Interface for testcase that can take screenshots
 *
 * @author Fabrizio Branca
 * @since 2011-11-20
 */
interface ScreenshotInterface
{

    /**
     * Take a screenshot
     *
     * @abstract
     * @param string $title
     * @param string $description
     * @param string $type
     * @param array $trace
     * @return return Screenshot
     */
    function takeScreenshot($title = null, $description = null, $type = null, array $trace = null);

    /**
     * Get all screenshots that were taken so far
     *
     * @return array array of Screenshot
     */
    function getScreenshots();

}
