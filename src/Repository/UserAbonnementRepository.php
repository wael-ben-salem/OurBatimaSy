<?php
namespace App\Repository;
use App\Entity\UserAbonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAbonnement>
 *
 * @method UserAbonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAbonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAbonnement[]    findAll()
 * @method UserAbonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAbonnement::class);
    }

    public function getSubscriptionDistribution(): array
    {
        // Get total subscriptions count
        $total = $this->count([]);
        
        if ($total === 0) {
            return []; // Prevent division by zero
        }
    
        // Get counts per subscription type
        $results = $this->createQueryBuilder('ua')
            ->select('
                a.nomAbonnement as name,
                COUNT(ua.id) as count
            ')
            ->join('ua.abonnement', 'a')
            ->groupBy('a.idAbonnement')
            ->getQuery()
            ->getResult();
    
        // Calculate percentages in PHP
        return array_map(function($item) use ($total) {
            $item['percentage'] = round(($item['count'] / $total) * 100, 1);
            return $item;
        }, $results);
    } // Other repository methods can be added below...

    /**
     * Example of another method you might have
     */
    public function countActiveSubscriptions(): int
    {
        return $this->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->where('ua.statut = :status')
            ->setParameter('status', 'actif')
            ->getQuery()
            ->getSingleScalarResult();
    }




    public function getMonthlySubscriptionTrends(): array
    {
        $results = $this->createQueryBuilder('ua')
            ->select('
                ua.dateDebut as date,
                a.nomAbonnement as name,
                ua.id
            ')
            ->join('ua.abonnement', 'a')
            ->getQuery()
            ->getResult();
    
        $grouped = [];
        foreach ($results as $item) {
            $month = (int)$item['date']->format('m');
            $key = $month.'_'.$item['name'];
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'month' => $month,
                    'name' => $item['name'],
                    'count' => 0
                ];
            }
            $grouped[$key]['count']++;
        }
    
        return array_values($grouped);
    }

    // /**
    //  * @return UserAbonnement[] Returns an array of UserAbonnement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAbonnement
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    */

    public function getMostPopularSubscription(): array
    {
        return $this->createQueryBuilder('ua')
            ->select('
                a.nomAbonnement as name,
                COUNT(ua.id) as totalSubscriptions
            ')
            ->join('ua.abonnement', 'a')
            ->groupBy('a.idAbonnement')
            ->orderBy('totalSubscriptions', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() ?? [];
    }
    
    public function getHighestRevenueSubscription(): array
    {
        return $this->createQueryBuilder('ua')
            ->select('
                a.nomAbonnement as name,
                SUM(a.prix) as totalRevenue
            ')
            ->join('ua.abonnement', 'a')
            ->groupBy('a.idAbonnement')
            ->orderBy('totalRevenue', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult() ?? [];
    }
   
}