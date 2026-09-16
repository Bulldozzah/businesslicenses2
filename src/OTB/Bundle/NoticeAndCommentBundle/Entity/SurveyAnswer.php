<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * surveyAnswer
 *
 * @ORM\Table(name="survey_answers")
 * @ORM\Entity
 */
class SurveyAnswer
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
     *
     * @ORM\Column(name="survey_response_id", type="integer")
     */
    /**
     * @ORM\ManyToOne(targetEntity="SurveyResponse", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="survey_response_id", referencedColumnName="id")
     */
    private $surveyResponse;

      /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="answer_value", type="text")
     */
    private $answerValue;


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
     * Set surveyResponseId
     *
     * @param integer $surveyResponseId
     * @return surveyAnswer
     */
    public function setSurveyResponse(SurveyResponse $surveyResponse = null)
    {
        $this->surveyResponse = $surveyResponse;

        return $this;
    }

    /**
     * Get SurveyResponse
     *
     * @return integer
     */
    public function getSurveyResponse()
    {
        return $this->surveyResponse;
    }

    /**
     * Set questionId
     *
     * @param integer $questionId
     * @return surveyAnswer
     */
    public function setQuestion(Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get questionId
     *
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answerValue
     *
     * @param string $answerValue
     * @return surveyAnswer
     */
    public function setAnswerValue($answerValue)
    {
        $this->answerValue = $answerValue;

        return $this;
    }

    /**
     * Get answerValue
     *
     * @return string
     */
    public function getAnswerValue()
    {
        return $this->answerValue;
    }
}
