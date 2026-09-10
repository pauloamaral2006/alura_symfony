<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeriesController extends AbstractController
{

    public function __construct(
        private SeriesRepository $serieRepository,
        private EntityManagerInterface $entityManager
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

        $seriesForm = $this->createForm(SeriesType::class, new Series(''));

        return $this->render('series/form.html.twig', [
            'seriesForm' => $seriesForm,
        ]);

    }

    #[Route('/series/create', name: 'app_add_series', methods:['POST'])]
    public function addSeries(Request $request): Response
    {

        $serie = new Series('');
        $serieForm = $this->createForm(SeriesType::class, $serie)->handleRequest($request);

        if (!$serieForm->isValid()) {
            return $this->render('series/form.html.twig', compact('seriesForm'));
        }

        $this->serieRepository->add($serie, true);

        $this->addFlash('success', "Série \"{$serie->getName()}\" adicionada com sucesso");

        return new RedirectResponse('/series');

    }

    #[Route('/series/edit/{series}', name: 'app_edit_series_form', methods: ['GET'])]
    public function editSeriesForm(Series $series): Response
    {
        $seriesForm = $this->createForm(SeriesType::class, $series, ['is_edit' => true]);
        return $this->render('series/form.html.twig', compact('seriesForm', 'series'));
    }

    #[Route('/series/edit/{series}', name: 'app_store_series_changes', methods: ['PATCH'])]
    public function storeSeriesChanges(Series $series, Request $request): Response
    {

        $seriesForm = $this->createForm(SeriesType::class, $series, ['is_edit' => true]);
        $seriesForm->handleRequest($request);

        if(!$seriesForm->isValid()) {
            return $this->render('series/form.html.twig', compact('seriesForm','series'));
        }

        $this->addFlash('success', "Série \"{$series->getName()}\" editada com sucesso");
        $this->entityManager->flush();

        return new RedirectResponse('/series');
    }

    #[Route(
        '/series/delete/{id}',
        name: 'app_delete_series',
        methods:['DELETE'],
        requirements: ['id' => '[0-9]+']
    )]
    public function deleteSeries(int $id, Request $request): Response
    {

        $this->serieRepository->removeById($id);

        $session = $request->getSession();
        $session->Set('success', 'Série removida com sucesso');

        return new RedirectResponse('/series');

    }

}
