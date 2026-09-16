<?php

namespace WebmastersAfrica\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="threadmetadata")
 */
class ThreadMetadata extends BaseThreadMetadata
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    protected $isDeleted = false;

    /**
     * Date of last message written by another participant
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime",name="last_participant_message_date", nullable=true)
     */
    protected $lastParticipantMessageDate;

    /**
     * Date of last message written by another participant
     *
     * @var DateTime
     *
     * @ORM\Column(type="datetime",name="last_message_date", nullable=true)
     */
    protected $lastMessageDate;

    /**
     * @ORM\ManyToOne(
     *   targetEntity="WebmastersAfrica\MessageBundle\Entity\Thread",
     *   inversedBy="metadata"
     * )
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @var ParticipantInterface
     */
    protected $participant;
}