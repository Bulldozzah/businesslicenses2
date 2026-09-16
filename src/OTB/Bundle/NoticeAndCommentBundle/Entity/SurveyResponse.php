<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * surveyResponse
 *
 * @ORM\Table(name="survey_responses")
 * @ORM\Entity
 */
class SurveyResponse
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
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="surveyResponses")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    private $survey;

    /**
     * @var string
     *
     * @ORM\Column(name="time_taken", type="text")
     */
    private $timeTaken;


    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="surveyResponse")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $surveyAnswers;


    public function __construct()
    {
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
     * Set surveyId
     *
     * @param integer $surveyId
     * @return surveyResponse
     */
    public function setSurvey(Survey $survey)
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get Survey $survey
     *
     * @return integer
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * Set timeTake
     *
     * @param string $timeTake
     * @return surveyResponse
     */
    public function setTimeTaken($timeTaken)
    {
        $this->timeTaken = $timeTaken;

        return $this;
    }

    /**
     * Get timeTake
     *
     * @return string
     */
    public function getTimeTaken()
    {
        return $this->timeTaken;
    }

    /**
     * Get SurveyAnswers for this survey
     *
     * @return ArrayCollection|SurveyAnswers[]
     **/
    public function getSurveyAnswers()
    {
        return $this->surveyAnswers;
    }
}
