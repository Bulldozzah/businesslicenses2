<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * ProcedureCategory
 *
 * @ORM\Table(name="procedure_category")
 * @ORM\Entity
 */
class ProcedureCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * 
     * @ORM\Column(name="publish", type="boolean")
     */
    private $publish;

    /**
     * @var integer
     * 
     * @ORM\Column(name="deleted", type="integer")
     */
    private $delete = 0;

    /**
     * @ORM\OneToMany(targetEntity="BusinessStartup", mappedBy="procedure_category")
     */
    protected $business_startup;

    public function __toString()
    {
        return strtoupper($this->title);
    }

    public function __construct()
    {
        $this->business_startup = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return ProcedureCategory
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return strtoupper($this->title);
    }
    /**
     * Set title
     *
     * @param string $title
     * @return ProcedureCategory
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return string 
     */
    public function getPublish()
    {
        return $this->publish;
    }
    /**
     * Set delete
     *
     * @param string $delete // set delete
     * 
     * @return ProcedureCategory
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * Get delete
     *
     * @return string 
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * Get Business Procedures
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBusinessProcedure()
    {
        return $this->business_startup;
    }
}
