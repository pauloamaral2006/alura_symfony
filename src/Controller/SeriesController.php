<?php

namespace App\Controller;

use App\DTO\SeriesCreationInputDTO;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Form\SeriesEditType;
use App\Form\SeriesType;
use App\Message\SeriesWasCreated;
use App\Message\SeriesWasDeleted;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class SeriesController extends AbstractController
{

    public function __construct(
        private SeriesRepository $serieRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messenger,
        private SluggerInterface $slugger,
    )
    {
    }

    #[Route('/series', name: 'app_series')]
    public function index(): Response
    {

        /* $serieList = [
            'Lost',
            'Grey\'s Anatomy',
            'Loki',
            'Suits'
        ]; */

        $serieList = $this->serieRepository->findAll();

        /* $html = '<ul>';

        foreach ($serieList as $serie) {
            $html .= '<li>' . $serie . '</li>';
        }

        $html .= '</ul>';

        return new Response($html); */

        return $this->render('series/index.html.twig', [
            'serieList' => $serieList,
        ]);
    }

    #[Route('/series/create', name: 'app_series_form', methods:['GET'])]
    public function addSeriesForm(): Response
    {

        $seriesForm = $this->createForm(SeriesType::class, new SeriesCreationInputDTO());

        return $this->render('series/form.html.twig', [
            'seriesForm' => $seriesForm,
        ]);

    }

    #[Route('/series/create', name: 'app_add_series', methods:['POST'])]
    public function addSeries(Request $request): Response
    {

        $input = new SeriesCreationInputDTO();
        $seriesForm = $this->createForm(SeriesType::class, $input)->handleRequest($request);

        if (!$seriesForm->isValid()) {
            return $this->render('series/form.html.twig', compact('seriesForm'));
        }

        /** @var UploadedFile $uploadedCoverImage */
        $uploadedCoverImage = $seriesForm->get('coverImage')->getData();

        if($uploadedCoverImage){

            $originalFileName = pathinfo($uploadedCoverImage->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFileName);
            $newFileName = $safeFilename  . '-' . uniqid() . '.' . $uploadedCoverImage->guessExtension();

            $uploadedCoverImage->move(
                $this->getParameter('cover_image_directory'),
                $newFileName
            );

            $input->coverImage = $newFileName;

        }

        $series = new Series($input->seriesName, $input->coverImage);

        for($i = 1; $i <= $input->seasonsQuantity; $i++){

            $season = new Season($i);

            for($j = 1; $j <= $input->episodesPerSeason; $j++){

                $season->addEpisode(new Episode($j));

            }

            $series->addSeason($season);

        }

        $this->serieRepository->add($series, true);

        $this->messenger->dispatch(new SeriesWasCreated($series));

        $this->addFlash('success', "Série \"{$series->getName()}\" adicionada com sucesso");

        return new RedirectResponse('/series');

    }

    #[Route('/series/edit/{series}', name: 'app_edit_series_form', methods: ['GET'])]
    public function editSeriesForm(Series $series): Response
    {
        $seriesForm = $this->createForm(SeriesEditType::class, $series);
        return $this->render('series/edit.html.twig', compact('seriesForm', 'series'));
    }

    #[Route('/series/edit/{series}', name: 'app_store_series_changes', methods: ['PATCH'])]
    public function storeSeriesChanges(Series $series, Request $request): Response
    {

        $seriesForm = $this->createForm(SeriesEditType::class, $series);
        $seriesForm->handleRequest($request);

        if(!$seriesForm->isValid()) {
            return $this->render('series/form.html.twig', compact('seriesForm','series'));
        }

        $this->addFlash('success', "Série \"{$series->getName()}\" editada com sucesso");
        $this->entityManager->flush();

        return new RedirectResponse('/series');
    }

    #[Route(
        '/series/delete/{series}',
        name: 'app_delete_series',
        methods:['DELETE'],
        requirements: ['id' => '[0-9]+']
    )]
    public function deleteSeries(Series $series): Response
    {

        $this->serieRepository->remove($series, true);
        $this->messenger->dispatch(new SeriesWasDeleted($series));
        $this->addFlash('success', 'Série removida com sucesso');

        return new RedirectResponse('/series');

    }

}
