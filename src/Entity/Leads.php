<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LeadsRepository")
 */
class Leads
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $gender;

    //TODO: ManyToOne relationship. in v 2.0
    /**
     * @ORM\Column(type="string", length=25)
     */
    private $list_id;

    /**
     * @ORM\Column(name="sent", type="boolean")
     */
    private $sent;

    public function __construct()
    {
        $this->sent = false;
    }

    public function getId()
    {
        return $this->id;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
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
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param mixed $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }




}
