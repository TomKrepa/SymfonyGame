<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\JeuxRepository;
use App\Entity\Jeux;
use App\Form\JeuType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_games_index")
     */
    public function index(JeuxRepository $repo)
    {
        $repo = $this->getDoctrine()->getRepository(Jeux::class);

        $jeux = $repo->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'jeux' => $jeux
        ]);
    }

    /**
     * @Route("/admin/new", name="admin_game_create")
     */
    public function new(Request $request)
    {
        $jeu = new Jeux();

        $form = $this->createForm(JeuType::class, $jeu);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $jeu->setCreatedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jeu);
            $entityManager->flush();
            $this->addFlash('success', 'Le jeu ' . $jeu->getTitre() . ' a bien été ajouté');

            return $this->redirectToRoute('admin_games_index');
        }

        return $this->render('admin/new.html.twig', [
            'jeu' => $jeu,
            'form' => $form->createView()
        ]);       
    }

    /**
     * @Route("/admin/edit/{id}", name="admin_game_edit", methods="POST|GET")
     */
    public function edit(Jeux $jeu, Request $request)
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

        return $this->render('admin/edit.html.twig', [
            'jeu' => $jeu,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/edit/{id}", name="admin_game_delete", methods="DELETE")
     */
    public function delete(Jeux $jeu, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $jeu->getId(), $request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jeu);
            $entityManager->flush();
            $this->addFlash('success', 'Le jeu ' . $jeu->getTitre() . ' a bien été supprimé');
        }

        return $this->redirectToRoute('admin_games_index');

    }
}