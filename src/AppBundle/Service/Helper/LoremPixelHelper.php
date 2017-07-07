<?php

namespace AppBundle\Service\Helper;

class LoremPixelHelper
{
    const LOREM_PIXEL_URL = "http://lorempixel.com";

    private $categories = array("sports","abstract","animals","business", "cats","city","fashion","nature");

    /**
     * @return string
     */
    public function generateRandomPictureUrl(){
        return self::LOREM_PIXEL_URL . '/' . $this->getRandomCategory() . '/' . 1900 . '/' . 1080;
    }

    /**
     * @return int
     */
    private function getRandomWidth(){
        return rand(400,800);
    }

    /**
     * @return int
     */
    private function getRandomHeight(){
        return rand(100,200);
    }

    /**
     * @return string
     */
    private function getRandomCategory(){
        return $this->categories[rand(0, count($this->categories)-1)];
    }
}