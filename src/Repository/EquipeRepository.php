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

    public function findPaginated(int $page, int $limit): array
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return [
            'results' => iterator_to_array($paginator->getIterator()),
            'totalItems' => count($paginator)
        ];
    }
}