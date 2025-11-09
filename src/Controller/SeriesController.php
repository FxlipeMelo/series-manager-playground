<?php

namespace App\Controller;

use App\DTO\SeriesCreationInputDTO;
use App\Entity\Series;
use App\Form\SeriesType;
use App\Message\SeriesWasCreate;
use App\Message\SeriesWasDeleted;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


final class SeriesController extends AbstractController
{
    public function __construct(
        private SeriesRepository $seriesRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $message,
        private SluggerInterface $slugger,
    )
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
        $form = $this->createForm(SeriesType::class, new SeriesCreationInputDTO());
        return $this->render('series/form.html.twig', compact('form'));
    }

    /**
     * @throws \Exception
     * @throws ExceptionInterface
     */
    #[Route('/series/create', name: 'app_add_series',methods: ['POST'])]
    public function addSeries(Request $request): Response
    {
        $input = new SeriesCreationInputDTO();
        $form = $this->createForm(SeriesType::class, $input)->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('series/form.html.twig', compact('form'));
        }

        /** @var UploadedFile $uploadCoverImage */
        $uploadCoverImage = $form->get('coverImage')->getData();

        if ($uploadCoverImage) {
            $originalFilename = pathinfo($uploadCoverImage->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = $this->slugger->slug($originalFilename);
            $newFileName = $safeFileName . '-' . uniqid() . '.' . $uploadCoverImage->guessExtension();

            $uploadCoverImage->move($this->getParameter('cover_image_directory'), $newFileName);
            $input->coverImage = $newFileName;
        }

        $series = $this->seriesRepository->add($input);
        $this->message->dispatch(new SeriesWasCreate($series));

        $this->addFlash('success', "Series \"{$series->getName()}\" add successfully");

        return new RedirectResponse('/series');
    }

    #[Route('/series/delete/{series}', name: 'app_series_delete', methods: ['DELETE'])]
    public function deleteSeries(Series $series): Response
    {
        $this->seriesRepository->remove($series, true);
        $this->message->dispatch(new SeriesWasDeleted($series));
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
