<?php

namespace App\Entity;

use App\Entity\ServiceItem;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]


class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user', 'serviceItem'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le nom d'utilisateur ne peut pas être vide.")]
    #[ORM\Column(length: 100)]
    #[Groups(['user', 'serviceItem'])]
    private ?string $username = null;

    // Contrainte pour le champ email login
    #[Assert\NotBlank]
    #[Assert\Email(message: "Veuillez saisir une adresse email valide.")]
    #[Assert\Length(
        min: 5,
        minMessage: "Votre email doit comporter au moins {{ limit }} caractères.",
        max: 180,
        maxMessage: "Votre email ne peut pas comporter plus de {{ limit }} caractères."
    )]
    // l'email doit être unique
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user', 'serviceItem'])]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide.")]
    #[Assert\Length(
        min: 12,
        minMessage: "Le mot de passe doit comporter au moins {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/[a-z]/",
        message: "Le mot de passe doit contenir au moins une lettre minuscule."
    )]
    #[Assert\Regex(
        pattern: "/[A-Z]/",
        message: "Le mot de passe doit contenir au moins une lettre majuscule."
    )]
    #[Assert\Regex(
        pattern: "/\d/",
        message: "Le mot de passe doit contenir au moins un chiffre."
    )]
    #[Assert\Regex(
        pattern: "/\W/",
        message: "Le mot de passe doit contenir au moins un caractère spécial."
    )]
    private ?string $plainPassword = null;

    #[ORM\Column]
    private bool $isVerified = false;
    /**
     * @var array The user roles
     */
    #[ORM\Column(type: 'json')]
    #[Groups(['user', 'serviceItem'])]
    private array $roles = [];

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user', 'serviceItem', 'userTraceability'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user', 'serviceItem', 'userTraceability'])]
    private ?string $lastName = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['user', 'serviceItem', 'userTraceability'])]
    private ?string $city = null;



    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user', 'serviceItem'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['user', 'serviceItem'])]
    private ?\DateTimeInterface $dateRegister = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user', 'serviceItem'])]
    private ?string $picture = null;

 

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user', 'serviceItem'])]
    private ?string $portfolio = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user', 'serviceItem'])]
    private ?string $bio = null;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(targetEntity: ServiceItem::class, mappedBy: 'user')]
    private Collection $serviceItems;

    public function __construct()
    {
        $this->serviceItems = new ArrayCollection();
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
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

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getDateRegister(): ?\DateTimeInterface
    {
        return $this->dateRegister;
    }

    public function setDateRegister(\DateTimeInterface $dateRegister): static
    {
        $this->dateRegister = $dateRegister;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPortfolio(): ?string
    {
        return $this->portfolio;
    }

    public function setPortfolio(?string $portfolio): static
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServiceItems(): Collection
    {
        return $this->serviceItems;
    }

    public function addServiceItem(ServiceItem $serviceItem): static
    {
        if (!$this->serviceItems->contains($serviceItem)) {
            $this->serviceItems->add($serviceItem);
            $serviceItem->setUser($this);
        }

        return $this;
    }

    public function removeService(ServiceItem $serviceItem): static
    {
        if ($this->serviceItems->removeElement($serviceItem)) {
            // set the owning side to null (unless already changed)
            if ($serviceItem->getUser() === $this) {
                $serviceItem->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    // #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;


    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }
}
