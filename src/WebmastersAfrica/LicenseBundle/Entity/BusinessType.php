<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * WebmastersAfrica\LicenseBundle\Entity\BusinessType
 *
 * @ORM\Table(name="businesstype")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\BusinessTypeRepository")
 */
class BusinessType implements Translatable
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
     * @var boolean $show_in_browse
     *
     * @ORM\Column(name="show_in_browse", type="boolean")
     */
    private $show_in_browse;

    /**
     * @ORM\ManyToMany(targetEntity="BusinessIndustry", inversedBy="businesstypes", cascade={"persist"})
     * @ORM\JoinTable(name="businesstypes_industries")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $industries; 
    
    /**
     * @ORM\ManyToMany(targetEntity="BusinessActivity", inversedBy="businesstypes", cascade={"persist"})
     * @ORM\JoinTable(name="businesstypes_activities")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $activities; 

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
     * @Gedmo\Timestampable(on="change", field={"name", "description", "deleted", "show_in_browse", "industries", "activities"})
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
     * @Gedmo\Timestampable(on="change", field={"name", "description", "deleted", "show_in_browse", "industries", "activities"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    public function __construct()
    {
        $this->industries = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->deleted = false;
    }

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
     * @return BusinessType
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
     * @return BusinessType
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
     * @return BusinessType
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
     * Set show_in_browse
     *
     * @param boolean $showInBrowse
     * @return BusinessType
     */
    public function setShowInBrowse($showInBrowse)
    {
        $this->show_in_browse = $showInBrowse;
    
        return $this;
    }

    /**
     * Get show_in_browse
     *
     * @return boolean 
     */
    public function getShowInBrowse()
    {
        return $this->show_in_browse;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BusinessType
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
     * @return BusinessType
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
     * @return BusinessType
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
     * Add industries
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry $industries
     * @return BusinessType
     */
    public function addIndustrie(\WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry $industries)
    {
        $this->industries[] = $industries;
    
        return $this;
    }

    /**
     * Remove industries
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry $industries
     */
    public function removeIndustrie(\WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry $industries)
    {
        $this->industries->removeElement($industries);
    }

    /**
     * Get industries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIndustries()
    {
        return $this->industries;
    }

    /**
     * Add activities
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities
     * @return BusinessType
     */
    public function addActivitie(\WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities)
    {
        $this->activities[] = $activities;
    
        return $this;
    }

    /**
     * Remove activities
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities
     */
    public function removeActivitie(\WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Set createdBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $createdBy
     * @return BusinessType
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
     * @return BusinessType
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
     * @return BusinessType
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