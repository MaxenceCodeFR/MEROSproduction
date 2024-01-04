<?php

namespace App\Entity;

use App\Repository\MotifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotifRepository::class)]
class Motif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $motif_influencer = null;

    #[ORM\Column(length: 255)]
    private ?string $motif_company = null;

    #[ORM\OneToMany(mappedBy: 'motif', targetEntity: ContactInfluencer::class)]
    private Collection $contactInfluencers;

    #[ORM\OneToMany(mappedBy: 'motif', targetEntity: ContactCompany::class)]
    private Collection $contactCompanies;

    public function __construct()
    {
        $this->contactInfluencers = new ArrayCollection();
        $this->contactCompanies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotifInfluencer(): ?string
    {
        return $this->motif_influencer;
    }

    public function setMotifInfluencer(string $motif_influencer): static
    {
        $this->motif_influencer = $motif_influencer;

        return $this;
    }

    public function getMotifCompany(): ?string
    {
        return $this->motif_company;
    }

    public function setMotifCompany(string $motif_company): static
    {
        $this->motif_company = $motif_company;

        return $this;
    }

    /**
     * @return Collection<int, ContactInfluencer>
     */
    public function getContactInfluencers(): Collection
    {
        return $this->contactInfluencers;
    }

    public function addContactInfluencer(ContactInfluencer $contactInfluencer): static
    {
        if (!$this->contactInfluencers->contains($contactInfluencer)) {
            $this->contactInfluencers->add($contactInfluencer);
            $contactInfluencer->setMotif($this);
        }

        return $this;
    }

    public function removeContactInfluencer(ContactInfluencer $contactInfluencer): static
    {
        if ($this->contactInfluencers->removeElement($contactInfluencer)) {
            // set the owning side to null (unless already changed)
            if ($contactInfluencer->getMotif() === $this) {
                $contactInfluencer->setMotif(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContactCompany>
     */
    public function getContactCompanies(): Collection
    {
        return $this->contactCompanies;
    }

    public function addContactCompany(ContactCompany $contactCompany): static
    {
        if (!$this->contactCompanies->contains($contactCompany)) {
            $this->contactCompanies->add($contactCompany);
            $contactCompany->setMotif($this);
        }

        return $this;
    }

    public function removeContactCompany(ContactCompany $contactCompany): static
    {
        if ($this->contactCompanies->removeElement($contactCompany)) {
            // set the owning side to null (unless already changed)
            if ($contactCompany->getMotif() === $this) {
                $contactCompany->setMotif(null);
            }
        }

        return $this;
    }
}
