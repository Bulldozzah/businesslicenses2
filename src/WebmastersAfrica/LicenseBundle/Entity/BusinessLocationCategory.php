<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessLocationCategory
 *
 * @ORM\Table("businesslocation_category")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\BusinessLocationCategoryRepository")
 * @UniqueEntity("name")
 */
class BusinessLocationCategory
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Name Required")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="\WebmastersAfrica\LicenseBundle\Entity\BusinessLocation", mappedBy="parentLocation")
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="published", type="boolean")
     */

    private $published = true;


    public function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     * @return BusinessLocationCategory
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
        return $this->name;
    }

    /**
     * Set published
     *
     * @param string $name
     * @return boolean
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return string
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Get locationId
     *
     * @return integer
     */
    public function getLocation()
    {
        return $this->location;
    }
}
