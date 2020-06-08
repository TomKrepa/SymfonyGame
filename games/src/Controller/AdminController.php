<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Jeux;
use App\Entity\Commentaires;
use App\Form\JeuType;
use App\Form\UserType;
use App\Form\RegistrationType;
use App\Repository\CommentairesRepository;
use App\Repository\JeuLikeRepository;
use App\Repository\JeuxRepository;
use App\Repository\UsersRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    // GESTION DES JEUX 
    /**
     * @Route("/admin/jeux", name="admin_games_index")
     */
    public function index_jeu(JeuxRepository $repo)
    {
        $repo = $this->getDoctrine()->getRepository(Jeux::class);
        $jeux = $repo->findAll();

        return $this->render('admin/index_jeux.html.twig', [
            'controller_name' => 'AdminController',
            'jeux' => $jeux
        ]);
    }

    // AJOUTER UN JEU 
    /**
     * @Route("/admin/jeux/nouveau", name="admin_game_create")
     */
    public function new_jeu(Request $request)
    {
        $jeu = new Jeux();
        $form = $this->createForm(JeuType::class, $jeu);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Nouvelle date de création 
            $jeu->setCreatedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jeu);
            $entityManager->flush();

            $this->addFlash('success', 'Le jeu ' . $jeu->getTitre() . ' a bien été ajouté');

            return $this->redirectToRoute('admin_games_index');
        }

        return $this->render('admin/new_jeux.html.twig', [
            'jeu' => $jeu,
            'form' => $form->createView()
        ]);       
    }

    // EDITER UN JEU 
    /**
     * @Route("/admin/jeux/edit/{id}", name="admin_game_edit", methods="POST|GET")
     */
    public function edit_jeu(Jeux $jeu, Request $request)
    {
        $form = $this->createForm(JeuType::class, $jeu);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jeu);
            $entityManager->flush();

            $this->addFlash('success', 'Le jeu ' . $jeu->getTitre() . ' a bien été modifié');

            return $this->redirectToRoute('admin_games_index');
        }

        return $this->render('admin/edit_jeux.html.twig', [
            'jeu' => $jeu,
            'form' => $form->createView()
        ]);
    }

    // SUPPRIMER UN JEU 
    /**
     * @Route("/admin/jeux/edit/{id}", name="admin_game_delete", methods="DELETE")
     */
    public function delete_jeu(Jeux $jeu, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $jeu->getId(), $request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jeu);
            $entityManager->flush();

            $this->addFlash('success', 'Le jeu ' . $jeu->getTitre() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('admin_games_index');

    }

    // GESTION DES USERS 
    /**
     * @Route("/admin/users", name="admin_users_index")
     */
    public function index_user(UsersRepository $repo)
    {
        $repo = $this->getDoctrine()->getRepository(Users::class);
        $users = $repo->findAll();

        return $this->render('admin/index_users.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users
        ]);
    }

    // AJOUTER UN USER 
    /**
     * @Route("/admin/users/nouveau", name="admin_user_create")
     */
    public function new_user(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new Users();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            /* Hashage du mot de passe */
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouvel utilisateur ' . $user->getEmail() . ' a bien été ajouté');

            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/new_users.html.twig', [
            'users' => $user,
            'form' => $form->createView()
        ]);       
    }

    // EDITER UN USER 
    /**
     * @Route("/admin/users/edit/{id}", name="admin_user_edit", methods="POST|GET")
     */
    public function edit_user(Users $user, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        
            $this->addFlash('success', 'Le user ' . $user->getEmail() . ' a bien été modifié');

            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/edit_users.html.twig', [
            'users' => $user,
            'form' => $form->createView()
        ]);
    }

    // SUPPRIMER UN USER 
    /**
     * @Route("/admin/users/edit/{id}", name="admin_user_delete", methods="DELETE")
     */
    public function delete_user(Users $user, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le user ' . $user->getPseudo() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('admin_users_index');

    }

    // DASHBOARD 
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */
    public function dashboard(CommentairesRepository $CommentRepo, JeuLikeRepository $LikeRepo)
    {
        $likes = $LikeRepo->findAll();
        $commentaires = $CommentRepo->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'commentaires' => $commentaires,
            'likes' => $likes
        ]);
    }

    // SUPPRIMER UN COMMENTAIRE 
    /**
     * @Route("/admin/dashboard/delete/{id}", name="admin_comment_delete", methods="DELETE")
     */
    public function delete_comment(Commentaires $commentaires, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $commentaires->getId(), $request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commentaires);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire de ' . $commentaires->getEmail() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('admin_dashboard');

    }
}