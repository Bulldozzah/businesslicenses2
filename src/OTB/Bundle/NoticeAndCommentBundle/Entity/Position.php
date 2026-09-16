<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation;

/**
 * Regulation
 *
 * @ORM\Table(name="position")
 * @ORM\Entity()
 */
class Position
{
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="regulation")
     * @ORM\OrderBy({"createAt" = "ASC"})
     */
    private $comments;

    public function __construct()
    {
        $this->regulation = new ArrayCollection();
    }

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
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;


    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Regulation")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $regulation;

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
     * Get Supporting Attachments
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function setRegulation(Regulation $regulation)
    {
        $this->regulation = $regulation;
        return $this;
    }

    public function getRegulation()
    {
        return $this->regulation;
    }
}
