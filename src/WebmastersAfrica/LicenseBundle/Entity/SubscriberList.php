<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebmastersAfrica\LicenseBundle\Entity\Search
 *
 * @ORM\Table(name="agency_subscriber_list")
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
     * @ORM\ManyToOne (targetEntity="BusinessAgency", inversedBy="subscribers")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id")
     */
    protected $agency;

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
     * Set agency
     *
     * @param integer BusinessAgency $agency
     */
    public function setAgency($agency)
    {
        $this->agency = $agency;
        return $this;
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
}
