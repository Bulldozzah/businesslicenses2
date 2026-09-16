<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlanMainCategory;
use WebmastersAfrica\UserBundle\Entity\User;
use OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans;

/**
 * WebmastersAfrica\LicenseBundle\Entity\BusinessAgency
 *
 * @ORM\Table(name="businessagency")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyRepository")
 * @UniqueEntity("title")
 * @UniqueEntity("acronym")
 */
class BusinessAgency implements Translatable
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
     * @var string $title
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=True)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 250,
     *      minMessage = "Your agency title must be at least {{ limit }} characters long",
     *      maxMessage = "Your agency title cannot be longer than {{ limit }} characters"
     * )
     */
    private $title;

    /**
     * @var string $name
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $name
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="acronym", type="string", length=255, unique=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 12,
     *      maxMessage = "Your acronym title cannot be longer than {{ limit }} characters"
     * )
     */
    private $acronym;


    /**
     * @var string $fax
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 250,
     *      maxMessage = "Your fax cannot be longer than {{ limit }} characters"
     * )
     */
    private $fax;

    /**
     * @var string $address
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 250,
     *      maxMessage = "Your address cannot be longer than {{ limit }} characters"
     * )
     */
    private $address;

    /**
     * @var string $telephone
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 150,
     *      maxMessage = "Your telephone number cannot be longer than {{ limit }} characters"
     * )
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
     * @Assert\Length(
     *      max = 150,
     *      maxMessage = "Your website cannot be longer than {{ limit }} characters"
     * )
     */
    private $website;

    /**
     * @var string $hours
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="hours", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Your Hours cannot be longer than {{ limit }} characters"
     * )
     */
    private $hours;

    /**
     * @var string $postal_address
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="postal_address", type="string", length=255, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 250,
     *      minMessage = "The postal address {{ limit }} characters long",
     *      maxMessage = "The postal address cannot be longer than {{ limit }} characters"
     * )
     */
    private $postal_address;

    /**
     * @var integer $location_id
     *
     * @ORM\Column(name="location_id", type="integer")
     */
    private $location_id;

    /**
     * @var float $latitude
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @Assert\Range(
     *      min = -10,
     *      minMessage = "You must be at least {{ limit }}"
     * )
     *
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
     * @var string $map_scan
     *
     * @ORM\Column(name="map_scan", type="string")
     *
     */
    private $map_scan;

    /**
     * @var string $business_no
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="business_no", type="string", length=255, nullable=true)
     */
    private $business_no;

    /**
     * @var integer $boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @ORM\ManyToOne (targetEntity="BusinessLocation", inversedBy="agencies")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $location;

    /**
     * @ORM\OneToMany(targetEntity="BusinessAgencyOffice", mappedBy="agency",cascade={"persist"})
     */
    protected $agencyoffices;

    /**
     * @ORM\OneToMany(targetEntity="SubscriberList", mappedBy="agency")
     */
    protected $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="BusinessLicense", mappedBy="agency")
     */
    protected $licenses;

    /**
     * @ORM\OneToMany(targetEntity="OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation", mappedBy="agency")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $regulations;

    /**
     * @ORM\OneToMany(targetEntity="OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlanMainCategory", mappedBy="agency")
     */
    protected $forwardPlans;

    /**
     * @ORM\OneToMany(targetEntity="Feedback", mappedBy="agency")
     */
    protected $feedbacks;

    /**
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\UserBundle\Entity\User", mappedBy="agencies", fetch="EAGER")
     * @Assert\NotBlank(message="User field Required")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", mappedBy="agencies")
     */
    private $workflow;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(name="slug", length=250, unique=true)
     */
    private $slug;

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
     * @Gedmo\Timestampable(on="change", field={"name", "fax", "address", "telephone", "email", "website", "hours", "postal_address", "location", "latitude", "longitude", "map_scan", "business_no", "deleted", "agencyoffices", "feedbacks", "users"})
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
     * @Gedmo\Timestampable(on="change", field={"title", "name", "fax", "address", "telephone", "email", "website", "hours", "postal_address", "location", "latitude", "longitude", "map_scan", "business_no", "deleted", "agencyoffices", "feedbacks", "users"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    /**
     * @Assert\File(maxSize="20M")
     */
    private $file;

    /**
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry", mappedBy="agencies", fetch="EAGER")
     * @ORM\JoinTable(name="businessagency_industries")
     * @Assert\NotBlank(message="Industry field required")
     */
    private $industries;

    public function __construct()
    {
        $this->agencyoffices = new ArrayCollection();
        $this->feedbacks = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->regulations = new ArrayCollection();
        $this->forwardPlans = new ArrayCollection();
        $this->deleted = false;
        $this->workflow = new ArrayCollection();
        $this->industries = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
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
     * @return BusinessAgency
     */
    public function setTitle($title)
    {
        $this->title = $title;
        $this->name = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return BusinessAgency
     */
    public function setName($name = null)
    {
        $this->name = $this->getTitle();

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return BusinessAgency
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
     * Set Abbr
     *
     * @param string $agency
     * @return BusinessAgency
     */
    public function setAcronym($acronym)
    {
        $this->acronym = $acronym;

        return $this;
    }

    /**
     * Get Abbre
     *
     * @return string
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * Set postal_address
     *
     * @param string $postalAddress
     * @return BusinessAgency
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
     * Set location_id
     *
     * @param integer $locationId
     * @return BusinessAgency
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
     * Set latitude
     *
     * @param float $latitude
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * Set business_no
     *
     * @param string $businessNo
     * @return BusinessAgency
     */
    public function setBusinessNo($businessNo)
    {
        $this->business_no = $businessNo;

        return $this;
    }

    /**
     * Get business_no
     *
     * @return string
     */
    public function getBusinessNo()
    {
        return $this->business_no;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return BusinessAgency
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
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

    public function upload($fileName)
    {
        // the file property can be empty if the field is not required
        if (null ===  $this->file) {
            return;
        }

        // set the path property to the filename where you've saved the file
        $this->map_scan =  $fileName;

        // clean up the file property as you won't need it anymore
        $this->file = null;
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
     * @return BusinessAgency
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
     * Add agencyoffices
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices
     * @return BusinessAgency
     */
    public function addAgencyOffice(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices)
    {
        $agencyoffices->setAgency($this);
        $agencyoffices->upload();

        $this->agencyoffices[] = $agencyoffices;

        return $this;
    }

    /**
     * Remove agencyoffices
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices
     */
    public function removeAgencyOffice(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice $agencyoffices)
    {
        $this->agencyoffices->removeElement($agencyoffices);
        return $this;
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
     * Add feedbacks
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\Feedback $feedbacks
     * @return BusinessAgency
     */
    public function addFeedback(\WebmastersAfrica\LicenseBundle\Entity\Feedback $feedbacks)
    {
        $this->feedbacks[] = $feedbacks;

        return $this;
    }

    /**
     * Remove feedbacks
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\Feedback $feedbacks
     */
    public function removeFeedback(\WebmastersAfrica\LicenseBundle\Entity\Feedback $feedbacks)
    {
        $this->feedbacks->removeElement($feedbacks);
    }

    /**
     * Get feedbacks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeedbacks()
    {
        return $this->feedbacks;
    }

    /**
     * Add users
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $users
     * @return BusinessAgency
     */
    public function addUser(User $users)
    {
        $this->users->add($users);
        $users->addAgencies($this);
        return $this;
    }

    /**
     * Remove users
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }
    /**
     * Add Workflow
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\Workflow $workflow
     * @return Workflow
     */
    public function addWorkflow(\WebmastersAfrica\LicenseBundle\Entity\Workflow $workflow)
    {
        $this->workflow[] = $workflow;

        return $this;
    }

    /**
     * Remove workflow
     *
     * @param \\WebmastersAfrica\LicenseBundle\Entity\Workflow
     */
    public function removeWorkflow(\WebmastersAfrica\LicenseBundle\Entity\Workflow $workflow)
    {
        $this->workflow->removeElement($workflow);
    }

    /**
     * Add Workflow
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry $industries
     * @return Workflow
     */
    // public function addIndustries(\WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry $industries)
    // {
    //     $this->industries->add($industries);
    //     $industries->addBusinessAgency($this);
    //     return $this;
    // }

    /**
     * Remove workflow
     *
     * @param \\WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry
     */
    // public function removeIndustries(\WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry $industries)
    // {
    //     $this->industries->removeElement($industries);
    // }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * @return BusinessAgency
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
     * Add licenses
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses
     * @return BusinessAgency
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

    /**
     * Get subscribers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * Get licenses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegulations()
    {
        return $this->regulations;
    }

    /**
     * Get Forward Plans
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getForwardPlans()
    {
        return $this->forwardPlans;
    }

    public function getForwardPlansCount()
    {
        $value = $this->getForwardPlans()->filter(
            function (ForwardPlanMainCategory $forwardPlans) {
                return $forwardPlans->getForwardPlans()->filter(
                    function (ForwardPlans $forwardPlanRegulation) {
                        return  $forwardPlanRegulation->getPublished() == true;
                    }
                );
            }
        );
        return $value;
    }


    /**
     * Get licenses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Get licenses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    // public function getIndustries()
    // {
    //     return $this->industries;
    // }
}
