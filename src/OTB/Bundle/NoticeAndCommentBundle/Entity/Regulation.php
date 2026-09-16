<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Regulation
 *
 * @ORM\Table(name="regulations")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationRepository")
 * @UniqueEntity("title")
 * @Gedmo\Loggable
 * @Vich\Uploadable
 */
class Regulation
{
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="regulation")
     * @ORM\OrderBy({"createAt" = "DESC"})
     */
    private $comments;
    /**
     * @ORM\OneToMany(targetEntity="Position", mappedBy="regulation")
     */
    private $position;

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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="Regulation Title Required")
     * @Assert\Length(
     *      min = 2,
     *      max = 250,
     *      minMessage = "Your regulation title must be at least {{ limit }} characters long",
     *      maxMessage = "Your regulation title cannot be longer than {{ limit }} characters"
     * )
     * @Gedmo\Versioned
     *
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     * @Gedmo\Versioned
     *
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="expected_outcome", type="text")
     * @Gedmo\Versioned
     *
     */
    private $expectedOutcome;

    /**
    * @ORM\Column(name="documents", type="string", length=255)
    */
    private $document;

    /**
     *
     * @Vich\UploadableField(mapping="main_document", fileNameProperty="document", size="documentSize")
     *
     */
    private $file;

    /**
     * @ORM\Column(type="integer", name="document_size")
     *
     * @var integer
     */
    private $documentSize;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publish_date", type="datetime")
     * @Gedmo\Versioned
     */
    private $publishDate;

    /**
     * @var string
     *
     * @ORM\Column(name="closing_date", type="text")
     * @Gedmo\Versioned
     */
    private $closingDate;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessAgency", inversedBy="regulations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Versioned
     */
    private $agency;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\Versioned
     */
    private $industry;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 250,
     *      minMessage = "Your tag list must be at least {{ limit }} characters long",
     *      maxMessage = "Your tag list cannot be longer than {{ limit }} characters"
     * )
     * @Gedmo\Versioned
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 250,
     *      minMessage = "Your keywords list list must be at least {{ limit }} characters long",
     *      maxMessage = "Your keywords list cannot be longer than {{ limit }} characters Long"
     * )
     * @Gedmo\Versioned
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="specific_instructions", type="text")
     * @Gedmo\Versioned
     */
    private $specificInstructions;

    /**
     * @var string
     *
     * @ORM\Column(name="offline_consultations", type="text")
     * @Gedmo\Versioned
     */
    private $offlineConsultations;

    /**
     * @var string
     *
     * @ORM\Column(name="supporting_materials", type="text")
     * @Gedmo\Versioned
     */
    private $supportingMaterials;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_login_required", type="boolean")
     * @Gedmo\Versioned
     */
    private $isLoginRequired = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     * @Gedmo\Versioned
     */
    private $deleted = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_public", type="boolean")
     * @Gedmo\Versioned
     */
    private $isPublic = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="consultation_stage", type="integer")
     * @Gedmo\Versioned
     */

    private $consultationStage;

    /**
     * @var integer
     *
     * @ORM\Column(name="main_highlight", type="integer")
     * @Gedmo\Versioned
     */

    private $mainHighlight = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="regulation_type", type="integer")
     * @Gedmo\Versioned
     */

    private $regulationType = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="published", type="boolean")
     * @Gedmo\Versioned
     */
    private $published =  true;

    /**
     * @var integer
     *
     * @ORM\Column(name="checking_closed", type="integer")
     * @Gedmo\Versioned
     */

    private $checkClosing = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string")
     * @Gedmo\Versioned
     */
    private $slug;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="RegulationAttachments", mappedBy="regulation", cascade={"persist"})
     *
     */
    private $supportingAttachments;

    /**
     * @var integer
     * @ORM\Column(name="review_comments", type="boolean")
     * @Gedmo\Versioned
     *
     */
    private $isReviewComments = false;

    /**
     * @var integer
     * @ORM\Column(name="enable_attachments", type="boolean")
     * @Gedmo\Versioned
     *
     */
    private $isAttachmentEnabled = true;

    /**
     * @var string
     * @ORM\Column(name="file_type", type="string")
     * @Gedmo\Versioned
     *
     */
    private $fileType = "pdf, docs, jpg, png, jpeg,txt, docx";


    /**
     * @var integer
     * @ORM\Column(name="file_size", type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 10000,
     *      minMessage = "File must be at least {{ limit }} MB",
     *      maxMessage = "File Cannot be more than {{ limit }} MB"
     * )
     * @Gedmo\Versioned
     *
     */
    private $fileSize = 1;

    /**
     * @ORM\OneToMany(targetEntity="RegulationFieldData", mappedBy="regulation", cascade={"persist"})
     */
    protected $fielddatas;

    /**
     * @ORM\OneToMany(targetEntity="Survey", mappedBy="regulation")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $surveys;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->position = new ArrayCollection();
        $this->supportingAttachments = new ArrayCollection();
        $this->fielddatas = new ArrayCollection();
        $this->surveys = new ArrayCollection();
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
     * Get Supporting Attachments
     *
     * @return integer
     */
    public function getSupportingAttachments()
    {
        return $this->supportingAttachments;
    }

    /**
     * Get Surveys this regulations
     *
     * @return ArrayCollection|Survey[]
     **/
    public function getSurveys()
    {
        return $this->surveys;
    }

    /**
     * Add Supporting attachment
     *
     * @param ForwardPlansAttachments $attachment
     *
     * @return Folder
     */
    public function addSupportingAttachment(RegulationAttachments $supportingAttachments)
    {
        // Bidirectional Ownership
        $supportingAttachments->setRegulation($this);
        $supportingAttachments->upload();
        $this->supportingAttachments[] = $supportingAttachments;

        return $this;
    }

    /**
     * Remove Supporting attachment
     *
     * @param ForwardPlansAttachments $attachment
     */
    public function removeSupportingAttachment(RegulationAttachments $supporting_attachments)
    {
        $this->supportingAttachments->removeElement($supporting_attachments);
    }
    /**
     * Get id
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
     * Set title
     *
     * @param string $title
     * @return Regulation
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * Set description
     *
     * @param string $description
     * @return Regulation
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
     * Set expectedOutcome
     *
     * @param string $expectedOutcome
     * @return Regulation
     */
    public function setExpectedOutcome($expectedOutcome)
    {
        $this->expectedOutcome = $expectedOutcome;

        return $this;
    }

    /**
     * Get expectedOutcome
     *
     * @return string
     */
    public function getExpectedOutcome()
    {
        return $this->expectedOutcome;
    }

    /**
     * Set documents
     *
     * @param string $documents
     * @return Regulation
     */
    public function setDocument(?string $document)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $file
     */
    public function setFile(?File $file = null): void
    {
        $this->file = $file;
        if (null !== $file) {
            $this->createAt =  new \DateTimeImmutable();
        }
    }

    public function upload($fileName)
    {
        // the file property can be empty if the field is not required
        if (null ===  $this->file) {
            return;
        }

        // set the path property to the filename where you've saved the file
        $this->document =  $fileName;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    public function setDocumentSize(?int $documentSize): void
    {
        $this->documentSize = $documentSize;
    }

    public function getDocumentSize(): ?int
    {
        return $this->documentSize;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile(): ?File
    {
        return $this->file;
    }



    /**
     * Get documents
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set publishDate
     *
     * @param \string $publishDate
     * @return Regulation
     */
    public function setPublishDate($publishDate)
    {
        if (!($publishDate instanceof \DateTime)) {
            $publishDate = new \DateTime($publishDate);
        }
        $this->publishDate = $publishDate;
        return $this;
    }

    /**
     * Get publishDate
     *
     * @return \string
     */
    public function getPublishDate()
    {
        if ($this->publishDate instanceof \DateTime) {
            return $this->publishDate->format('Y-m-d H:i:s');
        }
        return $this->publishDate;
    }

    /**
     * Set closingDate
     *
     * @param \string $closingDate
     * @return Regulation
     */
    public function setClosingDate($closingDate)
    {
        $this->closingDate = $closingDate;

        return $this;
    }

    /**
     * Get closingDate
     *
     * @return \string
     */
    public function getClosingDate()
    {
        return $this->closingDate;
    }

    /**
     * Set Agency
     *
     * @param BusinessAgency $agency
     * @return mixed $agency
     */
    public function setAgency(BusinessAgency $agency)
    {
        $this->agency = $agency;

        return $agency;
    }

    /**
     * Get agency
     *
     * @return integer
     */
    public function getAgency()
    {
        return $this->agency;
    }

    /**
     * Get list of published agency
     *
     * @return string
     */
    public function getActiveAgencyList()
    {
        return $this->getAgency()->filter(
            function (BusinessAgency $agencies) {
                return $agencies->getDeleted() == 0;
            }
        );
    }

    public function getAbusiveComments()
    {
        return $this->getComments()->filter(
            function (Comment $comment) {
                return $comment->getIsAbusive() == 1;
            }
        );
    }

    public function getUnpublishedComments()
    {
        return $this->getComments()->filter(
            function (Comment $comment) {
                return ($comment->getPublish() == 0 || $comment->getHidden() == 1) && is_null($comment->getParentRight());
            }
        );
    }
    public function getCommentPendingReview()
    {
        return $this->getComments()->filter(
            function (Comment $comment) {
                return $comment->getPendingReview() == 1 and $comment->getPublish() == 0;
            }
        );
    }
    public function getUserDeletedComment()
    {
        return $this->getComments()->filter(
            function (Comment $comment) {
                return $comment->getDeleted() == 1;
            }
        );
    }

    public function commentsCount()
    {
        return $this->getComments()->filter(
            function (Comment $comment) {
                return $comment->getDeleted() == 0 && $comment->getPublish() == 1 && $comment->getHidden() == 0 && is_null($comment->getParent());
            }
        );
    }

    /**
     * Set industry
     *
     * @param BusinessIndustry $industry
     * @return Regulation
     */
    public function setIndustry(BusinessIndustry $industry)
    {
        $this->industry = $industry;

        return $this;
    }

    /**
     * Get industry
     *
     * @return integer
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Regulation
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Regulation
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
     * Set specificInstructions
     *
     * @param string $specificInstructions
     * @return Regulation
     */
    public function setSpecificInstructions($specificInstructions)
    {
        $this->specificInstructions = $specificInstructions;

        return $this;
    }

    /**
     * Get specificInstructions
     *
     * @return string
     */
    public function getSpecificInstructions()
    {
        return $this->specificInstructions;
    }

    /**
     * Set supportingMaterials
     *
     * @param string $supportingMaterials
     * @return Regulation
     */
    public function setSupportingMaterials($supportingMaterials)
    {
        $this->supportingMaterials = $supportingMaterials;

        return $this;
    }

    /**
     * Get supportingMaterials
     *
     * @return string
     */
    public function getSupportingMaterials()
    {
        return $this->supportingMaterials;
    }
    
    /**
     * Set Offline Consultations
     *
     * @param string $offlineConsultations
     * @return Regulation
     */
    public function setOfflineConsultations($offlineConsultations)
    {
        $this->offlineConsultations = $offlineConsultations;

        return $this;
    }

    /**
     * Get offlineConsultations
     *
     * @return string
     */
    public function getOfflineConsultations()
    {
        return $this->offlineConsultations;
    }

    /**
     * Set isLoginRequired
     *
     * @param boolean $isLoginRequired
     * @return Regulation
     */
    public function setIsLoginRequired($isLoginRequired)
    {
        $this->isLoginRequired = $isLoginRequired;

        return $this;
    }

    /**
     * Get isLoginRequired
     *
     * @return boolean
     */
    public function getIsLoginRequired()
    {
        return $this->isLoginRequired;
    }

    /**
     * Set isPublic
     *
     * @param boolean $isPublic
     * @return Regulation
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }


    /**
     * Set IsReviewComments
     *
     * @param boolean $isPublic
     * @return Regulation
     */
    public function setIsReviewComments($isReviewComments)
    {
        $this->isReviewComments = $isReviewComments;

        return $this;
    }

    /**
     * Get isReviewComments
     *
     * @return boolean
     */
    public function getIsReviewComments()
    {
        return $this->isReviewComments;
    }


    /**
     * Set isAttachmentEnabled
     *
     * @param boolean $isPublic
     * @return Regulation
     */
    public function setIsAttachmentEnabled($isAttachmentEnabled)
    {
        $this->isAttachmentEnabled = $isAttachmentEnabled;

        return $this;
    }

    /**
     * Get isAttachmentEnabled
     *
     * @return boolean
     */
    public function getIsAttachmentEnabled()
    {
        return $this->isAttachmentEnabled;
    }

    /**
     * Set consultationStage
     *
     * @param boolean $isPublic
     * @return Regulation
     */
    public function setConsultationStage($consultationStage)
    {
        $this->consultationStage = $consultationStage;

        return $this;
    }

    public function setStage($consultationStage)
    {
        $this->consultationStage = $consultationStage;
        return $this;
    }

    public function getStage()
    {
        return $this->consultationStage;
    }
    /**
     * Get consultationStage
     *
     * @return boolean
     */
    public function getConsultationStage()
    {
        if ($this->consultationStage == 1) {
            return "Open";
        } else if ($this->consultationStage == 2) {
            return "Closed";
        } else if ($this->consultationStage == 3) {
            return "Closed";
        }
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments(Comment $comment)
    {
        return $this->comment = $comment;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted = false)
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function getMainHighlight()
    {
        return $this->mainHighlight;
    }

    public function setMainHighlight($mainHighlight = 0)
    {
        $this->mainHighlight = $mainHighlight;
        return $this;
    }

    public function setRegulationType($regulationType)
    {
        $this->regulationType = $regulationType;
        return $this;
    }

    public function getRegulationType()
    {
        return $this->regulationType;
    }

    public function setPublished($published = 0)
    {
        $this->published = $published;
        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }
    public function setCheckClosing($checkClosing)
    {
        $this->checkClosing = $checkClosing;
        return $this;
    }

    public function getCheckClosing()
    {
        return $this->checkClosing;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getPositionStatusAgree()
    {
        return $this->getPosition()->filter(
            function (Position $position) {
                return $position->getPosition() == 1;
            }
        );
    }

    public function getPositionStatusNeutral()
    {
        return $this->getPosition()->filter(
            function (Position $position) {
                return $position->getPosition() == 2;
            }
        );
    }

    public function getPositionStatusDisagree()
    {
        return $this->getPosition()->filter(
            function (Position $position) {
                return $position->getPosition() == 3;
            }
        );
    }

    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    public function getFileSize()
    {
        return $this->fileSize;
    }

    public function setFileType($fileType)
    {
        $this->fileType = $fileType;
        return $this;
    }

    public function getFileType()
    {
        return $this->fileType;
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
}
