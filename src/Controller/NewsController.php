<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/news")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/", name="news")
     */
    public function index(
        ArticleRepository $articleRepo
    ): Response {
        $allArticles = $articleRepo->findAllUnhidden();

        return $this->render('news/index.html.twig', [
            'breadcrumbs' => [
                ["News"]
            ],
            'all_articles' => $allArticles
        ]);
    }

    /**
     * @Route("/{id}", name="news_article")
     */
    public function article(
        int $id,
        ArticleRepository $articleRepo,
        ManagerRegistry $doctrine
    ): Response {
        /** @var User */
        $user = $this->getUser();
        $article = $articleRepo->find($id);
        $article->addSeenBy($user->getId());
        $doctrine->getManager()->flush();

        return $this->render('news/article.html.twig', [
            'breadcrumbs' => [
                ["News", "news"],
                [$article->getTitle()]
            ],
            'article' => $article
        ]);
    }
}
