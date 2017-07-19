<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    public function getManager(){
        return $this->get('doctrine')->getManager();
    }

    /**
     * @return \AppBundle\Repository\MediaRepository
     */
    public function getMediaRepository(){
        return $this->getManager()->getRepository('AppBundle:Media');
    }
    /**
     * @return ArrayCollection
     */
    public function getRandomMedias(){
        $mediaRepo = $this->getMediaRepository();

        $medias = $mediaRepo->findAll();
        $randomIds = array_rand($medias, round(count($medias) / 2));
        $randomMedias = count($randomIds) > 0 ? $randomIds : array();

        return new ArrayCollection($mediaRepo->findByIds($randomMedias));
    }
}