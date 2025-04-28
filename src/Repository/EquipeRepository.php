<?php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    public function findPaginated(int $page, int $limit, ?string $statut = null, ?string $pays = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC');

        if ($statut) {
            $qb->andWhere('e.statut = :statut')
                ->setParameter('statut', $statut);
        }
        if ($pays) {
            $qb->andWhere('e.pays = :pays')
                ->setParameter('pays', $pays);
        }

        $query = $qb->getQuery();

        $paginator = new Paginator($query);

        $totalItems = count($paginator);
        $pages = ceil($totalItems / $limit);
        $offset = ($page - 1) * $limit;

        $query->setFirstResult($offset)->setMaxResults($limit);

        return [
            'results' => $query->getResult(),
            'totalItems' => $totalItems,
        ];
    }

    public function findDistinctPays(): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('DISTINCT e.pays')
            ->orderBy('e.pays', 'ASC');

        return array_column($qb->getQuery()->getResult(), 'pays');
    }

}