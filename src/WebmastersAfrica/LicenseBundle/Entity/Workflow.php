<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use WebmastersAfrica\UserBundle\Entity\User;
use FOS\UserBundle\Model\GroupInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Workflow
 *
 * @ORM\Table(name="license_workflow")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\WorkflowRepository")
 * @UniqueEntity("title")
 */
class Workflow
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_level", type="integer")
     */
    private $orderLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="task_description", type="string")
     */
    private $taskDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="send_emails", type="boolean")
     */
    private $sendEmails;

    /**
     * @var boolean
     *
     * @ORM\Column(name="first_stage", type="boolean")
     */
    private $firstStage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = 0;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\UserBundle\Entity\User", inversedBy="workflow")
     * @ORM\JoinTable(name="license_workflow_users")
     */
    private $notificationsUser;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessAgency", inversedBy="workflow")
     * @ORM\JoinTable(name="license_workflow_agency")
     */
    private $agency;

    /**
     * Owning Side
     *
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\UserBundle\Entity\Group", fetch="EAGER", inversedBy="features")
     * @ORM\JoinTable(name="license_workflow_groups",
     *      joinColumns={@ORM\JoinColumn(name="workflow_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     *      )
     */
    private $accessGroups;

    /**
     * @var Interger
     *
     * @ORM\ManyToOne (targetEntity="WebmastersAfrica\UserBundle\Entity\User", inversedBy="workflow")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id", nullable=true)
     */
    private $assignee;

    /**
     * @ORM\OneToMany(targetEntity="WebmastersAfrica\TaskBundle\Entity\Task", mappedBy="stage", fetch="EAGER")
     */
    protected $task;

    /**
     * @ORM\OneToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessLicense", mappedBy="stage", fetch="EAGER")
     */
    protected $licenses;

    /**
     * @ORM\OneToMany(targetEntity="\WebmastersAfrica\LicenseBundle\Entity\ApplicationHistory", mappedBy="previousStage", fetch="EAGER")
     */
    protected $previousHistory;


    /**
     * @ORM\OneToMany(targetEntity="\WebmastersAfrica\LicenseBundle\Entity\ApplicationHistory", mappedBy="currentStage", fetch="EAGER")
     */
    protected $currentHistory;


    /**
     *
     * @ORM\OneToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", mappedBy="nextStage")
     */
    private $children;

    /**
     * One Stage points to another stage
     * @ORM\OneToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", fetch="EAGER", inversedBy="children")
     * @ORM\JoinColumn(name="next_stage", referencedColumnName="id", nullable=false)
     *
     */
    private $nextStage;

    /**
     *
     * @ORM\OneToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", mappedBy="previousStage")
     */
    private $parent;

    /**
     * One Stage points to another stage
     * @ORM\OneToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", inversedBy="parent")
     * @ORM\JoinColumn(name="reject_stage", referencedColumnName="id", nullable=true)
     *
     */
    private $previousStage;


    public function __construct()
    {
        $this->notificationsUser = new ArrayCollection();
        $this->agency = new ArrayCollection();
        $this->accessGroups = new ArrayCollection();
        $this->previousHistory = new ArrayCollection();
        $this->currentHistory = new ArrayCollection();
        $this->deleted = 0;
        // $this->licenses = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->title;
    }

    public function setNextStage(Workflow $nextStage = null)
    {
        $this->nextStage = $nextStage;
        return $this;
    }

    public function getNextStage()
    {
        return $this->nextStage;
    }

    public function setPreviousStage(Workflow $previousStage = null)
    {
        $this->previousStage = $previousStage;
        return $this;
    }

    public function getPreviousStage()
    {
        return $this->previousStage;
    }

    /**
     * Get Licenses
     *
     * @return integer
     */
    public function getLicenses()
    {
        return $this->licenses;
    }

    /**
     * Set accessGroups
     *
     * @param string $accessGroups
     * @return Workflow
     */
    public function setAccessGroups($accessGroups)
    {
        $this->accessGroups = $accessGroups;

        return $this;
    }

    public function getTask()
    {
        return $this->task;
    }

    /**
     * Get accessGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccessGroups()
    {
        return $this->accessGroups;
    }


    /**
     * Get accessGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCurrentHistory()
    {
        return $this->currentHistory;
    }

    /**
     * Get accessGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreviousHistory()
    {
        return $this->previousHistory;
    }

    public function setAssignee(User $user = null)
    {
        $this->assignee = $user;
        return $this;
    }


    /**
     * Add licenses
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses
     * @return BusiensLicense
     */
    public function addLicense(\WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $licenses)
    {
        $this->licenses[] = $licenses;

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
     * @return Workflow
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }


    /**
     * Set First Stage
     *
     * @param string $title
     * @return int
     */
    public function setFirstStage($firstStage)
    {
        $this->firstStage = $firstStage;

        return $this;
    }

    /**
     * Get First Stage
     *
     * @return string
     */
    public function getFirstStage()
    {
        return $this->firstStage;
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
     * Set sendEmails
     *
     * @param boolean $sendEmails
     * @return Workflow
     */
    public function setSendEmails($sendEmails)
    {
        $this->sendEmails = $sendEmails;

        return $this;
    }

    /**
     * Get Order Level
     *
     * @return string
     */
    public function getOrderLevel()
    {
        return $this->orderLevel;
    }

    /**
     * Set sendEmails
     *
     * @param boolean $sendEmails
     * @return Workflow
     */
    public function setOrderLevel($orderLevel)
    {
        $this->orderLevel = $orderLevel;

        return $this;
    }

    /**
     * Get sendEmails
     *
     * @return boolean
     */
    public function getSendEmails()
    {
        return $this->sendEmails;
    }

    /**
     * Set notificationsUser
     *
     * @param string $notificationsUser
     * @return Workflow
     */
    public function setNotificationsUser($notificationsUser)
    {
        $this->notificationsUser = $notificationsUser;

        return $this;
    }

    /**
     * Get notificationsUser
     *
     * @return string
     */
    public function getAdministrativeUser()
    {
        return $this->notificationsUser;
    }

    public function getNotificationsUser()
    {
        return $this->notificationsUser;
    }

    /**
     * Set Agency
     *
     * @param string $agency
     * @return Workflow
     */
    public function setAgency($agency)
    {
        $this->agency = $agency;

        return $this;
    }
    /**
     * Add agency
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencys
     * @return User
     */
    public function addAgency(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencys)
    {
        $this->agency[] = $agencys;

        return $this;
    }

    /**
     * Remove agency
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencys
     */
    public function removeAgency(\WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencys)
    {
        $this->agency->removeElement($agencys);
    }

    /**
     * Add groups
     *
     * @param \WebmastersAfrica\UserBundle\Entity\Group $groups
     * @return User
     */
    public function addAccessGroup(\FOS\UserBundle\Model\GroupInterface $groups)
    {
        $this->accessGroups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \WebmastersAfrica\UserBundle\Entity\Group $groups
     */
    public function removeAccessGroup(\FOS\UserBundle\Model\GroupInterface $groups)
    {
        $this->accessGroups->removeElement($groups);
    }

    /**
     * Add groups
     *
     * @param User $user
     * @return User
     */
    public function addNotificationsUser(User $user)
    {
        $this->notificationsUser[] = $user;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param User $user
     */
    public function removeNotificationsUser(User $user)
    {
        $this->notificationsUser->removeElement($user);
    }



    /**
     * Get agency
     *
     * @return string
     */
    public function getAgency(): Collection
    {
        return $this->agency;
    }

    public function getAssignee()
    {
        return $this->assignee;
    }

    public function setPublished($published = 1)
    {
        $this->published = $published;
        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }


    public function setTaskDescription($taskDescription)
    {
        $this->taskDescription = $taskDescription;
        return $this;
    }

    public function getTaskDescription()
    {
        return $this->taskDescription;
    }

    public function setType($taskType)
    {
        $this->type = $taskType;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getActiveLicenses()
    {
        return $this->getLicenses()->filter(
            function (BusinessLicense $licenses) {
                return $licenses->getDeleted() == 0;
            }
        );
    }

    public function getLicensesCount()
    {
        return $this->getLicenses()->filter(
            function (BusinessLicense $licenses) {
                return (in_array(
                    $licenses->getAgency()->getId(),
                    $_SESSION['my_agency']
                ) and $licenses->getDeleted() == 0);
            }
        );
    }
}
