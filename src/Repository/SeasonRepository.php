<?php

namespace App\Repository;

use App\Entity\Season;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Season>
 */
class SeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    /**
     * @throws Exception
     */
    public function addSeasonsQuantity(int $seasonsQuantity, int $seriesId): void
    {
        $connection = $this->getEntityManager()->getConnection();

        $params = array_fill(0, $seasonsQuantity, "(?, ?)");
        $sql = 'INSERT INTO season (series_id, number) VALUES ' . implode(",", $params);
        $statement = $connection->prepare($sql);

        $bindIndex = 1;
        for ($i = 1; $i <= $seasonsQuantity; $i++) {
            $statement->bindValue($bindIndex++, $seriesId, \PDO::PARAM_INT);
            $statement->bindValue($bindIndex++, $i, \PDO::PARAM_INT);
        }
        $statement->executeQuery();
    }

    //    /**
    //     * @return Season[] Returns an array of Season objects
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

    //    public function findOneBySomeField($value): ?Season
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
