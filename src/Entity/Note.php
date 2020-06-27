<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NoteRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"eleve:read", "note:read", "note:post"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"eleve:read", "note:moyenne-generale", "note:post"})
     * @Assert\NotBlank(message="La valeur est obligatoire")
     */
    private $valeur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"eleve:read", "note:read", "note:post"})
     * @Assert\NotBlank(message="La matière est obligatoire")
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity=Eleve::class, inversedBy="notes")
     * @Groups({"note:read", "note:post"})
     */
    private $eleve;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(string $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }
}
