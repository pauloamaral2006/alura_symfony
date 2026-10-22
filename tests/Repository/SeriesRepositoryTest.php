<?php

namespace App\Tests\Repository;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Repository\EpisodeRepository;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeriesRepositoryTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $container = static::getContainer();
        $seriesRepository = $container->get(SeriesRepository::class);

        $series = new Series('Series test');

        for($i = 1; $i <= 2; $i++){

            $season = new Season($i);

            for($j = 1; $j <= 5; $j++){

                $season->addEpisode(new Episode($j));

            }

            $series->addSeason($season);

        }

        $seriesRepository->add(
            $series
            /* new SeriesCreationInputDTO(
                'Series test',
                2,
                5
            ) */,
             true
        );

        $episodesRepository = $container->get(EpisodeRepository::class);
        $episodes = $episodesRepository->findAll();
        $this->assertCount(10, $episodes);
    }
}
