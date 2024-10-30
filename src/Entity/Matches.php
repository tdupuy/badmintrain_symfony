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

    #[ORM\Column(options: ['default' => 0])]
    private ?int $turn = null;

    #[ORM\Column(nullable: true)]
    private ?int $winnerteamid = null;

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

    public function getTurn(): ?int
    {
        return $this->turn;
    }

    public function setTurn(int $turn): static
    {
        $this->turn = $turn;

        return $this;
    }

    public function getWinnerteamid(): ?int
    {
        return $this->winnerteamid;
    }

    public function setWinnerteamid(?int $winnerteamid): static
    {
        $this->winnerteamid = $winnerteamid;

        return $this;
    }
}
