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
class VoteController extends Controller
{

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

        if ($voteHereAlready) {
            $this->addFlash('danger', 'Vous avez déjà voté pour cette catégorie !');

            return $this->render('AppBundle:vote:voteAlready.html.twig', array(
                "pictures" => $lorempixelWrapper->generateRandomPicturesUrl(7),
                "category" => $category,
                'vote' => $voteHereAlready
            ));
        } else {
            $vote = new Vote();

            if ($request->get('action') == 'vote') {
                $em = $this->getDoctrine()->getManager();

                // Set other attributes
                $vote->setIp($ip);
                $category->addVote($vote);

                $em->persist($vote);

                $em->flush();

                $this->addFlash('success', 'Votre vote a bien été enregistré !');

                return $this->render('AppBundle:vote:voteAlready.html.twig', array(
                    "pictures" => $lorempixelWrapper->generateRandomPicturesUrl(7),
                    "category" => $category,
                ));
            }
        }

        return $this->render('AppBundle:vote:form.html.twig', array(
            "pictures" => $lorempixelWrapper->generateRandomPicturesUrl(7),
            "category" => $category,
            'vote' => $vote
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
