<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Choice
 *
 * @ORM\Table(name="choices")
 * @ORM\Entity
 */
class Choice
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
     * @var integer
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="choices")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="choice_text", type="text")
     */
    private $choiceText;

    /**
     * @var integer
     *
     * @ORM\Column(name="choice_order", type="integer")
     */
    private $choiceOrder;
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
     * Set Question
     *
     * @param Question $question
     * @return Choice
     */
    public function setQuestion(Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return Question $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set choiceText
     *
     * @param string $choiceText
     * @return Choice
     */
    public function setChoiceText($choiceText)
    {
        $this->choiceText = $choiceText;

        return $this;
    }

    /**
     * Get choiceText
     *
     * @return string
     */
    public function getChoiceText()
    {
        return $this->choiceText;
    }

    /**
     * Set choiceOrder
     *
     * @param integer $choiceOrder
     * @return Choice
     */
    public function setChoiceOrder($choiceOrder)
    {
        $this->choiceOrder = $choiceOrder;

        return $this;
    }

    /**
     * Get choiceOrder
     *
     * @return integer
     */
    public function getChoiceOrder()
    {
        return $this->choiceOrder;
    }

}
