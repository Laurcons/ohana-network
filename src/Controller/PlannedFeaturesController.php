<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlannedFeaturesController extends AbstractController
{
    /**
     * @Route("/plannedFeatures", name="plannedFeatures")
     */
    public function index(): Response
    {
        return $this->render('planned_features/index.html.twig', [
            'controller_name' => 'PlannedFeaturesController',
        ]);
    }
}
