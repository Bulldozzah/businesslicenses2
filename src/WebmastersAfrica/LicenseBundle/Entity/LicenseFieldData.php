<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Webmasters\LicenseBundle\Entity\LicenseFieldData
 *
 * @ORM\Table(name="licensefielddata")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\LicenseFieldDataRepository")
 */
class LicenseFieldData implements Translatable
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
     * @var text $fielddata
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="fielddata", type="text")
     */
    private $fielddata;

    /**
     * @var integer $license_id
     *
     * @ORM\Column(name="license_id", type="integer")
     */
    private $license_id;

    /**
     * @var integer $field_id
     *
     * @ORM\Column(name="field_id", type="integer")
     */
    private $field_id;
    
     /**
     * @ORM\ManyToOne(targetEntity="BusinessLicense", inversedBy="fielddatas")
     * @ORM\JoinColumn(name="license_id", referencedColumnName="id")
     */
    protected $license;
    
     /**
     * @ORM\ManyToOne(targetEntity="LicenseField", inversedBy="fielddatas")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    protected $field;

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
     * @Gedmo\Timestampable(on="change", field={"fielddata", "license", "field"})
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
     * @Gedmo\Timestampable(on="change", field={"fielddata", "license", "field"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

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
     * Set fielddata
     *
     * @param string $fielddata
     * @return LicenseFieldData
     */
    public function setFielddata($fielddata)
    {
        $this->fielddata = $fielddata;
    
        return $this;
    }

    /**
     * Get fielddata
     *
     * @return string 
     */
    public function getFielddata()
    {
        return $this->fielddata;
    }

    /**
     * Set license_id
     *
     * @param integer $licenseId
     * @return LicenseFieldData
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
     * Set field_id
     *
     * @param integer $fieldId
     * @return LicenseFieldData
     */
    public function setFieldId($fieldId)
    {
        $this->field_id = $fieldId;
    
        return $this;
    }

    /**
     * Get field_id
     *
     * @return integer 
     */
    public function getFieldId()
    {
        return $this->field_id;
    }

    /**
     * Set license
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $license
     * @return LicenseFieldData
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
     * Set field
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\LicenseField $field
     * @return LicenseFieldData
     */
    public function setField(\WebmastersAfrica\LicenseBundle\Entity\LicenseField $field = null)
    {
        $this->field = $field;
    
        return $this;
    }

    /**
     * Get field
     *
     * @return \WebmastersAfrica\LicenseBundle\Entity\LicenseField 
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return LicenseFieldData
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
     * @return LicenseFieldData
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
     * @return LicenseFieldData
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
     * @return LicenseFieldData
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
     * @return LicenseFieldData
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
     * @return LicenseFieldData
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