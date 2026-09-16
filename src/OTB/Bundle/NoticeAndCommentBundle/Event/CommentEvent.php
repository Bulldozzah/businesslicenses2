<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Comment;

/**
 * Comment Event
 */
class CommentEvent extends Event
{
    /**
     * @var Comment $_comment
     *
     **/
    private $_comment;

    const NAME = "comment.replied";

    public function __construct(Comment $comment)
    {
        $this->_comment = $comment;
    }

    public function getComment()
    {
        return $this->_comment;
    }

    public function getUser()
    {
        $user = false;
        if ($this->_comment->getParentRight()) {
            if ($this->_comment->getParentRight()->getUser()) {
                $user[0] = $this->_comment->getParentRight()->getUser()->getEmail();
            }
            if ($this->_comment->getParent()->getUser()) {
                $user[1] = $this->_comment->getParent()->getUser()->getEmail();
            }
        }
        return $user;
    }
}
