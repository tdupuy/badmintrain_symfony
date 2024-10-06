<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idteam1 = null;

    #[ORM\Column]
    private ?int $idteam2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdteam1(): ?int
    {
        return $this->idteam1;
    }

    public function setIdteam1(int $idteam1): static
    {
        $this->idteam1 = $idteam1;

        return $this;
    }

    public function getIdteam2(): ?int
    {
        return $this->idteam2;
    }

    public function setIdteam2(int $idteam2): static
    {
        $this->idteam2 = $idteam2;

        return $this;
    }
}
