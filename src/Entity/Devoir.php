<?php

namespace App\Entity;

use App\Repository\DevoirRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevoirRepository::class)]
class Devoir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: 'devoirs')]
    private $id_lesson;

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

    public function getIdLesson(): ?Lesson
    {
        return $this->id_lesson;
    }

    public function setIdLesson(?Lesson $id_lesson): self
    {
        $this->id_lesson = $id_lesson;

        return $this;
    }
}
