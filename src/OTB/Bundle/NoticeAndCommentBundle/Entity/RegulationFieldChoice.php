<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldChoice
 *
 * @ORM\Table(name="regulationfieldchoice")
 * @ORM\Entity
 */
class RegulationFieldChoice implements Translatable
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
        return $this->choicename;
    }

    /**
     * @var string $choicename
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="choicename", type="string", length=255)
     */
    private $choicename;

    /**
     * @var integer $order
     *
     * @ORM\Column(name="choiceorder", type="integer")
     */
    private $order;

    /**
     * @var boolean $showed
     *
     * @ORM\Column(name="showed", type="boolean")
     */
    private $showed;

    /**
     * @var integer $field_id
     *
     * @ORM\Column(name="field_id", type="integer")
     */
    private $field_id;

     /**
     * @ORM\ManyToOne(targetEntity="RegulationField", inversedBy="fieldchoices",cascade={"persist"})
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     */
    protected $field;

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
     * @Gedmo\Timestampable(on="change", field={"choicename", "order", "showed", "field"})
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
     * @Gedmo\Timestampable(on="change", field={"choicename", "order", "showed", "field"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    public function __construct()
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->showed = true;
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
     * Set choicename
     *
     * @param string $choicename
     * @return RegulationFieldChoice
     */
    public function setChoicename($choicename)
    {
        $this->choicename = $choicename;
    
        return $this;
    }

    /**
     * Get choicename
     *
     * @return string
     */
    public function getChoicename()
    {
        return $this->choicename;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return RegulationFieldChoice
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
     * @return RegulationFieldChoice
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
     * Set field_id
     *
     * @param integer $fieldId
     * @return RegulationFieldChoice
     */
    public function setFieldId($fieldId)
    {
        $this->field_id = $fieldId;
    
        return $this;
    }

    /**
     * Get field_id
     *
     * @return integer
     */
    public function getFieldId()
    {
        return $this->field_id;
    }

    /**
     * Set field
     *
     * @param OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField $field
     * @return RegulationFieldChoice
     */
    public function setField(\OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField $field = null)
    {
        $this->field = $field;
    
        return $this;
    }

    /**
     * Get field
     *
     * @return OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return RegulationFieldChoice
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
     * @return RegulationFieldChoice
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
     * @return RegulationFieldChoice
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
     * @return RegulationFieldChoice
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
     * @return RegulationFieldChoice
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
     * @return RegulationFieldChoice
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
