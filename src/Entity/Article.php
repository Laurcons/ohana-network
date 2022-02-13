<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=5120)
     */
    private $contentMd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $published;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @ORM\Column(type="array")
     */
    private $seenBy = [];

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContentMd(): ?string
    {
        return $this->contentMd;
    }

    public function setContentMd(string $contentMd): self
    {
        $this->contentMd = $contentMd;

        return $this;
    }

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getSeenBy(): ?array
    {
        return $this->seenBy;
    }

    public function setSeenBy(array $seenBy): self
    {
        $this->seenBy = $seenBy;

        return $this;
    }

    /**
     * Adds a user id to the seen by array. Removes duplicates. Returns self.
     */
    public function addSeenBy(int $id): self
    {
        $this->seenBy[] = $id;
        array_unique($this->seenBy);
        return $this;
    }

    /**
     * Returns the part of contentMd until the ENDDESC mark, or the first 250 characters if the mark is not present.
     * The ENDDESC mark is '[//]: # (enddesc)' without quotes.
     */
    public function getDescription(): string
    {
        $content = $this->getContentMd();
        $end = strpos($content, "[//]: # (enddesc)");
        if ($end === false) {
            $substr = substr($content, 0, 250);
            if (strlen($substr) !== strlen($content))
                return $substr . "...";
            return $substr;
        }
        return substr($content, 0, $end);
    }
}
