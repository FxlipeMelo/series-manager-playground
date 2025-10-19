<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Series;
use App\Repository\SeasonRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class SeasonController extends AbstractController
{
    public function __construct(private CacheInterface $cache)
    {
    }

    #[Route('/series/{series}/season', name: 'app_season')]
    public function index(Series $series): Response
    {
        $season = $this->cache->get(
            "series_{$series->getId()}_season}",
            function (ItemInterface $item) use ($series) {
                $item->expiresAfter(new \DateInterval("PT10S"));

                /** @var PersistentCollection $season */
                $season = $series->getSeasons();
                $season->initialize();

                return $season;
        });

        return $this->render('season/index.html.twig', [
            'series' => $series,
            'season' => $season,
        ]);
    }
}
