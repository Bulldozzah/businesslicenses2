<?php

namespace WebmastersAfrica\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "sessions")
 */
class Session
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="id", type="string", length=250, nullable=false)
     */
    private $id;
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var DateTime
     *
     * @ORM\Column(name="access", type="datetime", nullable=false)
     */
    private $access;
    public function getAccess()
    {
        return $this->access;
    }
    public function setAccess($access)
    {
        $this->access = $access;
        return $this;
    }

    /**
     * @var text
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;
    public function getData()
    {
        return $this->data;
    }
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
