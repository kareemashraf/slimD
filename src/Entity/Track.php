<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrackRepository")
 */
class Track
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $email_id;

    /**
     * @ORM\Column(type="text", length=20, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $useragent;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $device;

    /**
     * @ORM\Column(type="text", length=100, nullable=true)
     */
    private $email;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     */
    private $tracking_date;

    public function __construct()
    {
        $this->tracking_date = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getEmailId()
    {
        return $this->email_id;
    }

    /**
     * @param mixed $email_id
     */
    public function setEmailId($email_id)
    {
        $this->email_id = $email_id;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getUseragent()
    {
        return $this->useragent;
    }

    /**
     * @param mixed $useragent
     */
    public function setUseragent($useragent)
    {
        $this->useragent = $useragent;
    }

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice($device)
    {
        $this->device = $device;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getTrackingDate()
    {
        return $this->tracking_date;
    }

    /**
     * @param mixed $tracking_date
     */
    public function setTrackingDate($tracking_date)
    {
        $this->tracking_date = $tracking_date;
    }


}
