<?php

namespace App\Controller;

use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EpisodeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/season/{season}/episode', name: 'app_episode', methods: ['GET'])]
    public function index(Season $season): Response
    {
        return $this->render('episode/index.html.twig', [
            'season' => $season,
            'series' => $season->getSeries(),
            'episodes' => $season->getEpisodes(),
        ]);
    }

    #[Route('/season/{season}/episode', name: 'app_watch_episode', methods: ['POST'])]
    public function watch(Season $season, Request $request): Response
    {
        $watchedEpisode = array_keys($request->request->all('episodes'));
        $episodes = $season->getEpisodes();

        foreach ($episodes as $episode) {
            $episode->setWatched(in_array($episode->getId(), $watchedEpisode));

            $this->entityManager->persist($episode);
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Episode watched successfully');

        return new RedirectResponse("/season/{$season->getId()}/episode");
    }
}
