<?php

namespace App\Repository;

use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teams>
 */
class TeamsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teams::class);
    }

    public function getTeamByExcludedPlayers(string $not_in, int $idtournament, int $played = 0)
    {
        return $this->createQueryBuilder('r')
        ->where('(r.player1 NOT IN ('.$not_in.') AND r.player2 NOT IN ('.$not_in.')) AND r.idtournament = :idtournament AND r.played = :played')
        ->setParameter('idtournament', $idtournament)
        ->setParameter('played', $played)
        ->orderBy('r.weight DESC')
        ->addOrderBy('r.id ASC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function getTeamsByPlayer(int $player)
    {
        return $this->createQueryBuilder('r')
        ->where('r.player1 = :player OR r.player2 = :player AND r.played = 0')
        ->setParameter('player', $player)
        ->getQuery()
        ->getResult();
    }

    //    /**
    //     * @return Teams[] Returns an array of Teams objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Teams
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
