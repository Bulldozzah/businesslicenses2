<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebmastersAfrica\LicenseBundle\Entity\Feedback;

/**
 * RepliedMessage
 *
 * @ORM\Table("replied_messages")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\PressBundle\Entity\RepliedMessageRepository")
 */
class RepliedMessage
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
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var integer
     *
     * 
     * @ORM\OneToOne(targetEntity="WebmastersAfrica\LicenseBundle\Entity\Feedback", mappedBy="replied")
     */
    private $messageId;


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
     * Set message
     *
     * @param string $message
     * @return RepliedMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set messageId
     *
     * @param integer $messageId
     * @return RepliedMessage
     */
    public function setMessageId(Feedback $messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * Get messageId
     *
     * @return integer 
     */
    public function getMessageId()
    {
        return $this->messageId;
    }
}
