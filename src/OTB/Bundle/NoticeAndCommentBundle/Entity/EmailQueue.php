<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Comment;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * EmailQueue
 *
 * @ORM\Table(name="comment_alert_sent")
 * @ORM\Entity
 */
class EmailQueue
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
     * @var integer
     *
     * @ORM\Column(name="comment_id", type="integer")
     */
    private $comment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sent", type="boolean")
     */
    private $isSent;


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
     * @param integer $comment
     * @return EmailQueue
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return integer
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set isSent
     *
     * @param boolean $isSent
     * @return EmailQueue
     */
    public function setIsSent($isSent)
    {
        $this->isSent = $isSent;

        return $this;
    }

    /**
     * Get isSent
     *
     * @return boolean
     */
    public function getIsSent()
    {
        return $this->isSent;
    }
}
