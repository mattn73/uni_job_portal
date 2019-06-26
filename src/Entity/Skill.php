<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SkillRepository")
 */
class Skill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Seeker", mappedBy="skill")
     */
    private $seekers;

    public function __construct()
    {
        $this->seekers = new ArrayCollection();
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

    /**
     * @return Collection|Seeker[]
     */
    public function getSeekers(): Collection
    {
        return $this->seekers;
    }

    public function addSeeker(Seeker $seeker): self
    {
        if (!$this->seekers->contains($seeker)) {
            $this->seekers[] = $seeker;
            $seeker->addSkill($this);
        }

        return $this;
    }

    public function removeSeeker(Seeker $seeker): self
    {
        if ($this->seekers->contains($seeker)) {
            $this->seekers->removeElement($seeker);
            $seeker->removeSkill($this);
        }

        return $this;
    }
}
