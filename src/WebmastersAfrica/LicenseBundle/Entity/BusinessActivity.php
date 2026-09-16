<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * WebmastersAfrica\LicenseBundle\Entity\BusinessActivity
 *
 * @ORM\Table(name="businessactivity")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\BusinessActivityRepository")
 */
class BusinessActivity implements Translatable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Locale
     */
    private $locale;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
    
    /**
     * @var string $name
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var text $description
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var boolean $deleted
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @ORM\ManyToMany(targetEntity="BusinessLicense", inversedBy="activities", cascade={"persist"})
     * @ORM\JoinTable(name="licenses_activities")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $licenses; 
    /**
     * Many User have Many

     * @ORM\ManyToMany(targetEntity="BusinessType")
     * @ORM\JoinTable(name="businesstypes_activities",
     *      joinColumns={@ORM\JoinColumn(name="businessactivity_id", referencedColumnName="id", unique=true)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="businesstype_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $businesstypes; 

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var datetime $contentChanged
     *
     * @ORM\Column(name="content_changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"name", "description","deleted","licenses","businesstypes"})
     */
    private $contentChanged;


    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @var User $contentChangedBy
     *
     * @Gedmo\Timestampable(on="change", field={"name", "description","deleted","licenses","businesstypes"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    public function __toString()
    {
        return $this->name;
    }
	
    public function __construct()
    {
        $this->licenses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->businesstypes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->deleted = false;
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
     * @return BusinessActivity
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
     * Set description
     *
     * @param string $description
     * @return BusinessActivity
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return BusinessActivity
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BusinessActivity
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return BusinessActivity
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set contentChanged
     *
     * @param \DateTime $contentChanged
     * @return BusinessActivity
     */
    public function setContentChanged($contentChanged)
    {
        $this->contentChanged = $contentChanged;
    
        return $this;
    }

    /**
     * Get contentChanged
     *
     * @return \DateTime 
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * Add licenses
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses
     * @return BusinessActivity
     */
    public function addLicense(\WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses)
    {
        $this->licenses[] = $licenses;
    
        return $this;
    }

    /**
     * Remove licenses
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses
     */
    public function removeLicense(\WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses)
    {
        $this->licenses->removeElement($licenses);
    }

    /**
     * Get licenses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLicenses()
    
    {
        return $this->licenses;
    }

    public function getPublishedLicenses()
    {
        return $this->getLicenses()->filter(
            function (BusinessLicense $licenses) {
                return $licenses->getStatus() == 1 && $licenses->getDeleted() == 0;
            }
        );
    }

    /**
     * Add businesstypes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes
     * @return BusinessActivity
     */
    public function addBusinesstype(\WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes)
    {
        $this->businesstypes[] = $businesstypes;
    
        return $this;
    }

    /**
     * Remove businesstypes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes
     */
    public function removeBusinesstype(\WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes)
    {
        $this->businesstypes->removeElement($businesstypes);
    }

        /**
     * Add businesstypes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes
     * @return BusinessActivity
     */
    public function addBusinesstypes(\WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes)
    {
        $this->businesstypes[] = $businesstypes;
    
        return $this;
    }

    /**
     * Remove businesstypes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes
     */
    public function removeBusinesstypes(\WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes)
    {
        $this->businesstypes->removeElement($businesstypes);
    }


    /**
     * Get businesstypes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBusinesstypes()
    {
        return $this->businesstypes;
    }

    /**
     * Set createdBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $createdBy
     * @return BusinessActivity
     */
    public function setCreatedBy(\WebmastersAfrica\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;
    
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $updatedBy
     * @return BusinessActivity
     */
    public function setUpdatedBy(\WebmastersAfrica\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;
    
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set contentChangedBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $contentChangedBy
     * @return BusinessActivity
     */
    public function setContentChangedBy(\WebmastersAfrica\UserBundle\Entity\User $contentChangedBy = null)
    {
        $this->contentChangedBy = $contentChangedBy;
    
        return $this;
    }

    /**
     * Get contentChangedBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getContentChangedBy()
    {
        return $this->contentChangedBy;
    }
}