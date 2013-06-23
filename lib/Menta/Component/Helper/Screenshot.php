<?php

namespace Menta\Component\Helper;

use Menta\Component\AbstractComponent;

/**
 * Screenhot helper
 *
 * @author Fabrizio Branca
 * @since 2011-11-18
 */
class Screenshot extends AbstractComponent
{

    /**
     * take screenshot
     *
     * @return string
     */
    public function takeScreenshotToString()
    {
        $base64Image = $this->getSession()->screenshot();
        return $base64Image;
    }

}

