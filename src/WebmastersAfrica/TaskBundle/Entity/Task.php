<?php

namespace WebmastersAfrica\TaskBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(
 *   repositoryClass="WebmastersAfrica\TaskBundle\Entity\TaskRepository"
 * )
 */
class Task
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
     * @ORM\Column(name="task_description", type="text")
     */
    private $taskDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="task_start_date", type="string")
     */
    private $taskStartDate;

    /**
     * @var string
     *
     * @ORM\Column(name="task_end_date", type="string")
     */
    private $taskEndDate;

    /**
     * @var string
     *
     * @ORM\Column(name="task_status", type="string", length=255)
     */
    private $taskStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="task_license_id", type="integer")
     */
    private $task_license_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="decline", type="integer")
     */
    private $decline;

    /**
     * @var integer
     *
     * @ORM\Column(name="assigned_by", type="integer")
     */
    private $assigned_by;

    /**
     * @var integer
     *
     * @ORM\Column(name="assigned_to", type="integer")
     */
    private $assigned_to;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User", inversedBy="senttasks")
     * @ORM\JoinColumn(name="assigned_by", referencedColumnName="id")
     */
    protected $assigner;


    /**
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumn(name="stage_id", referencedColumnName="id", nullable=false)
     */
    protected $stage;


    /**
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User", inversedBy="receivedtasks")
     * @ORM\JoinColumn(name="assigned_to", referencedColumnName="id")
     */
    protected $assignee;

    /**
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessLicense", inversedBy="tasks", cascade={"persist"})
     * @ORM\JoinColumn(name="task_license_id", referencedColumnName="id")
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
     * @Gedmo\Timestampable(on="change", field={"page_title", "page_breadcrumb_title", "page_content", "seo_keywords", "seo_description", "page_layout", "page_order", "published", "deleted", "menu"})
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
     * @Gedmo\Timestampable(on="change", field={"page_title", "page_breadcrumb_title", "page_content", "seo_keywords", "seo_description", "page_layout", "page_order", "published", "deleted", "menu"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    public function __construct()
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->deleted = true;
        $this->taskStatus = "pending";
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
     * Get id
     *
     * @return integer 
     */
    public function getDecline()
    {
        return $this->decline;
    }

    /**
     * decline decline
     *
     * @return integer 
     */
    public function setDecline($decline)
    {
        $this->decline = $decline;
        return $this;
    }

    /**
     * Set taskDescription
     *
     * @param string $taskDescription
     * @return Task
     */
    public function setTaskDescription($taskDescription)
    {
        $this->taskDescription = $taskDescription;

        return $this;
    }

    /**
     * Get taskDescription
     *
     * @return string 
     */
    public function getTaskDescription()
    {
        return $this->taskDescription;
    }

    /**
     * Set taskStartDate
     *
     * @param string start date
     * @return Task
     */
    public function setTaskStartDate($taskStartDate)
    {
        $this->taskStartDate = $taskStartDate;

        return $this;
    }

    /**
     * Get taskStartDate
     *
     * @return string
     */
    public function getTaskStartDate()
    {
        return $this->taskStartDate;
    }

    /**
     * Set taskEndDate
     *
     * @param \DateTime $taskEndDate
     * @return Task
     */
    public function setTaskEndDate($taskEndDate)
    {
        $this->taskEndDate = $taskEndDate;

        return $this;
    }

    /**
     * Get taskEndDate
     *
     * @return \DateTime 
     */
    public function getTaskEndDate()
    {
        return $this->taskEndDate;
    }

    /**
     * Set taskStatus
     *
     * @param string $taskStatus
     * @return Task
     */
    public function setTaskStatus($taskStatus)
    {
        $this->taskStatus = $taskStatus;

        return $this;
    }

    /**
     * Get taskStatus
     *
     * @return string 
     */
    public function getTaskStatus()
    {
        return $this->taskStatus;
    }

    /**
     * Set taskLicenseId
     *
     * @param integer $taskLicenseId
     * @return Task
     */
    public function setTaskLicenseId($taskLicenseId)
    {
        $this->task_license_id = $taskLicenseId;

        return $this;
    }

    /**
     * Get taskLicenseId
     *
     * @return integer 
     */
    public function getTaskLicenseId()
    {
        return $this->task_license_id;
    }

    /**
     * Set assignedBy
     *
     * @param integer $assignedBy
     * @return Task
     */
    public function setAssignedBy($assignedBy)
    {
        $this->assigned_by = $assignedBy;

        return $this;
    }

    /**
     * Get assignedBy
     *
     * @return integer 
     */
    public function getAssignedBy()
    {
        return $this->assigned_by;
    }

    /**
     * Set assignedTo
     *
     * @param integer $assignedTo
     * @return Task
     */
    public function setAssignedTo($assignedTo)
    {
        $this->assigned_to = $assignedTo;

        return $this;
    }

    /**
     * Get assignedTo
     *
     * @return integer 
     */
    public function getAssignedTo()
    {
        return $this->assigned_to;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Task
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
     * Set created
     *
     * @param \DateTime $created
     * @return Task
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
     * @return Task
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
     * @return Task
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
     * Set assigner
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $assigner
     * @return Task
     */
    public function setAssigner(\WebmastersAfrica\UserBundle\Entity\User $assigner = null)
    {
        $this->assigner = $assigner;

        return $this;
    }

    /**
     * Get assigner
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getAssigner()
    {
        return $this->assigner;
    }

    /**
     * Set assignee
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $assignee
     * @return Task
     */
    public function setAssignee(\WebmastersAfrica\UserBundle\Entity\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

        /**
     * Set assignee
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $assignee
     * @return Task
     */
    public function setStage(Workflow $stage = null)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \WebmastersAfrica\LicenseBundle\Entity\Workflow 
     */
    public function getStage()
    {
        return $this->stage;
    }


    /**
     * Set license
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $license
     * @return Task
     */
    public function setLicense(\WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $license = null)
    {
        $this->license = $license;

        return $this;
    }

    public function setStageId($workflow)
    {
        return $this->stage = $workflow;
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
     * Set createdBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $createdBy
     * @return Task
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
     * @return Task
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
     * @return Task
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
