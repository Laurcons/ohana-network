<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ChangePasswordDTO {
    /** @var callback */
    private $currentPasswordChecker;
    private $currentPassword;
    /**
     * @Assert\Length(min=6)
     */
    private $newPassword;

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getCurrentPasswordChecker() !== null && !$this->getCurrentPasswordChecker()($this)) {
            $context->buildViolation("The current password is incorrect!")
            ->atPath("currentPassword")
            ->addViolation();
        }
    }

    /**
     * Get the value of currentPassword
     */ 
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * Set the value of currentPassword
     *
     * @return  self
     */ 
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    /**
     * Get the value of newPassword
     */ 
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * Set the value of newPassword
     *
     * @return  self
     */ 
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * Get the value of currentPasswordChecker
     */ 
    public function getCurrentPasswordChecker()
    {
        return $this->currentPasswordChecker;
    }

    /**
     * Set the value of currentPasswordChecker
     *
     * @return  self
     */ 
    public function setCurrentPasswordChecker($currentPasswordChecker)
    {
        $this->currentPasswordChecker = $currentPasswordChecker;

        return $this;
    }
}