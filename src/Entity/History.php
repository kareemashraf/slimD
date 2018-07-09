<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoryRepository")
 */
class History
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    //TODO: ManyToOne relationship. in v 2.0
    /**
     * @ORM\Column(type="string", length=5)
     */
    private $user_id;

    //TODO: ManyToOne relationship. in v 2.0
    /**
     * @ORM\Column(type="string", length=5)
     */
    private $list_id;

    /**
     * @ORM\Column(type="text", length=100)
     */
    private $fromtext;

    /**
     * @ORM\Column(type="text", length=100)
     */
    private $subjecttext;

    /**
     * @ORM\Column(type="text")
     */
    private $message_html;

    /**
     * @ORM\Column(type="text")
     */
    private $message_plaintext;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     */
    private $order_date;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->isActive = true;
        $this->order_date = new \DateTime();
    }

    public function getId()
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
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * @param mixed $list_id
     */
    public function setListId($list_id)
    {
        $this->list_id = $list_id;
    }

    /**
     * @return mixed
     */
    public function getFromtext()
    {
        return $this->fromtext;
    }

    /**
     * @param mixed $fromtext
     */
    public function setFromtext($fromtext)
    {
        $this->fromtext = $fromtext;
    }

    /**
     * @return mixed
     */
    public function getSubjecttext()
    {
        return $this->subjecttext;
    }

    /**
     * @param mixed $subjecttext
     */
    public function setSubjecttext($subjecttext)
    {
        $this->subjecttext = $subjecttext;
    }

    /**
     * @return mixed
     */
    public function getMessageHtml()
    {
        return $this->message_html;
    }

    /**
     * @param mixed $message_html
     */
    public function setMessageHtml($message_html)
    {
        $this->message_html = $message_html;
    }

    /**
     * @return mixed
     */
    public function getMessagePlaintext()
    {
        return $this->message_plaintext;
    }

    /**
     * @param mixed $message_plaintext
     */
    public function setMessagePlaintext($message_plaintext)
    {
        $this->message_plaintext = $message_plaintext;
    }

    /**
     * @return mixed
     */
    public function getOrderDate()
    {
        return $this->order_date;
    }

    /**
     * @param mixed $order_date
     */
    public function setOrderDate($order_date)
    {
        $this->order_date = $order_date;
    }

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }



}
