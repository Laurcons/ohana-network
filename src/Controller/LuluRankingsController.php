<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\LuluRanking;
use App\Repository\UserRepository;
use App\Repository\LuluRankingRepository;
use App\Repository\SiteSettingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuluRankingsController extends AbstractController
{
    /**
     * @Route("/luluRankings", name="luluRankings")
     */
    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        SiteSettingRepository $settingRepo,
        UserRepository $userRepo,
        LuluRankingRepository $luluRepo
    ): Response
    {
        $rankerUuid = $settingRepo->getSetting("luluRankings_rankerUuid")->getValue();
        /** @var User */
        $currentUser = $this->getUser();
        $isRanker = $currentUser->getUuid() === $rankerUuid;

        $users = $userRepo->findActive();

        $userData = [];
        foreach ($users as $user) {
            if ($user->getUuid() === $rankerUuid)
                continue;
            $userData[] = [
                'user' => $user,
                'ranking' => $luluRepo->getLatestForUser($user)
            ];
        }

        if ($request->isMethod("POST")) {
            if (!$isRanker)
                throw $this->createAccessDeniedException();
            // handle the form
            if (!$this->isCsrfTokenValid('luluRankings', $request->request->get('csrf')))
                return new Response("Invalid CSRF.", 422);
            $manager = $doctrine->getManager();
            foreach ($userData as $data) {
                $value = $request->request->get('ranking_' . $data['user']->getUuid());
                $value = trim($value);
                if (
                    ($data['ranking'] === NULL && $value !== "") ||
                    ($data['ranking'] !== NULL && $value !== $data['ranking']->getValue())
                ) {
                    $r = new LuluRanking();
                    $r
                        ->setTimestamp(new DateTime())
                        ->setValue($value)
                        ->setTarget($data['user']);
                    $manager->persist($r);
                }
            }
            $manager->flush();
            $this->addFlash('notice', "Points successfully updated! Hooray!");
            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('lulu_rankings/index.html.twig', [
            'is_ranker' => $isRanker,
            'user_data' => $userData
        ]);
    }
}
