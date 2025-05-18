<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStripeSessionId(string $sessionId): ?Commande
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.stripeSessionId = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère les statistiques de vente des X derniers jours
     *
     * @param int $days Nombre de jours pour lesquels récupérer les statistiques
     * @param bool $formatInFrench Indique si les jours doivent être formatés en français
     * @return array
     */
    public function getSalesStatsByDays(int $days = 7, bool $formatInFrench = true): array
    {
        $result = [];
        $today = new \DateTime();

        // Créer un tableau avec les X derniers jours
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = clone $today;
            $date->modify("-$i days");
            $formattedDate = $date->format('Y-m-d');
            $result[$formattedDate] = [
                'date' => $date,
                'tickets' => 0,
                'revenue' => 0,
            ];
        }

        // Récupérer les ventes pour ces jours
        $startDate = (clone $today)->modify("-$days days");

        // Utiliser SUBSTRING au lieu de DATE
        $qb = $this->createQueryBuilder('c')
            ->select('SUBSTRING(c.createdAt, 1, 10) as saleDate, COUNT(b.id) as ticketCount, SUM(c.total) as totalRevenue')
            ->leftJoin('c.billets', 'b')
            ->where('c.createdAt >= :startDate')
            ->setParameter('startDate', $startDate)
            ->groupBy('saleDate')
            ->orderBy('saleDate', 'ASC');

        $sales = $qb->getQuery()->getResult();

        // Remplir le tableau avec les données réelles
        foreach ($sales as $sale) {
            $dateKey = $sale['saleDate'];
            if (isset($result[$dateKey])) {
                $result[$dateKey]['tickets'] = (int)$sale['ticketCount'];
                $result[$dateKey]['revenue'] = (float)$sale['totalRevenue'];
            }
        }

        $result = array_values($result);

        // Si demandé, formater les jours en français
        if ($formatInFrench) {
            $joursFrancais = [
                'Mon' => 'Lun',
                'Tue' => 'Mar',
                'Wed' => 'Mer',
                'Thu' => 'Jeu',
                'Fri' => 'Ven',
                'Sat' => 'Sam',
                'Sun' => 'Dim'
            ];
            
            foreach ($result as &$stat) {
                $dayInEnglish = $stat['date']->format('D');
                $stat['dayName'] = $joursFrancais[$dayInEnglish] ?? $dayInEnglish;
            }
        }

        return $result;
    }
}
