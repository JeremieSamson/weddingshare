<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use AppBundle\Form\Model\Participate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 */
class DefaultController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request){
        $lorempixelWrapper = $this->get('lorempixel.wrapper');

        $participate = new Participate();
        $form = $this->createForm('AppBundle\Form\Type\ParticipateType', $participate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Move file
            /** @var UploadedFile $file */
            $file = $participate->getFile();
            $media = new Media();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $media->setExtension($file->guessExtension());
            $media->setName($fileName);
            $media->setUrl($this->getParameter('kernel.project_dir') . '/web/uploads/' . $participate->getCategory()->getPath());
            $media->setAuthor($participate->getName());
            $participate->getCategory()->addMedia($media);

            // Move the file to the directory where brochures are stored
            $file->move(
                $media->getUrl(),
                $fileName
            );

            $em->persist($media);
            $em->flush();

            $this->addFlash('success', 'Votre participation a bien été enregistrée !');
        }

        return $this->render('AppBundle::index.html.twig', array(
            "pictures" => $lorempixelWrapper->generateRandomPicturesUrl(7),
            "categories" => $this->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:Category')->findAll(),
            'form' => $form->createView(),
        ));
    }
}