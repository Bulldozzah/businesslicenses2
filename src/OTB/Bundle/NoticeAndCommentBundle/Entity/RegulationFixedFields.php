<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * RegulationFixedField
 *
 * @ORM\Table(name="regulationfixedfield")
 * @ORM\Entity()
 * @Gedmo\Loggable
 */
class RegulationFixedFields
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
     * @Gedmo\Versioned
     * @ORM\Column(name="fieldid", type="integer")
     */
    private $fieldid;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="fieldlabel", type="string", length=255)
     */
    private $fieldlabel;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="showed", type="boolean")
     */
    private $showed;

    /**
     * @var string
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="required", type="boolean")
     */
    private $required;


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
     * Set fieldid
     *
     * @param integer $fieldid
     * @return RegulationFixedField
     */
    public function setFieldid($fieldid)
    {
        $this->fieldid = $fieldid;

        return $this;
    }

    /**
     * Get fieldid
     *
     * @return integer
     */
    public function getFieldid()
    {
        return $this->fieldid;
    }

    /**
     * Set fieldlabel
     *
     * @param string $fieldlabel
     * @return RegulationFixedField
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
     * Set showed
     *
     * @param string $showed
     * @return RegulationFixedField
     */
    public function setShowed($showed)
    {
        $this->showed = $showed;

        return $this;
    }

    /**
     * Get showed
     *
     * @return string
     */
    public function getShowed()
    {
        return $this->showed;
    }

    /**
     * Set required
     *
     * @param string $required
     * @return RegulationFixedField
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return string
     */
    public function getRequired()
    {
        return $this->required;
    }
}
