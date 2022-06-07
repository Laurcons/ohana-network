<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $uuid;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(min=6)
     * This assertion will be useless for a hashed password, but during the user activation process, this field will store
     *  the raw password until the last moment when it gets hashed, so that the validation is performed on the raw password.
     */
    private $password;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *      pattern="/([a-zA-Z0-9_-]{3,20})/",
     *      message="Your username must contain 3 to 20 letters, numbers, dashes or underscores."
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $real_name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *      pattern="#^[a-z]+/[a-z]+/[a-z]+$#",
     *      message="Your pronouns must be three words without spaces separated by slashes."
     * )
     */
    private $pronouns;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\OneToMany(targetEntity=LuluRanking::class, mappedBy="target", orphanRemoval=true)
     */
    private $luluRankings;

    /**
     * @ORM\OneToMany(targetEntity=Tellonym::class, mappedBy="destination", orphanRemoval=true)
     */
    private $tellonyms;

    /**
     * @ORM\OneToMany(targetEntity=Poll::class, mappedBy="author", orphanRemoval=true)
     */
    private $authoredPolls;

    public function __construct()
    {
        $this->luluRankings = new ArrayCollection();
        $this->tellonyms = new ArrayCollection();
        $this->authoredPolls = new ArrayCollection();
    }
    public const STATUS_NOT_ACTIVATED = "NOT_ACTIVATED";
    public const STATUS_ACTIVE = "ACTIVE";
    public const STATUS_PASSWORD_RESET = "PASSWORD_RESET";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_OHANA_MEMBER
        $roles[] = 'ROLE_OHANA_MEMBER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRealName(): ?string
    {
        return $this->real_name;
    }

    public function setRealName(string $real_name): self
    {
        $this->real_name = $real_name;

        return $this;
    }

    public function getPronouns(): ?string
    {
        return $this->pronouns;
    }

    public function setPronouns(string $pronouns): self
    {
        $this->pronouns = $pronouns;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    private function getPronounParts(): array
    {
        return explode('/', $this->getPronouns());
    }

    public function getPronounNominative(): ?string
    {
        return $this->getPronounParts()[0];
    }

    public function getPronounDative(): ?string
    {
        return $this->getPronounParts()[1];
    }

    public function getPronounGenitive(): ?string
    {
        return $this->getPronounParts()[2];
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @return Collection|LuluRanking[]
     */
    public function getLuluRankings(): Collection
    {
        return $this->luluRankings;
    }

    public function addLuluRanking(LuluRanking $luluRanking): self
    {
        if (!$this->luluRankings->contains($luluRanking)) {
            $this->luluRankings[] = $luluRanking;
            $luluRanking->setTarget($this);
        }

        return $this;
    }

    public function removeLuluRanking(LuluRanking $luluRanking): self
    {
        if ($this->luluRankings->removeElement($luluRanking)) {
            // set the owning side to null (unless already changed)
            if ($luluRanking->getTarget() === $this) {
                $luluRanking->setTarget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tellonym[]
     */
    public function getTellonyms(): Collection
    {
        return $this->tellonyms;
    }

    public function addTellonym(Tellonym $tellonym): self
    {
        if (!$this->tellonyms->contains($tellonym)) {
            $this->tellonyms[] = $tellonym;
            $tellonym->setDestination($this);
        }

        return $this;
    }

    public function removeTellonym(Tellonym $tellonym): self
    {
        if ($this->tellonyms->removeElement($tellonym)) {
            // set the owning side to null (unless already changed)
            if ($tellonym->getDestination() === $this) {
                $tellonym->setDestination(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Poll[]
     */
    public function getAuthoredPolls(): Collection
    {
        return $this->authoredPolls;
    }

    public function addAuthoredPoll(Poll $authoredPoll): self
    {
        if (!$this->authoredPolls->contains($authoredPoll)) {
            $this->authoredPolls[] = $authoredPoll;
            $authoredPoll->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthoredPoll(Poll $authoredPoll): self
    {
        if ($this->authoredPolls->removeElement($authoredPoll)) {
            // set the owning side to null (unless already changed)
            if ($authoredPoll->getAuthor() === $this) {
                $authoredPoll->setAuthor(null);
            }
        }

        return $this;
    }
}
