<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("sortie_data")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Groups("sortie_data")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=5)
     * @Groups("sortie_data")
     */
    private $cp;

    /**
     * @ORM\OneToMany(targetEntity=Lieu::class, mappedBy="ville")
     */
    private $lieux;

    public function __construct()
    {
        $this->lieux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieux(): Collection
    {
        return $this->lieux;
    }

    public function addLieux(Lieu $lieux): self
    {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux[] = $lieux;
            $lieux->setVille($this);
        }

        return $this;
    }

    public function removeLieux(Lieu $lieux): self
    {
        if ($this->lieux->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getVille() === $this) {
                $lieux->setVille(null);
            }
        }

        return $this;
    }
}
