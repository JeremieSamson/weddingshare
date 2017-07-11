<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 */
class DefaultController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
    /**
     * @Route("/", name="index")
     * @Method({"GET"})
     */
    public function indexAction(){
        $lorempixelWrapper = $this->get('lorempixel.wrapper');

        return $this->render('AppBundle::index.html.twig', array(
            "pictures" => $lorempixelWrapper->generateRandomPicturesUrl(7),
            "categories" => $this->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:Category')->findAll(),
        ));
    }
}