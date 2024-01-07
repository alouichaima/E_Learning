<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre_cours;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[ORM\ManyToOne(targetEntity: Enseignant::class, inversedBy: 'cours')]
    private $id_enseignant;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'cours')]
    private $id_categorie;

    #[ORM\OneToMany(mappedBy: 'id_cours', targetEntity: Lesson::class)]
    private $lessons;

    #[ORM\OneToMany(mappedBy: 'id_cours', targetEntity: Question::class)]
    private $questions;

    #[ORM\Column(type: 'float')]
    private $prix;

    #[ORM\Column(type: 'string', length: 255)]
    private $video;

    #[ORM\OneToMany(mappedBy: 'cours', targetEntity: Avis::class)]
    private $avis;

    #[ORM\ManyToMany(targetEntity: Apprenant::class, inversedBy: 'cours')]
    private $apprenant;

   

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->apprenant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreCours(): ?string
    {
        return $this->titre_cours;
    }

    public function setTitreCours(string $titre_cours): self
    {
        $this->titre_cours = $titre_cours;

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

    public function getIdCategorie(): ?Categorie
    {
        return $this->id_categorie;
    }

    public function setIdCategorie(?Categorie $id_categorie): self
    {
        $this->id_categorie = $id_categorie;

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setIdCours($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            // set the owning side to null (unless already changed)
            if ($lesson->getIdCours() === $this) {
                $lesson->setIdCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setIdCours($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getIdCours() === $this) {
                $question->setIdCours(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->titre_cours;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): self
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setCours($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getCours() === $this) {
                $avi->setCours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Apprenant>
     */
    public function getApprenant(): Collection
    {
        return $this->apprenant;
    }

    public function addApprenant(Apprenant $apprenant): self
    {
        if (!$this->apprenant->contains($apprenant)) {
            $this->apprenant[] = $apprenant;
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        $this->apprenant->removeElement($apprenant);

        return $this;
    }


    

}
