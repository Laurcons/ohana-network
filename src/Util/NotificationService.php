<?php

namespace App\Util;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Component\Security\Core\Security;

class NotificationService {
    /** @var User */
    private $currentUser;
    /** @var NotificationRepository */
    private $notificationRepository;

    public function __construct(
        Security $security,
        NotificationRepository $notifRepo
    ) {
        $this->currentUser = 
            $security->isGranted("IS_AUTHENTICATED_REMEMBERED") ?
            $security->getUser() :
            null;
        $this->notificationRepository = $notifRepo;
    }

    public function getUserNotifications(): array {
        return $this->notificationRepository->getUndismissedOf($this->currentUser);
    }

    

}