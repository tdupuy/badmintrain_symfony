<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column]
    private ?int $idtournament = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private ?int $played = null;

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

    public function getIdtournament(): ?int
    {
        return $this->idtournament;
    }

    public function setIdtournament(int $idtournament): static
    {
        $this->idtournament = $idtournament;

        return $this;
    }

    public function getPlayed(): ?int
    {
        return $this->played;
    }

    public function setPlayed(int $played): static
    {
        $this->played = $played;

        return $this;
    }
}
