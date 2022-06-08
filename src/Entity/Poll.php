<?php

namespace App\Entity;

use App\Repository\PollRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PollRepository::class)
 */
class Poll
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="authoredPolls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $description;

    /**
     * @ORM\Column(type="json")
     */
    private $answers = [];

    /**
     * @ORM\OneToMany(targetEntity=PollResponse::class, mappedBy="poll", orphanRemoval=true)
     */
    private $pollResponses;

    /**
     * @ORM\Column(type="json")
     */
    private $options = [];

    public function __construct()
    {
        $this->pollResponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAnswers(): ?array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * @return Collection|PollResponse[]
     */
    public function getPollResponses(): Collection
    {
        return $this->pollResponses;
    }

    public function addPollResponse(PollResponse $pollResponse): self
    {
        if (!$this->pollResponses->contains($pollResponse)) {
            $this->pollResponses[] = $pollResponse;
            $pollResponse->setPoll($this);
        }

        return $this;
    }

    public function removePollResponse(PollResponse $pollResponse): self
    {
        if ($this->pollResponses->removeElement($pollResponse)) {
            // set the owning side to null (unless already changed)
            if ($pollResponse->getPoll() === $this) {
                $pollResponse->setPoll(null);
            }
        }

        return $this;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }
}
