<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use WebmastersAfrica\UserBundle\Entity\User;
use OTB\Bundle\NoticeAndCommentBundle\Entity\EmailQueue;

/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="comment", type="string", length=255)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="new_comment", type="string", length=255)
     */
    private $newComment;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Regulation", inversedBy="comments")
     * @ORM\JoinColumn(name="regulation_id", referencedColumnName="id")
     *
     */
    private $regulation;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var integer
     *  @ORM\OneToMany(targetEntity="Like", mappedBy="comment")
     */
    private $likes;
    /**
     * @var integer
     *  @ORM\OneToMany(targetEntity="EmailQueue", mappedBy="comment")
     */
    private $emailQueue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_abusive", type="integer")
     */
    private $isAbusive = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_admin", type="integer")
     */
    private $isAdmin = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="string", nullable=true)
     */
    private $deletedAt;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\WebmastersAfrica\UserBundle\Entity\User", inversedBy="comments")
     *  @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    private $user;

    /**
     * One Comment has Many Replies.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parent")
     */
    private $children;

    /**
     * One Comment has Many Replies.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parentRight")
     */
    private $childrenRight;

    /**
     * @var integer
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position = 0;

    /**
     * @var integer
     * @ORM\Column(name="check_abusive", type="string", length=255)
     */
    private $checkAbusive = 0;

    /**
     * @var integer
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status = 0;

    /**
     * Attach documents
     * @var string
     *
     * @ORM\Column(name="documents", type="string", length=255)
     */
    private $document;

    /**
     * Many replies have One Comment.
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     *
     */
    private $parent;

    /**
     * Many replies have One Comment.
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="childrenRight")
     * @ORM\JoinColumn(name="parent_right", referencedColumnName="id")
     *
     */
    private $parentRight;

    /**
     * @var integer
     * @ORM\Column(name="publish", type="integer")
     */
    private $publish = 1;

    /**
     * @var integer
     * @ORM\Column(name="deleted", type="integer")
     */
    private $deleted = 0;

    /**
     * @var integer
     * @ORM\Column(name="hidden", type="integer")
     */
    private $hidden = 0;

    /**
     * @var integer
     * @ORM\Column(name="upvote_count", type="integer")
     */
    private $upvoteCount = 0;

    /**
     * @var integer
     * @ORM\Column(name="pending_review", type="integer")
     */
    private $pendingReview = 0;

    public function __construct()
    {
        $this->createAt = new \DateTime("now");
        $this->updatedAt = new \DateTime("now");
        $this->children = new ArrayCollection();
        $this->childrenRight = new ArrayCollection();
    }


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
     * @return Comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Set New comment
     *
     * @param string $comment
     * @return Comment
     */
    public function setNewComment($newComment)
    {
        $this->newComment = $newComment;

        return $this;
    }

    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Status
     *
     * @param string $comment
     * @return Comment
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Get comment
     *
     * @return string
     */
    public function getNewComment()
    {
        return $this->newComment;
    }

    /**
     * Set regulation
     *
     * @param integer $regulation
     * @return Comment
     */
    public function setRegulation($regulation)
    {
        $this->regulation = $regulation;

        return $this;
    }

    /**
     * Get regulation
     *
     * @return integer
     */
    public function getRegulation()
    {
        return $this->regulation;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return Comment
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }
    /**
     * Get createAt
     *
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    public function setPublish($publish)
    {
        $this->publish = $publish;
        return $this;
    }
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }
    
    public function getPublish()
    {
        return $this->publish;
    }

    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function getHidden()
    {
        return $this->hidden;
    }
    public function setUpvoteCount($upvoteCount)
    {
        $this->upvoteCount = $upvoteCount;
        return $this;
    }

    public function getUpvoteCount()
    {
        return $this->upvoteCount;
    }
    
    public function setPendingReview($review)
    {
        $this->pendingReview = $review;
        return $this;
    }

    public function getPendingReview()
    {
        return $this->pendingReview;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Comment
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     * @return Comment
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set isAbusive
     *
     * @param boolean $isAbusive
     * @return Comment
     */
    public function setIsAbusive($isAbusive)
    {
        $this->isAbusive = $isAbusive;

        return $this;
    }

    /**
     * Get isAbusive
     *
     * @return boolean
     */
    public function getIsAbusive()
    {
        return $this->isAbusive;
    }

    /**
     * Set is admin
     *
     * @param boolean $isAdmin
     * @return Comment
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Comment
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set user
     *
     * @param integer $user
     * @return Comment
     */
    public function setUser(\WebmastersAfrica\UserBundle\Entity\User $user = null)
    {
        if ($user instanceof User) {
            $this->user = $user;
            return $this;
        } else if (!is_null($user)) {
            $this->user = $user;
            return $this;
        } else {
            return false;
        }
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

    /**
     * Get Reply
     *
     * @return integer
     */
    public function getChildren()
    {
        return $this->children;
    }
    /**
     * Get Reply
     *
     * @return integer
     */
    public function getChildrenRight()
    {
        return $this->childrenRight;
    }

    public function setParent(Comment $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }
    public function setParentRight(Comment $parentRight = null)
    {
        $this->parentRight = $parentRight;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setPosition($position = 0)
    {
        $this->position = $position;

        return $this;
    }

    public function setCheckAbusive($checkAbusive)
    {
        $this->checkAbusive = $checkAbusive;

        return $this;
    }
    public function getCheckAbusive()
    {
        return $this->checkAbusive;
    }

    public function getPosition()
    {
        if ($this->position == 1) {
            return "Agree";
        } else if ($this->position == 2) {
            return "Agree with Reservations";
        } else if ($this->position == 3) {
            return "Disagrees";
        } else {
            return "n/a";
        }
    }

    /**
     * Set documents
     *
     * @param string $documents
     * @return Comment
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get documents
     *
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    public function getParentRight()
    {
        return $this->parentRight;
    }
}
