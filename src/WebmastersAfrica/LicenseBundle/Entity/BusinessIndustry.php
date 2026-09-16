<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation;

/**
 * WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry
 *
 * @ORM\Table(name="businessindustry")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\BusinessIndustryRepository")
 */
class BusinessIndustry implements Translatable
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
     * @ORM\ManyToMany(targetEntity="BusinessType", inversedBy="industries", cascade={"persist"})
     * @ORM\JoinTable(name="businesstypes_industries")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $businesstypes;

    /**
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessAgency", inversedBy="industries", cascade={"persist"})
     * @ORM\JoinTable(name="businessagency_industries")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $agencies;

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
     * @Gedmo\Timestampable(on="change", field={"name", "description", "deleted", "show_in_browse", "businesstypes"})
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
     * @var regulations
     *
     *
     * @ORM\OneToMany(targetEntity="OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation", mappedBy="industry", cascade={"persist"})
     */
    private $regulation;

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
     * @Gedmo\Timestampable(on="change", field={"name", "description", "deleted", "show_in_browse", "businesstypes"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    public function __construct()
    {
        $this->businesstypes = new ArrayCollection();
        $this->agencies = new ArrayCollection();
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->deleted = false;
        $this->show_in_browse = false;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @ORM\OneToMany(targetEntity="\OTB\Bundle\NoticeAndCommentBundle\Entity\SubscriberList", mappedBy="industry_id")
     */
    protected $subscribers;
    

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
     * @return BusinessIndustry
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
     * @return BusinessIndustry
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

    public function getRegulation()
    {
        return $this->regulation;
    }

    public function getRegulationCount()
    {
        return
        $this->getRegulation()->filter(function (Regulation $regulation) {
            return (int)$regulation->getPublished() == 1 && (int)$regulation->getIsPublic() == 1 && (int)$regulation->getDeleted() == 0;
        });
    }
    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return BusinessIndustry
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
     * @return BusinessIndustry
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
     * @return BusinessIndustry
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
     * @return BusinessIndustry
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
     * @return BusinessIndustry
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
     * Add businesstypes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes
     * @return BusinessIndustry
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
     * Get businesstypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinesstypes()
    {
        return $this->businesstypes;
    }

    public function getAgencies()
    {
        return $this->agencies;
    }

    /**
     * Add businesstypes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes
     * @return BusinessIndustry
     */
    public function addBusinessAgencies(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $businessAgency)
    {
        $this->businessAgency[] = $businessAgency;

        return $this;
    }

    /**
     * Remove businesstypes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessType $businesstypes
     */
    public function removeBusinessAgencies(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $businessAgency)
    {
        $this->businessAgency->removeElement($businessAgency);
    }

    /**
     * Get businesstypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessAgency()
    {
        return $this->businessAgency;
    }

    /**
     * Set createdBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $createdBy
     * @return BusinessIndustry
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
     * @return BusinessIndustry
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
     * @return BusinessIndustry
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

    /**
     * Get subscribers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }
}
