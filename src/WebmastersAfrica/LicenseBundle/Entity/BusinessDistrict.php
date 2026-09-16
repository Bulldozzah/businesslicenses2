<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * WebmastersAfrica\LicenseBundle\Entity\BusinessLocation
 *
 * @ORM\Table(name="businesslocation")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\BusinessDistrictRepository")
 */
class BusinessDistrict implements Translatable
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
     * @var boolean $is_default
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    private $is_default;

    /**
     * @var boolean $deleted
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @ORM\OneToMany(targetEntity="BusinessLicense", mappedBy="location")
     */
    protected $licenses;

    /**
     * @ORM\OneToMany(targetEntity="BusinessAgencyOffice", mappedBy="location")
     */
    protected $agencyoffices;

    /**
     * @ORM\OneToMany(targetEntity="BusinessAgency", mappedBy="location")
     */
    protected $agencies;

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
     * @Gedmo\Timestampable(on="change", field={"name", "is_default", "deleted", "licenses", "agencyoffices", "agencies"})
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
     * @Gedmo\Timestampable(on="change", field={"name", "is_default", "deleted", "licenses", "agencyoffices", "agencies"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    public function __construct()
    {
        $this->licenses = new ArrayCollection();
        $this->agencies = new ArrayCollection();
        $this->agencyoffices = new ArrayCollection();
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
     * @return BusinessLocation
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
     * Set is_default
     *
     * @param boolean $isDefault
     * @return BusinessLocation
     */
    public function setIsDefault($isDefault)
    {
        $this->is_default = $isDefault;

        return $this;
    }

    /**
     * Get is_default
     *
     * @return boolean 
     */
    public function getIsDefault()
    {
        return $this->is_default;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return BusinessLocation
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
     * Add licenses
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses
     * @return BusinessLocation
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
                return $licenses->getStatus() == 'published' && $licenses->getDeleted() == 0;
            }
        );
    }

    /**
     * Add agencyoffices
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices
     * @return BusinessLocation
     */
    public function addAgencyoffice(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices)
    {
        $this->agencyoffices[] = $agencyoffices;

        return $this;
    }

    /**
     * Remove agencyoffices
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices
     */
    public function removeAgencyoffice(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices)
    {
        $this->agencyoffices->removeElement($agencyoffices);
    }

    /**
     * Get agencyoffices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgencyoffices()
    {
        return $this->agencyoffices;
    }

    /**
     * Add agencies
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencies
     * @return BusinessLocation
     */
    public function addAgencie(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencies)
    {
        $this->agencies[] = $agencies;

        return $this;
    }

    /**
     * Remove agencies
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencies
     */
    public function removeAgencie(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencies)
    {
        $this->agencies->removeElement($agencies);
    }

    /**
     * Get agencies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAgencies()
    {
        return $this->agencies;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BusinessLocation
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
     * @return BusinessLocation
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
     * @return BusinessLocation
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
     * @return BusinessLocation
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
     * @return BusinessLocation
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
     * @return BusinessLocation
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
