<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * AbusiveTerms
 *
 * @ORM\Table(name="abusive_terms")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\AbusiveTermsRepository")
 */
class AbusiveTerms
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
     * @ORM\Column(name="terms", type="string", length=255)
     */
    private $terms;



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
    public function setTerm($terms)
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Comment
     */
    public function getTerms()
    {
        return $this->terms;
    }
}
