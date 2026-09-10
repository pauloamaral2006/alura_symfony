<?php

namespace App\Controller;

use App\Entity\Series;
use App\Repository\SeasonRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class SeasonsController extends AbstractController
{
    public function __construct(
        private SeasonRepository $repository,
        private CacheInterface $cache
    )
    {
    }

    #[Route('/series/{series}/seasons', name: 'app_seasons')]
    public function index(Series $series): Response
    {

        $seasons = $this->cache->get(
            "series_{$series->getId()}_seasons",
            function (ItemInterface $item) use ($series) {
                $item->expiresAfter(new \DateInterval('PT10S'));

                /** @var PersistentCollection $seasons */
                $seasons = $series->getSeasons();
                $seasons->initialize();

                return $seasons;
            }
        );

        return $this->render('seasons/index.html.twig', [
            'series' => $series,
            'seasons' => $seasons
        ]);
    }

    /* #[Route('/series/{seriesId}/seasons', name: 'app_seasons')]
    public function index(int $seriesId): Response
    {
        $seasons= $this->repository->findBy([
        'series' => $seriesId,
        ]);

        return $this->render('seasons/index.html.twig', [
            'seasons' => $seasons,
        ]);
    } */
}
