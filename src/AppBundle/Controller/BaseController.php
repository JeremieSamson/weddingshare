<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
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
        $mediasLong = $randomMedias = array();

        /** @var Media $media */
        foreach($medias as $media){
            $realPath = __DIR__ . '/../../../web/'. $media->getPath();

            if (file_exists($realPath)){
                list ($width, $height) = getimagesize(__DIR__ . '/../../../web/'. $media->getPath());

                if ($width > $height){
                    array_push($mediasLong, $media);
                }
            }
        }


        $randomIds = count($mediasLong) > 0 ? array_rand($mediasLong, count($mediasLong)) : array();
        $randomMedias = count($randomIds) > 0 ? $randomIds : array();

        return new ArrayCollection(count($randomMedias) ? $mediaRepo->findByIds($randomMedias) : $randomMedias);
    }
}