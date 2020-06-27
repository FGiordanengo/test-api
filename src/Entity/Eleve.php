<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EleveRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EleveRepository::class)
 */
class Eleve
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("eleve:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"eleve:read", "eleve:update"})
     * @Assert\NotBlank
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"eleve:read", "eleve:update"})
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     */
    private $prenom;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("eleve:read")
     */
    private $dateDeNaissance;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="eleve", cascade={"persist"}, orphanRemoval=true)
     * @Groups("eleve:read")
     */
    private $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): self
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setEleve($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless algroup1y changed)
            if ($note->getEleve() === $this) {
                $note->setEleve(null);
            }
        }

        return $this;
    }
}
