<?php

namespace App\Repository;

use App\Entity\Matches;
use App\Entity\Tournament;
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

    public function findMatchForTwoTeams(int $team1, int $team2, int $idtournament){
        return $this->createQueryBuilder('r')
        ->where('r.idtournament = :idtournament AND (r.idteam1 = :team1 OR r.idteam2 = :team1) AND (r.idteam2 = :team2 OR r.idteam2 = :team2)')
        ->setParameter('team1', $team1)
        ->setParameter('team2', $team2)
        ->setParameter('idtournament', $idtournament)
        ->setMaxResults(1)
        ->getQuery()
        ->getSingleResult();
    }

    public function findAvailableMatchForTurn($idtournament, $not_in = ''){
        return $this->createQueryBuilder('r')
        ->where('r.idteam1 NOT IN ('.$not_in.') AND r.idteam2 NOT IN ('.$not_in.') AND r.idtournament = :idtournament AND r.played = 0')
        ->setParameter('idtournament', $idtournament)
        ->setMaxResults(1)
        ->getQuery()
        ->getSingleResult();
    }

    public function findMatchesForTurn(int $turn, int $nbmatches, int $tournamentid): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.idtournament = :tournamentid')
            ->setFirstResult($turn * $nbmatches)
            ->setMaxResults($nbmatches)
            ->setParameter('tournamentid', $tournamentid)
            ->getQuery()
            ->getResult();
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
