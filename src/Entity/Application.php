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


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $notification;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seeker", inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seeker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\JobPosting", inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSeeker(): ?Seeker
    {
        return $this->seeker;
    }

    public function setSeeker(?Seeker $seeker): self
    {
        $this->seeker = $seeker;

        return $this;
    }
}
