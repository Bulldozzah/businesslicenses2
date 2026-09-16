<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;
use WebmastersAfrica\LicenseBundle\Entity\SubsidiaryLegislationAttachments;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WebmastersAfrica\LicenseBundle\Entity\BusinessLicense
 *
 * @ORM\Table(name="businesslicense")
 * @ORM\Entity(
 *      repositoryClass="WebmastersAfrica\LicenseBundle\Entity\BusinessLicenseRepository"
 * )
 * @UniqueEntity("name")
 * @Gedmo\Loggable
 */
class BusinessLicense implements Translatable
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
     * @Gedmo\Versioned
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Business License can't be blank")
     * @Assert\Length(
     *      min = 2,
     *      max = 250,
     *      minMessage = "Your license title must be at least {{ limit }} characters long",
     *      maxMessage = "Your license title cannot be longer than {{ limit }} characters"
     * )
     */
    private $name;

    /**
     * @var text $keywords
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="keywords", type="text", nullable=true)
     */
    private $keywords;

    /**
     * @var text $purpose
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="purpose", type="text", nullable=true)
     */
    private $purpose;

    /**
     * @var text $description
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer $agency_id
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="agency_id", type="integer", nullable=true)
     */
    private $agency_id;

    /**
     * @var text $comments
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    private $comments;

    /**
     * @var string $license_no
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="license_no", type="string", length=255, nullable=true)
     */
    private $license_no;

    /**
     * @var string $application_fee
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="application_fee", type="string", length=255, nullable=true)
     */
    private $application_fee;

    /**
     * @var string $license_fee
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="license_fee", type="string", length=1055, nullable=true)
     */
    private $license_fee;

    /**
     * @var string $max_processing_time
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="max_processing_time", type="string", length=255, nullable=true)
     */
    private $max_processing_time;

    /**
     * @var datetime $gazetted_on
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="gazetted_on", type="datetime", nullable=true)
     */
    private $gazetted_on;

    /**
     * @var text $related_websites
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="related_websites", type="text", nullable=true)
     */
    private $related_websites;

    /**
     * @var string $validity
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="validity", type="string", length=255, nullable=true)
     */
    private $validity;

    /**
     * @var datetime $enactment
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="enactment", type="datetime", nullable=true)
     */
    private $enactment;

    /**
     * @var string $gazetting_ref
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="gazetting_ref", type="string", length=255, nullable=true)
     */
    private $gazetting_ref;

    /**
     * @var text $contact_office
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="contact_office", type="text", nullable=true)
     */
    private $contact_office;

    /**
     * @var text $resolution_criteria
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="resolution_criteria", type="text", nullable=true)
     */
    private $resolution_criteria;

    /**
     * @var string $status
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var boolean $deleted
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @var boolean $universal
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="universal", type="boolean", nullable=true)
     */
    private $universal;

    /**
     * @var integer $views
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="views", type="integer", nullable=true)
     */
    private $views;

    /**
     * @var integer $location_id
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="location_id", type="integer")
     */
    private $location_id;

    /**
     * @ORM\ManyToOne (targetEntity="BusinessLocation", inversedBy="licenses")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     */
    protected $location;

    /**
     * @ORM\ManyToOne (targetEntity="BusinessAgency", inversedBy="licenses")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id")
     */
    protected $agency;

    /**
     * @ORM\ManyToMany(targetEntity="BusinessActivity", inversedBy="licenses", cascade={"persist"})
     * @ORM\JoinTable(name="licenses_activities")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="LicenseStatute", mappedBy="license")
     */
    protected $statutes;

    /**
     * @ORM\OneToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\ApplicationHistory", mappedBy="application")
     */
    protected $history;

    /**
     * @ORM\OneToMany(targetEntity="LicenseDownload", mappedBy="license",cascade={"persist"})
     */
    protected $downloads;

    /**
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="requirements", type="text", nullable=true)
     */
    protected $requirements;

    /**
     * @ORM\OneToMany(targetEntity="BusinessLocation", mappedBy="license")
     */
    protected $fielddatas;

    /**
     * @ORM\OneToMany(targetEntity="WebmastersAfrica\TaskBundle\Entity\Task", mappedBy="license")
     */
    protected $tasks;

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
     * @Gedmo\Timestampable(on="change", field={"name", "keywords", "purpose", "description", "agency", "comments", "license_no", "application_fee", "license_fee", "max_processing_time", "gazetted_on", "related_websites", "validity", "enactment", "gazetting_ref", "contact_office", "resolution_criteria", "status", "deleted", "universal", "views", "location", "activities", "statutes", "downloads", "fielddatas", "requirements"})
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
     * @Gedmo\Timestampable(on="change", field={"name", "keywords", "purpose", "description", "agency", "comments", "license_no", "application_fee", "license_fee", "max_processing_time", "gazetted_on", "related_websites", "validity", "enactment", "gazetting_ref", "contact_office", "resolution_criteria", "status", "deleted", "universal", "views", "location", "activities", "statutes", "downloads", "fielddatas", "requirements", })
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", fetch="EAGER", inversedBy="licenses")
     * * @ORM\JoinColumn(name="stage_id", referencedColumnName="id", nullable=false)
     */
    private $stage;

    /**
     * @var integer
     * 
     * @ORM\Column(name="slug", type="string")
     */

    private $slug;

    /**
     * @var text $principle_legislation
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="principle_legislation", type="text", nullable=false)
     * 
     */
    private $principle_legislation;


    /**
     * @ORM\Column(name="principle_legislation_attachment", type="string")
     */
    private $principle_legislation_attachment;

    /**
     * @var files $subsidiary_legislation
     *
     * @ORM\OneToMany(targetEntity="SubsidiaryLegislationAttachments", mappedBy="businessLicense", cascade={"persist"})
     */
    private $subsidiary_legislation;

    /**
     * @Assert\File(maxSize="20M")
     */
    private $file;


    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->downloads = new ArrayCollection();
        $this->subsidiary_legislation = new ArrayCollection();
        $this->fielddatas = new ArrayCollection();
        $this->statutes = new ArrayCollection();
        $this->tasks = new ArrayCollection();
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
     * Get Slug
     *
     * @return integer 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set SLug
     *
     * @param string $slug
     * @return Regulation
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BusinessLicense
     */
    public function setName($name)
    {
        $this->name = $name;

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
        $this->principle_legislation_attachment =  $fileName;

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
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return BusinessLicense
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set purpose
     *
     * @param string $purpose
     * @return BusinessLicense
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;

        return $this;
    }

    /**
     * Get purpose
     *
     * @return string 
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return BusinessLicense
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
     * Set Principle Legislation
     *
     * @param string $description
     * @return BusinessLicense
     */
    public function setPrincipleLegislation($principle_legislation)
    {
        $this->principle_legislation = $principle_legislation;

        return $this;
    }

    /**
     * Get Principle Legislation
     *
     * @return string
     */
    public function getPrincipleLegislation()
    {
        return $this->principle_legislation;
    }
    /**
     * Set Principle Legislation
     *
     * @param string $description
     * @return BusinessLicense
     */
    public function setPrincipleLegislationAttachment($principle_legislation_attachment = null)
    {
        $this->principle_legislation_attachment = $principle_legislation_attachment;

        return $this;
    }

    /**
     * Get Principle Legislation
     *
     * @return string
     */
    public function getPrincipleLegislationAttachment()
    {
        return $this->principle_legislation_attachment;
    }

    /**
     * Get Subsidiary Legislation
     *
     * @return string
     */
    public function getSubsidiaryLegislation()
    {
        return $this->subsidiary_legislation;
    }

    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set agency_id
     *
     * @param integer $agencyId
     * @return BusinessLicense
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
     * Set comments
     *
     * @param string $comments
     * @return BusinessLicense
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set license_no
     *
     * @param string $licenseNo
     * @return BusinessLicense
     */
    public function setLicenseNo($licenseNo)
    {
        $this->license_no = $licenseNo;

        return $this;
    }

    /**
     * Get license_no
     *
     * @return string 
     */
    public function getLicenseNo()
    {
        return $this->license_no;
    }

    /**
     * Set application_fee
     *
     * @param string $applicationFee
     * @return BusinessLicense
     */
    public function setApplicationFee($applicationFee)
    {
        $this->application_fee = $applicationFee;

        return $this;
    }

    /**
     * Get application_fee
     *
     * @return string 
     */
    public function getApplicationFee()
    {
        return $this->application_fee;
    }

    /**
     * Set license_fee
     *
     * @param string $licenseFee
     * @return BusinessLicense
     */
    public function setLicenseFee($licenseFee)
    {
        $this->license_fee = $licenseFee;

        return $this;
    }

    /**
     * Get license_fee
     *
     * @return string 
     */
    public function getLicenseFee()
    {
        return $this->license_fee;
    }

    /**
     * Set max_processing_time
     *
     * @param string $maxProcessingTime
     * @return BusinessLicense
     */
    public function setMaxProcessingTime($maxProcessingTime)
    {
        $this->max_processing_time = $maxProcessingTime;

        return $this;
    }

    /**
     * Get max_processing_time
     *
     * @return string 
     */
    public function getMaxProcessingTime()
    {
        return $this->max_processing_time;
    }

    /**
     * Set gazetted_on
     *
     * @param \DateTime $gazettedOn
     * @return BusinessLicense
     */
    public function setGazettedOn($gazettedOn)
    {
        $this->gazetted_on = $gazettedOn;

        return $this;
    }

    /**
     * Get gazetted_on
     *
     * @return \DateTime 
     */
    public function getGazettedOn()
    {
        return $this->gazetted_on;
    }

    /**
     * Set related_websites
     *
     * @param string $relatedWebsites
     * @return BusinessLicense
     */
    public function setRelatedWebsites($relatedWebsites)
    {
        $this->related_websites = $relatedWebsites;

        return $this;
    }

    /**
     * Get related_websites
     *
     * @return string 
     */
    public function getRelatedWebsites()
    {
        return $this->related_websites;
    }

    /**
     * Set validity
     *
     * @param string $validity
     * @return BusinessLicense
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Get validity
     *
     * @return string 
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set enactment
     *
     * @param \DateTime $enactment
     * @return BusinessLicense
     */
    public function setEnactment($enactment)
    {
        $this->enactment = $enactment;

        return $this;
    }

    /**
     * Get enactment
     *
     * @return \DateTime 
     */
    public function getEnactment()
    {
        return $this->enactment;
    }

    /**
     * Set gazetting_ref
     *
     * @param string $gazettingRef
     * @return BusinessLicense
     */
    public function setGazettingRef($gazettingRef)
    {
        $this->gazetting_ref = $gazettingRef;

        return $this;
    }

    /**
     * Get gazetting_ref
     *
     * @return string 
     */
    public function getGazettingRef()
    {
        return $this->gazetting_ref;
    }

    /**
     * Set contact_office
     *
     * @param string $contactOffice
     * @return BusinessLicense
     */
    public function setContactOffice($contactOffice)
    {
        $this->contact_office = $contactOffice;

        return $this;
    }

    /**
     * Get contact_office
     *
     * @return string 
     */
    public function getContactOffice()
    {
        return $this->contact_office;
    }

    /**
     * Set resolution_criteria
     *
     * @param string $resolutionCriteria
     * @return BusinessLicense
     */
    public function setResolutionCriteria($resolutionCriteria)
    {
        $this->resolution_criteria = $resolutionCriteria;

        return $this;
    }

    /**
     * Get resolution_criteria
     *
     * @return string 
     */
    public function getResolutionCriteria()
    {
        return $this->resolution_criteria;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return BusinessLicense
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusDescription()
    {
        $status = $this->getStatus();
        if ($status == 3) {
            return "in review";
        }
        if ($status == 5) {
            return "Make Corrections";
        }
        if ($status == 2) {
            return "Saved As A draft";
        }
        if ($status == 4) {
            return "unpublished";
        }
        if ($status == 1) {
            return "published";
        }
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return BusinessLicense
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
     * Set universal
     *
     * @param boolean $universal
     * @return BusinessLicense
     */
    public function setUniversal($universal)
    {
        $this->universal = $universal;

        return $this;
    }

    /**
     * Get universal
     *
     * @return boolean 
     */
    public function getUniversal()
    {
        return $this->universal;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return BusinessLicense
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set order_no
     *
     * @param integer $order_no
     * @return BusinessLicense
     */
    public function setOrderNo($order_no)
    {
        $this->order_no = $order_no;

        return $this;
    }

    /**
     * Get order_no
     *
     * @return integer 
     */
    public function getOrderNo()
    {
        return $this->order_no;
    }

    /**
     * Set location_id
     *
     * @param integer $locationId
     * @return BusinessLicense
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
     * Set location
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLocation $location
     * @return BusinessLicense
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
     * @return BusinessLicense
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
     * Add activities
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities
     * @return BusinessLicense
     */
    public function addActivities(\WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities
     */
    public function removeActivities(\WebmastersAfrica\LicenseBundle\Entity\BusinessActivity $activities)
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
     * Add statutes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\LicenseStatute $statutes
     * @return BusinessLicense
     */
    public function addStatute(\WebmastersAfrica\LicenseBundle\Entity\LicenseStatute $statutes)
    {
        $this->statutes[] = $statutes;

        return $this;
    }

    /**
     * Remove statutes
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\LicenseStatute $statutes
     */
    public function removeStatute(\WebmastersAfrica\LicenseBundle\Entity\LicenseStatute $statutes)
    {
        $this->statutes->removeElement($statutes);
    }

    /**
     * Get statutes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStatutes()
    {
        return $this->statutes;
    }

    /**
     * Add downloads
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\LicenseDownload $downloads
     * @return BusinessLicense
     */
    public function addDownload(\WebmastersAfrica\LicenseBundle\Entity\LicenseDownload $downloads)
    {
        $downloads->setLicense($this);
        $downloads->upload();

        $this->downloads[] = $downloads;

        return $this;
    }

    /**
     * Remove downloads
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\LicenseDownload $downloads
     */
    public function removeDownload(\WebmastersAfrica\LicenseBundle\Entity\LicenseDownload $downloads)
    {
        $this->downloads->removeElement($downloads);
    }

    /**
     * Get downloads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * Add fielddatas
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\LicenseFieldData $fielddatas
     * @return BusinessLicense
     */
    public function addFielddata(\WebmastersAfrica\LicenseBundle\Entity\LicenseFieldData $fielddatas)
    {
        $this->fielddatas[] = $fielddatas;

        return $this;
    }

    /**
     * Remove fielddatas
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\LicenseFieldData $fielddatas
     */
    public function removeFielddata(\WebmastersAfrica\LicenseBundle\Entity\LicenseFieldData $fielddatas)
    {
        $this->fielddatas->removeElement($fielddatas);
    }

    /**
     * Get fielddatas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFielddatas()
    {
        return $this->fielddatas;
    }

    /**
     * Get requirements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Set requierments
     *
     * @param string
     * @return string
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }


    /**
     * Set created
     *
     * @param \DateTime $created
     * @return BusinessLicense
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
     * @return BusinessLicense
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
     * @return BusinessLicense
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
     * @return BusinessLicense
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
     * @return BusinessLicense
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
     * @return BusinessLicense
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
     * Add tasks
     *
     * @param \WebmastersAfrica\TaskBundle\Entity\Task $tasks
     * @return BusinessLicense
     */
    public function addTask(\WebmastersAfrica\TaskBundle\Entity\Task $tasks)
    {
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \WebmastersAfrica\TaskBundle\Entity\Task $tasks
     */
    public function removeTask(\WebmastersAfrica\TaskBundle\Entity\Task $tasks)
    {
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function getStage()
    {
        return $this->stage;
    }

    public function setStage(Workflow $stage = null)
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * Add attachment
     *
     * @param SubsidiaryLegislationAttachments $attachment
     *
     * @return Folder
     */
    public function addSubsidiaryLegislation(SubsidiaryLegislationAttachments $subsidiary_legislation)
    {
        $subsidiary_legislation->setBusinessLicense($this);
        $subsidiary_legislation->upload();
        $this->subsidiary_legislation[] = $subsidiary_legislation;
        return $this;
    }

    /**
     * Remove attachment
     *
     * @param SubsidiaryLegislationAttachments $attachment
     */
    public function removeSubsidiaryLegislation(SubsidiaryLegislationAttachments $subsidiary_legislation)
    {
        $this->subsidiary_legislation->removeElement($subsidiary_legislation);
    }
}
