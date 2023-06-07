<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[UniqueEntity(fields: ['username'], message: 'Il y a déjà un compte avec ce pseudo')]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec ce email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("sortie_data")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("sortie_data")
     */
    #[Assert\NotBlank(message: "Le pseudo est obligatoire")]
    #[Assert\Length(min: 5, minMessage: "Le pseudo doit contenir au moins 5 caractères")]
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups("sortie_data")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("sortie_data")
     */
    #[Assert\NotBlank(message: "Prénom obligatoire")]
    #[Assert\Length(min: 3, minMessage: "Le prenom doit contenir au moins 3 caractères")]
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("sortie_data")
     */
    #[Assert\NotBlank(message: "Nom obligatoire")]
    #[Assert\Length(min: 3, minMessage: "Le nom doit contenir au moins 3 caractères")]
    private $lastname;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     * @Groups("sortie_data")
     */
    #[Assert\Regex(
        pattern: '/^(0\d{9})$/',
        message: 'Le numéro de téléphone doit contenir exactement 10 chiffres et doit commencer par 0.'
    )]
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups("sortie_data")
     */
    #[Assert\NotBlank(message: "Email obligatoire")]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        message: 'Veuillez respecter le format de mail.'
    )]
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="users")
     */
    private $sorties;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="users")
     */
    private $C;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="user")
     */
    private $sortie;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif = true;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->C = new ArrayCollection();
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->addUser($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->removeElement($sorty)) {
            $sorty->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getC(): Collection
    {
        return $this->C;
    }

    public function addC(Sortie $c): self
    {
        if (!$this->C->contains($c)) {
            $this->C[] = $c;
            $c->addUser($this);
        }

        return $this;
    }

    public function removeC(Sortie $c): self
    {
        if ($this->C->removeElement($c)) {
            $c->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie[] = $sortie;
            $sortie->setUser($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getUser() === $this) {
                $sortie->setUser(null);
            }
        }

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
