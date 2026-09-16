<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * WebmastersAfrica\LicenseBundle\Entity\LicenseStatute
 *
 * @ORM\Table(name="licensestatute")
 * @ORM\Entity
 */
class LicenseStatute implements Translatable
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
     * @var datetime $issued_on
     *
     * @ORM\Column(name="issued_on", type="date", nullable=true)
     */
    private $issued_on;

    /**
     * @var string $issued_by
     *
     * @ORM\Column(name="issued_by", type="string", length=255, nullable=true)
     */
    private $issued_by;

    /**
     * @var string $statute_no
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="statute_no", type="string", length=255, nullable=true)
     */
    private $statute_no;

    /**
     * @var date $valid_from
     *
     * @ORM\Column(name="valid_from", type="date", nullable=true)
     */
    private $valid_from;

    /**
     * @var date $valid_to
     *
     * @ORM\Column(name="valid_to", type="date", nullable=true)
     */
    private $valid_to;

    /**
     * @var boolean $deleted
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @var string $chapter
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="chapter", type="string", length=255, nullable=true)
     */
    private $chapter;

    /**
     * @var string $section
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="section", type="string", length=255, nullable=true)
     */
    private $section;

    /**
     * @var integer $license_id
     *
     * @ORM\Column(name="license_id", type="integer")
     */
    private $license_id;
    
    /**
    * @ORM\ManyToOne (targetEntity="BusinessLicense", inversedBy="statutes")
    * @ORM\JoinColumn(name="license_id", referencedColumnName="id")
    */
    protected $license;

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
     * @Gedmo\Timestampable(on="change", field={"name", "issued_on", "issued_by", "statute_no", "valid_from", "valid_to", "deleted", "chapter", "section", "license"})
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
     * @Gedmo\Timestampable(on="change", field={"name", "issued_on", "issued_by", "statute_no", "valid_from", "valid_to", "deleted", "chapter", "section", "license"})
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
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
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
     * @return LicenseStatute
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
     * Set issued_on
     *
     * @param \DateTime $issuedOn
     * @return LicenseStatute
     */
    public function setIssuedOn($issuedOn)
    {
        $this->issued_on = $issuedOn;
    
        return $this;
    }

    /**
     * Get issued_on
     *
     * @return \DateTime 
     */
    public function getIssuedOn()
    {
        return $this->issued_on;
    }

    /**
     * Set issued_by
     *
     * @param string $issuedBy
     * @return LicenseStatute
     */
    public function setIssuedBy($issuedBy)
    {
        $this->issued_by = $issuedBy;
    
        return $this;
    }

    /**
     * Get issued_by
     *
     * @return string 
     */
    public function getIssuedBy()
    {
        return $this->issued_by;
    }

    /**
     * Set statute_no
     *
     * @param string $statuteNo
     * @return LicenseStatute
     */
    public function setStatuteNo($statuteNo)
    {
        $this->statute_no = $statuteNo;
    
        return $this;
    }

    /**
     * Get statute_no
     *
     * @return string 
     */
    public function getStatuteNo()
    {
        return $this->statute_no;
    }

    /**
     * Set valid_from
     *
     * @param \DateTime $validFrom
     * @return LicenseStatute
     */
    public function setValidFrom($validFrom)
    {
        $this->valid_from = $validFrom;
    
        return $this;
    }

    /**
     * Get valid_from
     *
     * @return \DateTime 
     */
    public function getValidFrom()
    {
        return $this->valid_from;
    }

    /**
     * Set valid_to
     *
     * @param \DateTime $validTo
     * @return LicenseStatute
     */
    public function setValidTo($validTo)
    {
        $this->valid_to = $validTo;
    
        return $this;
    }

    /**
     * Get valid_to
     *
     * @return \DateTime 
     */
    public function getValidTo()
    {
        return $this->valid_to;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return LicenseStatute
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
     * Set chapter
     *
     * @param string $chapter
     * @return LicenseStatute
     */
    public function setChapter($chapter)
    {
        $this->chapter = $chapter;
    
        return $this;
    }

    /**
     * Get chapter
     *
     * @return string 
     */
    public function getChapter()
    {
        return $this->chapter;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return LicenseStatute
     */
    public function setSection($section)
    {
        $this->section = $section;
    
        return $this;
    }

    /**
     * Get section
     *
     * @return string 
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set license_id
     *
     * @param integer $licenseId
     * @return LicenseStatute
     */
    public function setLicenseId($licenseId)
    {
        $this->license_id = $licenseId;
    
        return $this;
    }

    /**
     * Get license_id
     *
     * @return integer 
     */
    public function getLicenseId()
    {
        return $this->license_id;
    }

    /**
     * Set license
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $license
     * @return LicenseStatute
     */
    public function setLicense(\WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $license = null)
    {
        $this->license = $license;
    
        return $this;
    }

    /**
     * Get license
     *
     * @return \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense 
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return LicenseStatute
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
     * @return LicenseStatute
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
     * @return LicenseStatute
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
     * Set createdBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $createdBy
     * @return LicenseStatute
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
     * @return LicenseStatute
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
     * @return LicenseStatute
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