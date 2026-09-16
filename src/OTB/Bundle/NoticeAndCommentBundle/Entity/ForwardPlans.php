<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ForwardPlans
 *
 * @ORM\Table("forward_plans")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlansRepository")
 * @UniqueEntity("title")
 */
class ForwardPlans
{
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
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="slug", length=250, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;
    /**
     * @var string
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = 0;
    /**
     * @var string
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     * @var string
     *
     * @ORM\Column(name="problem_addressed", type="text")
     */
    private $problemAddressed;

    /**
     * @var string
     *
     * @ORM\Column(name="public_consultation", type="string", length=255)
     */
    private $publicConsultation;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="ForwardPlansAttachments", mappedBy="forwardPlan",cascade={"persist"}, orphanRemoval=true)
     *
     */
    private $attachments;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessAgency", inversedBy="regulations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $agency;


    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlanMainCategory", inversedBy="forwardplans")
     * @ORM\JoinColumn(name="forward_plan_category_id", referencedColumnName="id")
     */
    private $forwardPlanCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="impact", type="text")
     */
    private $impact;

    /**
     * @var string
     *
     * @ORM\Column(name="offline_office", type="string", length=255)
     */
    private $offlineOffice;

    /**
     * @var string
     *
     * @ORM\Column(name="related_links", type="string")
     *
     */
    private $relatedLinks;

    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;



    public function __construct()
    {
        $this->attachments = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        return $this->slug = $slug;
        return $this;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        return $this->deleted = $deleted;
        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        return $this->published = $published;
        return $this;
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
     * @return ForwardPlans
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
    /**
     * Set Period
     *
     *
     * @return string
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }
    /**
     * Set Impact
     *
     * @return string
     */
    public function getPeriod()
    {

        return $this->period;
    }

    /**
     * Get Related Links
     *
     *
     * @return string
     */
    public function setRelatedLinks($relatedLinks)
    {
        $this->relatedLinks = $relatedLinks;

        return $this;
    }
    /**
     * Set Impact
     *
     * @return string
     */
    public function getRelatedLinks()
    {

        return $this->relatedLinks;
    }
    /**
     * Set title
     *
     * @param string $impact
     * @return string
     */
    public function setImpact($impact)
    {
        $this->impact = $impact;

        return $this;
    }
    /**
     * Get Impact
     *
     *
     * @return string
     */
    public function getImpact()
    {
        return $this->impact;
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
     * @return ForwardPlans
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
     * Set problemAddressed
     *
     * @param string $problemAddressed
     * @return ForwardPlans
     */
    public function setProblemAddressed($problemAddressed)
    {
        $this->problemAddressed = $problemAddressed;

        return $this;
    }

    /**
     * Get problemAddressed
     *
     * @return string
     */
    public function getProblemAddressed()
    {
        return $this->problemAddressed;
    }

    /**
     * Set publicConsultation
     *
     * @param string $publicConsultation
     * @return ForwardPlans
     */
    public function setPublicConsultation($publicConsultation)
    {
        $this->publicConsultation = $publicConsultation;

        return $this;
    }

    /**
     * Get publicConsultation
     *
     * @return string
     */
    public function getPublicConsultation()
    {
        return $this->publicConsultation;
    }

    public function getAgency()
    {
        return $this->agency;
    }

    public function setAgency($agency)
    {
        $this->agency = $agency;
        return $this;
    }

    /**
     * Get Active Agencies
     *
     * @return string
     */
    public function getActiveAgency()
    {
        return $this->getAgency()->filter(
            function (BusinessAgency $agencies) {
                return $agencies->getDeleted() == 0;
            }
        );
    }

    /**
     * Set offlineOffice
     *
     * @param string $offlineOffice
     * @return ForwardPlans
     */
    public function setOfflineOffice($offlineOffice)
    {
        $this->offlineOffice = $offlineOffice;

        return $this;
    }

    /**
     * Get offlineOffice
     *
     * @return string
     */
    public function getOfflineOffice()
    {
        return $this->offlineOffice;
    }


    /**
     * Add attachment
     *
     * @param ForwardPlansAttachments $attachment
     *
     * @return Folder
     */
    public function addAttachment(\OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlansAttachments $attachment)
    {
        // Bidirectional Ownership
        $attachment->setForwardPlan($this);
        $attachment->upload();
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Remove attachments
     *
     * @param ForwardPlansAttachments $attachment
     */
    public function removeAttachment(\OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlansAttachments $attachment)
    {
        $this->attachments->removeElement($attachment);
    }

    /**
     * Get attachment
     *
     * @return integer
     */
    public function getAttachments()
    {
        return $this->attachments;
    }


    public function setForwardPlanCategory(forwardPlanMainCategory $forwardPlanCategory = null)
    {
        $this->forwardPlanCategory = $forwardPlanCategory;
        return $this;
    }

    public function getForwardPlanCategory()
    {
        return $this->forwardPlanCategory;
    }

    /* Set createdBy
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
}
