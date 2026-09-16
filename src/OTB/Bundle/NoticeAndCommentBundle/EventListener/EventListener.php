<?php

namespace OTB\Bundle\NoticeAndCommentBundle\EventListener;

use OTB\Bundle\NoticeAndCommentBundle\Event\CommentEvent;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Notification;
use Doctrine\ORM\EntityManager;

// not in use
// delete for you own benefit
class EventListener
{
    protected $notification;
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function onCommentEvent(CommentEvent $event)
    {
        $comment = $event->getComment();
        $notification = new Notification;
        $notification  = $notification->setSender($comment->getUser());
        $notification = $notification->setRecipient($comment->getUser());
        $notification = $notification->setMessage("Replied your comment");
        $notification->setReference("Regulation");
        $notification->setReferenceId($comment->getRegulation()->getId());
        $notification->setIsRead(0);
        $em->persist($notification);
        $em->flush();
    }
}
