<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Series>
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    public function add(Series $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Series $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
<<<<<<< HEAD

        if  ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeByID(int $id): void
    {
        $series = $this->getEntityManager()->getReference(Series::class, $id);
        $this->remove($series, true);
    }
=======
    }

>>>>>>> afbfc35be12b4c2d35f02b243ac92d671ed543e7
    //    /**
    //     * @return Series[] Returns an array of Series objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Series
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
