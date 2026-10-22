<?php

namespace App\Tests\Entity;

use App\Entity\Episode;
use App\Entity\Season;
use PHPUnit\Framework\TestCase;

class SeasonTest extends TestCase
{
    public function testSomething(): void
    {

    $season = new Season(1);
    $episode1 = new Episode(1);
    $episode1->setWatched(true);

    $episode2 = new Episode(2);

    $season->addEpisode($episode1);
    $season->addEpisode($episode2);

    $watcedEpisodes = $season->getWatchedEpisodes();

    $this->assertCount(1, $watcedEpisodes);
    $this->assertSame($episode1, $watcedEpisodes->first());
    }
}
