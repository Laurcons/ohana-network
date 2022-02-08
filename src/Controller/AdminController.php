<?php

namespace App\Controller;

use App\Entity\User;
use App\DTO\AdminResetPasswordDTO;
use App\Repository\UserRepository;
use App\Repository\SiteSettingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'breadcrumbs' => [
                ["Administration"]
            ]
        ]);
    }

    /**
     * @Route("/users", name="admin_users", methods={"GET"})
     */
    public function users(
        UserRepository $userRepo
    ): Response
    {
        $allUsers = $userRepo->findAll();

        return $this->render('admin/users.html.twig', [
            'breadcrumbs' => [
                ["Administration", "admin"],
                ["Users"]
            ],
            'all_users' => $allUsers
        ]);
    }

    /**
     * @Route("/users/resetPassword", name="admin_users_resetPassword", methods={"POST"})
     */
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepo,
        ManagerRegistry $doctrine
    ): Response {
        (function () use ($request, $hasher, $userRepo, $doctrine) {

            $uuid = $request->request->get('uuid');
            $newPassword = $request->request->get('newPassword');

            $user = $userRepo->findUuid($uuid);
            if (!$user)
                return;
            if (array_search('ROLE_ADMIN', $user->getRoles()) === false)
                return;

            $hash = $hasher->hashPassword($user, $newPassword);

            $user->setStatus(User::STATUS_PASSWORD_RESET);
            $user->setPassword($hash);
            $doctrine->getManager()->flush();
        })();

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/users/deactivate", name="admin_users_deactivate", methods={"POST"})
     */
    public function deactivate(
        Request $request,
        UserRepository $userRepo,
        ManagerRegistry $doctrine
    ): Response {
        (function () use ($request, $userRepo, $doctrine) {

            $uuid = $request->request->get('uuid');

            $user = $userRepo->findUuid($uuid);
            if (!$user)
                return;
            if (array_search('ROLE_ADMIN', $user->getRoles()) === false)
                return;
            if (array_search('ROLE_ACTIVATED', $user->getRoles()) === false)
                return;

            $user->setStatus(User::STATUS_NOT_ACTIVATED);
            $roles = $user->getRoles();
            $roles = array_filter($roles, function ($role) { return $role !== "ROLE_ACTIVATED"; });
            $user->setRoles($roles);
            $doctrine->getManager()->flush();
        })();

        return $this->redirectToRoute('admin_users');
    }

    /**
     * @Route("/siteSettings", name="admin_siteSettings")
     */
    public function siteSettings(
        SiteSettingRepository $siteSettingRepo
    ): Response
    {
        $allSettings = $siteSettingRepo->findAll();

        return $this->render("admin/siteSettings.html.twig", [
            'breadcrumbs' => [
                ["Administration", "admin"],
                ["Site Settings"]
            ],
            'all_settings' => $allSettings
        ]);
    }

    /**
     * @Route("/siteSettings/update", name="admin_siteSettings_update", methods={"POST"})
     */
    public function updateSiteSettings(
        Request $request,
        SiteSettingRepository $siteSettingRepo,
        ManagerRegistry $doctrine
    ): Response {
        (function () use ($request, $siteSettingRepo, $doctrine) {

            $allSettings = $siteSettingRepo->findAll();

            foreach ($allSettings as $setting) {
                $newValue = $request->request->get($setting->getName());
                $setting->setValue($newValue);
            }

            $doctrine->getManager()->flush();

        })();

        return $this->redirectToRoute('admin_siteSettings');
    }
}
