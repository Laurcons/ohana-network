<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreferencesController extends AbstractController
{
    /**
     * @Route("/preferences", name="preferences")
     */
    public function index(): Response
    {
        return $this->render('preferences/index.html.twig', [
            'controller_name' => 'PreferencesController',
        ]);
    }
}
