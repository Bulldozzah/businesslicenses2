<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use WebmastersAfrica\PressBundle\Entity\ProcedureCategory;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * BusinessStartup
 *
 * @ORM\Table(name="business_startup")
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class BusinessStartup
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="links", type="text")
     * @Assert\NotBlank
     */
    private $links;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="text")
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_published", type="text")
     * @Assert\NotBlank
     */
    private $isPublished;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = 0;

    /**
     * @var integer
     * 
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\PressBundle\Entity\ProcedureCategory")
     */
    private $procedure_category;


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
     * Get Procedure Category
     *
     * 
     * @return integer
     */
    public function getProcedureCategory()
    {
        return $this->procedure_category;
    }

    /**
     * Set Procedure Category
     *  
     * @param integer $name
     * 
     * @return void
     */
    public function setProcedureCategory(ProcedureCategory $procedure_category)
    {
        $this->procedure_category = $procedure_category;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BusinessStartup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return strtoupper($this->name);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return BusinessStartup
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Set description
     *
     * @param string $links
     * @return BusinessStartup
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set description
     *
     * @param string $slug
     * @return BusinessStartup
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return BusinessStartup
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return BusinessStartup
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
