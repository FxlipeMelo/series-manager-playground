<?php

namespace App\Repository;

use AllowDynamicProperties;
use App\DTO\SeriesCreationInputDTO;
use App\Entity\Series;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Mapping\Entity;;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Series>
 */
#[AllowDynamicProperties] class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, SeasonRepository $seasonRepository, EpisodeRepository $episodeRepository)
    {
        parent::__construct($registry, Series::class);
        $this->seasonRepository = $seasonRepository;
        $this->episodeRepository = $episodeRepository;
    }

    /**
     * @throws Exception
     */
    public function add(SeriesCreationInputDTO $input): Series
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $connection->beginTransaction();

        try {
            $series = new Series($input->seriesName, $input->coverImage);
            $entityManager->persist($series);
            $entityManager->flush();

            $this->seasonRepository->addSeasonsQuantity($input->seasonQuantity, $series->getId());

            $seasons = $this->seasonRepository->findBy(['series' => $series]);
            $this->episodeRepository->addEpisodesPerSeason($input->episodesPerSeason, $seasons);

            $connection->commit();
        } catch (\Exception $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            if (isset($series) && $entityManager->contains($series)) {
                $entityManager->detach($series);
            }
            throw $e;
        }

        return $series;
    }

    public function remove(Series $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);


        if  ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeByID(int $id): void
    {
        $series = $this->getEntityManager()->getReference(Series::class, $id);
        $this->remove($series, true);
    }

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
