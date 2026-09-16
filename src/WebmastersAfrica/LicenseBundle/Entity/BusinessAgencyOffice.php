<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice
 *
 * @ORM\Table(name="businessagencyoffice")
 * @ORM\Entity
 */
class BusinessAgencyOffice implements Translatable
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string $fax
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string $address
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string $telephone
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @Assert\Email(
     *      message = "The email '{{ value }}' is not a valid email.",
     * )
     */
    private $email;

    /**
     * @var string $website
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string $hours
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="hours", type="string", length=255, nullable=true)
     */
    private $hours;

    /**
     * @var boolean $is_main_location
     *
     * @ORM\Column(name="is_main_location", type="boolean")
     */
    private $is_main_location;

    /**
     * @var string $postal_address
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="postal_address", type="string", length=255, nullable=true)
     */
    private $postal_address;

    /**
     * @var float $latitude
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @Assert\Range(
     *      min = -10,
     *      minMessage = "You must be at least {{ limit }}"
     * )
     */
    private $latitude;

    /**
     * @var float $longitude
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     * @Assert\Range(
     *      min = -10,
     *      minMessage = "You must be at least {{ limit }}"
     * )
     */
    private $longitude;

    /**
     * @Assert\File(maxSize="60M")
     */
    private $file;

    /**
     * @var string $map_scan
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="map_scan", type="string", length=255, nullable=true)
     */
    private $map_scan;

    /**
     * @var integer $location_id
     *
     * @ORM\Column(name="location_id", type="integer")
     */
    private $location_id;

    /**
     * @var integer $agency_id
     *
     * @ORM\Column(name="agency_id", type="integer")
     */
    private $agency_id;

    /**
     * @ORM\ManyToOne (targetEntity="BusinessLocation", inversedBy="agencyoffices")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $location;

    /**
     * @ORM\ManyToOne (targetEntity="BusinessAgency", inversedBy="agencyoffices")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id")
     */
    protected $agency;

    /**
     * @var boolean $deleted
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

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
     * @Gedmo\Timestampable(on="change", field={"name", "fax", "address", "telephone", "email", "website", "hours", "postal_address", "location", "latitude", "longitude", "map_scan", "deleted", "agency", "is_main_location"})
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
     * @Gedmo\Timestampable(on="change", field={"name", "fax", "address", "telephone", "email", "website", "hours", "postal_address", "location", "latitude", "longitude", "map_scan", "deleted", "agency", "is_main_location"})
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
     * @return BusinessAgencyOffice
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
     * Set fax
     *
     * @param string $fax
     * @return BusinessAgencyOffice
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return BusinessAgencyOffice
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return BusinessAgencyOffice
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return BusinessAgencyOffice
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return BusinessAgencyOffice
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set hours
     *
     * @param string $hours
     * @return BusinessAgencyOffice
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return string 
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set is_main_location
     *
     * @param boolean $isMainLocation
     * @return BusinessAgencyOffice
     */
    public function setIsMainLocation($isMainLocation)
    {
        $this->is_main_location = $isMainLocation;

        return $this;
    }

    /**
     * Get is_main_location
     *
     * @return boolean 
     */
    public function getIsMainLocation()
    {
        return $this->is_main_location;
    }

    /**
     * Set postal_address
     *
     * @param string $postalAddress
     * @return BusinessAgencyOffice
     */
    public function setPostalAddress($postalAddress)
    {
        $this->postal_address = $postalAddress;

        return $this;
    }

    /**
     * Get postal_address
     *
     * @return string 
     */
    public function getPostalAddress()
    {
        return $this->postal_address;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return BusinessAgencyOffice
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return BusinessAgencyOffice
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set map_scan
     *
     * @param string $mapScan
     * @return BusinessAgencyOffice
     */
    public function setMapScan($mapScan = null)
    {
        $this->map_scan = $mapScan;
        return $this;
    }

    /**
     * Get map_scan
     *
     * @return string 
     */
    public function getMapScan()
    {
        return $this->map_scan;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set location_id
     *
     * @param integer $locationId
     * @return BusinessAgencyOffice
     */
    public function setLocationId($locationId)
    {
        $this->location_id = $locationId;

        return $this;
    }

    /**
     * Get location_id
     *
     * @return integer 
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Set agency_id
     *
     * @param integer $agencyId
     * @return BusinessAgencyOffice
     */
    public function setAgencyId($agencyId)
    {
        $this->agency_id = $agencyId;

        return $this;
    }

    /**
     * Get agency_id
     *
     * @return integer 
     */
    public function getAgencyId()
    {
        return $this->agency_id;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return BusinessAgencyOffice
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
     * Set location
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLocation $location
     * @return BusinessAgencyOffice
     */
    public function setLocation(\WebmastersAfrica\LicenseBundle\Entity\BusinessLocation $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \WebmastersAfrica\LicenseBundle\Entity\BusinessLocation 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set agency
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agency
     * @return BusinessAgencyOffice
     */
    public function setAgency(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agency = null)
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * Get agency
     *
     * @return \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency 
     */
    public function getAgency()
    {
        return $this->agency;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BusinessAgencyOffice
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
     * @return BusinessAgencyOffice
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
     * @return BusinessAgencyOffice
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
     * @return BusinessAgencyOffice
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
     * @return BusinessAgencyOffice
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
     * @return BusinessAgencyOffice
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

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->map_scan = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }


    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return getcwd() . "/" . $this->getUploadDir();
    }
}
