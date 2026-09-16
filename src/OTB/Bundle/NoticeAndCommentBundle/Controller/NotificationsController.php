<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Notification;
use OTB\Bundle\NoticeAndCommentBundle\Entity\EmailQueue;
use Symfony\Component\EventDispatcher\EventDispatcher;
use OTB\Bundle\NoticeAndCommentBundle\Event\CommentEvent;
use OTB\Bundle\NoticeAndCommentBundle\Event\CommentSubscriber;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Comment;

/**
 * Notification controller.
 *
 * @Route("/notifications")
 */
class NotificationsController extends Controller
{
    public function getMyNotificationAction(Request $request)
    {
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $security_context->getToken()->getUser()->getId();
            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository(Notification::class)->findBy(['recipient' => $user, 'isRead' => 0], ['id' => 'desc']);
            $read_notifications = [];
            $unread_notifications = [];
            if ($notifications) {
                foreach ($notifications as $notification) {
                    if ($notification->getIsRead() == 0) {
                        $unread_notifications[] = array(
                            'key' => $notification->getReferenceId(),
                            'value' => $notification->getMessage()
                        );
                    }
                }
            }
            return new Response(
                json_encode(
                    [
                        "unread" => $unread_notifications
                    ]
                )
            );
        } else {
            return new Response(json_encode(['unread' => ['key' => '', 'value' => '']]));
        }
    }

    public function getTotalNumberOfUserNotificationsAction()
    {
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $security_context->getToken()->getUser()->getId();
            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository(Notification::class)->findBy(['recipient' => $user, 'isRead' => 0]);
            $this->dispatchReplyEmails();
            return new Response(json_encode(['count_unread' => count($notifications)]));
        } else {
            return new Response(json_encode(['count_unread' => ""]));
        }
    }

    public function markUserNotificationAsReadAction($resource)
    {
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $security_context->getToken()->getUser()->getId();
            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository(Notification::class)->findBy(['recipient' => $user, 'isRead' => 0, 'referenceId' => $resource]);
            if ($notifications) {
                foreach ($notifications as $notification) {
                    $notification->setIsRead(1);
                    $em->persist($notification);
                    $em->flush();
                }
                return new Response(json_encode(['success' => true]));
            } else {
                return new Response(json_encode(['success' => false, 'Notifications Not Found']));
            }
        } else {
            return new Response(json_encode(['success' => false, 'Notifications Not Found']));
        }
    }

    public function dispatchReplyEmails()
    {
        $em = $this->getDoctrine()->getManager();
        $comment_list = $em->getRepository(EmailQueue::class)->findAll();
        if ($comment_list) {
            foreach ($comment_list as $commentToSend) {
                $comment = $em->getRepository(Comment::class)->find($commentToSend->getComment());
                $dispatcher = $this->get('event_dispatcher');
                $event = new CommentEvent($comment);
                $dispatcher->dispatch(CommentEvent::NAME, $event);
                $em->remove($commentToSend);
            }
            $em->flush();
        }
    }
}
