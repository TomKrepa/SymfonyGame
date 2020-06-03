<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Jeux;
use PhpParser\Node\Expr\New_;

class JeuxFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++) {
            $jeu = new Jeux();
            $jeu->setTitre("Nom du jeu n°$i")
                ->setDetails("<p>Details sur le jeu n°$i</p>")
                ->setImage("http://placehold.it/350x150")
                ->setCreatedAt(new \DateTime())
                ->setPrix(50);

            $manager->persist($jeu);
        }

        $manager->flush();
    }
}
