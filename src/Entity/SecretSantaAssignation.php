<?php

namespace App\Entity;

use App\Repository\SecretSantaAssignationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SecretSantaAssignationRepository::class)
 */
class SecretSantaAssignation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $receiver;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $observations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $customStatus;

    /**
     * @ORM\Column(type="string", length=2048, nullable=true)
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function getCustomStatus(): ?string
    {
        return $this->customStatus;
    }

    public function setCustomStatus(?string $customStatus): self
    {
        $this->customStatus = $customStatus;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
