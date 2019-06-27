<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobPostingRepository")
 */
class JobPosting
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
    private $JobTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $JobReference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $ClosingDate;

    /**
     * @ORM\Column(type="text")
     */
    private $JobDescr;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="jobPostings")
     */
    private $company;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Application", mappedBy="job", cascade={"persist", "remove"})
     */
    private $application;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJobTitle(): ?string
    {
        return $this->JobTitle;
    }

    public function setJobTitle(string $JobTitle): self
    {
        $this->JobTitle = $JobTitle;

        return $this;
    }

    public function getJobReference(): ?string
    {
        return $this->JobReference;
    }

    public function setJobReference(string $JobReference): self
    {
        $this->JobReference = $JobReference;

        return $this;
    }

    public function getClosingDate(): ?\DateTimeInterface
    {
        return $this->ClosingDate;
    }

    public function setClosingDate(\DateTimeInterface $ClosingDate): self
    {
        $this->ClosingDate = $ClosingDate;

        return $this;
    }

    public function getJobDescr(): ?string
    {
        return $this->JobDescr;
    }

    public function setJobDescr(string $JobDescr): self
    {
        $this->JobDescr = $JobDescr;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        // set the owning side of the relation if necessary
        if ($this !== $application->getJob()) {
            $application->setJob($this);
        }

        return $this;
    }
}
