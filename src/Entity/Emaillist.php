<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmaillistRepository")
 */
class Emaillist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $list_name;


    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     * @Assert\NotBlank(message="Please, upload the Mailing list as a excel file.")
     * @Assert\File(mimeTypes={"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "text/excel", "application/excel"}, mimeTypesMessage = "Please upload a valid excel file" )
     */
    private $file;

    /**
     * @Assert\DateTime()
     * @ORM\Column(type="datetime")
     */
    private $data_added;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;


    public function __construct()
    {
        $this->isActive = true;
        $this->data_added = new \DateTime();
    }


    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getListName()
    {
        return $this->list_name;
    }

    /**
     * @param mixed $list_name
     */
    public function setListName($list_name)
    {
        $this->list_name = $list_name;
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
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }


    /**
     * @return mixed
     */
    public function getDataAdded()
    {
        return $this->data_added;
    }

    /**
     * @param mixed $data_added
     */
    public function setDataAdded($data_added)
    {
        $this->data_added = $data_added;
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
