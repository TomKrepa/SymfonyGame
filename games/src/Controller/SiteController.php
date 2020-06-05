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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function home()
    {
        return $this->render('site/home.html.twig');
    }

    /**
     * @Route("/games/{id}", name="game_show")
     */
    public function show(Jeux $jeu, Request $request, CommentairesRepository $repo, UserInterface $user)
    {

        $comment = new Commentaires();
        $form = $this->createForm(CommentType::class, $comment);

        /* Les commentaires du jeu */
        $commentaires = $repo->findBy(array('jeux' => $jeu->getId()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCreatedAt(new \DateTime())
                ->setJeux($jeu)
                ->setEmail($user->getUsername())
                ->setPseudo($user->getPseudo());

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

    /**
     * @Route("/panier", name="panier")
     */
    public function panier(SessionInterface $session, JeuxRepository $repo)
    {

        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'jeux' => $repo->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach ($panierWithData as $item) {
            $totalItem = $item['jeux']->getPrix() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->render('site/panier.html.twig', [
            'items' => $panierWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/panier/ajouter/{id}", name="panier_add")
     */
    public function ajouter($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('games');
    }

    /**
     * @Route("/panier/supprimer/{id}", name="panier_remove")
     */
    public function remove($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }
}
