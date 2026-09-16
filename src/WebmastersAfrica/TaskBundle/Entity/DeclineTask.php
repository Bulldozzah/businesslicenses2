<?php

namespace WebmastersAfrica\TaskBundle\Entity;

use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\UserBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeclineTask
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DeclineTask
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
     * @ORM\Column(name="comments", type="text")
     */
    private $comments;


    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessLicense")
     * @ORM\JoinColumn(name="license_id", referencedColumnName="id")
     */
    private $license;

    /**
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


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
     * Set comment
     *
     * @param string $comment
     * @return DeclineTask
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return DeclineTask
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
     * Set license
     *
     * @param integer $license
     * @return DeclineTask
     */
    public function setLicense(BusinessLicense $license)
    {
        $this->license = $license;

        return $this;
    }

    /**
     * Get license
     *
     * @return integer 
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set user
     *
     * @param integer $user
     * @return DeclineTask
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer 
     */
    public function getUser()
    {
        return $this->user;
    }
}
