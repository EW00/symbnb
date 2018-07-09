<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ad::class);
    }

//    /**
//     * @return Ad[] Returns an array of Ad objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findAllWithPagination($page = 1, $maxResults = 10, $sort = 'a.id', $direction = 'DESC'){
        if($sort == 'avgRatings') $sort = 'AVG(c.rating)';

        $start = $page * $maxResults - $maxResults;

        return $this->createQueryBuilder('a')
                    ->join('a.comments', 'c')
                    ->orderBy($sort, $direction)
                    ->groupBy('a.id')
                    ->setFirstResult($start)
                    ->setMaxResults($maxResults)
                    ->getQuery()
                    ->getResult();
    }

    public function countAll(){
        return $this->getEntityManager()->createQuery('SELECT COUNT(a.id) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    /*
    public function findOneBySomeField($value): ?Ad
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
