<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\Mapping\Index;

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
    private $campaign_id;

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
    private $sent_to;

    /**
     * @ORM\Column(name="opened", type="boolean")
     */
    private $opened;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message_id;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     */
    private $sent_date;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $opened_date;

    public function __construct()
    {
        $this->opened = false;
        $this->sent_date = new \DateTime();
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
    public function getCampaignId()
    {
        return $this->campaign_id;
    }

    /**
     * @param mixed $campaign_id
     */
    public function setCampaignId($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }

    /**
     * @return mixed
     */
    public function getSentTo()
    {
        return $this->sent_to;
    }

    /**
     * @param mixed $sent_to
     */
    public function setSentTo($sent_to)
    {
        $this->sent_to = $sent_to;
    }

    /**
     * @return mixed
     */
    public function getOpened()
    {
        return $this->opened;
    }

    /**
     * @param mixed $opened
     */
    public function setOpened($opened)
    {
        $this->opened = $opened;
    }

    /**
     * @return mixed
     */
    public function getSentDate()
    {
        return $this->sent_date;
    }

    /**
     * @param mixed $sent_date
     */
    public function setSentDate($sent_date)
    {
        $this->sent_date = $sent_date;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * @param mixed $message_id
     */
    public function setMessageId($message_id)
    {
        $this->message_id = $message_id;
    }


    /**
     * @return mixed
     */
    public function getOpenedDate()
    {
        return $this->opened_date;
    }

    /**
     * @param mixed $opened_date
     */
    public function setOpenedDate($opened_date)
    {
        $this->opened_date = $opened_date;
    }

}