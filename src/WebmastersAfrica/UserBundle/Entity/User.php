<?php
// src/WebmastersAfrica/UserBundle/Entity/User.php

namespace WebmastersAfrica\UserBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\GroupInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;

/**
 * @ORM\Entity(repositoryClass="WebmastersAfrica\UserBundle\Entity\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser implements ParticipantInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
     */
    private $phone_number;


    /**
     * @var integer $agency_id
     *
     * @ORM\Column(name="agency_id", type="integer", nullable=true)
     */
    private $agency_id;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessAgency", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="users_agencies")
     */
    private $agencies;

    /**
     * @ORM\OneToMany(targetEntity="WebmastersAfrica\TaskBundle\Entity\Task", mappedBy="assigner")
     */
    protected $senttasks;

    /**
     * @ORM\OneToMany(targetEntity="WebmastersAfrica\TaskBundle\Entity\Task", mappedBy="assignee")
     */
    protected $receivedtasks;

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
     * @Gedmo\Timestampable(on="change", field={"first_name", "last_name", "phone_number", "agencies", "groups", "username", "email", "password"})
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
     * @Gedmo\Timestampable(on="change", field={"first_name", "last_name", "phone_number", "agencies", "groups", "username", "email", "password"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow")
     * @ORM\JoinTable(name="license_workflow_users")
     */
    private $workflow;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_id", type="string", length=255)
     *
     */
    protected $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook_access_token", type="string", length=255)
     */
    protected $facebookAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="google_id", type="string", length=255)
     */
    protected $googleId;

    /**
     * @var string
     *
     * @ORM\Column(name="google_access_token", type="string", length=255)
     */
    protected $googleAccessToken;

    /**
     * @ORM\OneToMany(targetEntity="\OTB\Bundle\NoticeAndCommentBundle\Entity\Comment", mappedBy="user")
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="\WebmastersAfrica\LicenseBundle\Entity\ApplicationHistory", mappedBy="user")
     */
    protected $applicationHistory;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_admin", type="boolean")
     */
    protected $isAdmin = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="alias_name", type="string")
     */
    protected $aliasName;

    /**
     * @var string
     *
     * @ORM\Column(name="email_on", type="boolean")
     */
    protected $emailOn;

    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->agencies = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->workflow = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->applicationHistory = new ArrayCollection();
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
    }

    public function __toString()
    {
        return $this->getFirstName() . " " . $this->getLastName() . " (" . $this->getEmail() . ")";
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

    public function getWorkflow(): Collection
    {

        return $this->workflow;
    }

    /**
     * Get Comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Get History
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplicationHistory()
    {
        return $this->applicationHistory;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Get Full Name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    /**
     * Set phone_number
     *
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phone_number = $phoneNumber;

        return $this;
    }

    /**
     * Get phone_number
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * Add groups
     *
     * @param \WebmastersAfrica\UserBundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(\FOS\UserBundle\Model\GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \WebmastersAfrica\UserBundle\Entity\Group $groups
     */
    public function removeGroup(\FOS\UserBundle\Model\GroupInterface $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add agencies
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencys
     * @return User
     */
    public function addAgencies(BusinessAgency $agencies)
    {
        $this->agencies->add($agencies);

        return $this;
    }

    /**
     * Remove agencys
     *
     * @param \WebmastersAfrica\LicenseBundle\Entity\BusinessAgency $agencys
     */
    public function removeAgencies(BusinessAgency $agencies)
    {
        $this->agencies->removeElement($agencies);
    }

    public function getAgencies()
    {
        return $this->agencies;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Get credentials_expire_at
     *
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * @param \DateTime $date
     *
     * @return User
     */
    public function setExpiresAt(\DateTime $date = null)
    {
        $this->expiresAt = $date;

        return $this;
    }

    public function getIsAdmin()
    {
        return  $this->isAdmin;
    }

    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        return $this;
    }
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
        return $this;
    }
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;
        return $this;
    }

    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;
        return $this;
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }
    public function getGoogleId()
    {
        return $this->googleId;
    }
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    public function setAliasName($aliasName)
    {
        $this->aliasName = $aliasName;
        return $this;
    }

    public function getAliasName()
    {
        return $this->aliasName;
    }

    public function setIsReceiveReplyEmail($turnReplyOff)
    {
        $this->emailOn = $turnReplyOff;
        return $this;
    }

    public function getIsReceiveReplyEmail()
    {
        return (bool)$this->emailOn;
    }
}
