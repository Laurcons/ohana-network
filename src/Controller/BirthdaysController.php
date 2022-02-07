<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BirthdaysController extends AbstractController
{
    /**
     * @Route("/birthdays", name="birthdays")
     */
    public function index(
        UserRepository $userRepo
    ): Response
    {
        $allUsers = $userRepo->findAll();
        $today = new \DateTime();

        $sortedBirthdays = $allUsers; // make a copy
        usort($sortedBirthdays, function (User $a, User $b) {
            return $a->getBirthdate()->getTimestamp() - $b->getBirthdate()->getTimestamp();
        });

        /**
         * Normalize the birthdays. Match to each user a DateTime that:
         * - if the month-day combo is earlier than today in the year, assign currentYear-1 to the year
         * - if the month-day combo is later or equal to today in the year, assign currentYear to the year
         */
        $mapFunc = function(User $user) use ($today): array {
            $normalized = new \DateTime();
            $normalized->setDate(
                $today->format('Y'),
                $user->getBirthdate()->format('n'),
                $user->getBirthdate()->format('j')
            );
            if ($today->getTimestamp() - $normalized->getTimestamp() > 0) {
                // $normalized is strictly in the past
                $normalized->setDate(
                    $today->format('Y') + 1,
                    $user->getBirthdate()->format('n'),
                    $user->getBirthdate()->format('j')
                );
            }
            return [
                'user' => $user, 
                'normalized' => $normalized
            ];
        };
        $normalizedBirthdays = array_map($mapFunc, $sortedBirthdays);
        // find soonest birthday by sorting the normalized birthdays
        usort($normalizedBirthdays, function(array $a, array $b) {
            return $a['normalized']->getTimestamp() - $b['normalized']->getTimestamp();
        });

        // find today's birthdays
        $todaysBirthdays = // cursed PHP shit follows
            array_map(
                function (array $pair) {
                    return $pair['user'];
                },
                array_filter(
                    $normalizedBirthdays,
                    function (array $pair) use ($today) {
                        return $pair['normalized']->getTimestamp() == $today->getTimestamp();
                    }
                )
            );

        return $this->render('birthdays/index.html.twig', [
            'sorted_birthdays' => $sortedBirthdays,
            'normalized_birthdays' => $normalizedBirthdays,
            'todays_birthdays' => $todaysBirthdays
        ]);
    }
}
