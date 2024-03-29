<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isNew = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isSeen = null;

    #[ORM\OneToMany(mappedBy: 'notification', targetEntity: ContactCompany::class)]
    private Collection $contactCompanies;

    #[ORM\OneToMany(mappedBy: 'notification', targetEntity: ContactInfluencer::class)]
    private Collection $contactInfluencers;

    public function __construct()
    {
        $this->contactCompanies = new ArrayCollection();
        $this->contactInfluencers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function isIsSeen(): ?bool
    {
        return $this->isSeen;
    }

    public function setIsSeen(bool $isSeen): static
    {
        $this->isSeen = $isSeen;

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
            $contactCompany->setNotification($this);
        }

        return $this;
    }

    public function removeContactCompany(ContactCompany $contactCompany): static
    {
        if ($this->contactCompanies->removeElement($contactCompany)) {
            // set the owning side to null (unless already changed)
            if ($contactCompany->getNotification() === $this) {
                $contactCompany->setNotification(null);
            }
        }

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
            $contactInfluencer->setNotification($this);
        }

        return $this;
    }

    public function removeContactInfluencer(ContactInfluencer $contactInfluencer): static
    {
        if ($this->contactInfluencers->removeElement($contactInfluencer)) {
            // set the owning side to null (unless already changed)
            if ($contactInfluencer->getNotification() === $this) {
                $contactInfluencer->setNotification(null);
            }
        }

        return $this;
    }
}
