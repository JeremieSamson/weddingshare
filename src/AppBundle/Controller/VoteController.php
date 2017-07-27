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
     * @Route("/category/{id}/vote/media/{mediaId}", name="vote_new_media")
     */
    public function newMediaVoteAction(Request $request, $id, $mediaId)
    {
        $category = $media = null;

        try{
            $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);
        }catch(\Exception $e){
            $this->addFlash('danger', 'La page demandée n\'existe pas');
        }

        try{
            $media = $this->getDoctrine()->getRepository('AppBundle:Media')->find($mediaId);
        }catch(\Exception $e){
            $this->addFlash('danger', 'La photo demandée n\'existe pas');
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $voteHereAlready = $this->getDoctrine()->getRepository('AppBundle:Vote')->findOneBy(array(
            "category" => $category,
            "ip" => $ip
        ));

        if ($voteHereAlready) {
            $this->addFlash('danger', 'Vous avez déjà voté pour cette catégorie !');

            return $this->render('AppBundle:vote:voteAlready.html.twig', array(
                "pictures" => $this->getRandomMedias(),
                "category" => $category,
                'vote' => $voteHereAlready
            ));
        }

        if ($category && $media) {
            $em = $this->getDoctrine()->getManager();

            $vote = new Vote();

            $vote->setIp($ip);
            $category->addVote($vote);

            $em->persist($vote);

            $em->flush();

            $this->addFlash('success', 'Votre vote a bien été enregistré !');
        }

        return $this->render('AppBundle:vote:voteAlready.html.twig', array(
            "pictures" => $this->getRandomMedias(),
            "category" => $category,
        ));
    }

    /**
     * Deletes a vote entity.
     *
     * @Route("/category/{id}/vote/{voteId}/delete", name="vote_delete")
     */
    public function deleteAction(Request $request, Vote $vote)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $vote->getCategory();

        $em->remove($vote);
        $em->flush();

        return $this->redirectToRoute('vote_new', array(
            "id" => $category->getId()
        ));
    }

    /**
     * Creates a new vote entity.
     *
     * @Route("/category/{id}/vote", name="vote_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $id)
    {
        $lorempixelWrapper = $this->get('lorempixel.wrapper');

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
            $this->addFlash('danger', 'La page demandée n\'existe pas');
        }

        $voteHereAlready = $this->getDoctrine()->getRepository('AppBundle:Vote')->findOneBy(array(
            "category" => $category,
            "ip" => $ip
        ));
        $voteHereAlready = $voteHereAlready ? true : false;

        /*if ($voteHereAlready) {
            $this->addFlash('danger', 'Vous avez déjà voté pour cette catégorie !');

            return $this->render('AppBundle:vote:voteAlready.html.twig', array(
                "pictures" => $this->getRandomMedias(),
                "category" => $category,
                'vote' => $voteHereAlready
            ));
        }*/

        return $this->render('AppBundle:vote:form.html.twig', array(
            "pictures" => $this->getRandomMedias(),
            "medias" => $lorempixelWrapper->generateRandomPicturesUrl(7),
            "category" => $category,
            'vote' => $vote,
            'voteHereAlready' => $voteHereAlready
        ));
    }
}
