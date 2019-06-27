<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application
{
    CONST NEW     = 'new';
    CONST ACCEPT  = 'accept';
    CONST REJECT  = 'reject';
    CONST PENDING = 'pending';
    CONST TRUE    = true;
    CONST FALSE   = false;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Seeker", inversedBy="application", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $seeker;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\JobPosting", inversedBy="application", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $notification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeeker(): ?Seeker
    {
        return $this->seeker;
    }

    public function setSeeker(Seeker $seeker): self
    {
        $this->seeker = $seeker;

        return $this;
    }

    public function getJob(): ?JobPosting
    {
        return $this->job;
    }

    public function setJob(JobPosting $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getNotification(): ?bool
    {
        return $this->notification;
    }

    public function setNotification(bool $notification): self
    {
        $this->notification = $notification;

        return $this;
    }
}
