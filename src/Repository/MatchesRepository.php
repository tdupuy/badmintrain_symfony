<?php

namespace App\Repository;

use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matches>
 */
class MatchesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matches::class);
    }

    public function getPlayersForTurn(int $turn, int $idtournament)
    {
        return $this->createQueryBuilder('m')
        ->select('t.player1, t.player2')
        ->innerJoin('App\Entity\Teams', 't', 'WITH', 'm.idteam1 = t.id OR m.idteam2 = t.id')
        ->where('m.idtournament = :tournamentId AND m.turn = :turn')
        ->setParameter('turn', $turn)
        ->setParameter('tournamentId', $idtournament)
        ->getQuery()
        ->getResult();
    }

    public function getMatchesForPlayersByTournament(int $idtournament, int $player)
    {
        return $this->createQueryBuilder('r')
        ->select('count(r.id) as nbmatches')
        ->innerJoin('App\Entity\Teams', 't', 'WITH', 'r.idteam1 = t.id OR r.idteam2 = t.id')
        ->where('r.idtournament = :tournamentid')
        ->andWhere('t.player1 = :playerid OR t.player2 = :playerid')
        ->setParameter('tournamentid', $idtournament)
        ->setParameter('playerid', $player)
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function getWinsForPlayersByTournament(int $idtournament, int $player)
    {
        return $this->createQueryBuilder('r')
        ->select('count(r.id) as nbwins')
        ->innerJoin('App\Entity\Teams', 't', 'WITH', 'r.winnerteamid = t.id')
        ->where('r.idtournament = :tournamentid')
        ->andWhere('t.player1 = :playerid OR t.player2 = :playerid')
        ->setParameter('tournamentid', $idtournament)
        ->setParameter('playerid', $player)
        ->getQuery()
        ->getSingleScalarResult();
    }

    //    /**
    //     * @return Matches[] Returns an array of Matches objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Matches
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
