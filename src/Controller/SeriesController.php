<?php

namespace App\Controller;

use App\Entity\Series;

use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeriesController extends AbstractController
    public function __construct(private SeriesRepository $seriesRepository, private EntityManagerInterface $entityManager)
    {
    }
    #[Route('/series', name: 'app_series', methods: ['GET'])]
    public function seriesList(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();

        return $this->render('series/index.html.twig', [

            'seriesList' => $seriesList
        ]);
    }

    #[Route('/series/create', name: 'app_series_form', methods: ['GET'])]
    public function addSeriesForm(Request $request): Response
    {
        $form = $this->createForm(SeriesType::class, new Series());
        return $this->render('series/form.html.twig', compact('form'));
    }

    #[Route('/series/create', name: 'app_add_series',methods: ['POST'])]
    public function addSeries(Request $request): Response
    {
        $series = new Series();
        $form = $this->createForm(SeriesType::class, $series)->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('series/form.html.twig', compact('form'));
        }

        $this->addFlash('success', "Series \"{$series->getName()}\" add successfully");


        $this->seriesRepository->add($series, true);
        return new RedirectResponse('/series');
    }

    #[Route('/series/delete/{id}', name: 'app_series_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteSeries(int $id, Request $request): Response
    {
        $this->seriesRepository->removeByID($id);
        $this->addFlash('success', "Series delete successfully");

        return new RedirectResponse('/series');
    }

    #[Route('/series/edit/{series}', name: 'app_edit_series_form', methods: ['GET'])]
    public function editSeriesForm(Series $series): Response
    {
        $form = $this->createForm(SeriesType::class, $series, ['is_edit' => true]);
        return $this->render('series/form.html.twig', compact('form','series'));
    }

    #[Route('/series/edit/{series}', name: 'app_store_series_change', methods: ['PATCH'])]
    public function storeSeriesChanges(Series $series, Request $request): Response
    {
        $form = $this->createForm(SeriesType::class, $series, ['is_edit' => true])->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('series/form.html.twig', compact('form', $series));
        }

        $this->addFlash('success', "Series \"{$series->getname()}\" updated successfully");
        $this->entityManager->flush();

        return new RedirectResponse('/series');
    }
}
