<?php

namespace App\Controller;

use App\Entity\Jeux;
use App\Entity\JeuLike;
use App\Entity\Commentaires;
use App\Form\CommentType;
use App\Repository\JeuxRepository;
use App\Repository\CommentairesRepository;
use App\Repository\JeuLikeRepository;
use App\Service\Panier\PanierService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SiteController extends AbstractController
{
    // CATALOGUE DES JEUX 
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

    // LIKER OU UNLIKER UN JEU 
    /**
     * @Route("/games/{id}/like", name="games_like")
     */
    public function like(Jeux $jeu, JeuLikeRepository $likerepo) :
    Response {

        // récupère l'utilisateur
        $user = $this->getUser();

        // si l'utilisateur n'est pas connecté
        if(!$user) return $this->json([
            'message' => "Unauthorized"
        ], 403);

        // si l'utilisateur est connecté et qu'il a déja liker ce jeu, cherche le like et le suprrime
        if($jeu->isLikedByUser($user)) {
            $like = $likerepo->findOneBy([
                'jeu' => $jeu,
                'user' => $user
            ]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($like);
            $entityManager->flush();

            return $this->json([
                'message' => 'Like supprimé',
                'likes' => $likerepo->count(['jeu' => $jeu])
            ], 200);
        }

        // si l'utilisateur est connecté et qu'il n'aime pas ce post, ajoute un like
        $like = new JeuLike();
        $like->setJeu($jeu)
            ->setUser($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json([
            'message' => 'Like ajouté',
            'likes' => $likerepo->count(['jeu' => $jeu])
        ], 200);
    }

    // PAGE D'ACCEUIL
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('site/home.html.twig');
    }

    // LA PAGE D'UN JEU 
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

    // LE PANIER 
    /**
     * @Route("/panier", name="panier")
     */
    public function panier(PanierService $panierService)
    {
        // Utilise les fonctions du PanierService
        return $this->render('site/panier.html.twig', [
            'items' => $panierService->getFullPanier(),
            'total' => $panierService->getTotal()
        ]);
    }

    // AJOUTER AU PANIER 
    /**
     * @Route("/panier/ajouter/{id}", name="panier_add")
     */
    public function add($id, PanierService $panierService, Jeux $jeu)
    {
        // Utilise la fonction du PanierService
        $panierService->add($id);

        $this->addFlash('success', 'Le jeu ' . $jeu->getTitre() . ' a bien été ajouté au panier');

        return $this->redirectToRoute('games');
    }

    // SUPPRIMER DU PANIER 
    /**
     * @Route("/panier/supprimer/{id}", name="panier_remove")
     */
    public function remove($id, PanierService $panierService, Jeux $jeu)
    {
        // Utilise la fonction du PanierService
        $panierService->remove($id);

        return $this->redirectToRoute('panier');
    }
}
