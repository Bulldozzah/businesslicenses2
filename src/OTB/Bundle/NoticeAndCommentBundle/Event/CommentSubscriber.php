<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use OTB\Bundle\NoticeAndCommentBundle\Event\CommentEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Comment Subscriber
 */
class CommentSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Swift Mailer $mailer
     **/
    private $_mailer;
    private $_twig;
    private $_request;
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->_mailer = $mailer;
        $this->_twig = $twig;
        $this->_request = new Request;
    }

    public static function getSubscribedEvents()
    {
        return [
                CommentEvent::NAME => 'onCommentReplied'
        ];
    }

    public function onCommentReplied(CommentEvent $event)
    {
        // send an email to user comment published
        $comment = $event->getComment()->getComment();
        $user = $event->getUser();
        if ($user) {
            $body = $this->renderTemplate($event->getComment());
            $domain = "www.businesslicenses.gov.zm";
            $domain = str_replace("www.", "", $domain);
            $message = (\Swift_Message::newInstance()
                    ->setSubject('Business Regulations Consultation Platform: New Reply')
                    ->setFrom('info@' . $domain)
                    ->setTo($user)
                    ->setContentType("text/html")
                    ->setBody(
                        $body
                    ));
            $this->_mailer->send($message);
        }
    }

    public function renderTemplate($comment)
    {
        //error_log($request->getSchemeAndHttpHost());
        return $this->_twig->render(
            'WebmastersAfricaPressBundle:Press:new_reply.html.twig',
            array(
                'comment' => $comment,
                'path' => $this->_request->getSchemeAndHttpHost()
            )
        );
    }
}
