<?php

namespace App\Repository;

use App\Entity\MatchFootball;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MatchFootball>
 */
class MatchFootballRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchFootball::class);
    }

    public function findWithFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('m');

        if (!empty($filters['equipe'])) {
            $qb->andWhere('m.equipe1 = :equipe OR m.equipe2 = :equipe')
                ->setParameter('equipe', $filters['equipe']);
        }

        if (!empty($filters['stade'])) {
            $qb->andWhere('m.stade = :stade')
                ->setParameter('stade', $filters['stade']);
        }

        // Condition modifiée : ne s'applique QUE si une date est fournie
        if (!empty($filters['date'])) {
            $date = new \DateTimeImmutable($filters['date']);
            $start = $date->setTime(0, 0, 0);
            $end = $date->setTime(23, 59, 59);

            $qb->andWhere('m.dateEtHeure BETWEEN :start AND :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
        }


        return $qb->orderBy('m.dateEtHeure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(string $range): array
    {
        $qb = $this->createQueryBuilder('m');
        $now = new \DateTimeImmutable();
        $start = $now;
        $end = $now;

        switch ($range) {
            case 'today':
                $start = $now->setTime(0, 0, 0);
                $end = $now->setTime(23, 59, 59);
                break;
            case 'tomorrow':
                $start = $now->modify('+1 day')->setTime(0, 0, 0);
                $end = $now->modify('+1 day')->setTime(23, 59, 59);
                break;
            case 'week':
                $start = $now->modify('monday this week')->setTime(0, 0, 0);
                $end = $now->modify('sunday this week')->setTime(23, 59, 59);
                break;
            case 'weekend':
                $start = $now->modify('saturday this week')->setTime(0, 0, 0);
                $end = $now->modify('sunday this week')->setTime(23, 59, 59);
                break;
            case 'next-week':
                $start = $now->modify('monday next week')->setTime(0, 0, 0);
                $end = $now->modify('sunday next week')->setTime(23, 59, 59);
                break;
        }

        return $qb->where('m.dateEtHeure BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('m.dateEtHeure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNextMatches(int $limit = 3): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('m')
            ->where('m.dateEtHeure > :now')
            ->setParameter('now', $now)
            ->orderBy('m.dateEtHeure', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les matchs les plus populaires basés sur le nombre total de billets vendus
     * 
     * @param int $limit Le nombre maximum de matchs à retourner
     * @return array
     */
    public function findTopMatches(int $limit = 5): array
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->addSelect('(m.billetsVirageVendus + m.billetsPelouseVendus + m.billetsEnceinteVendus) as totalBillets')
            ->orderBy('totalBillets', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return MatchFootball[] Returns an array of MatchFootball objects
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

    //    public function findOneBySomeField($value): ?MatchFootball
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
