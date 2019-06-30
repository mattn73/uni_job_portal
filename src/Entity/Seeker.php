<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Asserts;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SeekerRepository")
 */
class Seeker
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $title;

    /**
     * @Asserts\Type("alpha")
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @Asserts\Type("alpha")
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @Asserts\Type(type="digit",
     *               message="Contact can only number")
     * @ORM\Column(type="string", length=255)
     */
    private $contact;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dob;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hqa;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $cv;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="seeker", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Skill", inversedBy="seekers")
     */
    private $skill;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Application", mappedBy="seeker", orphanRemoval=true)
     */
    private $applications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApplicationNotification", mappedBy="seeker", orphanRemoval=true)
     */
    private $notification;


    public function __construct()
    {
        $this->skill = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->notification = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getHqa(): ?string
    {
        return $this->hqa;
    }

    public function setHqa(string $hqa): self
    {
        $this->hqa = $hqa;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(string $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkill(): Collection
    {
        return $this->skill;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skill->contains($skill)) {
            $this->skill[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skill->contains($skill)) {
            $this->skill->removeElement($skill);
        }

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setSeeker($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->contains($application)) {
            $this->applications->removeElement($application);
            // set the owning side to null (unless already changed)
            if ($application->getSeeker() === $this) {
                $application->setSeeker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ApplicationNotification[]
     */
    public function getNotification(): Collection
    {
        return $this->notification;
    }

    public function addNotification(ApplicationNotification $notification): self
    {
        if (!$this->notification->contains($notification)) {
            $this->notification[] = $notification;
            $notification->setSeeker($this);
        }

        return $this;
    }

    public function removeNotification(ApplicationNotification $notification): self
    {
        if ($this->notification->contains($notification)) {
            $this->notification->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getSeeker() === $this) {
                $notification->setSeeker(null);
            }
        }

        return $this;
    }
}
