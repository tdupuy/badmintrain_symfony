<?php

namespace App\Entity;

use App\Repository\TeamsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsRepository::class)]
class Teams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $player1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $player2 = null;

    #[ORM\Column]
    private ?int $idtournament = null;

    #[ORM\Column]
    private ?int $played = null;

    // We don't need to save that in db
    private ?int $replayed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer1(): ?int
    {
        return $this->player1;
    }

    public function setPlayer1(int $player1): static
    {
        $this->player1 = $player1;

        return $this;
    }

    public function getPlayer2(): ?int
    {
        return $this->player2;
    }

    public function setPlayer2(?int $player2): static
    {
        $this->player2 = $player2;

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

    public function getReplayed(): ?int
    {
        return $this->replayed;
    }

    public function setReplayed(int $replayed): static
    {
        $this->replayed = $replayed;

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
