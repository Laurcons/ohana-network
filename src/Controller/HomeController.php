<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(
        ArticleRepository $articleRepo
    ): Response
    {
        // gets latest news article
        $latestNewsArticle = $articleRepo->findLatestUnhidden();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'latest_news_article' => $latestNewsArticle
        ]);
    }
}
