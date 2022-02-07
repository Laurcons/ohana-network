<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordDTO {
    /** @var string
     * @Assert\Length(min=6)
    */
    public $password;

    /**
     * Get the value of password
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */ 
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}