<?php

namespace App\Entity;

use App\Repository\PublicationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicationsRepository::class)]
class Publications
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?User $relation = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Plateform $plateform = null;

    #[ORM\ManyToOne(inversedBy: 'analytics')]
    private ?Analytics $analytics = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    private ?Impressions $impressions = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getRelation(): ?User
    {
        return $this->relation;
    }

    public function setRelation(?User $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getPlateform(): ?Plateform
    {
        return $this->plateform;
    }

    public function setPlateform(?Plateform $plateform): static
    {
        $this->plateform = $plateform;

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

    public function getImpressions(): ?Impressions
    {
        return $this->impressions;
    }

    public function setImpressions(?Impressions $impressions): static
    {
        $this->impressions = $impressions;

        return $this;
    }
}
