<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use AppBundle\Form\Model\Participate;
use AppBundle\Form\Model\Pictures;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 */
class DefaultController extends BaseController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction(Request $request){
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

        $pictures = new Pictures();
        $picturesForm = $this->createForm('AppBundle\Form\Type\PicturesType', $pictures);
        $picturesForm->handleRequest($request);

        if ($picturesForm->isSubmitted() && $picturesForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Move file
            /** @var UploadedFile $file */
            $files = $pictures->getFiles();

            foreach($files as $file) {
                $media = new Media();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $media->setExtension($file->guessExtension());
                $media->setName($fileName);
                $media->setUrl($this->getParameter('kernel.project_dir') . '/web/uploads/all');
                $media->setAuthor("anonyme");

                // Move the file to the directory where brochures are stored
                $file->move(
                    $media->getUrl(),
                    $fileName
                );

                $em->persist($media);
            }

            $em->flush();

            $this->addFlash('success', 'Merci beaucoup pour vos photos !');
        }

        return $this->render('AppBundle::index.html.twig', array(
            "pictures" => $this->getRandomMedias(),
            "categories" => $this->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:Category')->findAll(),
            'form' => $form->createView(),
            'picturesForm' => $picturesForm->createView(),
        ));
    }
}