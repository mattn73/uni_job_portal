<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Company", mappedBy="user", cascade={"persist", "remove"})
     */
    private $company;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Seeker", mappedBy="user", cascade={"persist", "remove"})
     */
    private $seeker;

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        // set the owning side of the relation if necessary
        if ($this !== $company->getUser()) {
            $company->setUser($this);
        }

        return $this;
    }

    public function getSeeker(): ?Seeker
    {
        return $this->seeker;
    }

    public function setSeeker(Seeker $seeker): self
    {
        $this->seeker = $seeker;

        // set the owning side of the relation if necessary
        if ($this !== $seeker->getUser()) {
            $seeker->setUser($this);
        }

        return $this;
    }



}
