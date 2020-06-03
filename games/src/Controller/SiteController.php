<?php

namespace App\Controller;

use App\Entity\Commentaires;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Jeux;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\JeuxRepository;
use App\Repository\CommentairesRepository;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends AbstractController
{
    /**
     * @Route("/games", name="games")
     */
    public function index(JeuxRepository $repo)
    {
        $repo = $this->getDoctrine()->getRepository(Jeux::class);

        $jeux = $repo->findAll();

        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'jeux' => $jeux
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('site/home.html.twig');
    }

    /**
     * @Route("/games/{id}", name="game_show")
     */
    public function show(Jeux $jeu, Request $request, CommentairesRepository $repo, UserInterface $user) {

        $comment = new Commentaires();
        $form = $this->createForm(CommentType::class, $comment);

        /* Les commentaires du jeu */
        $commentaires = $repo->findBy(array('jeux' => $jeu->getId()));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setCreatedAt(new \DateTime())
                    ->setJeux($jeu)
                    ->setEmail($user->getUsername())
                    /*Le pseudo est par défaut l'email, à modifié !*/
                    ->setPseudo($user->getUsername());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('game_show', ['id' => $jeu->getId()]);
        }

        return $this->render('site/show.html.twig', [
            'jeu' => $jeu,
            'commentaires' => $commentaires,
            'commentForm' => $form->createView()
        ]);
    }
}
