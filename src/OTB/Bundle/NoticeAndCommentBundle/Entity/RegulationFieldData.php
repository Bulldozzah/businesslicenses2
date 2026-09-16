<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldData
 *
 * @ORM\Table(name="regulationfielddata")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldDataRepository")
 */
class RegulationFieldData implements Translatable
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

    /**
     * @var text $fielddata
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="fielddata", type="text")
     */
    private $fielddata;

    /**
     * @var integer $regulation_id
     *
     * @ORM\Column(name="regulation_id", type="integer")
     */
    private $regulation_id;

    /**
     * @var integer $field_id
     *
     * @ORM\Column(name="field_id", type="integer")
     */
    private $field_id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Regulation", inversedBy="fielddatas")
     * @ORM\JoinColumn(name="regulation_id", referencedColumnName="id")
     */
    protected $regulation;
    
     /**
     * @ORM\ManyToOne(targetEntity="RegulationField", inversedBy="fielddatas")
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
     * @Gedmo\Timestampable(on="change", field={"fielddata", "regulation", "field"})
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
     * @Gedmo\Timestampable(on="change", field={"fielddata", "regulation", "field"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    public function __construct()
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
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
     * Set fielddata
     *
     * @param string $fielddata
     * @return RegulationFieldData
     */
    public function setFielddata($fielddata)
    {
        if ($fielddata == "") {
            $this->fielddata = null;
        } else {
            $this->fielddata = $fielddata;
        }
        return $this;
    }

    /**
     * Get fielddata
     *
     * @return string
     */
    public function getFielddata()
    {
        return $this->fielddata;
    }

    /**
     * Set regulation_id
     *
     * @param integer $regulationId
     * @return RegulationFieldData
     */
    public function setRegulationId($regulationId)
    {
        $this->regulation_id = $regulationId;
    
        return $this;
    }

    /**
     * Get regulation_id
     *
     * @return integer
     */
    public function getRegulationId()
    {
        return $this->regulation_id;
    }

    /**
     * Set field_id
     *
     * @param integer $fieldId
     * @return RegulationFieldData
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
     * Set Regulation
     *
     * @param Regulation $regulation
     * @return RegulationFieldData
     */
    public function setRegulation(\OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation $regulation = null)
    {
        $this->regulation = $regulation;
    
        return $this;
    }

    /**
     * Get Regulation
     *
     * @return \OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation
     */
    public function getRegulation()
    {
        return $this->regulation;
    }

    /**
     * Set field
     *
     * @param \OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField $field
     * @return RegulationFieldData
     */
    public function setField(\OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField $field = null)
    {
        $this->field = $field;
    
        return $this;
    }

    /**
     * Get field
     *
     * @return \OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return RegulationFieldData
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
     * @return RegulationFieldData
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
     * @return RegulationFieldData
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
     * @return RegulationFieldData
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
     * @return RegulationFieldData
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
     * @return RegulationFieldData
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
