<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OlaMundoController extends AbstractController
{
    #[Route('/ola_mundo', name: 'app_ola_mundo', methods:['GET'])]
    public function index(Request $request): Response
    {

        return new Response('<h1>ID: </h1>' . $request->query->get('id'));
    }
}
