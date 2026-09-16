<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry;

/**
 * WebmastersAfrica\LicenseBundle\Entity\Search
 *
 * @ORM\Table(name="industry_subscriber_list")
 * @ORM\Entity
 */
class SubscriberList
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
     * @var integer $user
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user;


    /**
     * @ORM\ManyToOne (targetEntity="\WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry", inversedBy="subscribers")
     * @ORM\JoinColumn(name="industry_id", referencedColumnName="id")
     */
    protected $industry;

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
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set industry
     *
     * @param integer BusinessIndustry $industry
     */
    public function setIndustry($industry)
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
}
