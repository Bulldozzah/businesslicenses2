<?php 

namespace OTB\Bundle\NoticeAndCommentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Notification;

class NotificationEvent extends Event 
{
    /** @var Notification */
    protected $notification;

    /** ... */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /** ... */
    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    /** ... */
    public function setNotification(Notification $notification): self
    {
        $this->notification = $notification;

        return $this;
    }
}