<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Question
 *
 * @ORM\Table(name="questions")
 * @ORM\Entity
 */
class Question
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
     * @Assert\NotBlank(message="Survey details required")
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="questions")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $survey;

    /**
     * @var string
     * @Assert\NotBlank(message="Question type required")
     *
     * @ORM\Column(name="question_type", type="text")
     */
    private $questionsType;

    /**
     * @var string
     * @Assert\NotBlank(message="Question type required")
     * @ORM\Column(name="question_text", type="string", length=255)
     */
    private $questionText;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_required", type="boolean")
     */
    private $isRequired;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_order", type="integer")
     */
    private $questionOrder;

    /**
     * @ORM\OneToMany(targetEntity="\OTB\Bundle\NoticeAndCommentBundle\Entity\Choice", mappedBy="question", cascade={"persist", "remove"}, fetch="EAGER", orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $choices;

    /** @ORM\Column(name="choice_list", type="string") **/
    private $choiceList;

    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="question")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $surveyAnswers;

    public function __construct()
    {
        $this->choices = new ArrayCollection();
        $this->surveyAnswers = new ArrayCollection();
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
     * Set survey
     *
     * @param integer $survey
     * @return Question
     */
    public function setSurvey(Survey $survey = null)
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get survey
     *
     * @return Survey
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set questionsType
     *
     * @param string $questionsType
     * @return Question
     */
    public function setQuestionsType($questionsType)
    {
        $this->questionsType = $questionsType;

        return $this;
    }

    /**
     * Get questionsType
     *
     * @return string
     */
    public function getQuestionsType()
    {
        return $this->questionsType;
    }


    /**
     * Get questionsType description
     *
     * @return string
     */
    public function getQuestionsTypeDescription()
    {
        $question_type = $this->questionsType;
        if ($question_type == 'input') {
            return 'Open Text';
        }

        if ($question_type == 'radio') {
            return 'Select One';
        }

        if ($question_type == 'checkbox') {
            return 'Select Many';
        }

        return $question_type;
    }

    /**
     * Set questionText
     *
     * @param string $questionText
     * @return Question
     */
    public function setQuestionText($questionText)
    {
        $this->questionText = $questionText;

        return $this;
    }

    /**
     * Get Choice List
     *
     * @return string
     */
    public function getChoiceList()
    {
        return $this->choiceList;
    }

    /**public function addChoice(Choice $choices = null)
    {
        $choices->setQuestion($this);
        $this->choices[] = $choices;
        return $this;
    } **/

    public function removeChoice(Choice $choice)
    {
      	$this->choices->removeElement($choice);
      	$choice->setQuestion(null);
        return $this;
    }

    public function addChoice(Choice $choice)
    {
    	if (!$this->choices->contains($choice)) {
						$choice->setQuestion($this);
						$this->choices[] = $choice;
						return $this;
    	}

    	return $this;
    }


    /**
     * Set Choice List
     *
     * @param string $choice List
     * @return Question
     */
    public function setChoiceList($choiceList)
    {
        $this->choiceList = $choiceList;

        return $this;
    }

    /**
     * Get questionText
     *
     * @return string
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }


    /**
     * Set isRequired
     *
     * @param boolean $isRequired
     * @return Question
     */
    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;

        return $this;
    }

    /**
     * Get isRequired
     *
     * @return boolean
     */
    public function getIsRequired()
    {
        return $this->isRequired;
    }

    /**
     * Set questionOrder
     *
     * @param integer $questionOrder
     * @return Question
     */
    public function setQuestionOrder($questionOrder)
    {
        $this->questionOrder = $questionOrder;

        return $this;
    }

    /**
     * Get questionOrder
     *
     * @return integer
     */
    public function getQuestionOrder()
    {
        return $this->questionOrder;
    }

    /**
     * Get Questions this survey
     *
     * @return ArrayCollection|Choice[]
     **/
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Get Questions this survey
     *
     * @return ArrayCollection|SurveyAnswer[]
     **/
    public function getSurveyAnswers()
    {
        return $this->surveyAnswers;
    }
}
