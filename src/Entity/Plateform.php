<?php

namespace App\Entity;

use App\Repository\PlateformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlateformRepository::class)]
class Plateform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'plateform', targetEntity: PromotedLink::class)]
    private Collection $promotedLinks;

    #[ORM\OneToMany(mappedBy: 'plateform', targetEntity: Publications::class)]
    private Collection $publications;

    public function __construct()
    {
        $this->promotedLinks = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, PromotedLink>
     */
    public function getPromotedLinks(): Collection
    {
        return $this->promotedLinks;
    }

    public function addPromotedLink(PromotedLink $promotedLink): static
    {
        if (!$this->promotedLinks->contains($promotedLink)) {
            $this->promotedLinks->add($promotedLink);
            $promotedLink->setPlateform($this);
        }

        return $this;
    }

    public function removePromotedLink(PromotedLink $promotedLink): static
    {
        if ($this->promotedLinks->removeElement($promotedLink)) {
            // set the owning side to null (unless already changed)
            if ($promotedLink->getPlateform() === $this) {
                $promotedLink->setPlateform(null);
            }
        }

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
            $publication->setPlateform($this);
        }

        return $this;
    }

    public function removePublication(Publications $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getPlateform() === $this) {
                $publication->setPlateform(null);
            }
        }

        return $this;
    }
}
