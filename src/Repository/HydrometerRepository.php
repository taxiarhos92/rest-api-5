<?php

namespace App\Repository;

use App\Entity\Hydrometer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hydrometer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hydrometer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hydrometer[]    findAll()
 * @method Hydrometer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HydrometerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hydrometer::class);
    }

    public function transform(Hydrometer $hydrometer)
    {
        return [
                'id'    => (int) $hydrometer->getId(),
                'owner' => (string) $hydrometer->getOwner(),
                
        ];
    }

    public function transformAll()
    {
        $hydrometers = $this->findAll();
        $hydrometersArray = [];

        foreach ($hydrometers as $hydrometer) {
            $hydrometersArray[] = $this->transform($hydrometer);
        }

        return $hydrometersArray;
    }

    // /**
    //  * @return Hydrometer[] Returns an array of Hydrometer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Hydrometer
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
