<?php

namespace App\Entity;

use App\Repository\ImpressionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImpressionsRepository::class)]
class Impressions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $likes = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $comments = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $shares = null;

    #[ORM\ManyToOne(inversedBy: 'impressions')]
    private ?Analytics $analytics = null;

    #[ORM\OneToMany(mappedBy: 'impressions', targetEntity: Publications::class)]
    private Collection $publications;

    public function __construct()
    {
        $this->publications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    public function getComments(): ?int
    {
        return $this->comments;
    }

    public function setComments(?int $comments): static
    {
        $this->comments = $comments;

        return $this;
    }

    public function getShares(): ?int
    {
        return $this->shares;
    }

    public function setShares(?int $shares): static
    {
        $this->shares = $shares;

        return $this;
    }

    public function getAnalytics(): ?Analytics
    {
        return $this->analytics;
    }

    public function setAnalytics(?Analytics $analytics): static
    {
        $this->analytics = $analytics;

        return $this;
    }

    /**
     * @return Collection<int, Publications>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publications $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setImpressions($this);
        }

        return $this;
    }

    public function removePublication(Publications $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getImpressions() === $this) {
                $publication->setImpressions(null);
            }
        }

        return $this;
    }
}
