<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ForwardPlanMainCategory
 *
 * @ORM\Table("forward_plans_category")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlanMainCategoryRepository")
 */
class ForwardPlanMainCategory
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /** @var string
     *
     * @ORM\Column(name="description", type="string")
     *
     **/
    private $description;

    /**
     * @ORM\ManyToOne (targetEntity="WebmastersAfrica\LicenseBundle\Entity\BusinessAgency", inversedBy="forwardPlans")
     * @ORM\JoinColumn(name="agency_id", referencedColumnName="id")
     *
     * @Assert\NotBlank(message="Agency Required")
     */
    private $agency;

    /**
     * @var string
     *
     * @ORM\Column(name="period", type="string", length=255)
     * @Assert\NotBlank(message="Forward Start Period Required")
     */
    private $period;

    /**
     * @var string
     *
     * @ORM\Column(name="period_2", type="string", length=255)
     */
    private $period_2;

    /**
     * @var string
     * @ORM\Column(name="slug", length=250, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted = 0;
    /**
     * @var string
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;


    /**
     * @ORM\OneToMany(targetEntity="\OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans", mappedBy="forwardPlanCategory", cascade={"persist"})
     */
    private $forwardPlans;

    public function __construct()
    {
        $this->forwardPlans = new ArrayCollection();
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
    }


    public function __toString()
    {
        return $this->name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        return $this->slug = $slug;
        return $this;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        return $this->deleted = $deleted;
        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        return $this->published = $published;
        return $this;
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
     * Set name
     *
     * @param string $name
     * @return ForwardPlanMainCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set agency
     *
     * @param integer $agency
     * @return ForwardPlans
     */
    public function setAgency(BusinessAgency $agency)
    {
        $this->agency = $agency;

        return $this;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return ForwardPlanMainCategory
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set agency
     *
     * @param integer $agency
     * @return ForwardPlans
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Get agency
     *
     * @return integer
     */
    public function getAgency()
    {

        return $this->agency;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set period
     *
     * @param string $period
     * @return ForwardPlanMainCategory
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get periodforwardPlan
     *
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }


    /**
     * Set period
     *
     * @param string $period
     * @return ForwardPlanMainCategory
     */
    public function setPeriod2($period_2)
    {
        $this->period_2 = $period_2;

        return $this;
    }

    /**
     * Get periodforwardPlan
     *
     * @return string
     */
    public function getPeriod2()
    {
        return $this->period_2;
    }

    /**
     * Set forwardPlan
     *
     * @param string $forwardPlan
     * @return ForwardPlan
     */
    public function addForwardPlan(\OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans $forwardPlans)
    {
        $forwardplans->setForwardPlanMainCategory($this);
        $this->forwardPlans[] = $forwardPlans;

        return $this;
    }


    /**
     * Remove Forward Plans
     *
     * @param ForwardPlan $forwardPlan
     */
    public function removeForwardPlan(\OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans $forwardPlans)
    {
        $this->forwardPlans->removeElement($forwardPlans);
    }

    public function getForwardPlansCount()
    {

        $value=  $this->getForwardPlans()->filter(
            function (ForwardPlans $forwards) {
                return $forwards->getPublished() == true;
            }
        );
        return $value;
    }


    /**
     * Get forwardPlan
     *
     * @return string
     */
    public function getForwardPlans()
    {
        return $this->forwardPlans;
    }
}
