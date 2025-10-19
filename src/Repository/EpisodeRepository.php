<?php

namespace App\Repository;

use App\Entity\Episode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Episode>
 */
class EpisodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Episode::class);
    }

    /**
     * @throws Exception
     */
    public function addEpisodesPerSeason(int $episodesPerSeason, array $seasons): void
    {
        $connection = $this->getEntityManager()->getConnection();

        $params = array_fill(0, $episodesPerSeason, '(?, ?)');
        $sql = 'INSERT INTO episode (season_id, number) VALUES ' . implode(",", $params);
        $statement = $connection->prepare($sql);

        foreach ($seasons as $season) {
            for ($i = 0; $i < $episodesPerSeason; $i++) {
                $statement->bindValue($i * 2 + 1, $season->getId(), \PDO::PARAM_INT);
                $statement->bindValue($i * 2 + 2, $i + 1, \PDO::PARAM_INT);
            }
            $statement->executeQuery();
        }
    }

    //    /**
    //     * @return Episode[] Returns an array of Episode objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Episode
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
