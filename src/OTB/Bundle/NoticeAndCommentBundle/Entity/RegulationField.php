<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField
 *
 * @ORM\Table(name="regulationfields")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldRepository")
 */
class RegulationField implements Translatable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Locale
     */
    private $locale;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    

    public function __toString()
    {
        return $this->fieldlabel;
    }

    /**
     * @var string $fieldlabel
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="fieldlabel", type="string", length=255)
     */
    private $fieldlabel;

    /**
     * @var integer $fieldtype
     *
     * @ORM\Column(name="fieldtype", type="integer")
     */
    private $fieldtype;

    /**
     * @var text $fieldoptions
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="fieldoptions", type="text", nullable=true)
     */
    private $fieldoptions;

    /**
     * @var integer $order
     *
     * @ORM\Column(name="fieldorder", type="integer")
     */
    private $order;

    /**
     * @var boolean $showed
     *
     * @ORM\Column(name="showed", type="boolean")
     */
    private $showed;

    /**
     * @ORM\OneToMany(targetEntity="RegulationFieldData", mappedBy="field",cascade={"persist"})
     */
    protected $fielddatas;

    /**
     * @ORM\OneToMany(targetEntity="RegulationRequirement", mappedBy="regulation",cascade={"persist"})
     * @ORM\OrderBy({"description" = "ASC"})
     */
    protected $requirements;

    /**
     * @ORM\OneToMany(targetEntity="RegulationFieldChoice", mappedBy="field",cascade={"persist"})
     * @ORM\OrderBy({"choicename" = "ASC"})
     */
    protected $fieldchoices;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var datetime $contentChanged
     *
     * @ORM\Column(name="content_changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"fieldlabel", "fieldtype", "fieldoptions", "order", "showed", "fielddatas", "fieldchoices"})
     */
    private $contentChanged;


    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @var User $contentChangedBy
     *
     * @Gedmo\Timestampable(on="change", field={"fieldlabel", "fieldtype", "fieldoptions", "order", "showed", "fielddatas", "fieldchoices"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fielddatas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fieldchoices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->fieldoptions = "0";
        $this->order = 0;
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
     * Set fieldlabel
     *
     * @param string $fieldlabel
     * @return RegulationField
     */
    public function setFieldlabel($fieldlabel)
    {
        $this->fieldlabel = $fieldlabel;
    
        return $this;
    }

    /**
     * Get fieldlabel
     *
     * @return string
     */
    public function getFieldlabel()
    {
        return $this->fieldlabel;
    }

    /**
     * Set fieldtype
     *
     * @param integer $fieldtype
     * @return RegulationField
     */
    public function setFieldtype($fieldtype)
    {
        $this->fieldtype = $fieldtype;
    
        return $this;
    }

    /**
     * Get fieldtype
     *
     * @return integer
     */
    public function getFieldtype()
    {
        return $this->fieldtype;
    }

    /**
     * Set fieldoptions
     *
     * @param string $fieldoptions
     * @return RegulationField
     */
    public function setFieldoptions($fieldoptions)
    {
        $this->fieldoptions = $fieldoptions;
    
        return $this;
    }

    /**
     * Get fieldoptions
     *
     * @return string
     */
    public function getFieldoptions()
    {
        return $this->fieldoptions;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return RegulationField
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set showed
     *
     * @param boolean $showed
     * @return RegulationField
     */
    public function setShowed($showed)
    {
        $this->showed = $showed;
    
        return $this;
    }

    /**
     * Get showed
     *
     * @return boolean
     */
    public function getShowed()
    {
        return $this->showed;
    }

    /**
     * Add fielddatas
     *
     * @param \OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldData $fielddatas
     * @return RegulationField
     */
    public function addFielddata(\OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldData $fielddatas)
    {
        $this->fielddatas[] = $fielddatas;
    
        return $this;
    }

    /**
     * Remove fielddatas
     *
     * @param \OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldData $fielddatas
     */
    public function removeFielddata(\OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldData $fielddatas)
    {
        $this->fielddatas->removeElement($fielddatas);
    }

    /**
     * Get fielddatas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFielddatas()
    {
        return $this->fielddatas;
    }

    /**
     * Add fieldchoices
     *
     * @param \OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldChoice $fieldchoices
     * @return RegulationField
     */
    public function addFieldchoice(\OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldChoice $fieldchoices)
    {
        $fieldchoices->setField($this);

        $this->fieldchoices[] = $fieldchoices;
    
        return $this;
    }

    /**
     * Remove fieldchoices
     *
     * @param \OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldChoice $fieldchoices
     */
    public function removeFieldchoice(\OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldChoice $fieldchoices)
    {
        $this->fieldchoices->removeElement($fieldchoices);
    }

    /**
     * Get fieldchoices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFieldchoices()
    {
        return $this->fieldchoices;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return RegulationField
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return RegulationField
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set contentChanged
     *
     * @param \DateTime $contentChanged
     * @return RegulationField
     */
    public function setContentChanged($contentChanged)
    {
        $this->contentChanged = $contentChanged;
    
        return $this;
    }

    /**
     * Get contentChanged
     *
     * @return \DateTime
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * Set createdBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $createdBy
     * @return RegulationField
     */
    public function setCreatedBy(\WebmastersAfrica\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;
    
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $updatedBy
     * @return RegulationField
     */
    public function setUpdatedBy(\WebmastersAfrica\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;
    
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set contentChangedBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $contentChangedBy
     * @return RegulationField
     */
    public function setContentChangedBy(\WebmastersAfrica\UserBundle\Entity\User $contentChangedBy = null)
    {
        $this->contentChangedBy = $contentChangedBy;
    
        return $this;
    }

    /**
     * Get contentChangedBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User
     */
    public function getContentChangedBy()
    {
        return $this->contentChangedBy;
    }
}
