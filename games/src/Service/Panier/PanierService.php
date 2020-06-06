<?php

namespace App\Service\Panier;

use App\Repository\JeuxRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService {

    protected $session;
    protected $jeuxRepository;

    public function __construct(SessionInterface $session, JeuxRepository $jeuxRepository) {

        $this->session = $session;
        $this->jeuxRepository = $jeuxRepository;
    }

    public function add(int $id) {

        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
    }

    public function remove(int $id) {

        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function getFullPanier() : array {

        /* Panier de la session */
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'jeux' => $this->jeuxRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $panierWithData;
    }

    public function getTotal() : float {

        $total = 0;

        foreach ($this->getFullPanier() as $item) {
            $total += $item['jeux']->getPrix() * $item['quantity'];
        }

        return $total;
    }
}