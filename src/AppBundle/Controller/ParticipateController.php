<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use AppBundle\Entity\Vote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Vote controller.
 */
class VoteController extends BaseController
{

    /**
     * Creates a new vote entity.
     *
     * @Route("/category/{id}/vote", name="vote_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $category = $vote = null;

        try{
            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
        }catch(\Exception $e){
            $this->addFlash('error', 'La page demandée n\'existe pas');
        }

        $voteHereAlready = $this->getDoctrine()->getRepository('AppBundle:Vote')->findOneBy(array(
            "category" => $category,
            "ip" => $ip
        ));

        if ($voteHereAlready) {
            $this->addFlash('error', 'Vous avez déjà voté pour cette catégorie !');

            return $this->render('AppBundle:vote:voteAlready.html.twig', array(
                "pictures" => $this->getRandomMedias(),
                "category" => $category,
            ));
        } else {
            $vote = new Vote();

            $form = $this->createForm('AppBundle\Form\Type\VoteType', $vote);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                // Set other attributes
                $vote->setIp($ip);
                $category->addVote($vote);

                // Move file
                /** @var UploadedFile $file */
                $file = $vote->getFile();
                $media = new Media();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $media->setExtension($file->guessExtension());
                $media->setName($fileName);
                $media->setUrl($this->getParameter('kernel.project_dir') . '/web/uploads/' . $category->getPath());

                // Move the file to the directory where brochures are stored
                $file->move(
                    $media->getUrl(),
                    $fileName
                );

                $em->persist($vote);
                $em->persist($media);
                $em->flush();

                $this->addFlash('success', 'Votre vote a bien été enregistré !');

                return $this->render('AppBundle:vote:voteAlready.html.twig', array(
                    "pictures" => $this->getRandomMedias(),
                    "category" => $category,
                ));
            }
        }

        return $this->render('AppBundle:vote:form.html.twig', array(
            "pictures" => $this->getRandomMedias(),
            "category" => $category,
            'vote' => $vote,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a vote entity.
     *
     * @Route("/{id}", name="vote_show")
     * @Method("GET")
     */
    public function showAction(Vote $vote)
    {
        $deleteForm = $this->createDeleteForm($vote);

        return $this->render('vote/show.html.twig', array(
            'vote' => $vote,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing vote entity.
     *
     * @Route("/{id}/edit", name="vote_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Vote $vote)
    {
        $deleteForm = $this->createDeleteForm($vote);
        $editForm = $this->createForm('AppBundle\Form\VoteType', $vote);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vote_edit', array('id' => $vote->getId()));
        }

        return $this->render('vote/edit.html.twig', array(
            'vote' => $vote,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a vote entity.
     *
     * @Route("/{id}", name="vote_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Vote $vote)
    {
        $form = $this->createDeleteForm($vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vote);
            $em->flush();
        }

        return $this->redirectToRoute('vote_index');
    }

    /**
     * Creates a form to delete a vote entity.
     *
     * @param Vote $vote The vote entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Vote $vote)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vote_delete', array('id' => $vote->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
