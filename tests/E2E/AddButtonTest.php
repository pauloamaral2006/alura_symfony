<?php

namespace App\Tests\E2E;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddButtonTest extends WebTestCase
{
    public function testAddBbuttonDoesNotExistwhUseIsnotLoggedIn(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/series');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.btn.btn-dark.mb-3');
    }


    public function testAddBbuttonDoesExistwhUseIsLoggedIn(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $useRepository = $container->get(UserRepository::class);
        $user = $useRepository->findOneBy(['email' => 'eeemail@eexample.com']);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/series');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.btn.btn-dark.mb-3');
    }
}
