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
