<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationNotificationRepository")
 */
class ApplicationNotification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Application", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $application;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seeker", inversedBy="notification")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seeker;

    public function __construct()
    {
        $this->seeker = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    /**
     * @return Collection|Seeker[]
     */
    public function getSeeker(): Collection
    {
        return $this->seeker;
    }

    public function setSeeker(?Seeker $seeker): self
    {
        $this->seeker = $seeker;

        return $this;
    }
}
