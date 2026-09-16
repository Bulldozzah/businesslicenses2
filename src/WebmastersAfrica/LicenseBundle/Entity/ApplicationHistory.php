<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use WebmastersAfrica\UserBundle\Entity\User;

/**
 * Workflow
 *
 * @ORM\Table(name="application_history")
 * @ORM\Entity()
 */
class ApplicationHistory
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
     * One Stage points to another stage
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User", inversedBy="applicationHistory")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     *
    **/
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="action_type", type="string")
     */
    private $actionType;

    /**
     * One Stage points to another stage
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", inversedBy="previousHistory")
     * @ORM\JoinColumn(name="previous_stage", referencedColumnName="id", nullable=true)
     *
    **/
    private $previousStage;


    /**
     * One Stage points to another stage
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessLicense", inversedBy="history", cascade={"persist"})
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=true)
     *
     **/
    private $application;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Workflow", inversedBy="currentHistory")
     * @ORM\JoinColumn(name="current_step", referencedColumnName="id", nullable=true)
     */
    private $currentStage;


    public function __toString()
    {
        return $this->actionType;
    }

    public function setCurrentStage(Workflow $currentStage)
    {
        $this->currentStage = $currentStage;
        return $this;
    }

    public function getCurrentStage()
    {
        return $this->currentStage;
    }

    public function getPreviousStage()
    {
        return $this->previousStage;
    }


    public function setPreviousStage(Workflow $previousStage)
    {
        $this->previousStage = $previousStage;
        return $this;
    }


    public function getActionType()
    {
        return $this->actionType;
    }

    public function setActionType($actionType)
    {
        $this->actionType = $actionType;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
    public function setApplication(BusinessLicense $license)
    {
        $this->application = $license;
        return $this;
    }

    public function getApplication()
    {
        return $this->application;
    }
}
