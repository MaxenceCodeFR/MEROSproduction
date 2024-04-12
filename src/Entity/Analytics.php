<?php

namespace App\Entity;

use App\Repository\AnalyticsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalyticsRepository::class)]
class Analytics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'analytics', targetEntity: Publications::class)]
    private Collection $analytics;

    #[ORM\Column(nullable: true)]
    private ?int $count = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: 'analytics', targetEntity: Impressions::class)]
    private Collection $impressions;

    public function __construct()
    {
        $this->analytics = new ArrayCollection();
        $this->impressions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Publications>
     */
    public function getAnalytics(): Collection
    {
        return $this->analytics;
    }

    public function addAnalytic(Publications $analytic): static
    {
        if (!$this->analytics->contains($analytic)) {
            $this->analytics->add($analytic);
            $analytic->setAnalytics($this);
        }

        return $this;
    }

    public function removeAnalytic(Publications $analytic): static
    {
        if ($this->analytics->removeElement($analytic)) {
            // set the owning side to null (unless already changed)
            if ($analytic->getAnalytics() === $this) {
                $analytic->setAnalytics(null);
            }
        }

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Impressions>
     */
    public function getImpressions(): Collection
    {
        return $this->impressions;
    }

    public function addImpression(Impressions $impression): static
    {
        if (!$this->impressions->contains($impression)) {
            $this->impressions->add($impression);
            $impression->setAnalytics($this);
        }

        return $this;
    }

    public function removeImpression(Impressions $impression): static
    {
        if ($this->impressions->removeElement($impression)) {
            // set the owning side to null (unless already changed)
            if ($impression->getAnalytics() === $this) {
                $impression->setAnalytics(null);
            }
        }

        return $this;
    }
}
