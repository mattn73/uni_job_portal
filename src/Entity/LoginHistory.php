<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LoginHistoryRepository")
 */
class LoginHistory
{
    const ALLOW = 'ALLOW';
    const NOT_ALLOW = 'NOT_ALLOW';
    const BLOCK = 'BLOCK';
    const LOGIN_ATTEMPT_ALLOW = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $UserIp;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * LoginHistory constructor.
     */
    public function __construct()
    {
        $timezone = new \DateTimeZone('Indian/Mauritius');
        $this->timestamp = new \DateTime();
        $this->timestamp->setTimezone($timezone);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIp(): ?string
    {
        return $this->UserIp;
    }

    public function setUserIp(string $UserIp): self
    {
        $this->UserIp = $UserIp;

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

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
