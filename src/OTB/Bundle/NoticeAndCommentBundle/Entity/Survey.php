<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Survey
 *
 * @ORM\Table(name="surveys")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\SurveyRepository")
 */
class Survey
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
     * @ORM\Column(name="survey_name", type="string", length=255)
     */
    private $surveyName;

    /**
     * @var integer
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = false;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="Regulation", inversedBy="surveys")
     * @ORM\JoinColumn(name="regulation_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $regulation;

    /**
     * @ORM\OneToMany(targetEntity="Question", mappedBy="survey", cascade={"persist"}, fetch="EAGER")
     * @ORM\OrderBy({"questionOrder" = "ASC"})
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity="SurveyResponse", mappedBy="survey")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $surveyResponses;
    
    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->surveyResponses = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->surveyName;
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

    public function setPublished($published)
    {
        $this->published = $published;
        return $this;
    }

    public function getPublished()
    {
        return (bool)$this->published;
    }
    /**
     * Set surveyName
     *
     * @param string $surveyName
     * @return Survey
     */
    public function setSurveyName($surveyName)
    {
        $this->surveyName = $surveyName;

        return $this;
    }

    /**
     * Get surveyName
     *
     * @return string
     */
    public function getSurveyName()
    {
        return $this->surveyName;
    }

    /**
     * Set regulation
     *
     * @param integer $regulation
     * @return Survey
     */
    public function setRegulation(regulation $regulation = null)
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
     * Get Questions this survey
     *
     * @return ArrayCollection|Question[]
     **/
    public function getQuestions()
    {
        return $this->questions;
    }
    /**
     * Get SurveyResponse for this survey
     *
     * @return ArrayCollection|SurveyResponse[]
     **/
    public function getSurveyResponses()
    {
        return $this->surveyResponses;
    }

    public function addQuestion(Question $questions = null)
    {
        if ($questions) {
            $questions->setSurvey($this);
            $this->questions[] = $questions;
            return $this;
        }

        return;
    }

    public function removeQuestion(Question $questions)
    {
        $this->questions->removeElement($this);
    }
}
