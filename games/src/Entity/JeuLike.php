<?php

namespace App\Entity;

use App\Repository\JeuLikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JeuLikeRepository::class)
 */
class JeuLike
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Jeux::class, inversedBy="likes")
     */
    private $jeu;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="likes")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJeu(): ?Jeux
    {
        return $this->jeu;
    }

    public function setJeu(?Jeux $jeu): self
    {
        $this->jeu = $jeu;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }
}
