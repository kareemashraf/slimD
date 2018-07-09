<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\Mapping\Index;
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

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $list_name;


    //TODO: ManyToOne relationship. in v 2.0
    /**
     * @ORM\Column(type="string", length=5)
     */
    private $user_id;

    //, mimeTypesMessage = "Please upload a valid CSV file"
    //TODO: Determine either CSV, Excel or both! 

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank(message="Please, upload the Mailing list as a excel file.")
     * @Assert\File(mimeTypes={"text/plain",  "text/csv", "application/csv"},
     *     mimeTypesMessage = "Please upload a valid CSV"
     * )
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

    public function getId()
    {
        return $this->id;
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
