<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet adresse email est déjà associée a un compte.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $resetToken = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Media::class)]
    private Collection $images;

    #[ORM\ManyToMany(targetEntity: Social::class, inversedBy: 'users', cascade: ['persist'])]
    private Collection $social;

    #[ORM\ManyToMany(targetEntity: Specialty::class, inversedBy: 'users')]
    private Collection $specialty;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ContactInfluencer::class)]
    private Collection $contactInfluencers;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Calendar::class)]
    private Collection $calendar;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->social = new ArrayCollection();
        $this->specialty = new ArrayCollection();
        $this->contactInfluencers = new ArrayCollection();
        $this->calendar = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Media $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setUser($this);
        }

        return $this;
    }

    public function removeImage(Media $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getUser() === $this) {
                $image->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Social>
     */
    public function getSocial(): Collection
    {
        return $this->social;
    }

    public function addSocial(Social $social): static
    {
        if (!$this->social->contains($social)) {
            $this->social->add($social);
        }

        return $this;
    }

    public function removeSocial(Social $social): static
    {
        $this->social->removeElement($social);

        return $this;
    }

    /**
     * @return Collection<int, Specialty>
     */
    public function getSpecialty(): Collection
    {
        return $this->specialty;
    }

    public function addSpecialty(Specialty $specialty): static
    {
        if (!$this->specialty->contains($specialty)) {
            $this->specialty->add($specialty);
        }

        return $this;
    }

    public function removeSpecialty(Specialty $specialty): static
    {
        $this->specialty->removeElement($specialty);

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
            $contactInfluencer->setUser($this);
        }

        return $this;
    }

    public function removeContactInfluencer(ContactInfluencer $contactInfluencer): static
    {
        if ($this->contactInfluencers->removeElement($contactInfluencer)) {
            // set the owning side to null (unless already changed)
            if ($contactInfluencer->getUser() === $this) {
                $contactInfluencer->setUser(null);
            }
        }

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendar(): Collection
    {
        return $this->calendar;
    }

    public function addCalendar(Calendar $calendar): static
    {
        if (!$this->calendar->contains($calendar)) {
            $this->calendar->add($calendar);
            $calendar->setUser($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        if ($this->calendar->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getUser() === $this) {
                $calendar->setUser(null);
            }
        }

        return $this;
    }
}
