<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[ORM\ManyToOne(targetEntity: Enseignant::class, inversedBy: 'classes')]
    private $id_enseignant;

    #[ORM\ManyToOne(targetEntity: Apprenant::class, inversedBy: 'classes')]
    private $id_apprenant;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIdEnseignant(): ?Enseignant
    {
        return $this->id_enseignant;
    }

    public function setIdEnseignant(?Enseignant $id_enseignant): self
    {
        $this->id_enseignant = $id_enseignant;

        return $this;
    }

    public function getIdApprenant(): ?Apprenant
    {
        return $this->id_apprenant;
    }

    public function setIdApprenant(?Apprenant $id_apprenant): self
    {
        $this->id_apprenant = $id_apprenant;

        return $this;
    }
}
