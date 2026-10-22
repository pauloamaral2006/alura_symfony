<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\SeriesWasCreated;
use App\Message\SeriesWasDeleted;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteSeriesImageHandler
{


    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function __invoke(SeriesWasDeleted  $message)
    {

        $coverImagePath = $message->series->getCoverImagePath();
        unlink(
            $this->parameterBag->get('cover_image_directory') . DIRECTORY_SEPARATOR . $coverImagePath
        );

    }
}



