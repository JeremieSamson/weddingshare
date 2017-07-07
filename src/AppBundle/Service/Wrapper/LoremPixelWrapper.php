<?php

namespace AppBundle\Service\Wrapper;

use AppBundle\Service\Helper\LoremPixelHelper;

/**
 * Class LoremPixelWrapper
 * @package AppBundle\Service\Wrapper
 */
class LoremPixelWrapper
{
    /** @var LoremPixelHelper $helper */
    private $helper;

    /**
     * @param LoremPixelHelper $helper
     */
    public function __construct(LoremPixelHelper $helper){
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function generateRandomPicturesUrl($number = 3){
        $picturesUrl = array();

        for($i=0 ; $i<$number ; $i++){
            array_push($picturesUrl, $this->helper->generateRandomPictureUrl());
        }

        return $picturesUrl;
    }
}