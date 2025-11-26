<?php

namespace App\Controller;

use App\Entity\Series;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeriesController extends AbstractController
{

    public function __construct(private SeriesRepository $serieRepository)
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

    #[Route('/series/create', methods:['GET'])]
    public function addSeriesForm(): Response
    {

        return $this->render('series/form.html.twig');

    }

    #[Route('/series/create', methods:['POST'])]
    public function addSeries(Request $request): Response
    {

        $serieName = $request->request->get('name');
        $serie = new Series($serieName);

        $this->serieRepository->add($serie, true);

        return new RedirectResponse('/series');

    }

}
